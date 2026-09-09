<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;
public function getRouteKeyName()
{
    return 'property_uid'; 
}
    protected $fillable = [
        'user_id',
        'project_id',
        'city',
        'property_type',
        'listing_type',
        'configuration',
        'construction_status',
        'total_price',
        'area',
        'area_unit',
        'furnishing_types',
        'title',
        'slug',
        'seo_data',
        'property_details',
        'advanced_details',
        'price_details',
        'amenities',
        'galleries',
        'is_verified',
        'active',
        'status',
        'step_id'
    ];

    protected $casts = [
        'property_details' => 'array',
        'advanced_details' => 'array',
        'price_details' => 'array',
        'amenities' => 'array',
        'galleries' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getPricePerSqftAttribute()
    {
        return $this->area ? round($this->total_price / $this->area) : null;
    }
    public function amenityItems()
    {
        return \App\Models\AminityList::whereIn('id', $this->amenities ?? [])->get();
    }
	
	protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            do {
                $random = random_int(1000000000, 9999999999); // 10-digit number
            } while (self::where('property_uid', $random)->exists()); // ensure uniqueness

            $model->property_uid = $random;
        });
    }
	
}
