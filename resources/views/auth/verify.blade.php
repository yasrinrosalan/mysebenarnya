@extends('layouts.app')

@section('title', 'Verify Your Email')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm w-100" style="max-width: 500px;">
        <div class="card-body text-center">
            <h4 class="card-title mb-3">Verify Your Email Address</h4>

            @if (session('status') === 'verification-link-sent')
                <div class="alert alert-success">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="mb-3">
                Before proceeding, please check your email for a verification link.
                If you didn’t receive the email, you can request another below.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
            </form>

            <hr class="my-4">

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
