<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use App\Models\AgencyUser;
use Illuminate\Support\Facades\Log;

class PasswordChangeController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        // Reset force_password_change in the relevant model
        if ($user->isAdminUser() && $user->adminUser) {
            $user->adminUser->force_password_change = false;
            $user->adminUser->save();
        }

        if ($user->isAgencyUser() && $user->agencyUser) {
            $user->agencyUser->force_password_change = false;
            $user->agencyUser->save();
        }

        // Log password change (optional)
        Log::info("User ID {$user->id} changed their password", [
            'role' => $user->role,
            'time' => now(),
        ]);

        // Redirect based on role
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('success', 'Password updated successfully.'),
            'agency' => redirect()->route('agency.dashboard')->with('success', 'Password updated successfully.'),
            'public' => redirect()->route('public.dashboard')->with('success', 'Password updated successfully.'),
            default => redirect('/home')->with('success', 'Password updated successfully.'),
        };
    }
}
