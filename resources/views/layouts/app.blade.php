<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'MySebenarnya')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #1e1e2f;
            color: #fff;
            position: fixed;
        }
        .sidebar a {
            color: #ccc;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #343a40;
            color: #fff;
        }
        .main {
            margin-left: 250px;
            padding: 1.5rem;
        }
        .navbar {
            margin-left: 250px;
        }
        .sidebar .btn {
            color: #ffffff;
            font-weight: 500;
            text-align: left;
            background-color: #f53e3e;
            border: none;
            padding: 5px 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .sidebar .btn:hover {
            background-color: #eae4e2;
            color: #000;
        }
        .sidebar .sidebar-link {
            color: #ccc;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar .sidebar-link:hover {
            background-color: #343a40;
            color: #fff;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .navbar, .main {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    @if(session('success'))
        <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="toast align-items-center text-white bg-danger border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="toast align-items-center text-white bg-warning border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>

{{-- Sidebar --}}
@if(Auth::check())
    @php
        $user = Auth::user();
        $dashboardUrl = '#';
        if ($user->isAdminUser()) {
            $dashboardUrl = route('admin.dashboard');
        } elseif ($user->isAgencyUser()) {
            $dashboardUrl = route('agency.dashboard');
        } elseif ($user->isPublicUser()) {
            $dashboardUrl = route('public.dashboard');
        }
    @endphp

    <div class="sidebar bg-dark d-flex flex-column p-3">
        <h4 class="text-white text-center mb-4">MySebenarnya</h4>

        <a href="{{ $dashboardUrl }}">🏠 Dashboard</a>

        @if($user->isAdminUser())
                <a href="{{ route('admin.users.index') }}">👥 Manage Users</a>
                <a href="{{ route('admin.inquiries.manage') }}">📥 Manage Inquiries</a>
                <a href="{{ route('admin.register.agency.form') }}">🏢 Register Agency</a>
                <a href="{{ route('admin.inquiries.report') }}">📊 Inquiry Reports</a>
                

            @elseif($user->isAgencyUser())
                <a href="#">📥 Assigned Inquiries</a>
                <a href="#">📝 Submit Response</a>
            @elseif($user->isPublicUser())
                <a href="{{ route('public.inquiries.create') }}">➕ Submit Inquiry</a>
                <a href="{{ route('public.inquiries.index') }}">📂 My Inquiries</a>
                <a href="{{ route('public.inquiries.public') }}">🌍 Browse Public Inquiries</a>
            @endif

        <hr class="border-secondary">
        <a href="{{ route('profile.edit') }}" class="sidebar-link">⚙️ Edit Profile</a>

        <form action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-light w-100 text-start">
                🚪 Logout
            </button>
        </form>
    </div>
@endif

<!-- Top Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-4">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarMain">
            @auth
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">👤 Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="dropdown-item" type="submit">🚪 Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @else
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                </ul>
            @endauth
        </div>
    </div>
</nav>

{{-- Main Content --}}
<main class="main">
    @yield('content')
</main>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
