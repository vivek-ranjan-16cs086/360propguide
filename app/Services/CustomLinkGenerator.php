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
        'within_a_year' => 'Within A Year',
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

    public function syncLocation(Location $location): int
    {
        if (!$location->status) {
            return 0;
        }

        $location->loadMissing('parent:id,city');

        return $this->save($this->locationPayloads($location));
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

        $payloads[] = $this->make(
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
