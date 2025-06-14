<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\PublicUser;

class AuthController extends Controller
{
    /**
     * Default redirection path.
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
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
}
