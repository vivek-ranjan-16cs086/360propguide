<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Project;
use App\Models\Location;
use App\Models\Developer;
use App\Observers\LocationObserver;
use App\Observers\DeveloperObserver;
use App\Observers\ProjectObserver; 

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
        Location::observe(LocationObserver::class);
        Developer::observe(DeveloperObserver::class);
        Project::observe(ProjectObserver::class);

        View::composer('frontend.layouts.app', function ($view) {
			$service = app(\App\Services\ProjectLinksService::class);
			$footerLimit = 32;
			$path = strtolower(request()->path());

			$footerCustomLinks = $service->getFooterCustomLinks($path);
			$view->with('footerCustomLinks', array_slice($footerCustomLinks, 0, $footerLimit));
			$view->with('footerCustomLinksTotal', count($footerCustomLinks));

			$propertyLinks = $service->getPropertyLinks();
			$view->with('propertyLinks', array_slice($propertyLinks, 0, 16));
			$view->with('propertyLinksTotal', count($propertyLinks));
			$view->with('cities', $service->getCities());
			$view->with('relatedCityLinks', []);
			$view->with('configurationLinks', []);
			$view->with('generalLinks', []);
			$view->with('onProjects', str_contains($path, 'flats') || request()->is('projects*'));
			$view->with('onProperties', str_contains($path, 'properties') || str_contains($path, 'apartments') || request()->is('properties*'));
		});


    }
}
