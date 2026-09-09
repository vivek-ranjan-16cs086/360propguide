<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;

class StorageFileController extends Controller
{
    public function show(string $path)
    {
        $path = ltrim(str_replace('\\', '/', urldecode($path)), '/');
        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        if (str_starts_with($path, 'projects/')) {
            return redirect('/storage/public/' . $path, 301);
        }

        foreach (storageFileCandidates($path) as $file) {
            if (is_file($file)) {
                return response()->file($file);
            }
        }

        abort(404);
    }
}
