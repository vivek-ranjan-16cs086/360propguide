<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomLink extends Model
{
    use HasFactory;
	protected $fillable = [
        'title',
        'slug',
		'keywords',
		'canonical',
        'description',
		'name',
        'type',
		'links_description',
        'is_active',
    ];
}
