<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     * This will be overridden by redirectTo() method.
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application using username.
     */
    public function login(Request $request)
    {
        $loginField = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Retrieve user first
        $user = \App\Models\User::where($loginField, $request->input('username'))->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => __('These credentials do not match our records.'),
            ]);
        }

        // If the user is 'public', enforce login via email only
        if ($user->role === 'public' && $loginField !== 'email') {
            throw ValidationException::withMessages([
                'username' => __('Public users must log in using email.'),
            ]);
        }

        // Now attempt login
        $credentials = [
            $loginField => $request->input('username'),
            'password' => $request->input('password'),
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo());
        }

        throw ValidationException::withMessages([
            'username' => __('These credentials do not match our records.'),
        ]);
    }


    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Determine where to redirect users after login based on role.
     */
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
}
