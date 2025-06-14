@extends('layouts.app')

@section('title', 'Public Dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Welcome, {{ Auth::user()->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">You are logged in as a <strong>Public User</strong>.</p>

                    <div class="alert alert-info">
                        This is your dashboard. You can submit inquiries, check statuses, and update your profile.
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary btn-sm">My Profile</a>
                        {{-- Add more action buttons here as needed --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
