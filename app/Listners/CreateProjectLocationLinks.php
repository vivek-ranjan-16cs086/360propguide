<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Models\admin\CustomLink;
use Illuminate\Support\Str;

class CreateProjectLocationLinks
{
    public function handle(ProjectCreated $event): void
    {
        $project = $event->project;

        $city = trim($project->cities ?? '');
        $location = trim($project->location ?? '');

        /*
        |--------------------------------------------------------------------------
        | City Link
        |--------------------------------------------------------------------------
        */

        if (!empty($city)) {

            $typologies = is_array($project->typology)
                ? $project->typology
                : json_decode($project->typology ?? '[]', true);

            $typologies = collect(is_array($typologies) ? $typologies : [])
                ->map(fn($typology) => strtolower(trim($typology)));

            $cityLinkTypes = [
                'flats' => [
                    'matches' => $typologies->contains(fn($typology) => preg_match('/^\d+\s*-?\s*BHK$/i', $typology)),
                    'slug' => 'flats-in-' . Str::slug($city),
                    'label' => 'Flats in ',
                ],
                'shops' => [
                    'matches' => $typologies->contains('shops'),
                    'slug' => 'shops-in-' . Str::slug($city),
                    'label' => 'Shops in ',
                ],
                'studio' => [
                    'matches' => $typologies->contains(fn($typology) => in_array($typology, ['studio', 'studio apartments'])),
                    'slug' => 'studio-apartments-in-' . Str::slug($city),
                    'label' => 'Studio Apartments in ',
                ],
            ];

            foreach ($cityLinkTypes as $type => $cityLink) {
                if (!$cityLink['matches']) {
                    continue;
                }

                CustomLink::firstOrCreate(
                    ['slug' => $cityLink['slug']],
                    [
                        'title' => $cityLink['label'] . $city,
                        'name' => $cityLink['label'] . $city,
                        'type' => 'city-' . $type,
                        'is_active' => 1,
                    ]
                );
            }

            foreach ($typologies as $typology) {
                if (!preg_match('/^(\d+)\s*-?\s*BHK$/i', trim($typology), $matches)) {
                    continue;
                }

                $bhk = (int) $matches[1];
                $bhkSlug = $bhk . '-bhk-flats-in-' . Str::slug($city);

                CustomLink::firstOrCreate(
                    [
                        'slug' => $bhkSlug,
                    ],
                    [
                        'title' => $bhk . ' BHK Flats in ' . $city,
                        'name' => $bhk . ' BHK Flats in ' . $city,
                        'type' => 'bhk-city',
                        'is_active' => 1,
                    ]
                );
            }

            $statusLinks = [
                'ready_to_move' => 'Ready to Move Flats in ',
                'new_launch' => 'New Launch Flats in ',
                'under_construction' => 'Under Construction Flats in ',
            ];

            if (isset($statusLinks[$project->project_status])) {
                $statusSlug = str_replace('_', '-', $project->project_status)
                    . '-flats-in-' . Str::slug($city);

                CustomLink::firstOrCreate(
                    ['slug' => $statusSlug],
                    [
                        'title' => $statusLinks[$project->project_status] . $city,
                        'name' => $statusLinks[$project->project_status] . $city,
                        'type' => 'configuration',
                        'is_active' => 1,
                    ]
                );
            }

            if ((float) $project->price > 30000000) {
                $luxurySlug = 'luxury-flats-in-' . Str::slug($city);

                CustomLink::firstOrCreate(
                    ['slug' => $luxurySlug],
                    [
                        'title' => 'Luxury Flats in ' . $city,
                        'name' => 'Luxury Flats in ' . $city,
                        'type' => 'configuration',
                        'is_active' => 1,
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Location Link
        |--------------------------------------------------------------------------
        */

        if (!empty($city) && !empty($location)) {

            $locationSlug = 'flats-in-' . Str::slug($location);
            $legacyLocationSlug = 'flats-in-' . Str::slug($city) . '-' . Str::slug($location);

            CustomLink::where('slug', $legacyLocationSlug)->delete();

            CustomLink::firstOrCreate(
                [
                    'slug' => $locationSlug,
                ],
                [
                    'title' => 'Flats in ' . $location,
                    'name' => 'Flats in ' . $location,
                    'type' => 'location',
                    'is_active' => 1,
                ]
            );
        }
    }
}