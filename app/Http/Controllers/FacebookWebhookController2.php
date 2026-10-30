<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookWebhookController extends Controller
{
    /** Meta webhook verification: GET /api/facebook/webhook */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');
        $verifyToken = (string) config('services.meta.verify_token');

        if ($mode === 'subscribe' && $token && hash_equals($verifyToken, (string) $token)) {
            Log::info('Meta webhook verified successfully');
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('Meta webhook verification failed', ['mode' => $mode]);
        return response('Invalid verification token', 403);
    }

    /** Receive Facebook Instant Form webhook: POST /api/facebook/webhook */
    public function receive(Request $request)
    {
        try {
            $rawPayload = $request->getContent();
            $signature = $request->header('X-Hub-Signature-256');
            $appSecret = (string) config('services.meta.app_secret');

            if (!$signature || !$appSecret) {
                Log::warning('Meta webhook signature/app secret missing');
                return response('Invalid signature', 403);
            }

            $expectedSignature = 'sha256=' . hash_hmac('sha256', $rawPayload, $appSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Invalid Meta webhook signature');
                return response('Invalid signature', 403);
            }

            $payload = json_decode($rawPayload, true);
            if (!is_array($payload)) {
                Log::warning('Invalid Meta webhook JSON payload');
                return response('Invalid payload', 400);
            }

            Log::info('Meta webhook received', ['object' => $payload['object'] ?? null]);

            foreach ($payload['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    if (($change['field'] ?? null) !== 'leadgen') {
                        continue;
                    }

                    $value = $change['value'] ?? [];
                    $leadId = $value['leadgen_id'] ?? null;

                    if (!$leadId) {
                        Log::warning('leadgen event missing leadgen_id', ['value' => $value]);
                        continue;
                    }

                    // Preserve the existing Page restriction.
                    $configuredPageId = config('services.meta.page_id');
                    if ($configuredPageId && isset($value['page_id'])
                        && (string) $value['page_id'] !== (string) $configuredPageId) {
                        Log::warning('Lead received from unexpected Page', [
                            'page_id' => $value['page_id'],
                        ]);
                        continue;
                    }

                    // Preserve duplicate-lead protection.
                    if (Lead::where('facebook_lead_id', $leadId)->exists()) {
                        Log::info('Facebook lead already exists', ['lead_id' => $leadId]);
                        continue;
                    }

                    $this->fetchAndSaveLead((string) $leadId, $value);
                }
            }

            return response('EVENT_RECEIVED', 200);
        } catch (Throwable $e) {
            Log::error('Facebook webhook processing error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response('Webhook processing failed', 500);
        }
    }

    /** Fetch actual lead details from Meta and save to database. */
    private function fetchAndSaveLead(string $leadId, array $meta): void
    {
        try {
            $version = config('services.meta.graph_version', 'v26.0');
            $pageAccessToken = config('services.meta.page_access_token');

            if (!$pageAccessToken) {
                throw new \RuntimeException('META_PAGE_ACCESS_TOKEN is missing');
            }

            // Existing lead API request, extended with official metadata fields.
            $response = Http::withToken($pageAccessToken)
                ->timeout(15)
                ->retry(2, 500, null, false)
                ->get("https://graph.facebook.com/{$version}/{$leadId}", [
                    'fields' => implode(',', [
                        'id', 'created_time', 'field_data', 'form_id',
                        'ad_id', 'ad_name', 'adset_id', 'adset_name',
                        'campaign_id', 'campaign_name', 'is_organic',
                    ]),
                ]);

            if (!$response->successful()) {
                Log::error('Facebook lead fetch failed', [
                    'lead_id' => $leadId,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return; // Preserve the existing handling of a failed lead fetch.
            }

            $lead = $response->json();

            // Additional metadata lookup is best-effort and cannot prevent saving.
            $pageId = $meta['page_id'] ?? null;
            $formId = $lead['form_id'] ?? $meta['form_id'] ?? null;
            $pageName = $this->getMetaObjectName($pageId, $pageAccessToken);
            $formName = $this->getMetaObjectName($formId, $pageAccessToken);

            // A separate Ads token is optional. It may be required for Ads API access.
            $adsAccessToken = config('services.meta.ads_access_token') ?: $pageAccessToken;
            $adId = $lead['ad_id'] ?? $meta['ad_id'] ?? null;
            $adName = $lead['ad_name'] ?? null;
            $adsetId = $lead['adset_id'] ?? null;
            $adsetName = $lead['adset_name'] ?? null;
            $campaignId = $lead['campaign_id'] ?? null;
            $campaignName = $lead['campaign_name'] ?? null;

            // Optional fallback to retrieve names/IDs missing from the Lead node.
            if ($adId && (!$adName || !$adsetId || !$campaignId)) {
                $adDetails = $this->getMetaObject(
                    $adId,
                    'id,name,adset_id,campaign_id',
                    $adsAccessToken
                );
                $adName = $adName ?: ($adDetails['name'] ?? null);
                $adsetId = $adsetId ?: ($adDetails['adset_id'] ?? null);
                $campaignId = $campaignId ?: ($adDetails['campaign_id'] ?? null);
            }

            // Retain the original webhook adgroup_id fallback.
            // $adsetId = $adsetId ?? $meta['adgroup_id'] ?? $meta['adset_id'] ?? null;
			$adsetId = $adsetId ?? $meta['adset_id'] ?? null;

            if ($adsetId && !$adsetName) {
                $adsetName = $this->getMetaObjectName($adsetId, $adsAccessToken);
            }
            if ($campaignId && !$campaignName) {
                $campaignName = $this->getMetaObjectName($campaignId, $adsAccessToken);
            }

            // Preserve original custom-question parsing and multiple values.
            $fields = collect($lead['field_data'] ?? [])
                ->mapWithKeys(function ($field) {
                    $name = $field['name'] ?? null;
                    if (!$name) {
                        return [];
                    }
                    $values = $field['values'] ?? [];
                    $value = count($values) === 1 ? $values[0] : $values;
                    return [$name => $value];
                })
                ->toArray();

            $firstName = $fields['first_name'] ?? null;
            $lastName = $fields['last_name'] ?? null;
            $name = $fields['full_name'] ?? null;
            if (!$name) {
                $name = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
            }
            if ($name === '') {
                $name = null;
            }
            $phone = $fields['phone_number'] ?? $fields['phone'] ?? null;
            $email = $fields['email'] ?? null;

            // Same database columns and updateOrCreate as your original controller.
            $savedLead = Lead::updateOrCreate(
                ['facebook_lead_id' => $lead['id'] ?? $leadId],
                [
                    'facebook_page_id' => $pageId,
                    'facebook_form_id' => $formId,
                    'facebook_ad_id' => $adId,
                    'facebook_adset_id' => $adsetId,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'source' => 'facebook',
					'source_page_name' => @$pageName,
                    'raw_data' => [
                        'fields' => $fields,
                        'facebook' => [
                            'lead_id' => $lead['id'] ?? $leadId,
                            'page_id' => $pageId,
                            'page_name' => $pageName,
                            'form_id' => $formId,
                            'form_name' => $formName,
                            'ad_id' => $adId,
                            'ad_name' => $adName,
                            'adgroup_id' => $meta['adgroup_id'] ?? null,
                            'adset_id' => $adsetId,
                            'adset_name' => $adsetName,
                            'campaign_id' => $campaignId,
                            'campaign_name' => $campaignName,
                            'is_organic' => $lead['is_organic'] ?? null,
                        ],
                    ],
                    'submitted_at' => !empty($lead['created_time'])
                        ? Carbon::parse($lead['created_time'])
                        : now(),
                ]
            );

            Log::info('Facebook lead saved successfully', [
                'database_id' => $savedLead->id,
                'facebook_lead_id' => $savedLead->facebook_lead_id,
                'form_id' => $savedLead->facebook_form_id,
                'name' => $savedLead->name,
                'has_phone' => !empty($savedLead->phone),
                'has_email' => !empty($savedLead->email),
                'has_page_name' => !empty($pageName),
                'has_form_name' => !empty($formName),
                'has_ad_name' => !empty($adName),
                'has_adset_name' => !empty($adsetName),
                'has_campaign_name' => !empty($campaignName),
            ]);
        } catch (Throwable $e) {
            Log::error('Facebook lead processing failed', [
                'lead_id' => $leadId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }

    /** Best-effort Meta lookup; failures must not interrupt lead saving. */
    private function getMetaObject($id, string $fields, ?string $accessToken): array
    {
        if (!$id || !$accessToken) {
            return [];
        }

        try {
            $version = config('services.meta.graph_version', 'v26.0');
            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->retry(2, 500, null, false)
                ->get('https://graph.facebook.com/' . $version . '/' . rawurlencode((string) $id), [
                    'fields' => $fields,
                ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning('Meta metadata lookup failed', [
                'object_id' => $id,
                'status' => $response->status(),
                'error' => $response->json('error.message'),
            ]);
        } catch (Throwable $e) {
            Log::warning('Meta metadata lookup exception', [
                'object_id' => $id,
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }

    private function getMetaObjectName($id, ?string $accessToken): ?string
    {
        $object = $this->getMetaObject($id, 'id,name', $accessToken);
        return $object['name'] ?? null;
    }
}
