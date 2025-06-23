@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5>Edit Profile</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route(name: 'profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control" required>
                        </div>

                        {{-- Contact Info --}}
                        <div class="mb-3">
                            <label class="form-label">Contact Info</label>
                            <input type="text" name="contact_info" value="{{ old('contact_info', Auth::user()->contact_info) }}" class="form-control">
                        </div>

                        {{-- Profile Picture --}}
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>

                            @php
                                $profilePicture = Auth::user()->profile_picture_url
                                    ? asset('storage/' . Auth::user()->profile_picture_url)
                                    : asset('uploads/profile_pictures/default.png');
                            @endphp

                            <div class="mb-2 text-center">
                                <img src="{{ $profilePicture }}"
                                    class="rounded-circle shadow"
                                    width="100" height="100"
                                    style="object-fit: cover; border: 2px solid #dee2e6;">
                            </div>

                            <input type="file" name="profile_picture" class="form-control">
                        </div>


                        {{-- Admin Only --}}
                        @if(Auth::user()->isAdminUser())
                            <div class="mb-3">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control"
                                       value="{{ old('department', Auth::user()->adminUser->department ?? '') }}">
                            </div>
                        @endif

                        {{-- Agency Only --}}
                        @if(Auth::user()->isAgencyUser())
                            <div class="mb-3">
                                <label class="form-label">Agency Name</label>
                                <input type="text" name="agency_name" class="form-control"
                                       value="{{ old('agency_name', Auth::user()->agencyUser->agency_name ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Agency Contact</label>
                                <input type="text" name="agency_contact" class="form-control"
                                       value="{{ old('agency_contact', Auth::user()->agencyUser->agency_contact ?? '') }}">
                            </div>
                        @endif

                        {{-- Submit & Change Password --}}
<div class="d-flex justify-content-between mt-4">
    {{-- Change Password Button (Triggers Modal) --}}
<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#verifyPasswordModal">
    Change Password (with Email Verification)
</button>



    <button type="submit" class="btn btn-primary">
        Save Changes
    </button>
</div>


                    </form>
                    <!-- Modal -->
<div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('password.change.verify') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyPasswordModalLabel">Verify Email to Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(session('message'))
                        <div class="alert alert-info">{{ session('message') }}</div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Verification Code</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('password.change.request') }}" class="btn btn-outline-info">Resend Code</a>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </div>
        </form>
    </div>
</div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
