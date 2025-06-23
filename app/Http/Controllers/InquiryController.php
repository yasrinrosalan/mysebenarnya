<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\AgencyUser;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InquiryExport;

class InquiryController extends Controller
{
    // MCMC ADMIN: View All Inquiries
    public function adminIndex()
    {
        $inquiries = Inquiry::with('category', 'attachments')
            ->orderByDesc('submitted_at')
            ->get();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    // MCMC ADMIN: View Inquiry Details
    public function show($id)
    {
        $inquiry = Inquiry::with([
            'category',
            'attachments',
            'auditLogs',
            'assignment.agencyUser'
        ])->findOrFail($id);

        $agencies = AgencyUser::all();

        return view('admin.inquiries.show', compact('inquiry', 'agencies'));
    }

    // MCMC ADMIN: Validate or Reject Inquiry
    public function validateInquiry(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $request->validate([
            'status' => 'required|in:validated,discarded',
            'review_notes' => 'required|string',
        ]);

        $inquiry->update([
            'status' => $request->status,
            'review_notes' => $request->review_notes,
        ]);

        $actionMessage = $request->status === 'validated' ? 'Inquiry Validated' : 'Inquiry Discarded';

        AuditLog::create([
            'action' => $actionMessage,
            'details' => $request->review_notes,
            'timestamp' => now(),
            'inquiry_id' => $inquiry->inquiry_id,
            'user_id' => Auth::id(),
        ]);

        $statusText = $request->status === 'validated' ? 'Inquiry validated.' : 'Inquiry discard.';

        return redirect()->route('admin.inquiries.manage')->with('success', $statusText);
    }

    // Admin: Manage Inquiries View (Pending + History)
    public function manage(Request $request)
    {
        $pendingInquiries = Inquiry::with(['category', 'attachments', 'publicUser'])
            ->whereIn('status', ['pending', 'validated', 'rejected'])
            ->orderByDesc('submitted_at')
            ->get();

        $historyQuery = Inquiry::with(['category', 'attachments', 'publicUser', 'assignment.agencyUser'])
            ->where('status', '!=', 'pending');

        if ($request->filled('status')) {
            $historyQuery->where('status', $request->status);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $historyQuery->whereBetween('submitted_at', [$request->from, $request->to]);
        }

        if ($request->filled('agency_id')) {
            $historyQuery->whereHas('assignment', function ($q) use ($request) {
                $q->where('agency_user_id', $request->agency_id);
            });
        }

        $historyInquiries = $historyQuery->orderByDesc('submitted_at')->get();
        $agencies = User::where('role', 'agency')->get();

        return view('admin.inquiries.manage', compact('pendingInquiries', 'historyInquiries', 'agencies'));
    }

    // Admin: Inquiry Report View + Chart
    public function report(Request $request)
    {
        $query = Inquiry::with('category')->whereNotNull('public_user_id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('submitted_at', [$request->from, $request->to]);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $inquiries = $query->get();

        $monthlyStats = Inquiry::whereNotNull('public_user_id')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('category_id'), fn($q) => $q->where('inquiries.category_id', $request->category_id))
            ->when($request->filled('from') && $request->filled('to'), fn($q) => $q->whereBetween('submitted_at', [$request->from, $request->to]))
            ->join('categories', 'inquiries.category_id', '=', 'categories.category_id')
            ->selectRaw('categories.name as category_name, DATE_FORMAT(submitted_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month', 'categories.name')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        $categories = Category::all();

        return view('admin.inquiries.report', [
            'inquiries' => $inquiries,
            'monthlyStats' => $monthlyStats,
            'categories' => $categories,
        ]);
    }

    // Public: Create Inquiry Form
    public function create()
    {
        $categories = Category::all();
        return view('public.inquiries.create', compact('categories'));
    }

    // Public: Submit New Inquiry
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $inquiry = Inquiry::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'public_user_id' => auth()->id(),
            'is_public' => $request->has('is_public') ? 1 : 0,
            'status' => 'pending',
        ]);

        if ($request->hasFile('evidence')) {
            $filePath = $request->file('evidence')->store('evidence', 'public');

            Attachment::create([
                'inquiry_id' => $inquiry->inquiry_id,
                'file_type' => $request->file('evidence')->getClientOriginalExtension(),
                'url_path' => $filePath,
            ]);
        }

        return redirect()->route('public.inquiries.index')->with('success', 'Inquiry submitted successfully.');
    }

    // Public: View Own Inquiries
    public function index()
    {
        $inquiries = Inquiry::where('public_user_id', Auth::id())
            ->with('category')
            ->orderByDesc('submitted_at')
            ->get();

        return view('public.inquiries.index', compact('inquiries'));
    }

    // Public: Browse Validated Public Inquiries
    public function viewPublic(Request $request)
    {
        $query = Inquiry::where('is_public', 1)
            ->where('status', 'validated')
            ->with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $inquiries = $query->orderByDesc('submitted_at')->get();
        $categories = Category::all();

        return view('public.inquiries.public', compact('inquiries', 'categories'));
    }

    // Agency: View Assigned Inquiries
    public function agencyIndex(Request $request)
    {
        $userId = auth()->id();

        $query = Inquiry::whereHas('assignment', function ($q) use ($userId) {
                $q->where('agency_user_id', $userId);
            })
            ->with(['category', 'assignment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('submitted_at', [$request->from, $request->to]);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $inquiries = $query->orderByDesc('submitted_at')->get();
        $categories = Category::all();

        return view('agency.inquiries.index', compact('inquiries', 'categories'));
    }

    // Agency: View Inquiry Details
    public function agencyShow($id)
    {
        $inquiry = Inquiry::with(['category', 'attachments', 'assignment', 'auditLogs'])->findOrFail($id);
        return view('agency.inquiries.show', compact('inquiry'));
    }

    // Agency: Update Assignment Status (Sync to Inquiry)
    public function updateAssignmentStatus(Request $request, $assignmentId)
    {
        $request->validate([
            'status' => 'required|in:assigned,under_investigation,verified_true,fake,rejected',
            'comment' => 'nullable|string',
        ]);

        $assignment = Assignment::findOrFail($assignmentId);

        $assignment->update([
            'status' => $request->status,
            'comment' => $request->comment,
            'last_updated_at' => now(),
        ]);

        Inquiry::where('inquiry_id', $assignment->inquiry_id)
            ->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully.');
    }

    // Admin: View Audit Logs of an Inquiry
    public function viewAuditLog($id)
    {
        $inquiry = Inquiry::with('auditLogs')->findOrFail($id);
        return view('admin.inquiries.audit', compact('inquiry'));
    }

    // Export: Excel
    public function exportExcel(Request $request)
    {
        return Excel::download(new InquiryExport($request), 'inquiry_report.xlsx');
    }

    // Export: PDF
    public function exportPDF(Request $request)
    {
        $query = Inquiry::with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('submitted_at', [$request->from, $request->to]);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $inquiries = $query->get();

        $pdf = PDF::loadView('admin.inquiries.report_pdf', compact('inquiries'));
        return $pdf->download('inquiry_report.pdf');
    }

    // Relationship: Public User (used only in some contexts)
    public function publicUser()
    {
        return $this->belongsTo(User::class, 'public_user_id', 'id');
    }
}
