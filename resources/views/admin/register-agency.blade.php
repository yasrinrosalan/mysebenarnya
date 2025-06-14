@extends('layouts.app')

@section('title', 'Register Agency')

@section('content')
<div class="container">
    <h3 class="mb-4">Register New Agency User</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.register.agency') }}">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Agency Name</label>
            <input type="text" name="agency_name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Agency Contact</label>
            <input type="text" name="agency_contact" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Register Agency</button>
    </form>

    @if(session('generated_username') && session('generated_password'))
    <div class="alert alert-info mt-4">
        <strong>Login Credentials:</strong><br>
        <strong>Username:</strong> {{ session('generated_username') }}<br>
        <strong>Password:</strong> {{ session('generated_password') }}
    </div>
@endif

</div>
@endsection
