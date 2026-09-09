<?php

namespace App\Observers;

use App\Jobs\SyncDeveloperCustomLinksJob;
use App\Models\Developer;

class DeveloperObserver
{
    public function saved(Developer $developer): void
    {
        if ($developer->id) {
            SyncDeveloperCustomLinksJob::dispatch($developer->id);
        }
    }
}
