<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No user logged in.');
        }

        if ($user->role !== 'admin') {
            abort(403, 'You are logged in, but not an admin. Role: ' . $user->role);
        }

        return $next($request);
    }
}
