<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Show the default user password reset form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link to standard users.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link has been sent.')
            : back()->with('error', 'Unable to send reset link. Please try again.');
    }

    /**
     * Show the admin password reset form.
     */
    public function showAdminReset()
    {
        return view('auth.admin-forgot-password');
    }

    /**
     * Handle sending password reset link for admin users only.
     */
    public function sendAdminResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and is an admin
        if (!$user || !$user->isAdminUser()) {
            return back()->with('error', 'This email does not belong to an admin user.');
        }

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Admin password reset link sent successfully.')
            : back()->with('error', 'Failed to send reset link. Please try again.');
    }
}
