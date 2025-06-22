<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Models\User;
use App\Models\PublicUser;

class AuthController extends Controller
{
    use ConfirmsPasswords, VerifiesEmails;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'confirmPasswordForm', 'updatePassword', 'verifyEmail', 'resendVerificationEmail']);
        $this->middleware('auth')->only(['confirmPasswordForm', 'updatePassword', 'verifyEmail', 'resendVerificationEmail']);
        $this->middleware('signed')->only('verifyEmail');
        $this->middleware('throttle:6,1')->only('verifyEmail', 'resendVerificationEmail');
    }

    // ========================
    // 📥 LOGIN METHODS
    // ========================

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginField = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $request->input('username'))->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => __('These credentials do not match our records.'),
            ]);
        }

        if ($user->role === 'public' && $loginField !== 'email') {
            throw ValidationException::withMessages([
                'username' => __('Public users must log in using email.'),
            ]);
        }

        $credentials = [
            $loginField => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo());
        }

        throw ValidationException::withMessages([
            'username' => __('These credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function redirectTo()
    {
        $role = Auth::user()->role;

        return match ($role) {
            'admin'  => '/admin/dashboard',
            'agency' => '/agency/dashboard',
            'public' => '/dashboard',
            default  => '/home',
        };
    }

    // ========================
    // 📝 REGISTER METHODS
    // ========================

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        Auth::login($user);

        return redirect($this->redirectTo());
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'         => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'unique:users'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_info' => ['required', 'string', 'max:255'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'agency_name'  => ['nullable', 'string', 'required_if:role,agency'],
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name'         => $data['name'],
            'username'     => $data['username'],
            'email'        => $data['email'],
            'contact_info' => $data['contact_info'],
            'password'     => Hash::make($data['password']),
            'role'         => 'public',
        ]);

        PublicUser::create([
            'user_id' => $user->user_id,
            'registered_at' => now(),
        ]);

        return $user;
    }

    // ========================
    // 🔁 PASSWORD METHODS
    // ========================

    public function confirmPasswordForm()
    {
        return view('auth.confirm-password');
    }

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

    // ========================
    // 📧 EMAIL VERIFICATION
    // ========================

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
}
