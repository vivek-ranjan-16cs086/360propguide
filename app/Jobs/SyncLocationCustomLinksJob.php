<?php

namespace App\Jobs;

use App\Models\Location;
use App\Services\CustomLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncLocationCustomLinksJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    public function __construct(public int $locationId)
    {
    }

    public function uniqueId(): string
    {
        return 'custom-links-location-' . $this->locationId;
    }

    public function handle(CustomLinkGenerator $generator): void
    {
        $location = Location::with('parent:id,city')->find($this->locationId);
        if ($location) {
            $generator->syncLocation($location);
        }
    }
}
