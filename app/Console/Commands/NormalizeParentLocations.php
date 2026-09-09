<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NormalizeParentLocations extends Command
{
    protected $signature = 'locations:normalize-parents';

    protected $description = 'Keep only the six parent cities and move every other location under them';

    private array $parentNames = [
        'Dehradun',
        'Ghaziabad',
        'Gurugram',
        'Noida',
        'Shimla',
        'Yamuna Expressway',
    ];

    private array $cityToParent = [
        'dehradun' => 'Dehradun',
        'ghaziabad' => 'Ghaziabad',
        'gurugram' => 'Gurugram',
        'gurgaon' => 'Gurugram',
        'noida' => 'Noida',
        'shimla' => 'Shimla',
        'yamuna expressway' => 'Yamuna Expressway',
        'greater noida' => 'Yamuna Expressway',
        'greater noida west' => 'Noida',
        'greater noida, uttar pradesh' => 'Yamuna Expressway',
        'noida expressway' => 'Noida',
        'kanpur' => 'Noida',
    ];

    public function handle(): int
    {
        DB::transaction(function () {
            $parents = $this->ensureParents();

            Location::query()
                ->with('parent')
                ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                ->get()
                ->each(function (Location $location) use ($parents) {
                    $sourceName = $location->parent_id
                        ? ($location->parent->city ?? $location->city)
                        : $location->city;

                    $canonicalParentName = $this->resolveParentName($sourceName) ?? 'Noida';

                    if (Str::lower($location->city) === Str::lower($canonicalParentName)) {
                        $location->parent_id = null;
                        $location->city = $canonicalParentName;
                        $location->save();
                        return;
                    }

                    $parent = $parents[Str::lower($canonicalParentName)];
                    if ($location->id === $parent->id) {
                        return;
                    }

                    $location->parent_id = $parent->id;
                    $location->save();
                });

            $updated = 0;

            Project::query()->get()->each(function (Project $project) use ($parents, &$updated) {
                $parentName = $this->resolveParentName($project->cities)
                    ?? $this->resolveParentName($project->location)
                    ?? 'Noida';
                $parent = $parents[Str::lower($parentName)];
                $localityName = $this->normalizeName($project->location);

                $child = null;
                if ($localityName !== '' && Str::lower($localityName) !== Str::lower($parent->city)) {
                    $child = Location::query()
                        ->where('parent_id', $parent->id)
                        ->whereRaw('LOWER(TRIM(city)) = ?', [Str::lower($localityName)])
                        ->first();

                    if (!$child) {
                        $child = new Location();
                        $child->country = $parent->country ?: 'India';
                        $child->state = $parent->state;
                        $child->city = $localityName;
                        $child->parent_id = $parent->id;
                        $child->status = 1;
                        $child->slug = $this->uniqueSlug($localityName);
                        $child->save();
                    }
                }

                $project->location_id = $parent->id;
                $project->sublocation_id = $child?->id;
                $project->cities = $parent->city;
                $project->save();
                $updated++;
            });

            $this->info('Parent locations: ' . Location::parents()->count());
            Location::parents()->orderBy('city')->get(['city'])->each(function ($p) {
                $count = Location::where('parent_id', $p->id)->count();
                $this->line('  ' . $p->city . ' (' . $count . ' sublocations)');
            });
            $this->info("Projects updated: {$updated}");
        });

        return self::SUCCESS;
    }

    private function ensureParents(): array
    {
        $parents = [];

        foreach ($this->parentNames as $name) {
            $parent = Location::query()
                ->whereRaw('LOWER(TRIM(city)) = ?', [Str::lower($name)])
                ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                ->first();

            if (!$parent) {
                $parent = new Location();
                $parent->country = 'India';
                $parent->city = $name;
                $parent->parent_id = null;
                $parent->status = 1;
                $parent->slug = $this->uniqueSlug($name);
                $parent->save();
            } else {
                $parent->city = $name;
                $parent->parent_id = null;
                $parent->status = 1;
                $parent->save();
            }

            $parents[Str::lower($name)] = $parent;
        }

        return $parents;
    }

    private function resolveParentName(?string $value): ?string
    {
        $value = Str::lower($this->normalizeName($value));
        if ($value === '') {
            return null;
        }

        return $this->cityToParent[$value] ?? null;
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

        while (Location::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
