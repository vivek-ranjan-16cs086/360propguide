<?php

namespace App\Console\Commands;

use App\Models\FacebookLeadErpOutbox;
use App\Models\Lead;
use App\Services\FacebookLeadErpSync;
use Illuminate\Console\Command;

class SyncFacebookLeadsToErp extends Command
{
    protected $signature = 'facebook:sync-pending-leads';

    protected $description = 'Retry sending Facebook leads that are pending ERP sync';

    public function handle(FacebookLeadErpSync $sync): int
    {
        Lead::whereNotNull('facebook_lead_id')
            ->whereNotIn('facebook_lead_id', FacebookLeadErpOutbox::select('facebook_lead_id'))
            ->orderBy('id')
            ->chunkById(100, function ($leads) use ($sync) {
                foreach ($leads as $lead) {
                    $sync->queueAndSync($lead);
                }
            });

        FacebookLeadErpOutbox::whereNull('synced_at')
            ->where('attempts', '<', FacebookLeadErpSync::MAX_ATTEMPTS)
            ->where('updated_at', '<=', now()->subMinutes(5))
            ->orderBy('id')
            ->chunkById(100, function ($outboxRecords) use ($sync) {
                foreach ($outboxRecords as $outbox) {
                    $sync->syncPending($outbox);
                }
            });

        return self::SUCCESS;
    }
}
