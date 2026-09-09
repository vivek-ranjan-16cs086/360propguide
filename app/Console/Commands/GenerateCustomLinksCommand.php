<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAllCustomLinksJob;
use App\Services\CustomLinkGenerator;
use Illuminate\Console\Command;

class GenerateCustomLinksCommand extends Command
{
    protected $signature = 'custom-links:generate
        {--fresh : Delete all existing custom links first}
        {--sync : Run immediately instead of queueing}';

    protected $description = 'Generate custom links from locations, sublocations, developers, and possession statuses';

    public function handle(CustomLinkGenerator $generator): int
    {
        $fresh = (bool) $this->option('fresh');
        $job = new GenerateAllCustomLinksJob($fresh);

        if ($this->option('sync')) {
            $saved = $job->handle($generator);
            $this->info('Custom links generated: ' . $saved);
            return self::SUCCESS;
        }

        dispatch($job);
        $this->info('Custom link generation queued.');

        return self::SUCCESS;
    }
}
