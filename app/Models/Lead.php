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
        'facebook_form_id',
        'facebook_ad_id',
        'facebook_adset_id',
        'name',
        'phone',
        'email',
        'source',
        'raw_data',
        'submitted_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'submitted_at' => 'datetime',
    ];
}