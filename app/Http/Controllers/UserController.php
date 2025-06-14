<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
        ]);

        $user = Auth::user();
        $user->update($request->only('name', 'contact_info'));

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

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }

    // Show force password change form
    public function showForcePasswordForm()
    {
        return view('auth.force-password-change');
    }

    // Handle force password update
    public function forceUpdatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = Hash::make($request->password);

        if ($user->role === 'admin' && $user->adminUser) {
            $user->adminUser->force_password_change = false;
            $user->adminUser->save();
        } elseif ($user->role === 'agency' && $user->agencyUser) {
            $user->agencyUser->force_password_change = false;
            $user->agencyUser->save();
        }

        $user->save();

        return redirect()->route('home')->with('success', 'Password updated successfully.');
    }
}
