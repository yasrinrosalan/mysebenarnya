<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        return view('profile.edit');
    }

    /**
     * Handle the profile update request.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'department' => 'nullable|string|max:255',
            'agency_name' => 'nullable|string|max:255',
            'agency_contact' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->contact_info = $request->contact_info;

        if ($request->hasFile('profile_picture')) {
            $filename = 'profile_' . $user->id . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = $request->file('profile_picture')->storeAs('profile_pictures', $filename, 'public');
            $user->profile_picture_url = $path;
        }

        // Admin-only updates
        if ($user->isAdminUser() && $user->adminUser) {
            $user->adminUser->department = $request->department;
            $user->adminUser->save();
        }

        // Agency-only updates
        if ($user->isAgencyUser() && $user->agencyUser) {
            $user->agencyUser->agency_name = $request->agency_name;
            $user->agencyUser->agency_contact = $request->agency_contact;
            $user->agencyUser->save();
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
