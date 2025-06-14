@extends('layouts.app')

@section('title', 'Agency Dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Agency Dashboard - Welcome, {{ Auth::user()->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">You are logged in as an <strong>Agency User</strong> from
                        <strong>{{ Auth::user()->agency_name ?? 'Your Agency' }}</strong>.
                    </p>

                    <div class="alert alert-success">
                        From here, you can manage assigned inquiries and respond to public submissions.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-primary w-100">
                                📥 View Assigned Inquiries
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-secondary w-100">
                                ✍️ Submit Response or Report
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
