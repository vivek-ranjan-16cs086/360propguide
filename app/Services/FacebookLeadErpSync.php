<?php

namespace App\Services;

use App\Models\FacebookLeadErpOutbox;
use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookLeadErpSync
{
    public function queueAndSync(Lead $lead): void
    {
        $outbox = FacebookLeadErpOutbox::firstOrNew([
            'facebook_lead_id' => $lead->facebook_lead_id,
        ]);

        if ($outbox->synced_at) {
            return;
        }

        $outbox->payload = $this->payloadFor($lead);
        $outbox->save();

        $this->syncPending($outbox);
    }

    public function syncPending(FacebookLeadErpOutbox $outbox): bool
    {
        if ($outbox->synced_at) {
            return true;
        }

        $erpUrl = rtrim((string) config('services.erp.url'), '/');
        $erpKey = (string) config('services.erp.key');

        if ($erpUrl === '' || $erpKey === '') {
            $error = 'ERP_API_URL or ERP_API_KEY is not configured.';
            $this->recordFailure($outbox, $error);

            Log::error('Facebook lead ERP sync is not configured', [
                'lead_id' => $outbox->facebook_lead_id,
                'outbox_id' => $outbox->id,
            ]);
            return false;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['x-api-key' => $erpKey])
                ->post($erpUrl . '/api/v1/leads/store', $outbox->payload);

            if (!$response->successful()) {
                $error = 'ERP returned HTTP ' . $response->status();
                $this->recordFailure($outbox, $error);

                Log::warning('Facebook lead ERP sync attempt failed', [
                    'lead_id' => $outbox->facebook_lead_id,
                    'outbox_id' => $outbox->id,
                    'attempt' => $outbox->attempts,
                    'error' => $error,
                ]);
                return false;
            }

            $outbox->attempts++;
            $outbox->last_error = null;
            $outbox->synced_at = now();
            $outbox->save();

            Log::info('Facebook lead synced to ERP', [
                'lead_id' => $outbox->facebook_lead_id,
                'outbox_id' => $outbox->id,
                'status' => $response->status(),
            ]);
            return true;
        } catch (Throwable $e) {
            $this->recordFailure($outbox, $e->getMessage());

            Log::warning('Facebook lead ERP sync attempt failed', [
                'lead_id' => $outbox->facebook_lead_id,
                'outbox_id' => $outbox->id,
                'attempt' => $outbox->attempts,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function recordFailure(FacebookLeadErpOutbox $outbox, string $error): void
    {
        $outbox->attempts++;
        $outbox->last_error = mb_substr($error, 0, 2000);
        $outbox->save();
    }

    private function payloadFor(Lead $lead): array
    {
        $facebook = data_get($lead->raw_data, 'facebook', []);
        $messageParts = ['Facebook Lead Ads'];

        foreach ([
            'Form' => $lead->facebook_form_name ?: ($facebook['form_name'] ?? null),
            'Ad' => $lead->facebook_ad_name ?: ($facebook['ad_name'] ?? null),
            'Campaign' => $lead->facebook_campaign_name ?: ($facebook['campaign_name'] ?? null),
        ] as $label => $value) {
            if ($value) {
                $messageParts[] = $label . ': ' . $value;
            }
        }

        return [
            'external_id' => 'facebook:' . $lead->facebook_lead_id,
            'source_website' => '360propguide.com',
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'project_name' => null,
            'source_page_name' => $lead->facebook_page_name
                ?: ($facebook['page_name'] ?? 'Facebook Lead Ads'),
            'source_url' => null,
            'message' => implode(' | ', $messageParts),
        ];
    }
}
