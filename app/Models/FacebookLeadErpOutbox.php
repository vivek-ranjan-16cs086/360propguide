<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookLeadErpOutbox extends Model
{
    protected $table = 'facebook_lead_erp_outbox';

    protected $fillable = [
        'facebook_lead_id',
        'payload',
        'attempts',
        'last_error',
        'synced_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];
}
