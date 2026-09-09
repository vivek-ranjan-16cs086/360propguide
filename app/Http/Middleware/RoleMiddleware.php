<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (auth()->check() && (int) auth()->user()->role_id === (int) $role) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
