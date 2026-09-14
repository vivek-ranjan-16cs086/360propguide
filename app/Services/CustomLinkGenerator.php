<?php

namespace App\Services;

use App\Models\admin\CustomLink;
use App\Models\Developer;
use App\Models\Location;
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

        return $saved;
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
        if ($place === '') {
            return [];
        }

        $placeSlug = $location->slug ?: Str::slug($place);
        $isChild = $location->parent_id !== null;
        $type = $isChild ? 'sublocation' : 'location';
        $parentName = $isChild ? trim((string) ($location->parent?->city ?? '')) : '';
        $payloads = [];

        $payloads[] =
            $this->make(
                'flats-in-' . $placeSlug,
                'Flats in ' . $place,
                $type,
                $place,
                'Flats',
                $parentName !== '' ? ' near ' . $parentName : ''
            );



        if (!$isChild) {
            foreach (self::POSSESSIONS as $status => $label) {
                $statusSlug = Str::slug(str_replace('_', ' ', $status));
                $payloads[] =
                    $this->make(
                        $statusSlug . '-flats-in-' . $placeSlug,
                        $label . ' Flats in ' . $place,
                        'possession',
                        $place,
                        $label
                    );
            }
        }
        foreach ($this->getBhkTypesForLocation($location) as $typology) {

            $typologySlug = Str::slug($typology);

            if ($typologySlug === '') {
                continue;
            }

            $isBhk = preg_match('/^\d+\s*BHK$/i', $typology);

            if ($isBhk) {
                $slug = $typologySlug . '-flats-in-' . $placeSlug;
                $name = $typology . ' Flats in ' . $place;
            } else {
                $slug = Str::plural($typologySlug) . '-in-' . $placeSlug;
                $name = Str::plural($typology) . ' in ' . $place;
            }

            $payloads[] = $this->make(
                $slug,
                $name,
                'typology',
                $place,
                $typology
            );
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
