<?php

namespace App\Services;

use App\Models\admin\CustomLink;
use App\Models\Location;
use App\Models\Project;
use Illuminate\Support\Str;

class ProjectLinksService
{
    public function getCities(): array
    {
        $cities = Location::parentCityNames()
            ->map(fn ($city) => strtolower(trim($city)))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        usort($cities, fn ($a, $b) => strlen($b) - strlen($a));

        return $cities;
    }

    public function getProjectLinks(): array
    {
        return CustomLink::where(function ($q) {
                $q->where('type', '!=', 'property')->orWhereNull('type');
            })
            ->orderBy('id', 'DESC')
            ->get()
            ->map(fn($link) => [
                'text'  => $link->name,
                'title' => $link->title,
                'url'   => trim(strtolower($link->slug), '/'),
                'type'  => $link->type,
            ])
            ->unique(fn($link) => $link['url'])
            ->toArray();
    }

    public function getFooterCustomLinks(?string $currentPath = null): array
    {
        $path = strtolower(trim((string) $currentPath, '/'));
        $typeOrder = [
            'location' => 1,
            'sublocation' => 2,
            'possession' => 3,
            'developer' => 4,
        ];

        return CustomLink::query()
            ->where('is_active', 1)
            ->whereIn('type', ['location', 'sublocation', 'possession', 'developer'])
            ->get(['name', 'title', 'slug', 'type'])
            ->map(fn ($link) => [
                'text' => $link->name ?: $link->title,
                'title' => $link->title,
                'url' => trim(strtolower((string) $link->slug), '/'),
                'type' => $link->type,
            ])
            ->filter(function ($link) use ($path) {
                return $link['url'] !== '' && $link['url'] !== $path;
            })
            ->unique('url')
            ->sortBy(function ($link) use ($typeOrder) {
                $order = $typeOrder[$link['type']] ?? 9;

                return sprintf('%d-%s', $order, strtolower((string) $link['text']));
            })
            ->values()
            ->all();
    }

