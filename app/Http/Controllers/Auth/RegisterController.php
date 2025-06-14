<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PublicUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     */
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

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        // Create main user
        $user = User::create([
            'name'         => $data['name'],
            'username'     => $data['username'],
            'email'        => $data['email'],
            'contact_info' => $data['contact_info'],
            'password'     => Hash::make($data['password']),
            'role'         => 'public',
            // 'is_verified'  => false,
        ]);

        // Create corresponding public_user record
        PublicUser::create([
            'user_id' => $user->user_id,
            // 'email_verified' => false,
            'registered_at' => now(),
        ]);

        return $user;
    }


    /**
     * Custom register method to fire events and redirect.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }
}
