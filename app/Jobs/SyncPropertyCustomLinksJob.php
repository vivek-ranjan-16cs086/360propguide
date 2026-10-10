<?php

namespace App\Jobs;

use App\Services\CustomLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SyncPropertyCustomLinksJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    public function uniqueId(): string
    {
        return 'custom-links-properties';
    }

    public function handle(CustomLinkGenerator $generator): void
    {
        $generator->syncPropertyLinks();
        Cache::forget('frontend.property-custom-link-cities');
    }
}
