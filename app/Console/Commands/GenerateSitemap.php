<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Property;
use App\Models\admin\CustomLink;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'generate:sitemap';
    protected $description = 'Generate the sitemap.xml file';

    public function handle()
    {
        $items = [];

        // Static URLs
        $staticUrls = [
            '/',
            '/about-us',
            '/projects',
            '/blogs',
            '/properties',
            '/careers',
            '/contact',
            '/privacy',
            '/disclaimer',
            '/terms',
			'/max-estates-105',
			'/experion-151',
			
        ];

        foreach ($staticUrls as $path) {
            $items[] = [
                'loc' => (string) url($path),
                'lastmod' => null,
                'priority' => '1.0',
            ];
        }

        // Projects
		foreach (Project::where('status', '1')->get() as $project) {
            $items[] = [
                'loc' => (string) url('/projects/' . $project->slug),
                'lastmod' => Carbon::parse($project->updated_at)->toAtomString(),
                'priority' => '0.9',
            ];
        }

        // Blogs
		foreach (Blog::where('status', '1')->get() as $blog) {
            $items[] = [
                'loc' => (string) url('/blogs/' . $blog->slug),
                'lastmod' => Carbon::parse($blog->updated_at)->toAtomString(),
                'priority' => '0.7',
            ];
        }
		
        // Custom Links
        foreach (CustomLink::all() as $link) {
            $items[] = [
                'loc' => (string) url($link->slug),
                'lastmod' => null,
                'priority' => '0.6',
            ];
        }
		
		// Properies
		foreach (Property::where('status', 'approved')->get() as $property) {
			$items[] = [
				'loc' => url('/properties/' . $property->slug),
				'lastmod' => null,
				'priority' => '0.6',
			];
		} 

        // Save the rendered XML view to file
        $xml = view('sitemap', ['items' => $items])->render();
        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('sitemap.xml has been generated.');
    }
}
