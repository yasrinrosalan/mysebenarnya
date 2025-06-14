@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label for="username" class="form-label">Username or Email</label>
        <input type="text" name="username" id="username" class="form-control" required autofocus>
    </div>


    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control" name="password" required>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" name="remember" id="remember">
        <label class="form-check-label" for="remember">Remember Me</label>
    </div>

    <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Login</button>
    </div>

    <div class="text-center">
        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
    </div>
</form>
@endsection
