@extends('layouts.auth')

@section('title', '')

@section('content')
<style>
    body {
        background-color: #1a1a1a; /* Dark background */
        color: #000000; /* Light text */
    }

    .form-control {
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #444;
    }

    .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    label {
        color: #212121;
    }

    .form-check-label {
        color: #2a2a2a;
    }

    a {
        color: #dc3545;
    }

    a:hover {
        color: #ff4b5c;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
</style>

<!-- Logo Section -->
<div class="text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="MySebenarnya Logo" class="img-fluid" style="max-width: 250px;">
</div>

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
        <button type="submit" class="btn btn-danger">Login</button>
    </div>

    <div class="text-center mb-2">
        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
    </div>

    <div class="text-center">
        <span>Don't have an account?</span>
        <a href="{{ route('register') }}" class="btn btn-link">Register here</a>
    </div>
</form>
@endsection
