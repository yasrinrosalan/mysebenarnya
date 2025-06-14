<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Exports\UsersReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $role = $request->input('role');
        $agencyUserId = $request->input('agency_id'); // Actually user_id from agency_users

        // Base user query
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

        // Bar chart: Total users by role
        $usersByRole = $userQuery->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        // Line chart: Monthly user registrations (last 6 months)
        $monthlyRegistrations = (clone $userQuery)->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("COUNT(*) as count")
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Get list of agencies using user_id as identifier
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

    public function exportExcel(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
            'role'       => $request->input('role'),
            'agency_id'  => $request->input('agency_id'),
        ];

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

        $pdf = PDF::loadView('admin.report-pdf', compact('users'));
        return $pdf->download('user-report.pdf');
    }
}
