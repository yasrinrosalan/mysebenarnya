{{-- resources/views/users/profile.blade.php --}}
@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">User Profile</h4>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</p>

            @if(auth()->user()->isAgencyUser())
                <p><strong>Agency Name:</strong> {{ auth()->user()->agency_name ?? 'N/A' }}</p>
            @endif

            <p><strong>Contact Info:</strong> {{ auth()->user()->contact_info ?? 'N/A' }}</p>
            <p><strong>Email Verified:</strong>
                {{ auth()->user()->email_verified_at ? 'Yes' : 'No' }}
            </p>

            <div class="mt-4">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Edit Profile</a>
                <a href="{{ route('password.request') }}" class="btn btn-warning btn-sm">Change Password</a>
            </div>
        </div>
    </div>
</div>
@endsection