    public function getPropertyLinks(): array
    {
        return CustomLink::where('type', 'property')
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($link) {
                $text = preg_replace('/\bflats\b/i', 'Apartments', $link->name ?: $link->title);
                $title = preg_replace('/\bflats\b/i', 'Apartments', $link->title ?: $text);

                return [
                'text'  => $text,
                'title' => $title,
                'url'   => $link->slug,
                ];
            })
            ->toArray();
    }

    public function getCityTypeLinks(?string $city): array
    {
        if (!$city) {
            return [];
        }

        $typologies = Project::query()
            ->where('status', 1)
            ->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower(trim($city))])
            ->pluck('typology')
            ->flatMap(function ($value) {
                $values = is_array($value) ? $value : json_decode($value ?? '[]', true);
                return collect(is_array($values) ? $values : [])
                    ->map(fn($typology) => strtolower(trim($typology)));
            });

        $cityName = ucwords(trim($city));
        $citySlug = Str::slug($city);
        $links = collect();

        if ($typologies->contains(fn($typology) => preg_match('/^\d+\s*-?\s*BHK$/i', $typology))) {
            $links->push(['text' => 'Flats in ' . $cityName, 'title' => 'Flats in ' . $cityName, 'url' => 'flats-in-' . $citySlug]);
        }

        if ($typologies->contains('shops')) {
            $links->push(['text' => 'Shops in ' . $cityName, 'title' => 'Shops in ' . $cityName, 'url' => 'shops-in-' . $citySlug]);
        }

        if ($typologies->contains(fn($typology) => in_array($typology, ['studio', 'studio apartments']))) {
            $links->push(['text' => 'Studio Apartments in ' . $cityName, 'title' => 'Studio Apartments in ' . $cityName, 'url' => 'studio-apartments-in-' . $citySlug]);
        }

        return $links->all();
    }

    public function getLocationLinkForPath(string $path): array
    {
        $locations = Project::query()
            ->where('status', 1)
            ->whereNotNull('location')
            ->pluck('location')
            ->map(fn($location) => trim($location))
            ->filter()
            ->unique(fn($location) => strtolower($location))
            ->sortByDesc(fn($location) => strlen(Str::slug($location)));

        foreach ($locations as $location) {
            $locationSlug = Str::slug($location);

            if ($locationSlug && str_contains(strtolower($path), $locationSlug)) {
                return [[
                    'text' => 'Flats in ' . $location,
                    'title' => 'Flats in ' . $location,
                    'url' => 'flats-in-' . $locationSlug,
                ]];
            }
        }

        return [];
    }

    public function getLocationLinksForCity(?string $city): array
    {
        if (!$city) {
            return [];
        }

        return Project::query()
            ->where('status', 1)
            ->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower(trim($city))])
            ->whereNotNull('location')
            ->pluck('location')
            ->map(fn($location) => trim($location))
            ->filter()
            ->unique(fn($location) => strtolower($location))
            ->map(fn($location) => [
                'text' => 'Flats in ' . $location,
                'title' => 'Flats in ' . $location,
                'url' => 'flats-in-' . Str::slug($location),
            ])
            ->values()
            ->all();
    }

    public function getConfigurationLinksForCity(?string $city): array
    {
        if (!$city) {
            return [];
        }

        $projects = Project::query()
            ->where('status', 1)
            ->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower(trim($city))])
            ->get(['project_status', 'price']);

        $links = collect();
        $cityName = ucwords(trim($city));
        $citySlug = Str::slug($city);

        foreach ([
            'ready_to_move' => 'Ready to Move Flats in ',
            'new_launch' => 'New Launch Flats in ',
            'under_construction' => 'Under Construction Flats in ',
        ] as $status => $label) {
            if ($projects->contains('project_status', $status)) {
                $links->push([
                    'text' => $label . $cityName,
                    'title' => $label . $cityName,
                    'url' => str_replace('_', '-', $status) . '-flats-in-' . $citySlug,
                    'type' => 'configuration',
                ]);
            }
        }

        $luxuryProjects = Project::query()
            ->where('status', 1)
            ->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower(trim($city))])
            ->where('price', '>', 30000000)
            ->exists();

        if ($luxuryProjects) {
            $links->push([
                'text' => 'Luxury Projects in ' . $cityName,
                'title' => 'Luxury Projects in ' . $cityName,
                'url' => 'luxury-flats-in-' . $citySlug,
                'type' => 'configuration',
            ]);
        }

        return $links->unique('url')->values()->all();
    }

    public function getRelatedCityLinks(?string $currentCity = null, ?string $projectType = null): array
    {
        $links = [];
        $cities = Project::query()
            ->where('status', 1)
            ->whereNotNull('cities')
            ->pluck('cities')
            ->map(fn($city) => trim($city))
            ->filter()
            ->unique(fn($city) => strtolower($city))
            ->sortBy(function ($city) use ($currentCity) {
                return $currentCity && strtolower(trim($city)) === strtolower(trim($currentCity))
                    ? 0
                    : 1;
            })
            ->values();

        foreach ($cities as $city) {
            $cityProjects = Project::query()
                ->where('status', 1)
                ->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower($city)])
                ->pluck('typology')
                ->flatMap(function ($typology) use ($projectType) {
                    $typologies = is_array($typology)
                        ? $typology
                        : json_decode($typology ?? '[]', true);

                    return collect(is_array($typologies) ? $typologies : [])
                        ->map(function ($value) use ($projectType) {
                            if ($projectType === 'shop') {
                                return strtolower(trim($value)) === 'shops' ? 'shops' : null;
                            }

                            return preg_match('/^(\d+)\s*-?\s*BHK$/i', trim($value), $matches)
                                ? (int) $matches[1]
                                : null;
                        })
                        ->filter();
                })
                ->unique();

            if ($projectType === 'shop') {
                $cityLinks = $cityProjects->isNotEmpty()
                    ? [[
                        'text' => 'Shops in ' . ucwords($city),
                        'url' => 'shops-in-' . Str::slug($city),
                    ]]
                    : [];
            } else {
                $cityLinks = $cityProjects
                    ->sort()
                    ->take(4)
                    ->map(fn($bhk) => [
                        'text' => $bhk . ' BHK Flats in ' . $city,
                        'url' => $bhk . '-bhk-flats-in-' . Str::slug($city),
                    ])
                    ->all();
            }

            if (empty($cityLinks)) {
                continue;
            }

            $links[] = [
                'city' => $city,
                'links' => $cityLinks,
            ];
        }

        return $links;
    }

    /**
     * group the links.
     */
    public function getGroupedLinksForPath(string $path): array
    {
        $path = strtolower(trim($path, '/'));
        $cities = $this->getCities();
        $customLinks = $this->getProjectLinks();

        $location = null;
        foreach ($cities as $city) {
            $citySlug = str_replace(' ', '-', $city);
            if (str_contains($path, $citySlug)) {
                $location = $city;
                break;
            }
        }

        $filtered = [];
        foreach ($customLinks as $link) {
            $text = strtolower($link['text']);

            if ($location) {
                if (!str_contains($text, $location)) continue;

                $tooSpecific = false;
                foreach ($cities as $city) {
                    if ($city !== $location && str_contains($city, $location) && str_contains($text, $city)) {
                        $tooSpecific = true;
                        break;
                    }
                }
                if ($tooSpecific) continue;
            }
            $filtered[] = $link;
        }

        if (empty($filtered)) {
            $filtered = $customLinks;
        }

        // current page ka link list se hata do
        $filtered = array_values(array_filter($filtered, function ($link) use ($path) {
            $linkPath = trim(strtolower(parse_url($link['url'], PHP_URL_PATH) ?? $link['url']), '/');
            return $linkPath !== $path;
        }));

        $status = $bhk = $luxury = $general = [];

        foreach ($filtered as $link) {
            $text = strtolower(trim($link['text']));
            $isConfiguration = ($link['type'] ?? null) === 'configuration';

            $isStatus  = str_contains($text, 'ready') || str_contains($text, 'under');
            $isBhk     = (bool) preg_match('/[1-4] bhk/', $text);
            $isLuxury  = str_contains($text, 'luxury');
            $isGeneral = str_starts_with($text, 'flats')
                || str_starts_with($text, 'shops')
                || str_starts_with($text, 'plots')
                || str_starts_with($text, 'studio apartments');

            if ($isStatus && !$isConfiguration)  $status[]  = $link;
            if ($isBhk)     $bhk[]     = $link;
            if ($isLuxury && !$isConfiguration)  $luxury[]  = $link;
            if ($isConfiguration) $bhk[] = $link;
            if (!$isStatus && !$isBhk && !$isLuxury && $isGeneral) {
                $general[] = $link;
            }
        }

        if ($location) {
            $configurationProjects = Project::query()
                ->where('status', 1)
                ->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower($location)])
                ->where(function ($query) {
                    $query->whereIn('project_status', [
                        'ready_to_move',
                        'new_launch',
                        'under_construction',
                    ])->orWhere('price', '>=', 30000000);
                })
                ->pluck('typology');

            $dynamicBhkLinks = $configurationProjects
                ->flatMap(function ($typology) {
                    $typologies = is_array($typology)
                        ? $typology
                        : json_decode($typology ?? '[]', true);

                    return collect(is_array($typologies) ? $typologies : [])
                        ->map(function ($value) {
                            return preg_match('/^(\d+)\s*-?\s*BHK$/i', trim($value), $matches)
                                ? (int) $matches[1]
                                : null;
                        })
                        ->filter();
                })
                ->unique()
                ->sort()
                ->map(fn($bhk) => [
                    'text' => $bhk . ' BHK Flats in ' . ucwords($location),
                    'title' => $bhk . ' BHK Flats in ' . ucwords($location),
                    'url' => $bhk . '-bhk-flats-in-' . Str::slug($location),
                ]);

            $bhk = collect($bhk)
                ->concat($dynamicBhkLinks)
                ->unique('url')
                ->values()
                ->all();
        }

        return compact('general', 'status', 'bhk', 'luxury');
    }
}