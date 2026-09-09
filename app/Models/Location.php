<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'locations';

    protected $casts = [
        'status' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id')->where('status', 1)->orderBy('city');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'location_id');
    }

    public function sublocationProjects()
    {
        return $this->hasMany(Project::class, 'sublocation_id');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public static function parentCityNames()
    {
        return static::parents()
            ->active()
            ->orderBy('city')
            ->pluck('city')
            ->values();
    }

    public static function sublocationNames(): array
    {
        return static::query()
            ->whereNotNull('parent_id')
            ->active()
            ->orderBy('city')
            ->pluck('city')
            ->unique()
            ->values()
            ->all();
    }

    public static function sublocationParentMap(): array
    {
        return static::query()
            ->whereNotNull('parent_id')
            ->active()
            ->with('parent:id,city')
            ->orderBy('city')
            ->get()
            ->filter(fn ($location) => filled($location->city) && filled($location->parent?->city))
            ->mapWithKeys(fn ($location) => [$location->city => $location->parent->city])
            ->toArray();
    }
}
