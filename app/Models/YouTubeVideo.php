<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YouTubeVideo extends Model
{
    use HasFactory;
	use SoftDeletes;
    protected $table = 'youtube_videos';
    protected $fillable = [
        'video_id',
        'title',
        'thumbnail',
        'published_time',
        'duration',
        'views'
    ];
}
