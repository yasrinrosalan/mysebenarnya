<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Illuminate\Support\Str;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'department' => 'nullable|string|max:255',
            'agency_name' => 'nullable|string|max:255',
            'agency_contact' => 'nullable|string|max:255',
        ]);

        $changes = [];

        if ($user->name !== $request->name) {
            $changes['name'] = ['old' => $user->name, 'new' => $request->name];
            $user->name = $request->name;
        }

        if ($user->contact_info !== $request->contact_info) {
            $changes['contact_info'] = ['old' => $user->contact_info, 'new' => $request->contact_info];
            $user->contact_info = $request->contact_info;
        }

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if it exists
            if ($user->profile_picture_url && \Storage::disk('public')->exists($user->profile_picture_url)) {
                \Storage::disk('public')->delete($user->profile_picture_url);
            }

            // Generate unique filename
            $uniqueFilename = Str::uuid() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $path = $request->file('profile_picture')->storeAs('profile_pictures', $uniqueFilename, 'public');

            $changes['profile_picture_url'] = ['old' => $user->profile_picture_url, 'new' => $path];
            $user->profile_picture_url = $path;
        }

        // Admin-only updates
        if ($user->isAdminUser() && $user->adminUser) {
            if ($user->adminUser->department !== $request->department) {
                $changes['department'] = ['old' => $user->adminUser->department, 'new' => $request->department];
                $user->adminUser->department = $request->department;
            }
            $user->adminUser->save();
        }

        // Agency-only updates
        if ($user->isAgencyUser() && $user->agencyUser) {
            if ($user->agencyUser->agency_name !== $request->agency_name) {
                $changes['agency_name'] = ['old' => $user->agencyUser->agency_name, 'new' => $request->agency_name];
                $user->agencyUser->agency_name = $request->agency_name;
            }
            if ($user->agencyUser->agency_contact !== $request->agency_contact) {
                $changes['agency_contact'] = ['old' => $user->agencyUser->agency_contact, 'new' => $request->agency_contact];
                $user->agencyUser->agency_contact = $request->agency_contact;
            }
            $user->agencyUser->save();
        }

        $user->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => "Updated Profile",
            'details' => "User updated their profile information",
            'timestamp' => now(),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}