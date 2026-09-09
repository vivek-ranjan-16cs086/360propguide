<?php

namespace App\Jobs;

use App\Models\Developer;
use App\Services\CustomLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncDeveloperCustomLinksJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    public function __construct(public int $developerId)
    {
    }

    public function uniqueId(): string
    {
        return 'custom-links-developer-' . $this->developerId;
    }

    public function handle(CustomLinkGenerator $generator): void
    {
        $developer = Developer::query()->find($this->developerId);
        if ($developer) {
            $generator->syncDeveloper($developer);
        }
    }
}
