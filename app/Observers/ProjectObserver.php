<?php

namespace App\Observers;

use App\Jobs\SyncProjectCustomLinksJob;
use App\Models\Project;

class ProjectObserver
{
    public function saved(Project $project): void
    {
        if ($project->id) {
            SyncProjectCustomLinksJob::dispatch($project->id);
        }
    }
}
