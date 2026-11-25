<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
	'recaptcha' => [
    'site_key'   => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],
	'erp' => [
		'url' => env('ERP_API_URL'),
		'key' => env('ERP_API_KEY'),
	],

'facebook' => [
    'verify_token' => env('FACEBOOK_VERIFY_TOKEN'),
    'version' => env('FACEBOOK_GRAPH_VERSION', env('META_GRAPH_VERSION', 'v26.0')),
    'pages' => array_filter([
        env('FACEBOOK_360_PAGE_ID', env('META_PAGE_ID')) => [
            'access_token' => env('FACEBOOK_360_PAGE_TOKEN', env('META_PAGE_ACCESS_TOKEN')),
            'brand' => '360 PropGuide',
        ],
        env('FACEBOOK_TWS_PAGE_ID') => [
            'access_token' => env('FACEBOOK_TWS_PAGE_TOKEN'),
            'brand' => 'The Wooden Strings',
        ],
    ], static function ($page, $pageId) {
        return is_scalar($pageId) && (string) $pageId !== '';
    }, ARRAY_FILTER_USE_BOTH),
],

'meta' => [
    'app_secret' => env('META_APP_SECRET'),
    'page_access_token' => env('META_PAGE_ACCESS_TOKEN'),
    'verify_token' => env('META_WEBHOOK_VERIFY_TOKEN'),
    'page_id' => env('META_PAGE_ID'),
    'graph_version' => env('META_GRAPH_VERSION', 'v26.0'),
]
];
