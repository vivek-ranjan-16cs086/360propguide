<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
		// dd((int)$role);
        $path = $request->path();

        //  If not logged in, redirect to respective login
        if (!$user) {
			// dd($role);
            return redirect()->route((int) $role === 1 ? 'loginPage' : 'frontend.login');
        }
		if ($user && $request->path() === '7439/login') {
			return redirect()->route('dashboard');
}

        //  If already logged in and trying to access login page again
        if ((int) $user->role_id === 1 && $path === '7439/login') {
            return redirect()->route('dashboard');
        }

        if ((int) $user->role_id === 2 && ($path === 'login' || $path === 'frontend/login')) {
			return redirect()->route('list');
        }

        //  Role mismatch — logout and redirect
        if ((int) $user->role_id !== (int) $role) {
            Auth::logout();
            return redirect()->route((int) $role === 1 ? 'login' : 'frontend.login')
                ->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
