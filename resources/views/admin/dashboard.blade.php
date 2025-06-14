@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Admin Dashboard - Welcome, {{ Auth::user()->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">You are logged in as an <strong>Administrator</strong>.</p>

                    <div class="alert alert-warning">
                        You have full control over user management, inquiries, and system settings.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100">
                                👥 Manage Users
                            </a>
                        </div>
                        <div class="col-md-6">
                            {{-- Add another admin action --}}
                            <a href="#" class="btn btn-outline-secondary w-100">
                                📊 View System Logs
                            </a>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('profile.show') }}" class="btn btn-sm btn-link">View My Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
