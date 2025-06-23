<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\AgencyUser;
use App\Models\AuditLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersReportExport;
use PDF;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /** ========== USER PROFILE ========== */
    public function showProfile()
    {
        return view('users.profile', ['user' => Auth::user()]);
    }

    public function edit()
    {
        return view('users.edit_profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // Generate a unique filename using UUID
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store in /storage/app/public/uploads/profile_pictures/
            $path = $file->storeAs('uploads/profile_pictures', $filename, 'public');

            // Optional: delete old profile picture if exists
            if ($user->profile_picture_url && \Storage::disk('public')->exists($user->profile_picture_url)) {
                \Storage::disk('public')->delete($user->profile_picture_url);
            }

            $user->profile_picture_url = $path;
        }

        $user->name = $request->name;
        $user->contact_info = $request->contact_info;
        $user->save();

        AuditLog::create([
            'user_id' => $user->user_id,
            'action' => 'Profile Updated',
            'details' => "User updated their profile information",
            'timestamp' => now(),
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }


    public function changePasswordForm()
    {
        return view('users.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password']);
        }

        $user->password = Hash::make($request->new_password);
        $user->force_password_change = false;
        $user->save();

        AuditLog::create([
            'user_id' => $user->user_id,
            'action' => 'Password Changed',
            'details' => "User changed their password",
            'timestamp' => now(),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }

    public function showForcePasswordForm()
    {
        return view('auth.force-password-change');
    }

    public function forceUpdatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);

        if ($user->role === 'admin' && $user->adminUser) {
            $user->adminUser->force_password_change = false;
            $user->adminUser->save();
        } elseif ($user->role === 'agency' && $user->agencyUser) {
            $user->agencyUser->force_password_change = false;
            $user->agencyUser->save();
        }

        $user = Auth::user();
        $user->save();

        AuditLog::create([
            'user_id' => $user->user_id,
            'action' => 'Forced Password Change',
            'details' => "User completed forced password change",
            'timestamp' => now(),
        ]);

        return redirect()->route('home')->with('success', 'Password updated successfully.');
    }

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

    public function editUser($id) // renamed to avoid conflict with edit() above
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $user->update($request->all());

        AuditLog::create([
            'user_id' => $user->user_id,
            'action' => 'Profile Updated',
            'details' => "User updated their profile information",
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
            'action' => 'Admin Deleted User',
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
            'user_id' => $user->user_id,
            'action' => 'Password Changed',
            'details' => "User changed their password",
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Password reset successfully.');
    }

    /** ========== EXPORT ========== */
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
            'details' => 'User exported user report to Excel',
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

        // === 📊 Chart Data Generation ===
        $usersByRole = $query->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        $monthlyRegistrations = (clone $query)->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("COUNT(*) as count")
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // === 📈 Generate Base64 Images with QuickChart.io ===
        $usersByRoleChart = 'data:image/png;base64,' . base64_encode(
            file_get_contents('https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'bar',
                'data' => [
                    'labels' => array_keys($usersByRole),
                    'datasets' => [[
                        'label' => 'Users by Role',
                        'data' => array_values($usersByRole),
                        'backgroundColor' => ['#4e73df', '#1cc88a', '#f6c23e'],
                    ]]
                ],
                'options' => ['plugins' => ['legend' => ['display' => false]]]
            ])))
        );

        $monthlyRegistrationsChart = 'data:image/png;base64,' . base64_encode(
            file_get_contents('https://quickchart.io/chart?c=' . urlencode(json_encode([
                'type' => 'line',
                'data' => [
                    'labels' => array_keys($monthlyRegistrations),
                    'datasets' => [[
                        'label' => 'Monthly Registrations',
                        'data' => array_values($monthlyRegistrations),
                        'fill' => true,
                        'borderColor' => '#36b9cc',
                        'backgroundColor' => 'rgba(54, 185, 204, 0.2)',
                        'tension' => 0.4,
                    ]]
                ]
            ])))
        );

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Export PDF',
            'details' => 'User exported user report to PDF with charts',
            'timestamp' => now(),
        ]);

        $pdf = PDF::loadView('admin.report-pdf', compact('users', 'usersByRoleChart', 'monthlyRegistrationsChart'));
        return $pdf->download('user-report.pdf');
    }




    /** ========== AGENCY REGISTRATION ========== */
    public function createAgency()
    {
        return view('admin.register-agency');
    }

    public function storeAgency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'agency_name' => 'required|string|max:255',
            'agency_contact' => 'required|string|max:255',
        ]);

        $generatedPassword = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->agency_name,
            'password' => Hash::make($generatedPassword),
            'contact_info' => $request->agency_contact,
            'role' => 'agency',
            'email_verified_at' => now(),
        ]);

        AgencyUser::create([
            'user_id' => $user->user_id,
            'agency_name' => $request->agency_name,
            'agency_contact' => $request->agency_contact,
            'username' => $request->agency_name,
            'force_password_change' => true,
            'admin_id' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Register Agency',
            'details' => "Admin registered new agency: {$request->agency_name}",
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Agency registered successfully.')->with([
            'generated_username' => $request->agency_name,
            'generated_password' => $generatedPassword,
        ]);
    }
}