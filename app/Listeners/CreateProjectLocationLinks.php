<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Jobs\SyncProjectCustomLinksJob;

class CreateProjectLocationLinks
{
    public function handle(ProjectCreated $event): void
    {
        if ($event->project->id) {
            SyncProjectCustomLinksJob::dispatch($event->project->id);
        }
    }
}
