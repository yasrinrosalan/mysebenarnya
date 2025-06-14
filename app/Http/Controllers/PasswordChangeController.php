<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use App\Models\AgencyUser;

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

        // Reset force_password_change in relevant table
        if ($user->isAdminUser() && $user->adminUser) {
            $user->adminUser->force_password_change = false;
            $user->adminUser->save();
        }

        if ($user->isAgencyUser() && $user->agencyUser) {
            $user->agencyUser->force_password_change = false;
            $user->agencyUser->save();
        }

        return redirect()->intended('/dashboard')->with('success', 'Password updated successfully.');
    }
}
