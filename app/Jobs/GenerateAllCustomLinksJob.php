<?php

namespace App\Jobs;

use App\Services\CustomLinkGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAllCustomLinksJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public int $uniqueFor = 120;

    public function __construct(public bool $fresh = true)
    {
    }

    public function uniqueId(): string
    {
        return 'custom-links-generate-all';
    }

    public function handle(CustomLinkGenerator $generator): int
    {
        if ($this->fresh) {
            $generator->flushAll();
        }

        return $generator->generateAll();
    }
}
