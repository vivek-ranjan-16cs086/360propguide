<?php

namespace App\Jobs;

use App\Models\Project;
use App\Services\CustomLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProjectCustomLinksJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    public function __construct(public int $projectId)
    {
    }

    public function uniqueId(): string
    {
        return 'custom-links-project-' . $this->projectId;
    }

    public function handle(CustomLinkGenerator $generator): void
    {
        $project = Project::query()->find($this->projectId);
        if ($project) {
            $generator->syncProject($project);
        }
    }
}
