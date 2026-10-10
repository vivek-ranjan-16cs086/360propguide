<?php

namespace App\Services;

use App\Models\admin\CustomLink;
use App\Models\Developer;
use App\Models\Location;
use App\Models\Property;
use App\Models\Project;
use Illuminate\Support\Str;


class CustomLinkGenerator
{
    public const POSSESSIONS = [
        'new_launch' => 'New Launch',
        'under_construction' => 'Under Construction',
        'ready_to_move' => 'Ready To Move',
        'completed' => 'Completed',
        // 'within_a_year' => 'Within A Year',
    ];

    public function flushAll(): int
    {
        $count = CustomLink::query()->count();
        CustomLink::query()->delete();

        return $count;
    }

    public function generateAll(): int
    {
        $saved = 0;

        Location::query()
            ->active()
            ->with('parent:id,city')
            ->orderBy('id')
            ->chunkById(100, function ($locations) use (&$saved) {
                foreach ($locations as $location) {
                    $saved += $this->syncLocation($location);
                }
            });

        Developer::query()
            ->orderBy('id')
            ->chunkById(50, function ($developers) use (&$saved) {
                foreach ($developers as $developer) {
                    $saved += $this->syncDeveloper($developer);
                }
            });

        $saved += $this->syncPropertyLinks();

        return $saved;
    }

    public function syncPropertyLinks(): int
    {
        $propertiesByCity = [];

        Property::query()
            ->where('status', 'approved')
            ->whereNotNull('city')
            ->select(['id', 'city', 'property_type', 'listing_type'])
            ->chunkById(500, function ($properties) use (&$propertiesByCity) {
                foreach ($properties as $property) {
                    $city = trim((string) $property->city);
                    $cityKey = strtolower($city);

                    if ($cityKey === '') {
                        continue;
                    }

                    $propertiesByCity[$cityKey]['city'] ??= $city;
                    $propertiesByCity[$cityKey]['property_types'][] = strtolower(trim((string) $property->property_type));
                    $propertiesByCity[$cityKey]['listing_types'][] = strtolower(trim((string) $property->listing_type));
                }
            });

        $payloads = [];

        foreach ($propertiesByCity as $properties) {
            $payloads = array_merge(
                $payloads,
                $this->propertyPayloads(
                    $properties['city'],
                    array_unique($properties['property_types']),
                    array_unique($properties['listing_types'])
                )
            );
        }

        $currentSlugs = collect($payloads)->pluck('slug')->all();
        $generatedPrefixes = [
            'apartments-in-',
            'plots-in-',
            'sale-properties-in-',
            'rent-properties-in-',
        ];

        $staleLinks = CustomLink::query()
            ->where('type', 'property')
            ->where(function ($query) use ($generatedPrefixes) {
                foreach ($generatedPrefixes as $prefix) {
                    $query->orWhere('slug', 'like', $prefix . '%');
                }
            });

        if ($currentSlugs !== []) {
            $staleLinks->whereNotIn('slug', $currentSlugs);
        }

        $staleLinks->delete();

        return $this->save($payloads);
    }

