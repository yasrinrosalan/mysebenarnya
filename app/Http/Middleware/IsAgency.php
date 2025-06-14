<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAgency
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $guard = $this->auth->guard();

        if ($guard->check() && $guard->user()->role === 'agency') {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
