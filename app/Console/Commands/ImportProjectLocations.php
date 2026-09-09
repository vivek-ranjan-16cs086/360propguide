<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportProjectLocations extends Command
{
    protected $signature = 'locations:import-from-projects';

    protected $description = 'Create locations and sublocations from existing project cities/locations, then set project location_id and sublocation_id';

    public function handle(): int
    {
        $projects = Project::query()
            ->select('id', 'cities', 'location')
            ->get();

        if ($projects->isEmpty()) {
            $this->warn('No projects found.');
            return self::SUCCESS;
        }

        $cityMap = [];
        $subMap = [];
        $parentsCreated = 0;
        $childrenCreated = 0;
        $projectsUpdated = 0;

        DB::transaction(function () use ($projects, &$cityMap, &$subMap, &$parentsCreated, &$childrenCreated, &$projectsUpdated) {
            foreach ($projects as $project) {
                $cityName = $this->normalizeName($project->cities);
                $localityName = $this->normalizeName($project->location);

                if ($cityName === '') {
                    continue;
                }

                $cityKey = Str::lower($cityName);

                if (!isset($cityMap[$cityKey])) {
                    $parent = Location::query()
                        ->parents()
                        ->whereRaw('LOWER(TRIM(city)) = ?', [$cityKey])
                        ->first();

                    if (!$parent) {
                        $parent = new Location();
                        $parent->country = 'India';
                        $parent->city = $cityName;
                        $parent->parent_id = null;
                        $parent->status = 1;
                        $parent->slug = $this->uniqueSlug($cityName);
                        $parent->save();
                        $parentsCreated++;
                    }

                    $cityMap[$cityKey] = $parent;
                }

                $parent = $cityMap[$cityKey];
                $child = null;

                if ($localityName !== '' && Str::lower($localityName) !== $cityKey) {
                    $subKey = $cityKey . '|' . Str::lower($localityName);

                    if (!isset($subMap[$subKey])) {
                        $child = Location::query()
                            ->where('parent_id', $parent->id)
                            ->whereRaw('LOWER(TRIM(city)) = ?', [Str::lower($localityName)])
                            ->first();

                        if (!$child) {
                            $child = new Location();
                            $child->country = $parent->country;
                            $child->state = $parent->state;
                            $child->city = $localityName;
                            $child->parent_id = $parent->id;
                            $child->status = 1;
                            $child->slug = $this->uniqueSlug($localityName);
                            $child->save();
                            $childrenCreated++;
                        }

                        $subMap[$subKey] = $child;
                    }

                    $child = $subMap[$subKey];
                }

                $project->location_id = $parent->id;
                $project->sublocation_id = $child?->id;
                $project->save();
                $projectsUpdated++;
            }
        });

        $this->info("Parent locations created: {$parentsCreated}");
        $this->info("Sublocations created: {$childrenCreated}");
        $this->info("Projects updated: {$projectsUpdated}");
        $this->info('Locations total: ' . Location::count());

        return self::SUCCESS;
    }

    private function normalizeName(?string $value): string
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return $value;
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name) ?: 'location';
        $base = $slug;
        $i = 1;

        while (
            Location::withTrashed()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
