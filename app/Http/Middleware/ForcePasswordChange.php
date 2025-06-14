<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) return redirect('/login');

        $needsChange = false;

        if ($user->isAdminUser() && $user->adminUser && $user->adminUser->force_password_change) {
            $needsChange = true;
        }

        if ($user->isAgencyUser() && $user->agencyUser && $user->agencyUser->force_password_change) {
            $needsChange = true;
        }

        if ($needsChange && !$request->is('force-password-change')) {
            return redirect()->route('password.force.change.form');
        }

        return $next($request);
    }
}
