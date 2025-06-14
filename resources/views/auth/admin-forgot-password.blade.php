@extends('layouts.auth') {{-- or layouts.app if you're using it --}}

@section('title', 'Admin Forgot Password')

@section('content')
<div class="container mt-5" style="max-width: 500px;">

    {{-- Include Toasts --}}
    @include('partials.alerts') {{-- Optional: use the inline version from step 1 if not using partials --}}

    <form method="POST" action="{{ route('admin.password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" id="email" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
</div>
@endsection
