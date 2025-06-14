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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $inquiry = Inquiry::with(['category', 'attachments'])->findOrFail($id);
        $agencies = AgencyUser::all(); // for assignment dropdown

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

        AuditLog::create([
            'action' => 'Validate Inquiry',
            'details' => $request->review_notes,
            'timestamp' => now(),
            'inquiry_id' => $inquiry->inquiry_id,
            'user_id' => Auth::id(),
        ]);

        $statusText = $request->status === 'validated' ? 'Inquiry validated.' : 'Inquiry discard.';

        return redirect()->route('admin.inquiries.manage')->with('success', $statusText);


    }

    public function publicUser()
    {
        return $this->belongsTo(User::class, 'public_user_id', 'id');
    }


    public function manage(Request $request)
    {
        $query = Inquiry::with(['category', 'attachments', 'publicUser']);


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('submitted_at', [$request->from, $request->to]);
        }

        if ($request->filled('agency_id')) {
            $query->whereHas('assignment', function ($q) use ($request) {
                $q->where('agency_user_id', $request->agency_id);
            });
        }

        $inquiries = $query->orderByDesc('submitted_at')->get();

        $agencies = User::where('role', 'agency')->get(); // for filter dropdown

        return view('admin.inquiries.manage', compact('inquiries', 'agencies'));
    }

    public function report(Request $request)
{
    $query = Inquiry::with('category');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('from') && $request->filled('to')) {
        $query->whereBetween('submitted_at', [$request->from, $request->to]);
    }

    $inquiries = $query->get();

    // Grouped data for chart (monthly)
    $monthlyStats = Inquiry::selectRaw('MONTH(submitted_at) as month, COUNT(*) as total')
        ->groupBy(DB::raw('MONTH(submitted_at)'))
        ->orderBy(DB::raw('MONTH(submitted_at)'))
        ->pluck('total', 'month');

    return view('admin.inquiries.report', [
        'inquiries' => $inquiries,
        'monthlyStats' => $monthlyStats,
    ]);
}

    // MCMC ADMIN: Assign Inquiry to Agency
    public function assignInquiry(Request $request, $id)
    {
        $request->validate([
            'agency_user_id' => 'required|exists:agency_user,user_id',
            'comment' => 'nullable|string',
        ]);

        Assignment::create([
            'inquiry_id' => $id,
            'agency_user_id' => $request->agency_user_id,
            'status' => 'assigned',
            'assigned_at' => now(),
            'last_updated_at' => now(),
            'comment' => $request->comment,
        ]);

        Inquiry::where('inquiry_id', $id)->update(['status' => 'assigned']);

        AuditLog::create([
            'action' => 'Assigned Inquiry',
            'details' => 'Assigned to agency ID: ' . $request->agency_user_id,
            'timestamp' => now(),
            'inquiry_id' => $id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry assigned to agency.');
    }

    // PUBLIC: Show Create Inquiry Form
    public function create()
    {
        $categories = Category::all();
        return view('public.inquiries.create', compact('categories'));
    }

    // PUBLIC: Store New Inquiry
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required',
        'category_id' => 'required|exists:categories,category_id', // ✅ FIXED TABLE NAME
        'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        'is_public' => 'nullable',
    ]);

    $inquiry = Inquiry::create([
        'title' => $request->title,
        'description' => $request->description,
        'submitted_at' => now(),
        'status' => 'pending',
        'is_public' => $request->has('is_public'),
        'public_user_id' => Auth::id(),
        'category_id' => $request->category_id,
    ]);

    if ($request->hasFile('evidence')) {
        $path = $request->file('evidence')->store('evidence');

        Attachment::create([
            'file_type' => $request->file('evidence')->getClientOriginalExtension(),
            'url_path' => $path,
            'uploaded_at' => now(),
            'inquiry_id' => $inquiry->inquiry_id,
        ]);
    }

    return redirect()->route('public.inquiries.index')->with('success', 'Inquiry submitted successfully.');
}


    // PUBLIC: View Own Inquiries
    public function index()
    {
        $inquiries = Inquiry::where('public_user_id', Auth::id())
            ->with('category')
            ->orderByDesc('submitted_at')
            ->get();

        return view('public.inquiries.index', compact('inquiries'));
    }

    // PUBLIC: Browse All Public Inquiries
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


}
