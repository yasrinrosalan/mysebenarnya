@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container py-4">
    <h4>User Details</h4>
    <div class="card p-4 shadow-sm">
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Username:</strong> {{ $user->username }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        <p><strong>Contact Info:</strong> {{ $user->contact_info ?? '-' }}</p>
        <p><strong>Registered At:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>

        @if($user->role === 'agency')
            <p><strong>Agency Name:</strong> {{ $user->agency_name }}</p>
            <p><strong>Agency Contact:</strong> {{ $user->agencyUser->agency_contact ?? '-' }}</p>
        @elseif($user->role === 'admin')
            <p><strong>Department:</strong> {{ $user->adminUser->department ?? '-' }}</p>
        @endif

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">Back to List</a>
    </div>
</div>
@endsection
