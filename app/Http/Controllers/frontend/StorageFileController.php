<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageFileController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        foreach (storageFileCandidates($path) as $file) {
            if (is_file($file)) {
                return response()->file($file);
            }
        }

        abort(404);
    }
}
