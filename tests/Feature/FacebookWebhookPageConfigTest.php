<?php

namespace Tests\Feature;

use App\Services\FacebookLeadErpSync;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class FacebookWebhookPageConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.prefix' => '',
            'services.meta.app_secret' => 'test-app-secret',
            'services.facebook.pages' => [
                '112167769471879' => [
                    'access_token' => '360-page-token',
                    'brand' => '360 PropGuide',
                ],
                '126691377202158' => [
                    'access_token' => 'tws-page-token',
                    'brand' => 'The Wooden Strings',
                ],
            ],
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('leads', function ($table) {
            $table->id();
            $table->string('facebook_lead_id')->nullable();
            $table->string('facebook_page_id')->nullable();
            $table->string('facebook_page_name')->nullable();
            $table->string('facebook_form_id')->nullable();
            $table->string('facebook_form_name')->nullable();
            $table->string('facebook_ad_id')->nullable();
            $table->string('facebook_ad_name')->nullable();
            $table->string('facebook_adset_id')->nullable();
            $table->string('facebook_adset_name')->nullable();
            $table->string('facebook_campaign_id')->nullable();
            $table->string('facebook_campaign_name')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('source')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_webhook_uses_the_matching_page_token_and_keeps_custom_answers(): void
    {
        $twsTokenUsed = false;
        $legacyTokenUsed = false;
        Http::fake(function (ClientRequest $request) use (&$twsTokenUsed, &$legacyTokenUsed) {
            $twsTokenUsed = $twsTokenUsed
                || $request->hasHeader('Authorization', 'Bearer tws-page-token');
            $legacyTokenUsed = $legacyTokenUsed
                || $request->hasHeader('Authorization', 'Bearer 360-page-token');
            $url = $request->url();

            if (strpos($url, '/lead-100') !== false) {
                return Http::response([
                    'id' => 'lead-100',
                    'created_time' => '2026-11-25T10:00:00+0000',
                    'form_id' => 'form-200',
                    'field_data' => [
                        ['name' => 'full_name', 'values' => ['Wooden Strings Customer']],
                        ['name' => 'phone_number', 'values' => ['9876543210']],
                        ['name' => 'where_is_your_property_located?', 'values' => ['Pune']],
                        ['name' => 'what_is_your_property_size?', 'values' => ['2400 sq ft']],
                    ],
                ]);
            }

            return Http::response(['id' => 'meta-object', 'name' => 'Meta object']);
        });

        $sync = Mockery::mock(FacebookLeadErpSync::class);
        $sync->shouldReceive('queueAndSync')->once();
        $this->app->instance(FacebookLeadErpSync::class, $sync);

        $body = json_encode([
            'object' => 'page',
            'entry' => [[
                'id' => '126691377202158',
                'changes' => [[
                    'field' => 'leadgen',
                    'value' => [
                        'leadgen_id' => 'lead-100',
                        'page_id' => '126691377202158',
                        'form_id' => 'form-200',
                    ],
                ]],
            ]],
        ]);
        $signature = 'sha256='.hash_hmac('sha256', $body, 'test-app-secret');

        $response = $this->call(
            'POST',
            '/api/facebook/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HUB_SIGNATURE_256' => $signature,
            ],
            $body
        );

        $response->assertOk();
        $this->assertTrue($twsTokenUsed);
        $this->assertFalse($legacyTokenUsed);
        $this->assertDatabaseHas('leads', [
            'facebook_lead_id' => 'lead-100',
            'facebook_page_id' => '126691377202158',
            'name' => 'Wooden Strings Customer',
            'phone' => '9876543210',
        ]);

        $lead = DB::table('leads')->where('facebook_lead_id', 'lead-100')->first();
        $rawData = json_decode($lead->raw_data, true);
        $this->assertSame('The Wooden Strings', $rawData['facebook']['brand']);
        $this->assertSame('Pune', $rawData['fields']['where_is_your_property_located?']);
        $this->assertSame('2400 sq ft', $rawData['fields']['what_is_your_property_size?']);
    }

    public function test_historical_import_paginates_and_skips_existing_leads(): void
    {
        DB::table('leads')->insert([
            'facebook_lead_id' => 'existing-lead',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $twsTokenUsed = false;
        Http::fake(function (ClientRequest $request) use (&$twsTokenUsed) {
            $twsTokenUsed = $twsTokenUsed
                || $request->hasHeader('Authorization', 'Bearer tws-page-token');
            $path = parse_url($request->url(), PHP_URL_PATH);
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            if (strpos($path, '/leadgen_forms') !== false) {
                if (($query['after'] ?? null) === 'forms-next') {
                    return Http::response(['data' => [['id' => 'form-b', 'name' => 'Form B']]]);
                }

                return Http::response([
                    'data' => [['id' => 'form-a', 'name' => 'Form A']],
                    'paging' => [
                        'cursors' => ['after' => 'forms-next'],
                        'next' => 'https://graph.facebook.com/next',
                    ],
                ]);
            }

            if (strpos($path, '/form-a/leads') !== false) {
                if (($query['after'] ?? null) === 'leads-next') {
                    return Http::response([
                        'data' => [[
                            'id' => 'historical-lead-2',
                            'form_id' => 'form-a',
                            'field_data' => [
                                ['name' => 'full_name', 'values' => ['Historical Customer 2']],
                            ],
                        ]],
                    ]);
                }

                return Http::response([
                    'data' => [[
                        'id' => 'historical-lead-1',
                        'form_id' => 'form-a',
                        'field_data' => [
                            ['name' => 'full_name', 'values' => ['Historical Customer 1']],
                        ],
                    ]],
                    'paging' => [
                        'cursors' => ['after' => 'leads-next'],
                        'next' => 'https://graph.facebook.com/next',
                    ],
                ]);
            }

            if (strpos($path, '/form-b/leads') !== false) {
                return Http::response(['data' => [['id' => 'existing-lead']]]);
            }

            return Http::response(['id' => 'meta-object', 'name' => 'Meta object']);
        });

        $sync = Mockery::mock(FacebookLeadErpSync::class);
        $sync->shouldReceive('queueAndSync')->twice();
        $this->app->instance(FacebookLeadErpSync::class, $sync);

        $this->artisan('facebook:import-leads', ['--page' => '126691377202158'])
            ->expectsOutput('Imported 2 leads; skipped 1 duplicates.')
            ->assertExitCode(0);

        $this->assertTrue($twsTokenUsed);
        $this->assertDatabaseHas('leads', [
            'facebook_lead_id' => 'historical-lead-1',
            'facebook_page_id' => '126691377202158',
        ]);
        $this->assertDatabaseHas('leads', [
            'facebook_lead_id' => 'historical-lead-2',
            'facebook_page_id' => '126691377202158',
        ]);
        $this->assertDatabaseCount('leads', 3);
    }
}
