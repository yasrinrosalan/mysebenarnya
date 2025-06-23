@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"> Password Change</h5>
                </div>
                <div class="card-body">

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Password Change Form --}}
                    <form method="POST" action="{{ route('password.force.change') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" required minlength="8">
                            <small class="text-muted">Password must be at least 8 characters long.</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update Password</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
