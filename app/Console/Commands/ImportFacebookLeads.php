<?php

namespace App\Console\Commands;

use App\Http\Controllers\FacebookWebhookController;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class ImportFacebookLeads extends Command
{
    protected $signature = 'facebook:import-leads {--page= : Facebook Page ID to import leads from}';

    protected $description = 'Import historical Instant Form leads into the ERP';

    public function handle(FacebookWebhookController $webhook): int
    {
        $pageId = (string) $this->option('page');
        $pageConfig = config('services.facebook.pages.'.$pageId);

        if ($pageId === '' || ! is_array($pageConfig) || empty($pageConfig['access_token'])) {
            $this->error('Provide a configured Facebook Page ID with a Page Access Token.');

            return self::FAILURE;
        }

        $token = $pageConfig['access_token'];
        $imported = 0;
        $skipped = 0;

        try {
            foreach ($this->graphPages(
                rawurlencode($pageId).'/leadgen_forms',
                $token,
                ['fields' => 'id,name', 'limit' => 100]
            ) as $form) {
                if (empty($form['id'])) {
                    continue;
                }

                $formId = (string) $form['id'];
                foreach ($this->graphPages(
                    rawurlencode($formId).'/leads',
                    $token,
                    [
                        'fields' => implode(',', [
                            'id', 'created_time', 'field_data', 'form_id', 'ad_id',
                            'ad_name', 'adset_id', 'adset_name', 'campaign_id',
                            'campaign_name', 'is_organic',
                        ]),
                        'limit' => 100,
                    ]
                ) as $leadData) {
                    if (empty($leadData['id'])) {
                        continue;
                    }

                    $leadId = (string) $leadData['id'];
                    if (Lead::where('facebook_lead_id', $leadId)->exists()) {
                        $skipped++;

                        continue;
                    }

                    $webhook->importHistoricalLead(
                        $leadId,
                        $pageId,
                        $formId,
                        $pageConfig,
                        $leadData + ['form_id' => $formId]
                    );
                    $imported++;
                }
            }
        } catch (Throwable $e) {
            $this->error('Facebook lead import failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Imported {$imported} leads; skipped {$skipped} duplicates.");

        return self::SUCCESS;
    }

    private function graphPages(string $endpoint, string $token, array $query): iterable
    {
        $version = config('services.facebook.version', 'v26.0');
        $after = null;

        do {
            if ($after !== null) {
                $query['after'] = $after;
            }

            $response = Http::withToken($token)
                ->timeout(30)
                ->get("https://graph.facebook.com/{$version}/{$endpoint}", $query)
                ->throw()
                ->json();

            if (! is_array($response) || ! is_array($response['data'] ?? null)) {
                throw new RuntimeException('Meta returned an invalid paginated response.');
            }

            foreach ($response['data'] as $row) {
                if (is_array($row)) {
                    yield $row;
                }
            }

            $nextAfter = $response['paging']['cursors']['after'] ?? null;
            $hasNext = ! empty($response['paging']['next'])
                && is_string($nextAfter)
                && $nextAfter !== ''
                && $nextAfter !== $after;
            $after = $hasNext ? $nextAfter : null;
        } while ($after !== null);
    }
}
