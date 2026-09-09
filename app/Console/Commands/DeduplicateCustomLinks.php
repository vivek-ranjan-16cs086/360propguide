<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\admin\CustomLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeduplicateCustomLinks extends Command
{
    protected $signature = 'custom-links:deduplicate';

    protected $description = 'Remove duplicate custom links and normalize locality slugs';

    public function handle(): int
    {
        $legacyToCanonical = [];

        Project::query()
            ->whereNotNull('cities')
            ->whereNotNull('location')
            ->get(['cities', 'location'])
            ->each(function ($project) use (&$legacyToCanonical) {
                $citySlug = Str::slug($project->cities);
                $locationSlug = Str::slug($project->location);

                if ($citySlug && $locationSlug) {
                    $legacyToCanonical[
                        'flats-in-' . $citySlug . '-' . $locationSlug
                    ] = 'flats-in-' . $locationSlug;
                }
            });

        $removed = 0;

        DB::transaction(function () use ($legacyToCanonical, &$removed) {
            $links = CustomLink::query()->orderBy('id')->get();
            $keepers = [];

            foreach ($links as $link) {
                $slug = strtolower(trim($link->slug ?? '', '/'));
                $canonicalSlug = $legacyToCanonical[$slug] ?? $slug;

                if ($link->type === 'property' && preg_match('/^(\d+-bhk)-flats-in-(.+)$/', $canonicalSlug, $matches)) {
                    $canonicalSlug = $matches[1] . '-apartment-in-' . $matches[2];
                }

                if ($canonicalSlug === '') {
                    $link->delete();
                    $removed++;
                    continue;
                }

                if (isset($keepers[$canonicalSlug])) {
                    $link->delete();
                    $removed++;
                    continue;
                }

                if ($link->slug !== $canonicalSlug) {
                    $link->slug = $canonicalSlug;
                    $link->canonical = $canonicalSlug;
                    $link->save();
                }

                $keepers[$canonicalSlug] = $link->id;
            }
        });

        $this->info("Removed {$removed} duplicate custom links.");

        return self::SUCCESS;
    }
}
