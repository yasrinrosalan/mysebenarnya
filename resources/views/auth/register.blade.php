@extends('layouts.auth')

@section('title', '')

@section('content')
<style>
    body {
        background-color: #1a1a1a; /* Dark background */
        color: #000000;
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

    label,
    .form-check-label {
        color: #000000;
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
<div class="text-center mb-1">
    <img src="{{ asset('images/logo.png') }}" alt="MySebenarnya Logo" class="img-fluid" style="max-width: 150px;">
</div>

@if ($errors->any())
    <div class="alert alert-danger small">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li class="small">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
    </div>

    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required>
    </div>

    <div class="mb-3">
        <label for="contact_info" class="form-label">Phone Number</label>
        <input id="contact_info" type="tel" class="form-control" name="contact_info" value="{{ old('contact_info') }}" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control" name="password" required>
    </div>

    <div class="mb-3">
        <label for="password-confirm" class="form-label">Confirm Password</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-person-plus-fill me-1"></i> Register
        </button>
    </div>

    <div class="text-center mt-3">
        <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
    </div>
</form>
@endsection
