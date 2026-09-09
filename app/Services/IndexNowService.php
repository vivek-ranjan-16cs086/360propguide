<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    public function notifySearchEngines(string $url): bool
    {
        $response = Http::post('https://api.indexnow.org/indexnow', [
            'host' => 'www.360propguide.com',
            'key' => env('INDEXNOW_KEY'),
            'keyLocation' => 'https://www.360propguide.com/' . env('INDEXNOW_KEY') . '.txt',
            'urlList' => [$url]
        ]);

        Log::info('IndexNow Response', [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return $response->successful();
    }
}