<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Login | MySebenarnya')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            background-color: #f8f9fa;
        }
        .auth-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .auth-box {
            width: 100%;
            max-width: 420px;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .auth-title {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <div class="auth-title">@yield('title')</div>
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
