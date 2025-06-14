@extends('layouts.auth')

@section('title', 'Register')

@section('content')

        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
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
                <label for="contact_info" class="form-label">Phone</label>
                <input id="contact_info" type="text" class="form-control" name="contact_info" value="{{ old('contact_info') }}" required>
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

            <div class="d-grid">
                <button type="submit" class="btn btn-success">Register</button>
            </div>

            <div class="mt-3 text-center">
                <small>Already have an account? <a href="{{ route('login') }}">Login here</a></small>
            </div>
        </form>
@endsection
