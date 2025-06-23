<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersReportExport;
use PDF;

class AdminController extends Controller
{
    /** ========== DASHBOARD ========== */
    public function dashboard(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $role = $request->input('role');
        $agencyUserId = $request->input('agency_id');

        $userQuery = User::query();

        if ($startDate) {
            $userQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $userQuery->whereDate('created_at', '<=', $endDate);
        }

        if ($role) {
            $userQuery->where('role', $role);
        }

        if ($agencyUserId) {
            $userQuery->where('user_id', $agencyUserId);
        }

        $usersByRole = $userQuery->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        $monthlyRegistrations = (clone $userQuery)->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("COUNT(*) as count")
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $agencies = DB::table('agency_users')
            ->select('user_id as id', 'agency_name')
            ->distinct()
            ->get();

        return view('admin.dashboard', compact(
            'usersByRole',
            'monthlyRegistrations',
            'agencies'
        ));
    }

    /** ========== USER MANAGEMENT ========== */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Updated',
            'details' => "Admin updated user ID {$user->user_id} ({$user->name})",
            'timestamp' => now(),
        ]);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $user->update($request->all());

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Updated',
            'details' => "Admin updated user ID {$user->user_id} ({$user->name})",
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Deleted',
            'details' => "Admin deleted user ID {$user->user_id} ({$user->name})",
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('defaultpassword123');
        $user->force_password_change = true;
        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Password Reset',
            'details' => "Admin reset password for user ID {$user->user_id} ({$user->name})",
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Password reset successfully.');
    }

    /** ========== EXPORTS ========== */
    public function exportExcel(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
            'role'       => $request->input('role'),
            'agency_id'  => $request->input('agency_id'),
        ];

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Export Excel',
            'details' => 'Admin exported users report to Excel',
            'timestamp' => now(),
        ]);

        return Excel::download(new UsersReportExport($filters), 'user-report.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $query = User::query();

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->agency_id) {
            $query->where('user_id', $request->agency_id);
        }

        $users = $query->get();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Export PDF',
            'details' => 'Admin exported users report to PDF',
            'timestamp' => now(),
        ]);

        $pdf = PDF::loadView('admin.report-pdf', compact('users'));
        return $pdf->download('user-report.pdf');
    }
}