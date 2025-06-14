<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AgencyUser;
use Illuminate\Support\Str;


class AgencyController extends Controller
{
    public function create()
    {
        return view('admin.register-agency');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'agency_name' => 'required|string|max:255',
            'agency_contact' => 'required|string|max:255',
        ]);

        // Auto-generate username and password
        // $generatedUsername = strtolower(Str::slug($request->agency_name)) . rand(100, 999);
        $generatedPassword = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->agency_name,
            'password' => Hash::make($generatedPassword),
            'contact_info' => $request->agency_contact,
            'role' => 'agency',
            'is_verified' => 1,
        ]);


        // Create agency user profile
        AgencyUser::create([
            'user_id' => $user->user_id,
            'agency_name' => $request->agency_name,
            'agency_contact' => $request->agency_contact,
            'username' => $request->agency_name,
            'force_password_change' => true,
            'admin_id' => Auth::id(),
        ]);

        // OPTIONAL: show generated credentials on screen
        return back()->with('success', 'Agency registered successfully.')->with([
            'generated_username' => $request->agency_name,
            'generated_password' => $generatedPassword,
        ]);
    }
}
