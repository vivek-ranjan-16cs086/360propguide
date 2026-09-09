<?php

namespace App\Observers;

use App\Jobs\SyncLocationCustomLinksJob;
use App\Models\Location;

class LocationObserver
{
    public function saved(Location $location): void
    {
        if ($location->id) {
            SyncLocationCustomLinksJob::dispatch($location->id);
        }
    }
}