    private function propertyPayloads(string $city, array $propertyTypes, array $listingTypes): array
    {
        $cityName = ucwords(strtolower(trim($city)));
        $citySlug = Str::slug($city);

        if ($citySlug === '') {
            return [];
        }

        $definitions = [];

        if (in_array('apartment', $propertyTypes, true)) {
            $definitions[] = ['apartments-in-' . $citySlug, 'Apartments in ' . $cityName];
        }

        if (in_array('plots', $propertyTypes, true)) {
            $definitions[] = ['plots-in-' . $citySlug, 'Plots in ' . $cityName];
        }

        foreach (['sale' => 'Sale', 'rent' => 'Rent'] as $listingType => $label) {
            if (in_array($listingType, $listingTypes, true)) {
                $definitions[] = [
                    $listingType . '-properties-in-' . $citySlug,
                    $label . ' Properties in ' . $cityName,
                ];
            }
        }

        return array_map(function (array $definition) use ($cityName) {
            [$slug, $name] = $definition;
            $payload = $this->make($slug, $name, 'property', $cityName, $name);
            $payload['keywords'] = strtolower($name . ', properties in ' . $cityName . ', real estate in ' . $cityName);
            $payload['links_description'] = 'Browse available ' . strtolower($name) . ' and compare property details in ' . $cityName . '.';

            return $payload;
        }, $definitions);
    }
    private function getPossessionsForLocation(Location $location): array
    {
        return Project::query()
            ->where(function ($q) use ($location) {
                $q->where('location_id', $location->id)
                    ->orWhereHas('sublocation', function ($q) use ($location) {
                        $q->where('parent_id', $location->id);
                    });
            })
            ->whereNotNull('project_status')
            ->whereIn('project_status', array_keys(self::POSSESSIONS))
            ->pluck('project_status')
            ->unique()
            ->filter(fn($status) => isset(self::POSSESSIONS[$status]))
            ->mapWithKeys(fn($status) => [
                $status => self::POSSESSIONS[$status],
            ])
            ->all();
    }
    private function removeStaleBhkLinks(
        Location $location,
        array $payloads
    ): void {
        $currentBhkSlugs = collect($payloads)
            ->where('type', 'bhk')
            ->pluck('slug')
            ->all();
        $placeSlug = $location->slug ?: Str::slug($location->city);
        if ($placeSlug === '') {
            return;
        }

        $query = CustomLink::query()
            ->where('type', 'bhk')
            ->where('slug', 'like', '%-flats-in-' . $placeSlug);


        if (empty($currentBhkSlugs)) {
            $query->delete();
            return;
        }

        $query
            ->whereNotIn('slug', $currentBhkSlugs)
            ->delete();
    }
    private function removeDisabledPossessionLinks(Location $location): void
    {
        if ($location->parent_id !== null) {
            return;
        }

        $placeSlug = $location->slug ?: Str::slug($location->city);

        if ($placeSlug === '') {
            return;
        }

        CustomLink::query()
            ->where('type', 'possession')
            ->whereIn('slug', [
                'within-a-year-flats-in-' . $placeSlug,
            ])
            ->delete();
    }
   public function syncLocation(Location $location): int
{
    if (!$location->status) {
        return 0;
    }

    $location->loadMissing('parent:id,city');

    $payloads = $this->locationPayloads($location);

    $this->removeStaleBhkLinks($location, $payloads);
    $this->removeDisabledPossessionLinks($location);

    // Remove old child-location slug
    if ($location->parent_id !== null) {
        $oldSlug = $location->slug ?: Str::slug($location->city);

        CustomLink::query()
            ->where('type', 'sublocation')
            ->where('slug', 'flats-in-' . $oldSlug)
            ->delete();
    }

    return $this->save($payloads);
}
    public function syncDeveloper(Developer $developer): int
    {
        $name = trim((string) $developer->developer_name);
        if ($name === '') {
            return 0;
        }

        return $this->save($this->developerPayloads($developer));
    }

    public function syncProject(Project $project): int
    {
        $saved = 0;
        $city = $project->location_id
            ? Location::query()->find($project->location_id)
            : null;

        if (!$city && filled($project->cities)) {
            $city = Location::parents()
                ->active()
                ->whereRaw('LOWER(TRIM(city)) = ?', [strtolower(trim($project->cities))])
                ->first();
        }

        if ($city) {
            $saved += $this->syncLocation($city);
        }

        $sublocation = $project->sublocation_id
            ? Location::query()->find($project->sublocation_id)
            : null;

        if (!$sublocation && filled($project->location)) {
            $sublocation = Location::query()
                ->whereNotNull('parent_id')
                ->active()
                ->whereRaw('LOWER(TRIM(city)) = ?', [strtolower(trim($project->location))])
                ->first();
        }

        if ($sublocation) {
            $saved += $this->syncLocation($sublocation);
        }

        if (filled($project->developer_name)) {
            $developer = Developer::query()
                ->whereRaw('LOWER(TRIM(developer_name)) = ?', [strtolower(trim($project->developer_name))])
                ->first();

            if ($developer) {
                $saved += $this->syncDeveloper($developer);
            }
        }

        return $saved;
    }

