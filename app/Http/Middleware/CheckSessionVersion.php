<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionVersion
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            $user = Auth::user();

            if (session('session_version') != $user->session_version) {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('loginPage')
                    ->with('error', 'Your session has expired. Please login again.');
            }
        }

        return $next($request);
    }
}