@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4>Edit User</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="agency" {{ $user->role === 'agency' ? 'selected' : '' }}>Agency</option>
                        <option value="public" {{ $user->role === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="mcmc" {{ $user->role === 'mcmc' ? 'selected' : '' }}>MCMC</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Contact Info</label>
                    <input type="text" name="contact_info" class="form-control" value="{{ old('contact_info', $user->contact_info) }}">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="email_verified" class="form-check-input" id="emailVerified"
                        value="1" {{ $user->email_verified_at ? 'checked' : '' }}>
                    <label class="form-check-label" for="emailVerified">Email Verified</label>
                </div>

                <div class="mb-3">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_picture" class="form-control">
                    @if ($user->profile_picture_url)
                        <img src="{{ asset('storage/' . $user->profile_picture_url) }}" alt="Profile" class="mt-2" width="100">
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Update User</button>
            </form>
        </div>
    </div>
</div>
@endsection