    private function getBhkTypesForLocation(Location $location): array
    {
        // Typology links ONLY for major cities
        if ($location->parent_id !== null) {
            return [];
        }

        $query = Project::query()
            ->whereNotNull('typology')
            ->where(function ($q) use ($location) {
                $q->where('location_id', $location->id)
                    ->orWhereHas('sublocation', function ($q) use ($location) {
                        $q->where('parent_id', $location->id);
                    });
            });
        // $query = Project::query()
        //     ->whereNotNull('typology')
        //     ->whereRaw(
        //         'LOWER(TRIM(cities)) = ?',
        //         [strtolower(trim($location->city))]
        //     );

        $typologies = [];

        $query
            ->select(['id', 'typology'])
            ->chunkById(100, function ($projects) use (&$typologies) {

                foreach ($projects as $project) {

                    $values = $project->typology;

                    if (is_string($values)) {

                        $decoded = json_decode($values, true);

                        if (
                            json_last_error() === JSON_ERROR_NONE &&
                            is_array($decoded)
                        ) {
                            $values = $decoded;
                        } else {
                            $values = array_filter(
                                array_map(
                                    'trim',
                                    explode(',', $values)
                                )
                            );
                        }
                    }

                    if (!is_array($values)) {
                        continue;
                    }

                    foreach ($values as $typology) {

                        $typology = trim((string) $typology);

                        if ($typology === '') {
                            continue;
                        }

                        // Keep every typology:
                        // 2 BHK, 3 BHK, Plot, Villa, Studio, etc.
                        $typologies[] = preg_replace(
                            '/\s+/',
                            ' ',
                            $typology
                        );
                    }
                }
            });

        return collect($typologies)
            ->filter()
            ->unique(fn($type) => strtolower($type))
            ->values()
            ->all();
    }


   private function locationPayloads(Location $location): array
{
    $place = trim((string) $location->city);

    // Remove "Flats in" if it already exists in location name
    $place = preg_replace('/^flats\s+in\s+/i', '', $place);
    $place = trim($place);

    if ($location->parent_id !== null) {
        $place = preg_replace('/\s*\([^)]*\)/', '', $place);
        $place = trim($place);
    }

    if ($place === '') {
        return [];
    }

    $isChild = $location->parent_id !== null;

    $type = $isChild ? 'sublocation' : 'location';

    $parentName = $isChild
        ? trim((string) ($location->parent?->city ?? ''))
        : '';

    $stateName = trim((string) $location->state);

    if ($isChild && $stateName !== '') {
        $placeSlug = Str::slug($place) . '-' . Str::slug($stateName);
    } else {
        // IMPORTANT: Do not use $location->slug here
        $placeSlug = Str::slug($place);
    }

    $payloads = [];

    // Main Flats link
    $payloads[] = $this->make(
        'flats-in-' . $placeSlug,
        'Flats in ' . $place,
        $type,
        $place,
        'Flats',
        $parentName !== '' ? ' near ' . $parentName : ''
    );

    // Possession links only for parent locations
    if (!$isChild) {
        foreach (self::POSSESSIONS as $status => $label) {

            $statusSlug = Str::slug(
                str_replace('_', ' ', $status)
            );

            $payloads[] = $this->make(
                $statusSlug . '-flats-in-' . $placeSlug,
                $label . ' Flats in ' . $place,
                'possession',
                $place,
                $label
            );
        }
    }

    return $payloads;
}
    public function testPayloads(Location $location)
    {
        $val = $this->locationPayloads($location);

        dd($val);
    }


    private function developerPayloads(Developer $developer): array
    {
        $name = trim((string) $developer->developer_name);
        $devSlug = Str::slug($name);
        if ($devSlug === '') {
            return [];
        }

        return [
            $this->make(
                $devSlug . '-projects',
                $name . ' Projects',
                'developer',
                'Delhi NCR',
                $name
            ),
        ];
    }

    private function make(string $slug, string $name, string $type, string $place, string $config = 'Flats', string $extra = ''): array
    {
        return [
            'slug' => $slug,
            'canonical' => $slug,
            'name' => $name,
            'title' => $name . ' | 360PropGuide',
            'type' => $type,
            'description' => 'Explore ' . $name . ' with updated prices, possession timelines, and connectivity in ' . $place . '.',
            'keywords' => strtolower($name . ', flats in ' . $place . ', projects in ' . $place . ', ' . $config . ' ' . $place),
            'links_description' => 'Browse ' . $name . $extra . '. Compare layouts, possession status, and pricing to shortlist the right home in ' . $place . '.',
            'is_active' => 1,
        ];
    }

    private function save(array $payloads): int
    {
        $count = 0;

        foreach ($payloads as $payload) {
            $slug = trim((string) ($payload['slug'] ?? ''));
            if ($slug === '') {
                continue;
            }

            CustomLink::updateOrCreate(
                ['slug' => $slug],
                $payload
            );
            $count++;
        }

        return $count;
    }
}
