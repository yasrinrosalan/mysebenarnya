@extends('layouts.app')

@section('title', 'All Users')

@section('content')
<div class="container">
    <h3 class="mb-4">All Registered Users</h3>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="row mb-4">
        <div class="col-md-3">
            <label for="role">Filter by Role:</label>
            <select name="role" id="role" class="form-select">
                <option value="">All</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="agency" {{ request('role') == 'agency' ? 'selected' : '' }}>Agency</option>
                <option value="public" {{ request('role') == 'public' ? 'selected' : '' }}>Public</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="date">Filter by Registration Date:</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    {{-- User Table --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Registered At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $user->user_id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->user_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.users.update', ['id' => $user->user_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel{{ $user->user_id }}">Edit User - {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="agency" {{ $user->role === 'agency' ? 'selected' : '' }}>Agency</option>
                                <option value="public" {{ $user->role === 'public' ? 'selected' : '' }}>Public</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Contact Info -->
                        <div class="mb-3">
                            <label class="form-label">Contact Info</label>
                            <input type="text" class="form-control" name="contact_info" value="{{ $user->contact_info }}">
                        </div>

                        <!-- Verified -->
                        <div class="mb-3">
                            <label class="form-label">Is Verified</label>
                            <select class="form-select" name="is_verified">
                                <option value="1" {{ $user->is_verified ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !$user->is_verified ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Profile Picture -->
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" name="profile_picture">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile" class="mt-2" width="80">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

                        <td>{{ $user->name }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', ['id' => $user->user_id]) }}" class="btn btn-sm btn-info">View</a>
                            <!-- Edit Button -->
<button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->user_id }}">
    Edit
</button>

                            <form action="{{ route('admin.users.destroy', ['id' => $user->user_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</div>

@endsection
