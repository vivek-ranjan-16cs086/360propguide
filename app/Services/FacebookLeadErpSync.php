<?php

namespace App\Services;

use App\Models\FacebookLeadErpOutbox;
use App\Models\Lead;
use Illuminate\Http\Client\Response;
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

        $lead = Lead::where('facebook_lead_id', $outbox->facebook_lead_id)->first();
        if ($lead) {
            $outbox->payload = $this->payloadFor($lead);
            $outbox->save();
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
                $responseMessage = $this->responseErrorMessage($response);
                if ($responseMessage !== null) {
                    $error .= ': ' . $responseMessage;
                }

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

    private function responseErrorMessage(Response $response): ?string
    {
        $body = $response->json();
        if (is_array($body)) {
            foreach (['message', 'error', 'detail'] as $key) {
                $value = $body[$key] ?? null;
                if (is_array($value)) {
                    $value = $value['message'] ?? null;
                }
                if (is_string($value) && trim($value) !== '') {
                    return mb_substr(trim($value), 0, 1000);
                }
            }

            return null;
        }

        $contentType = (string) $response->header('Content-Type');
        if (stripos($contentType, 'text/html') !== false) {
            return null;
        }

        $message = trim(strip_tags($response->body()));
        return $message === '' ? null : mb_substr($message, 0, 1000);
    }

    private function payloadFor(Lead $lead): array
    {
        $externalId = filter_var($lead->facebook_lead_id, FILTER_VALIDATE_INT);
        if ($externalId === false) {
            throw new \UnexpectedValueException('Facebook lead ID must be a numeric ERP external ID.');
        }

        $facebook = data_get($lead->raw_data, 'facebook', []);
        $fields = data_get($lead->raw_data, 'fields', []);
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

        if (is_array($fields)) {
            foreach ($fields as $question => $answer) {
                $formattedAnswer = $this->formatFieldAnswer($answer);
                if ($formattedAnswer !== null) {
                    $messageParts[] = $question . ': ' . $formattedAnswer;
                }
            }
        }

        $phone = $lead->phone;
        if (!$phone && is_array($fields)) {
            $phone = $fields['phone_number'] ?? $fields['phone'] ?? $fields['whatsapp_number'] ?? null;
        }

        return [
            'external_id' => $externalId,
            'source_website' => '360propguide.com',
            'name' => $lead->name,
            'phone' => $phone,
            'email' => $lead->email,
            'project_name' => null,
            'source_page_name' => $lead->facebook_page_name
                ?: ($facebook['page_name'] ?? 'Facebook Lead Ads'),
            'source_url' => null,
            'message' => implode(' | ', $messageParts),
        ];
    }

    private function formatFieldAnswer($answer): ?string
    {
        if (is_array($answer)) {
            $values = array_values(array_filter(array_map(
                fn ($value) => $this->formatFieldAnswer($value),
                $answer
            ), static fn ($value) => $value !== null));
            return $values === [] ? null : implode(', ', $values);
        }

        if (is_bool($answer)) {
            return $answer ? 'Yes' : 'No';
        }

        if (is_scalar($answer)) {
            $answer = trim((string) $answer);
            return $answer === '' ? null : $answer;
        }

        return null;
    }
}
