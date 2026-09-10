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
    /**
     * Meta webhook verification.
     *
     * GET /api/facebook/webhook
     */
    public function verify(Request $request)
    { 
        $mode = $request->query('hub_mode')
            ?? $request->query('hub.mode');

        $token = $request->query('hub_verify_token')
            ?? $request->query('hub.verify_token');

        $challenge = $request->query('hub_challenge')
            ?? $request->query('hub.challenge');

        $verifyToken = (string) config(
            'services.meta.verify_token'
        );

        if (
            $mode === 'subscribe'
            && $token
            && hash_equals($verifyToken, (string) $token)
        ) {
            Log::info('Meta webhook verified successfully');

            return response(
                (string) $challenge,
                200
            )->header('Content-Type', 'text/plain');
        }

        Log::warning('Meta webhook verification failed', [
            'mode' => $mode,
        ]);

        return response(
            'Invalid verification token',
            403
        );
    }

    /**
     * Receive Facebook Instant Form webhook.
     *
     * POST /api/facebook/webhook
     */
    public function receive(Request $request)
    {
        try {

            $rawPayload = $request->getContent();

            /*
            |--------------------------------------------------------------------------
            | Verify Meta signature
            |--------------------------------------------------------------------------
            */

            $signature = $request->header(
                'X-Hub-Signature-256'
            );

            $appSecret = (string) config(
                'services.meta.app_secret'
            );

            if (!$signature || !$appSecret) {

                Log::warning(
                    'Meta webhook signature/app secret missing'
                );

                return response(
                    'Invalid signature',
                    403
                );
            }

            $expectedSignature =
                'sha256=' .
                hash_hmac(
                    'sha256',
                    $rawPayload,
                    $appSecret
                );

            if (
                !hash_equals(
                    $expectedSignature,
                    $signature
                )
            ) {

                Log::warning(
                    'Invalid Meta webhook signature'
                );

                return response(
                    'Invalid signature',
                    403
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Decode webhook
            |--------------------------------------------------------------------------
            */

            $payload = json_decode(
                $rawPayload,
                true
            );

            if (!is_array($payload)) {

                Log::warning(
                    'Invalid Meta webhook JSON payload'
                );

                return response(
                    'Invalid payload',
                    400
                );
            }

            Log::info(
                'Meta webhook received',
                [
                    'object' =>
                        $payload['object'] ?? null,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Process leadgen events
            |--------------------------------------------------------------------------
            */

            foreach (
                $payload['entry'] ?? []
                as $entry
            ) {

                foreach (
                    $entry['changes'] ?? []
                    as $change
                ) {

                    if (
                        ($change['field'] ?? null)
                        !== 'leadgen'
                    ) {
                        continue;
                    }

                    $value =
                        $change['value'] ?? [];

                    $leadId =
                        $value['leadgen_id']
                        ?? null;

                    if (!$leadId) {

                        Log::warning(
                            'leadgen event missing leadgen_id',
                            [
                                'value' => $value,
                            ]
                        );

                        continue;
                    }

                    /*
                     * Optional security check:
                     * only accept our 360 PropGuide Page.
                     */
                    $configuredPageId =
                        config(
                            'services.meta.page_id'
                        );

                    if (
                        $configuredPageId
                        && isset($value['page_id'])
                        && (string) $value['page_id']
                            !==
                            (string) $configuredPageId
                    ) {

                        Log::warning(
                            'Lead received from unexpected Page',
                            [
                                'page_id' =>
                                    $value['page_id'],
                            ]
                        );

                        continue;
                    }

                    /*
                     * Don't request Meta again
                     * if lead is already stored.
                     */
                    if (
                        Lead::where(
                            'facebook_lead_id',
                            $leadId
                        )->exists()
                    ) {

                        Log::info(
                            'Facebook lead already exists',
                            [
                                'lead_id' => $leadId,
                            ]
                        );

                        continue;
                    }

                    $this->fetchAndSaveLead(
                        (string) $leadId,
                        $value
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Meta needs HTTP 200 response
            |--------------------------------------------------------------------------
            */

            return response(
                'EVENT_RECEIVED',
                200
            );

        } catch (Throwable $e) {

            Log::error(
                'Facebook webhook processing error',
                [
                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            /*
             * For unexpected processing failures
             * returning 500 allows Meta to retry.
             */
            return response(
                'Webhook processing failed',
                500
            );
        }
    }

    /**
     * Fetch actual lead details from Meta
     * and save to database.
     */
    private function fetchAndSaveLead(
        string $leadId,
        array $meta
    ): void {

        try {

            $version = config(
                'services.meta.graph_version',
                'v26.0'
            );

            $pageAccessToken =
                config(
                    'services.meta.page_access_token'
                );

            if (!$pageAccessToken) {

                throw new \RuntimeException(
                    'META_PAGE_ACCESS_TOKEN is missing'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Fetch the actual lead
            |--------------------------------------------------------------------------
            */
				
				$response = Http::timeout(15)
				->retry(2, 500, null, false)
				->get(
					"https://graph.facebook.com/{$version}/{$leadId}",
					[
						'fields' => 'id,created_time,field_data',
						'access_token' => $pageAccessToken,
					]
				);

            if (!$response->successful()) {

                Log::error(
                    'Facebook lead fetch failed',
                    [
                        'lead_id' => $leadId,

                        'status' =>
                            $response->status(),

                        'response' =>
                            $response->json(),
                    ]
                );

                return;
            }

            $lead = $response->json();

            /*
            |--------------------------------------------------------------------------
            | Convert Meta field_data
            |--------------------------------------------------------------------------
            |
            | Facebook gives:
            |
            | [
            |   {
            |      "name": "full_name",
            |      "values": ["Rahul"]
            |   }
            | ]
            |
            | Convert to:
            |
            | [
            |   "full_name" => "Rahul"
            | ]
            |
            */

            $fields = collect(
                $lead['field_data'] ?? []
            )
                ->mapWithKeys(
                    function ($field) {

                        $name =
                            $field['name']
                            ?? null;

                        if (!$name) {
                            return [];
                        }

                        $values =
                            $field['values']
                            ?? [];

                        /*
                         * If Meta gives one value,
                         * save simple string.
                         *
                         * If multiple values,
                         * preserve the whole array.
                         */
                        $value =
                            count($values) === 1
                                ? $values[0]
                                : $values;

                        return [
                            $name => $value,
                        ];
                    }
                )
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | Extract common fields
            |--------------------------------------------------------------------------
            */

            $firstName =
                $fields['first_name']
                ?? null;

            $lastName =
                $fields['last_name']
                ?? null;

            $name =
                $fields['full_name']
                ?? null;

            /*
             * Some Instant Forms may use
             * first_name + last_name instead.
             */
            if (!$name) {

                $name = trim(
                    ($firstName ?? '')
                    . ' '
                    . ($lastName ?? '')
                );
            }

            if ($name === '') {
                $name = null;
            }

            $phone =
                $fields['phone_number']
                ?? $fields['phone']
                ?? null;

            $email =
                $fields['email']
                ?? null;

            /*
            |--------------------------------------------------------------------------
            | Save Lead
            |--------------------------------------------------------------------------
            */

            $savedLead =
                Lead::updateOrCreate(
                    [
                        'facebook_lead_id' =>
                            $lead['id']
                            ?? $leadId,
                    ],
                    [
                        'facebook_page_id' =>
                            $meta['page_id']
                            ?? null,

                        'facebook_form_id' =>
                            $meta['form_id']
                            ?? null,

                        'facebook_ad_id' =>
                            $meta['ad_id']
                            ?? null,

                        /*
                         * Meta webhook normally
                         * sends adgroup_id for
                         * the ad set.
                         */
                        'facebook_adset_id' =>
                            $meta['adgroup_id']
                            ?? $meta['adset_id']
                            ?? null,

                        'name' =>
                            $name,

                        'phone' =>
                            $phone,

                        'email' =>
                            $email,

                        'source' =>
                            'facebook',

                        /*
                         * Store everything so
                         * custom questions such as:
                         *
                         * budget
                         * city
                         * project
                         * configuration
                         *
                         * are not lost.
                         */
                        'raw_data' => [
                            'fields' =>
                                $fields,

                            'facebook' => [
                                'lead_id' =>
                                    $lead['id']
                                    ?? $leadId,

                                'page_id' =>
                                    $meta['page_id']
                                    ?? null,

                                'form_id' =>
                                    $meta['form_id']
                                    ?? null,

                                'ad_id' =>
                                    $meta['ad_id']
                                    ?? null,

                                'adgroup_id' =>
                                    $meta['adgroup_id']
                                    ?? null,
                            ],
                        ],

                        'submitted_at' =>
                            !empty(
                                $lead[
                                    'created_time'
                                ]
                            )
                                ? Carbon::parse(
                                    $lead[
                                        'created_time'
                                    ]
                                )
                                : now(),
                    ]
                );

            Log::info(
                'Facebook lead saved successfully',
                [
                    'database_id' =>
                        $savedLead->id,

                    'facebook_lead_id' =>
                        $savedLead
                            ->facebook_lead_id,

                    'form_id' =>
                        $savedLead
                            ->facebook_form_id,

                    'name' =>
                        $savedLead->name,

                    /*
                     * Avoid putting full
                     * contact data into logs.
                     */
                    'has_phone' =>
                        !empty(
                            $savedLead->phone
                        ),

                    'has_email' =>
                        !empty(
                            $savedLead->email
                        ),
                ]
            );

        } catch (Throwable $e) {

            Log::error(
                'Facebook lead processing failed',
                [
                    'lead_id' =>
                        $leadId,

                    'message' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            throw $e;
        }
    }
}