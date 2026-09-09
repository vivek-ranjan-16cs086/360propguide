<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        // Check if user is accessing admin area or frontend
        if ($request->is('7439*')) {
            return route('login'); // Admin login route
        } else {
            return route('frontend.login'); // Frontend login route
        }
    }
}

}
