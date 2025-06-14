<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\AgencyUser;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    use ConfirmsPasswords, VerifiesEmails;

    /**
     * Where to redirect users after password confirmation or email verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Middleware configuration.
     */
    public function __construct()
    {
        $this->middleware('auth')->only([
            'confirmPasswordForm',
            'updatePassword',
            'verifyEmail',
            'resendVerificationEmail'
        ]);

        $this->middleware('signed')->only('verifyEmail');
        $this->middleware('throttle:6,1')->only('verifyEmail', 'resendVerificationEmail');
    }

    // 🔐 Confirm Password Form
    public function confirmPasswordForm()
    {
        return view('auth.confirm-password');
    }

    // 🔁 Forgot Password (Standard)
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link has been sent.')
            : back()->with('error', 'Unable to send reset link. Please try again.');
    }

    // 🔁 Forgot Password (Admin)
    public function showAdminReset()
    {
        return view('auth.admin-forgot-password');
    }

    public function sendAdminResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->isAdminUser()) {
            return back()->with('error', 'This email does not belong to an admin user.');
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Admin password reset link sent successfully.')
            : back()->with('error', 'Failed to send reset link. Please try again.');
    }

    // 🔁 Reset Password
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // 📧 Email Verification
    public function verifyEmail(Request $request)
    {
        $request->fulfill();
        return redirect($this->redirectTo);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    }

    // 🔒 Password Change (force change after login)
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        if ($user->isAdminUser() && $user->adminUser) {
            $user->adminUser->force_password_change = false;
            $user->adminUser->save();
        }

        if ($user->isAgencyUser() && $user->agencyUser) {
            $user->agencyUser->force_password_change = false;
            $user->agencyUser->save();
        }

        Log::info("User ID {$user->id} changed their password", [
            'role' => $user->role,
            'time' => now(),
        ]);

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard')->with('success', 'Password updated successfully.'),
            'agency' => redirect()->route('agency.dashboard')->with('success', 'Password updated successfully.'),
            'public' => redirect()->route('public.dashboard')->with('success', 'Password updated successfully.'),
            default => redirect('/home')->with('success', 'Password updated successfully.'),
        };
    }
}
