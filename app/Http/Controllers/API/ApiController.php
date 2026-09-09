<?php

namespace App\Http\Controllers\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Exceptions\RouteNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class ApiController extends Controller
{
     public function getBlogs()
    {
        $blogs = Blog::latest()->paginate(10);

        $blogs->getCollection()->transform(function ($blog) {
            return [
                'id'                => $blog->id,
                'title'             => $blog->title,
                'slug'              => $blog->slug,
                'short_description' => $blog->short_description,
                'description'       => $blog->description,
                'seo_data'          => json_decode($blog->seo_data, true),
                'featured_image'    => $blog->featured_image 
                                        ? url('storage/' . $blog->featured_image) 
                                        : null,
            ];
        });

        return response()->json($blogs);
    }

    
    public function getBlogDetail($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $data = [
            'id'                => $blog->id,
            'title'             => $blog->title,
            'slug'              => $blog->slug,
            'short_description' => $blog->short_description,
            'description'       => $blog->description,
            'seo_data'          => json_decode($blog->seo_data, true),
            'featured_image'    => $blog->featured_image 
                                    ? url('storage/' . $blog->featured_image) 
                                    : null,
            'created_at'        => $blog->created_at->format('Y-m-d'),
        ];

        return response()->json($data);
    }
}