<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['source_path', 'destination_url', 'status_code', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'status_code' => 'integer'];
}
