<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;

class ApplyManagedRedirects
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['GET', 'HEAD'], true) && !str_starts_with($request->path(), '7439/')) {
            $sourcePath = $request->getPathInfo();
            $redirect = Redirect::where('source_path', $sourcePath)->where('is_active', true)->first();

            if ($redirect) {
                $destinationPath = parse_url($redirect->destination_url, PHP_URL_PATH);
                if ($destinationPath !== $sourcePath || filter_var($redirect->destination_url, FILTER_VALIDATE_URL)) {
                    return redirect()->to($redirect->destination_url, $redirect->status_code);
                }
            }
        }

        return $next($request);
    }
}
