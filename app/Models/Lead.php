<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
	
	protected $fillable = [
		'facebook_lead_id',

		'facebook_page_id',
		'facebook_page_name',

		'facebook_form_id',
		'facebook_form_name',

		'facebook_ad_id',
		'facebook_ad_name',

		'facebook_adset_id',
		'facebook_adset_name',

		'facebook_campaign_id',
		'facebook_campaign_name',

		'name',
		'phone',
		'email',

		'status',
		'source',

		'raw_data',
		'submitted_at',
	];

    protected $casts = [
        'raw_data' => 'array',
        'submitted_at' => 'datetime',
    ];
}