<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\admin\CustomLink;
use Illuminate\Support\Str;
use App\Models\Project; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        View::composer('*', function ($view) {
			$service = app(\App\Services\ProjectLinksService::class);
			$limit = 10;

			$path = strtolower(request()->path());
			$grouped = $service->getGroupedLinksForPath($path);

			$propertyLinks = $service->getPropertyLinks();
			$view->with('propertyLinks', array_slice($propertyLinks, 0, $limit));
			$view->with('propertyLinksTotal', count($propertyLinks));

			$view->with('cities', $service->getCities());

			$viewData = $view->getData();
			$project = $viewData['projects'] ?? null;
			$currentCity = $project instanceof \App\Models\Project
				? $project->cities
				: null;

			$currentCity = data_get($viewData, 'filters.city') ?: $currentCity;
			$projectType = null;
			if ($project instanceof \App\Models\Project) {
				$typologies = is_array($project->typology)
					? $project->typology
					: json_decode($project->typology ?? '[]', true);
				$projectType = collect(is_array($typologies) ? $typologies : [])
					->map(fn($typology) => strtolower(trim($typology)))
					->contains('shops') ? 'shop' : null;
			}

			if (!$currentCity) {
				foreach ($service->getCities() as $city) {
					if (str_contains($path, Str::slug($city))) {
						$currentCity = $city;
						break;
					}
				}
			}

			$configurationLinks = $projectType === 'shop'
				? []
				: $service->getConfigurationLinksForCity($currentCity);
			$grouped['general'] = collect($grouped['general'])
				->concat($service->getCityTypeLinks($currentCity))
				->unique('url')
				->values()
				->all();
			$view->with('configurationLinks', $configurationLinks);
			$grouped['bhk'] = $configurationLinks;
			$grouped['luxury'] = [];

			foreach (['general', 'status', 'bhk', 'luxury'] as $key) {
				$view->with($key.'Links', array_slice($grouped[$key], 0, $limit));
				$view->with($key.'LinksTotal', count($grouped[$key]));
			}

			$view->with(
				'relatedCityLinks',
				$service->getRelatedCityLinks($currentCity, $projectType)
			);
			$view->with('projectType', $projectType);

			$view->with('onProjects', str_contains($path, 'flats') || request()->is('projects*'));
			$view->with('onProperties', str_contains($path, 'properties') || str_contains($path, 'apartments') || request()->is('properties*'));
		});


    }
}
