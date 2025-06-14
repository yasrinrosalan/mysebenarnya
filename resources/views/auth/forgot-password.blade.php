@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    @csrf

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="mb-3">
        <label>Email address</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
    </div>
</form>
@endsection
