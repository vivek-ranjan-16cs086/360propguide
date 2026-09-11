<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Blog;
use App\Models\Project;
use App\Models\Property; 
use App\Models\Career;
use App\Models\Developer;
use App\Models\AminityList;
use App\Models\admin\CustomLink; 
use App\Models\YouTubeVideo;
use App\Models\Location;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FrontendPageController extends Controller
{
    public function __construct()
    {
       
    }

	public function getHomePageData()
	{
		$pageData = [];
		//blogs
		$blogs = Blog::orderBy('id', 'DESC')->where('status',1)->take(5)->get();
		$pageData['blogs'] = $blogs;
		//projects
		$projects = Project::orderBy('id', 'DESC')->where('status', 1)->take(5)->get(); 
		$projects = $projects->map(function ($project) {
			$typologies = json_decode($project->typology, true);
			$project->typology_string = is_array($typologies) ? implode(', ', $typologies) : 'N/A';
			return $project;
		});
		$pageData['projects'] = $projects;
		//project city
		$pageData['cities'] = Location::parentCityNames();
		$pageData['projectCount'] = Project::where('status', 1)->count();
		$pageData['propertyCount'] = Property::where('status', 'approved')->count();
		$feeds = data_get($this->facebookPostData(), 'feed', []);

		//Youtube video Api
		$videos = YouTubeVideo::orderBy('published_time', 'DESC')->take(30)->get();

		// helper: ISO 8601 duration ko total seconds me convert karo
		$toSeconds = function ($duration) {
			preg_match('/PT(?:(\d+)M)?(?:(\d+)S)?/', $duration, $matches);
			$minutes = isset($matches[1]) ? intval($matches[1]) : 0;
			$seconds = isset($matches[2]) ? intval($matches[2]) : 0;
			return ($minutes * 60) + $seconds;
		};

		// Long videos: > 2 minutes (120 seconds)
		$filteredVideos = $videos->filter(function ($video) use ($toSeconds) {
			return $toSeconds($video->duration) > 120;
		})->take(6);

		// Shorts: <= 60 seconds
		$filteredShorts = $videos->filter(function ($video) use ($toSeconds) {
			return $toSeconds($video->duration) <= 60;
		})->take(6);

		$youtubeVideo = json_decode($filteredVideos);
		$youtubeShorts = json_decode($filteredShorts);

		// return view with data
		return view('frontend.home', compact('pageData', 'feeds', 'youtubeVideo', 'youtubeShorts'));
	}

    private function facebookPostData()
    {
        return Cache::remember('homepage.facebook-feed', now()->addMinutes(30), function () {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://graph.facebook.com/v21.0/me?fields=id%2Cname%2Cfeed%7Bfull_picture%2Ccreated_time%2Cmessage%2Cpermalink_url%7D&access_token=EAAIxmpZAdhYwBRzUBWc54LRTZCfF7xlD95hIzhh5T6ZA9X7IqybCIpXnNuzZBnXGfx705vyyOAih2BDFPoyCYPecYMPdfAXpSPklI28c1Q0ZC302lg0YOgd5iRw01ZCPZC9590D1DpXzHkWaOCfnKZBu7dWoHNWDfZCvF68OBnRJUUgMEneH2TFGCAl2uAYMqkV77zMsZD',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 4,
            ]);

            $response = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || $status < 200 || $status >= 300) {
                return [];
            }

            return json_decode($response, true) ?: [];
        });
    }
    
	//Career Page

    public function getCareerPageData()
    {
        $pageData = [];
        //projects
        $careers = Career::orderBy('id', 'DESC')->get();
        $pageData['careers'] = $careers;

        // return view with data
        return view('frontend.career', compact('pageData'));
    }

    public function getCareerDetails($slug)
    {

        $careers = Career::where('slug', $slug)->firstOrFail();

        // return view with data
        return view('frontend.career-details', compact('careers'));
    }
	
	// Project Listing Page

	public function getListingsPageData(Request $request)
	{
		$selected = $this->resolveListingSelection($request);
		$minPrice = (int) Project::where('status', '1')->min('price');
		$maxPrice = (int) Project::where('status', '1')->max('price');

		$localityCityMap = Location::sublocationParentMap();
		$locations = Location::parentCityNames()->all();
		$locality = Location::sublocationNames();
		$developers = Developer::select('id', 'developer_name')->orderBy('developer_name')->get();

		$projectsQuery = Project::query()->where('status', '1');
		$this->applyProjectListingFilters($projectsQuery, $selected);
		$projects = $projectsQuery->paginate(9)->withQueryString();
		$this->transformListedProjects($projects);

		$itemList = [
			"@context" => "https://schema.org",
			"@type" => "ItemList",
			"name" => "Latest Real Estate Projects",
			"itemListElement" => [],
		];

		foreach ($projects as $index => $project) {
			$itemList['itemListElement'][] = [
				"@type" => "ListItem",
				"position" => $index + 1 + (($projects->currentPage() - 1) * $projects->perPage()),
				"url" => url('/projects/' . $project->slug),
			];
		}

		$schema = '<script type="application/ld+json">' . json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
		$filters = [];

		return view('frontend.listing', compact(
			'projects',
			'minPrice',
			'maxPrice',
			'locations',
			'developers',
			'schema',
			'locality',
			'localityCityMap',
			'selected',
			'filters'
		));
	}
	
	public function applyFilters(Request $request)
	{
		$legacy = (array) $request->input('filters', []);
		$selected = $this->resolveListingSelection($request);

		if (empty($selected['location']) && !empty($legacy['location'])) {
			$selected['location'] = array_values((array) $legacy['location']);
		}
		if (empty($selected['locality']) && !empty($legacy['locality'])) {
			$selected['locality'] = array_values((array) $legacy['locality']);
		}
		if (empty($selected['type']) && !empty($legacy['propertyType'])) {
			$selected['type'] = array_values((array) $legacy['propertyType']);
		}
		if (empty($selected['possession']) && !empty($legacy['possession'])) {
			$selected['possession'] = array_map(function ($value) {
				return strtolower(str_replace(' ', '_', (string) $value));
			}, array_values((array) $legacy['possession']));
		}
		if (empty($selected['developer']) && !empty($legacy['developer'])) {
			$selected['developer'] = array_map('strval', array_values((array) $legacy['developer']));
		}
		if ($selected['q'] === '' && !empty($legacy['search_params'])) {
			$selected['q'] = trim((string) $legacy['search_params']);
		}
		if (!empty($legacy['sorting'])) {
			$selected['sort'] = match ($legacy['sorting']) {
				'LowToHigh' => 'price_asc',
				'HighToLow' => 'price_desc',
				default => 'newest',
			};
		}
		if (isset($legacy['budget']['min']) && is_numeric($legacy['budget']['min'])) {
			$selected['min_price'] = (int) $legacy['budget']['min'];
		}
		if (isset($legacy['budget']['max']) && is_numeric($legacy['budget']['max'])) {
			$selected['max_price'] = (int) $legacy['budget']['max'];
		}

		return redirect()->route('projects', $this->listingQueryParams($selected));
	}

	public function showFilteredProjects(Request $request, $slug)
	{
		if (preg_match('/^(\d+-bhk-)?projects-in-(.+)$/', $slug, $matches)) {
			return redirect('/' . ($matches[1] ?? '') . 'flats-in-' . $matches[2], 301);
		}

		if (str_contains($slug, 'central-noida')) {
			$newSlug = str_replace('central-noida', 'noida', $slug);
			return redirect('/' . $newSlug, 301);
		}

		$url = $slug;

		$link = CustomLink::where('slug', $url)->first();

		if (!$link && preg_match('/^\d+-bhk-/', strtolower($slug))) {
			abort(404);
		}

		$title = $link->title ?? null;
		$name = $link->name ?? null;
		$description = $link->description ?? null;
		$links_description = $link->links_description ?? null;
		$keywords = $link->keywords ?? null;
		$linkType = $link->type ?? null;

		// Handle property custom links
		if ($linkType === 'property') {
			return $this->handlePropertyCustomLink($slug, $link, $title, $name, $description, $keywords);
		}

		$filters = [];
		$isValidSlug = false;
		$isTypologyValid = false;

		// Convert slug to readable string
		$slugParts = explode('-', $slug);
		$slugString = implode(' ', $slugParts);

		// Luxury check
		$isLuxury = str_contains(strtolower($slug), 'luxury');

		if (!$link) {
			$first = strtolower($slugParts[0] ?? '');

			$allowedFirstWords = [
				'projects',
				'flats',
				'plots',
				'shops',
				'studio',
				'studioapartments',
				'new',
				'ready',
				'under',
				'possession',
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'project',
				'luxury'
			];

			$isNumberOrBhk = preg_match('/^\d+(-?bhk)?$/', $first);
			$isFirstAllowed = in_array($first, $allowedFirstWords) || $isNumberOrBhk;

			if (!$isFirstAllowed) {
				abort(404);
			}
		}

		$slugParts = explode('-', $slug);
		$slugString = implode(' ', $slugParts);

		// Get all cities from table
		

// ============================================================
// LOCATION / CITY DETECTION
// ============================================================

$filters = [];
$isValidSlug = false;
$isTypologyValid = false;

$slugParts = explode('-', strtolower($slug));
$slugString = implode(' ', $slugParts);


// ------------------------------------------------------------
// LOCALITY + CITY MAPPING
// ------------------------------------------------------------

$locationCityRows = Project::query()
    ->whereNotNull('location')
    ->whereNotNull('cities')
    ->select('location', 'cities')
    ->distinct()
    ->get();


// ------------------------------------------------------------
// 1. LOCALITY FIRST
// ------------------------------------------------------------

$matchedLocality = null;
$matchedLocalityCity = null;

$localityRows = $locationCityRows
    ->sortByDesc(function ($row) {
        return strlen(Str::slug($row->location));
    });

foreach ($localityRows as $row) {

    $locationName = trim($row->location);

    if (empty($locationName)) {
        continue;
    }

    $locationSlug = strtolower(Str::slug($locationName));

    if (
        $locationSlug !== '' &&
        Str::contains(strtolower($slug), $locationSlug)
    ) {
        $matchedLocality = $locationName;
        $matchedLocalityCity = trim($row->cities);
        break;
    }
}


// ------------------------------------------------------------
// 2. LOCALITY MIL GAYI TO USKI CITY BHI SET KARO
// ------------------------------------------------------------

if ($matchedLocality) {

    $filters['locality'] = $matchedLocality;

    if ($matchedLocalityCity) {
        $filters['city'] = $matchedLocalityCity;
    }

    $isValidSlug = true;
}


// ------------------------------------------------------------
// 3. LOCALITY NA MILE TAB CITY CHECK KARO
// ------------------------------------------------------------

if (!$matchedLocality) {

    $cityRows = Project::query()
        ->whereNotNull('cities')
        ->select('cities')
        ->distinct()
        ->get()
        ->sortByDesc(function ($row) {
            return strlen(Str::slug($row->cities));
        });

    foreach ($cityRows as $row) {

        $cityName = trim($row->cities);

        if (empty($cityName)) {
            continue;
        }

        $citySlug = strtolower(Str::slug($cityName));

if (
    $citySlug === strtolower($slug) ||
    Str::contains(strtolower($slug), $citySlug)
) {

    $filters['city'] = $cityName;
    $isValidSlug = true;

    break;
}
    }
}


if (empty($filters['city']) && empty($filters['locality'])) {
    $locationRows = Location::query()
        ->active()
        ->with('parent:id,city')
        ->get()
        ->sortByDesc(fn ($row) => strlen(Str::slug($row->city)));

    foreach ($locationRows as $row) {
        $placeSlug = strtolower(Str::slug($row->city));
        if ($placeSlug === '' || !Str::contains(strtolower($slug), $placeSlug)) {
            continue;
        }

        if ($row->parent_id) {
            $filters['locality'] = $row->city;
            if ($row->parent?->city) {
                $filters['city'] = $row->parent->city;
            }
        } else {
            $filters['city'] = $row->city;
        }
        $isValidSlug = true;
        break;
    }
}


		// Step 2: Typology
		$typologyMappings = [
			'plots' => 'Plots',
			'shops' => 'Shops',
			'studio' => 'Studio Apartments',
		];

		// Check plain keyword matches first
		foreach ($typologyMappings as $slugKey => $displayName) {

			if (in_array($slugKey, $slugParts)) {

				$filters['typologyToRender'] = $displayName;
				$filters['typology'] = $displayName;
				$isValidSlug = true;
				$isTypologyValid = true;
				break;
			}
		}

		// Check for "2-bhk"
		if (empty($filters['typology'])) {

			foreach ($slugParts as $index => $part) {

				if (
					is_numeric($part) &&
					isset($slugParts[$index + 1]) &&
					$slugParts[$index + 1] === 'bhk'
				) {

					$bhk = $part . ' BHK';

					$filters['typologyToRender'] = $bhk;
					$filters['typology'] = $bhk;

					$isValidSlug = true;
					$isTypologyValid = true;

					break;
				}
			}
		}

		// Step 2b: Get dynamic BHKs
		$cityProjectsQuery = Project::query();

if (!empty($filters['city'])) {
    $cityProjectsQuery->whereRaw(
        'LOWER(TRIM(cities)) = ?',
        [strtolower(trim($filters['city']))]
    );
}

if (!empty($filters['locality'])) {
    $cityProjectsQuery->whereRaw(
        'LOWER(TRIM(location)) = ?',
        [strtolower(trim($filters['locality']))]
    );
}

$cityProjects = $cityProjectsQuery->get();

		$availableTypologies = collect();

		foreach ($cityProjects as $project) {

			$typologies = json_decode($project->typology, true);

			if (is_array($typologies)) {
				$availableTypologies = $availableTypologies->merge($typologies);
			}
		}

		$availableTypologies = $availableTypologies->unique()->map(function ($typ) {

			$typ = strtolower(str_replace([' ', '-'], '', $typ));

			if (preg_match('/^(\d+)bhk$/', $typ, $matches)) {
				return $matches[1];
			}

			if (in_array($typ, ['plots', 'shops', 'studio', 'studioapartments'])) {
				return str_replace('studioapartments', 'studio', $typ);
			}

			return null;

		})->filter()->values()->all();

		foreach ($slugParts as $part) {

			$part = strtolower($part);

			if (in_array($part, $availableTypologies)) {

				$filters['typology'] = $part === 'plots'
					? 'Plots'
					: ($part === 'studio'
						? 'Studio Apartments'
						: ($part === 'shops'
							? 'Shops'
							: $part . ' BHK'));

				$isTypologyValid = true;

				break;
			}
		}

		// Step 3: Project Status
		$statusMappings = [
			'new launch' => 'new_launch',
			'ready to move' => 'ready_to_move',
			'under construction' => 'under_construction',
			'within a year' => 'within_a_year',
			'completed' => 'completed',
		];

		foreach ($statusMappings as $phrase => $mapped) {

			if (str_contains($slugString, $phrase)) {

				$filters['project_status'] = $mapped;
				$isValidSlug = true;

				break;
			}
		}

		// Step 4: Price range
		$minPrice = (int) Project::min('price');
		$maxPrice = (int) Project::max('price');

		$locations = Location::parentCityNames()->all();
		$locality = Location::sublocationNames();

		$developers = Developer::select('id', 'developer_name')->get();

		$matchedDeveloper = $developers
			->sortByDesc(fn ($developer) => strlen(Str::slug($developer->developer_name)))
			->first(function ($developer) use ($slug) {
				$devSlug = Str::slug($developer->developer_name);
				return $devSlug !== '' && str_starts_with($slug, $devSlug . '-projects');
			});

		if ($matchedDeveloper) {
			$filters['developer'] = $matchedDeveloper->id;
			$filters['developer_name'] = $matchedDeveloper->developer_name;
			$isValidSlug = true;
		}

		$localityCityMap = Location::sublocationParentMap();

		// Build project query
		$projectsQuery = Project::query()
    ->where('status', '1');


// CITY FILTER
if (!empty($filters['city'])) {

    $projectsQuery->whereRaw(
        'LOWER(TRIM(cities)) = ?',
        [strtolower(trim($filters['city']))]
    );
}


// LOCALITY FILTER
if (!empty($filters['locality'])) {

    $projectsQuery->whereRaw(
        'LOWER(TRIM(location)) = ?',
        [strtolower(trim($filters['locality']))]
    );
}
		if (!empty($filters['typology'])) {
			$projectsQuery->whereJsonContains('typology', $filters['typology']);
		}

		if (!empty($filters['project_status'])) {
			$projectsQuery->where('project_status', $filters['project_status']);
		}

		if (!empty($filters['developer_name'])) {
			$projectsQuery->whereRaw(
				'LOWER(TRIM(developer_name)) = ?',
				[strtolower(trim($filters['developer_name']))]
			);
		}

		if ($isLuxury) {
			$projectsQuery->where('price', '>', 30000000);
		}

		$totalResults = $projectsQuery->count();

		$projectsQuery->orderBy('id', 'DESC');

		$projects = $projectsQuery->paginate(9);

		$projects->getCollection()->transform(function ($project) {

			$typologies = json_decode($project->typology, true);

			$project->typology = is_array($typologies)
				? implode(', ', $typologies)
				: 'N/A';

			$project->project_status = clean($project->project_status);

			$project->logo_image = storageUrl($project->logo_image);

			return $project;
		});

		$nonProjectTypologies = ['plots', 'studio apartments', 'shops'];

		$titleParts = [];

		$selectedTypology = strtolower($filters['typology'] ?? '');

		if (!empty($filters['typology'])) {

			$typologyFilter = strtolower($filters['typology']);

			$projectsQuery->where(function ($query) use ($typologyFilter) {

				$query->whereRaw("LOWER(typology) LIKE ?", ['%"' . $typologyFilter . '"%'])
					->orWhereRaw("LOWER(typology) LIKE ?", ['%"' . str_replace(' apartments', '', $typologyFilter) . '"%'])
					->orWhereRaw("LOWER(typology) LIKE ?", ['%"' . str_replace(' ', '', $typologyFilter) . '"%']);
			});
		}

		$isNonProjectType = in_array($selectedTypology, $nonProjectTypologies);

		$propertyKeyword = $isNonProjectType ? '' : 'Flats';

		// STATUS FIRST
		if (!empty($filters['project_status'])) {

			$statusReadable = ucwords(str_replace('_', ' ', $filters['project_status']));

			$titleParts[] = $statusReadable;
		}

		// TYPOLOGY
		if (!empty($filters['typology'])) {
			$titleParts[] = ucwords($filters['typology']);
		}

		// LOCALITY PRIORITY
		if (!empty($filters['locality'])) {

			$titleParts[] = ($propertyKeyword ? $propertyKeyword . ' in ' : 'in ')
				. $filters['locality'];

		} elseif (!empty($filters['city'])) {

			$titleParts[] = ($propertyKeyword ? $propertyKeyword . ' in ' : 'in ')
				. $filters['city'];
		}

		$totalResultsText = $totalResults . ' Results';

		$dynamicTitle = count($titleParts)
			? implode(' ', $titleParts)
			: ($propertyKeyword ? 'All Flats' : 'All Properties');

		// Add Luxury Word
		if ($isLuxury) {
			$dynamicTitle = 'Luxury ' . $dynamicTitle;
		}

		// Step 5: Validation
		$hasAnyValidFilter =
			!empty($filters['city']) ||
			!empty($filters['locality']) ||
			!empty($filters['typology']) ||
			!empty($filters['project_status']) ||
			!empty($filters['developer_name']);

		if (!$hasAnyValidFilter) {
			abort(404);
		}

		if (!empty($filters['city'])) {
			$cityExists = Location::parents()
					->whereRaw('LOWER(TRIM(city)) = ?', [strtolower(trim($filters['city']))])
					->exists()
				|| Project::whereRaw('LOWER(TRIM(cities)) = ?', [strtolower(trim($filters['city']))])->exists();

			if (!$cityExists) {
				abort(404);
			}
		}

		if (!empty($filters['locality'])) {
			$localityExists = Location::query()
					->whereNotNull('parent_id')
					->whereRaw('LOWER(TRIM(city)) = ?', [strtolower(trim($filters['locality']))])
					->exists()
				|| Project::whereRaw('LOWER(TRIM(location)) = ?', [strtolower(trim($filters['locality']))])->exists();

			if (!$localityExists) {
				abort(404);
			}
		}

		if (!empty($filters['typology'])) {

			$typologyExists = Project::whereJsonContains('typology', $filters['typology'])->exists();

			if (!$typologyExists) {
				abort(404);
			}
		}


		$selected = $this->resolveListingSelection($request, $filters);
		return view('frontend.listing', compact(
		    'projects',
			'minPrice',
			'maxPrice',
			'filters',
			'locations',
			'title',
			'description',
			'keywords',
			'name',
			'links_description',
			'totalResults',
			'dynamicTitle',
			'totalResultsText',
			'developers',
			'locality',
			'localityCityMap',
			'selected'
		));
	}
			



private function generateRelatedCityLinks(?string $currentCity = null): array
{
$relatedCityLinks = [];

    $allCities = Location::parentCityNames();

    foreach ($allCities as $relatedCity) {

        // Current city ko skip karo
        if (
            $currentCity &&
            strtolower(trim($relatedCity)) === strtolower(trim($currentCity))
        ) {
            continue;
        }

        $cityProjects = Project::query()
            ->where('status', 1)
            ->whereNotNull('typology')
            ->whereRaw(
                'LOWER(TRIM(cities)) = ?',
                [strtolower(trim($relatedCity))]
            )
            ->get();

        if ($cityProjects->isEmpty()) {
            continue;
        }

        $bhks = collect();

        foreach ($cityProjects as $project) {

            $typologies = json_decode($project->typology, true);

            if (!is_array($typologies)) {
                continue;
            }

            foreach ($typologies as $typology) {

                $typology = trim($typology);

                if (
                    preg_match(
                        '/^(\d+)\s*-?\s*BHK$/i',
                        $typology,
                        $matches
                    )
                ) {
                    $bhks->push((int) $matches[1]);
                }
            }
        }

        $bhks = $bhks
            ->unique()
            ->sort()
            ->values();

        if ($bhks->isEmpty()) {
            continue;
        }

        $cityLinks = [];

        foreach ($bhks as $bhk) {

            $cityLinks[] = [
                'text' => $bhk . ' BHK Flats in ' . $relatedCity,

                'url' => $bhk . '-bhk-flats-in-' . Str::slug($relatedCity),
            ];
        }

        $relatedCityLinks[] = [
            'city' => $relatedCity,
            'links' => $cityLinks,
        ];
    }

    return $relatedCityLinks;
}




   public function getProjectDetails($slug)
{
    // projects
    $projects = Project::where('slug', $slug)
        ->where('status', '1')
        ->firstOrFail();

    $typologies = json_decode($projects->typology, true);
    $projects->typology_string = is_array($typologies)
        ? implode(', ', $typologies)
        : 'N/A';

    $aminitiesIds = json_decode($projects->amenities_description);
    $amenitiesDetails = [];

    if (!empty($aminitiesIds) && count($aminitiesIds) > 0) {
        foreach ($aminitiesIds as $key => $amenity) {
            $amenitiesDetails[$key] = AminityList::findOrFail($amenity);
        }
    }

    $projects->amenitiesDetails = $amenitiesDetails;

    $developerId = json_decode($projects->floor_plans_description);

    $developerDetails = Developer::findOrFail($developerId);
    $projects->developerDetails = $developerDetails;

    // Recommended Projects
    $currentPrice = $projects->price;

    $priceMin = $currentPrice * 0.8;
    $priceMax = $currentPrice * 1.2;

    $recommendedProjects = Project::where('id', '!=', $projects->id)
        ->where('status', 1)
        ->where('cities', $projects->cities)
        ->whereBetween('price', [$priceMin, $priceMax])
        ->latest()
        ->take(4)
        ->get();

    foreach ($recommendedProjects as $project) {
        $typologies = json_decode($project->typology, true);

        $project->typology = is_array($typologies)
            ? implode(', ', $typologies)
            : 'N/A';
    }

    /*
    |--------------------------------------------------------------------------
    | LOCATION CUSTOM LINKS
    |--------------------------------------------------------------------------
    */
/*
|--------------------------------------------------------------------------
| CITY + LOCALITY CUSTOM LINKS
|--------------------------------------------------------------------------
*/

$customLinks = collect();

$projectCity = trim($projects->cities ?? '');
$projectLocation = trim($projects->location ?? '');

if (!empty($projectCity)) {
    $localities = Project::query()
        ->where('status', 1)
        ->whereNotNull('location')
        ->whereNotNull('cities')
        ->whereRaw(
            'LOWER(TRIM(cities)) = ?',
            [strtolower($projectCity)]
        )
        ->select('location')
        ->distinct()
        ->pluck('location')
        ->filter()
        ->map(function ($location) {
            return trim($location);
        })
        ->unique()
        ->values();

    $customLinks = CustomLink::where('is_active', 1)
        ->where(function ($query) use ($projectCity, $localities) {
            $query->whereRaw(
                'LOWER(slug) = ?',
                ['flats-in-' . strtolower(Str::slug($projectCity))]
            );

			$query->orWhereRaw(
				'LOWER(slug) = ?',
				['shops-in-' . strtolower(Str::slug($projectCity))]
			);

			$query->orWhereRaw(
				'LOWER(slug) = ?',
				['studio-apartments-in-' . strtolower(Str::slug($projectCity))]
			);

            foreach ($localities as $location) {
                $query->orWhereRaw(
                    'LOWER(slug) = ?',
					['flats-in-' . strtolower(Str::slug($location))]
                );
            }
        })
        ->orderBy('id', 'DESC')
        ->get()
        ->unique('slug')
        ->values();
}
    /*
    |--------------------------------------------------------------------------
    | FAQ
    |--------------------------------------------------------------------------
    */

    $projects->seo_data = json_decode($projects->seo_data, true);

    $faqsData = json_decode($projects->faqs_data, true);

    $faqSchema = null;

    if (!empty($faqsData) && is_array($faqsData)) {

        $mainEntities = [];

      

        if (!empty($mainEntities)) {

            $faqSchemaArray = [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => $mainEntities,
            ];

            $faqSchema = json_encode(
                $faqSchemaArray,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_PRETTY_PRINT
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'frontend.project-details',
        compact(
            'projects',
            'recommendedProjects',
            'faqSchema',
            'customLinks'
        )
    );
}
	
	// property details page for single page
	
    public function getPropertyDetails($slug)
    {
		
        $property = Property::where('slug', $slug)->where(function ($query) {
                $query->where('status', 'approved')
                      ->orWhere('user_id', Auth::id());
            })->firstOrFail();
       
        $aminitiesIds = $property->amenities;
        $amenitiesDetails = []; 

        if (!empty($aminitiesIds) && count($aminitiesIds) > 0) {
            foreach ($aminitiesIds as $key => $amenity) {
                $amenitiesDetails[$key] = AminityList::findOrFail($amenity);
            }
        }
		 
        // Assign amenities details to a property if you need it later
        $property->amenitiesDetails = $amenitiesDetails;
        $propertyImages = $property->galleries ?? [];
        $projectImages = array_filter([
            $property->project?->logo_image,
            $property->project?->feature_image,
            $property->project?->amenities_images,
            $property->project?->logo_image,
            $property->project?->developer_background_image,
        ]);

        $allImages = array_merge($propertyImages, $projectImages);
        $finalImages = array_slice($allImages, 0, 5);

        $developerId = json_decode($property->project->floor_plans_description);
        $developerDetails = Developer::findOrFail($developerId);
        
        $property->developerDetails = $developerDetails;

        $recommendedProjects = Project::where('status', 1)
			->orderBy('created_at', 'desc')
			->take(4)
			->get();
       
        foreach ($recommendedProjects as $project) {
            $typologies = json_decode($project->typology, true);
            $project->typology = is_array($typologies) ? implode(', ', $typologies) : 'N/A';
        }
        $property->seo_data = json_decode($property->seo_data, true);
		
        // return view with data
		$user = $property->user; 
        return view('frontend.property-details', compact('property', 'recommendedProjects','finalImages','user'));
		
    }


    // Get Blog listing pageData

	
	public function getBlogsPageData(Request $request)
	{
		$pageData = [];
		$search = $request->input('search');

		$query = Blog::where('status', 1);

		if (!empty($search)) {
			$query->where(function ($q) use ($search) {
				$q->where('title', 'LIKE', '%' . $search . '%')
				  ->orWhere('short_description', 'LIKE', '%' . $search . '%')
				  ->orWhere('description', 'LIKE', '%' . $search . '%');
			});
		}

		// paginate
		$blogs = $query->orderBy('id', 'DESC')->paginate(6);
		$blogs->appends(['search' => $search]);

		$pageData['blogs'] = $blogs;

		return view('frontend.blogs', compact('pageData', 'search'));
	}

    //Get blog Details single pageData
	
    public function getBlogDetails($slug)
    {
        //blogs
        $blogs = Blog::where('slug', $slug)->where('status',1)->firstOrFail();
        
        $recommendedBlogs = Blog::where('slug', '!=', $slug)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $recommendedProjects = Project::where('status', 1)
			->orderBy('created_at', 'desc')
			->take(5)
			->get();
		   
		foreach ($recommendedProjects as $project) {
			$typologies = json_decode($project->typology, true);
			$project->typology = is_array($typologies) ? implode(', ', $typologies) : 'N/A';
		}
        $blogs->seo_data = json_decode($blogs->seo_data, true);
       json_decode($blogs->faqs_data, true);
	   
		$faqsData = json_decode($blogs->faqs_data, true);
		$faqSchema = null;
		if (!empty($faqsData) && is_array($faqsData)) {
			$mainEntities = [];
			foreach ($faqsData as $faq) {
				$q = isset($faq['question']) ? trim(strip_tags($faq['question'])) : '';
				$a = isset($faq['answer']) ? trim($faq['answer']) : '';
				if (!empty($q) && !empty($a)) {
					$mainEntities[] = [
						"@type" => "Question",
						"name" => $q,
						"acceptedAnswer" => [
							"@type" => "Answer",
							"text" => $a
						]
					];
				}
			}

			if (!empty($mainEntities)) {
				$faqSchemaArray = [
					"@context" => "https://schema.org",
					"@type" => "FAQPage",
					"mainEntity" => $mainEntities,
				];
				$faqSchema = json_encode($faqSchemaArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			}
		}
		
        return view('frontend.blog-details', compact('blogs', 'recommendedBlogs', 'recommendedProjects', 'faqSchema'));
    }

    // Get About us page 
	
    public function getAboutUsPageData()
    {
        $pageData = [];
        //projects
        $blogs = Blog::orderBy('id', 'DESC')->get();
        $pageData['blogs'] = $blogs;

        // return view with data
        return view('frontend.about-us', compact('pageData'));
    }


    //Function For Searchabar

public function SearchProjects(Request $req)
{
    try {
        $keyword = trim((string) $req->input('keyword', ''));
        $location = trim((string) $req->input('location', ''));
        $bhkType = trim((string) $req->input('bhkType', ''));
        $like = '%' . addcslashes($keyword, '%_\\') . '%';
        $hasLocationColumn = Schema::hasColumn('projects', 'location');
        $results = [];
        $seen = [];

        $push = static function (array $item) use (&$results, &$seen) {
            $name = trim((string) ($item['name'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            if ($name === '' || $url === '') {
                return;
            }
            $key = strtolower($item['type'] . '|' . $name . '|' . $url);
            if (isset($seen[$key])) {
                return;
            }
            $seen[$key] = true;
            $results[] = $item;
        };

        if ($keyword !== '') {
            $cities = Location::parents()
                ->active()
                ->where('city', 'LIKE', $like)
                ->orderBy('city')
                ->limit(6)
                ->pluck('city');

            foreach ($cities as $cityName) {
                $push([
                    'name' => $cityName,
					'slug' => 'flats-in-' . Str::slug($cityName),
					'url' => url('/flats-in-' . Str::slug($cityName)),
                    'type' => 'custom',
                    'label' => 'City',
                ]);
            }

            $localities = Location::query()
                ->whereNotNull('parent_id')
                ->active()
                ->where('city', 'LIKE', $like)
                ->with('parent:id,city')
                ->orderBy('city')
                ->limit(8)
                ->get();

foreach ($localities as $locality) {

    $localityCity = $locality->parent?->city;
    $localitySlug = Str::slug($locality->city);

    $push([
        'name' => $locality->city,
        'slug' => 'flats-in-' . $localitySlug,
        'url' => url('/flats-in-' . $localitySlug),
        'type' => 'locality',
        'label' => 'Locality',
        'subtitle' => $localityCity,
    ]);
}

            if ($hasLocationColumn) {
                $projectLocalities = Project::query()
                    ->where('status', true)
                    ->whereNotNull('location')
                    ->where('location', 'LIKE', $like)
                    ->when($location !== '', function ($q) use ($location) {
                        $q->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower($location)]);
                    })
                    ->select('location', 'cities')
                    ->limit(12)
                    ->get()
                    ->unique(function ($row) {
                        return strtolower(trim((string) $row->location));
                    })
                    ->take(8);

               foreach ($projectLocalities as $row) {

    $localityName = trim((string) $row->location);
    $localitySlug = Str::slug($localityName);

    $push([
        'name' => $localityName,
        'slug' => 'flats-in-' . $localitySlug,
        'url' => url('/flats-in-' . $localitySlug),
        'type' => 'locality',
        'label' => 'Locality',
        'subtitle' => $row->cities,
    ]);
}
            }

            $customLinks = CustomLink::query()
                ->where('is_active', 1)
                ->where(function ($q) use ($like) {
                    $q->where('name', 'LIKE', $like)
                        ->orWhere('title', 'LIKE', $like)
                        ->orWhere('slug', 'LIKE', $like);
                })
                ->limit(8)
                ->get();

            foreach ($customLinks as $link) {
                $slug = ltrim((string) $link->slug, '/');
                if ($slug === '') {
                    continue;
                }
                $push([
                    'name' => $link->name ?: $link->title,
                    'slug' => $slug,
                    'url' => url('/' . $slug),
                    'type' => 'custom',
                    'label' => 'Area',
                ]);
            }
        }

        if ($keyword !== '' || $location !== '' || $bhkType !== '') {
            $query = Project::query()->where('status', true);

            if ($keyword !== '') {
                $query->where(function ($q) use ($like, $hasLocationColumn) {
                    $q->where('project_name', 'LIKE', $like)
                        ->orWhere('cities', 'LIKE', $like)
                        ->orWhere('developer_name', 'LIKE', $like);
                    if ($hasLocationColumn) {
                        $q->orWhere('location', 'LIKE', $like);
                    }
                });
            }

            if ($location !== '') {
                $query->where(function ($q) use ($location) {
                    $q->whereRaw('LOWER(TRIM(cities)) = ?', [strtolower($location)]);
                    if (Schema::hasColumn('projects', 'location_id')) {
                        $parentId = Location::parents()
                            ->active()
                            ->whereRaw('LOWER(TRIM(city)) = ?', [strtolower($location)])
                            ->value('id');
                        if ($parentId) {
                            $q->orWhere('location_id', $parentId);
                        }
                    }
                });
            }

            if ($bhkType !== '' && $bhkType !== 'Shops') {
                $bhkLike = '%' . addcslashes($bhkType, '%_\\') . '%';
                $query->where('typology', 'LIKE', $bhkLike);
            } elseif ($bhkType === 'Shops') {
                $query->where('typology', 'LIKE', '%Shop%');
            }

            $projects = $query->orderByDesc('id')->limit(12)->get();

            foreach ($projects as $project) {
                $slug = trim((string) $project->slug);
                if ($slug === '') {
                    continue;
                }
                $push([
                    'name' => $project->project_name,
                    'slug' => $slug,
                    'id' => $project->id,
                    'type' => 'project',
                    'label' => 'Project',
					'subtitle' => $project->cities,
                    'url' => url('/projects/' . $slug),
                ]);
            }
        }

        if ($keyword !== '') {
            $properties = Property::query()
                ->where('status', 'approved')
                ->where(function ($q) use ($like) {
                    $q->where('title', 'LIKE', $like)
                        ->orWhere('city', 'LIKE', $like)
                        ->orWhere('property_type', 'LIKE', $like)
                        ->orWhere('configuration', 'LIKE', $like);
                })
                ->when($location !== '', function ($q) use ($location) {
                    $q->whereRaw('LOWER(TRIM(city)) = ?', [strtolower($location)]);
                })
                ->orderByDesc('id')
                ->limit(8)
                ->get(['id', 'title', 'slug', 'city']);

            foreach ($properties as $property) {
                $slug = trim((string) $property->slug);
                if ($slug === '') {
                    continue;
                }
                $push([
                    'name' => $property->title,
                    'slug' => $slug,
                    'type' => 'property',
                    'label' => 'Property',
                    'subtitle' => $property->city,
                    'url' => url('/properties/' . $slug),
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'data' => array_values($results),
        ]);
    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'status' => false,
            'data' => [],
            'message' => 'Search is unavailable right now.',
        ], 200);
    }
}



    // to get property listing page
	
    public function getPropertyListings(Request $request)
	{
		// Show only approved properties
		$properties = Property::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        // Format properties with galleries
        foreach ($properties as $property) {
            if ($property->galleries) {
                $galleries = is_string($property->galleries) ? json_decode($property->galleries, true) : $property->galleries;
                $property->galleries = is_array($galleries) ? $galleries : [];
            } else {
                $property->galleries = [];
            }
        }

		// Price range for approved properties
		$minPrice = Property::where('status', 'approved')->min('total_price') ?? 100000;
		$maxPrice = Property::where('status', 'approved')->max('total_price') ?? 0;

		// Locations of approved properties only
		$locations = Property::where('status', 'approved')
			->pluck('city')
			->filter()
			->map(function ($item) {
				return ucwords(strtolower(trim($item)));
			})
			->unique()
			->values()
			->all();
			
		$configurations = Property::pluck('configuration')
			->filter()
			->unique()
			->sortBy(function ($item) {
				return (int) filter_var($item, FILTER_SANITIZE_NUMBER_INT);
			})
			->values()
			->all();
			
		$constructionStatuses = Property::where('status', 'approved')
			->pluck('construction_status')
			->filter()
			->unique()
			->values()
			->all();

		$furnishingTypes = Property::where('status', 'approved')
			->pluck('furnishing_types')
			->filter()
			->unique()
			->values()
			->all();	
	
        $links_description = null;
		$totalResults = Property::where('status', 'approved')->count();
		$dynamicTitle = 'All Properties';
		
		return view('frontend.property-listing', compact(
			'locations',
			'minPrice',
			'maxPrice',
			'configurations',
			'properties',
			'links_description',
			'constructionStatuses',
            'furnishingTypes',
			'totalResults',      
            'dynamicTitle',
		));
	}
	
	
	// For property filters 
	
	
	public function filterProperties(Request $request)
	{
		try {
			$filters = $request->input('filters', []);
			if (!is_array($filters)) {
				$filters = [];
			}

			$list = static function (string $key) use ($filters): array {
				if (!isset($filters[$key]) || $filters[$key] === '' || $filters[$key] === null) {
					return [];
				}

				return array_values(array_filter(array_map('strval', (array) $filters[$key]), static function ($item) {
					return $item !== '';
				}));
			};

			$query = Property::with(['project', 'user'])
				->where('status', 'approved');

			if (!empty($filters['search'])) {
				$search = trim((string) $filters['search']);
				if ($search !== '') {
					$like = '%' . addcslashes($search, '%_\\') . '%';

					$query->where(function ($q) use ($like) {
						$q->where('title', 'LIKE', $like)
						  ->orWhere('city', 'LIKE', $like)
						  ->orWhere('property_type', 'LIKE', $like)
						  ->orWhere('configuration', 'LIKE', $like)
						  ->orWhereHas('project', function ($projectQuery) use ($like) {
							  $projectQuery->where('project_name', 'LIKE', $like);
						  });
					});
				}
			}

			$listingTypes = $list('listingType');
			if ($listingTypes !== []) {
				$query->whereIn('listing_type', $listingTypes);
			}

			$propertyTypes = $list('propertyType');
			if ($propertyTypes !== []) {
				$query->whereIn('property_type', $propertyTypes);
			}

			$configurations = $list('configuration');
			if ($configurations !== []) {
				$query->whereIn('configuration', $configurations);
			}

			$cities = array_values(array_filter(array_map(static function ($city) {
				return strtolower(trim($city));
			}, $list('location'))));

			if ($cities !== []) {
				$query->where(function ($q) use ($cities) {
					foreach ($cities as $city) {
						$q->orWhereRaw('LOWER(TRIM(city)) = ?', [$city]);
					}
				});
			}

			$budget = is_array($filters['budget'] ?? null) ? $filters['budget'] : [];
			$minBudget = $budget['min'] ?? null;
			$maxBudget = $budget['max'] ?? null;
			if (is_numeric($minBudget) && is_numeric($maxBudget)) {
				$query->whereBetween('total_price', [(int) $minBudget, (int) $maxBudget]);
			}

			$constructionStatuses = $list('constructionStatus');
			if ($constructionStatuses !== []) {
				$query->whereIn('construction_status', $constructionStatuses);
			}

			$furnishingTypes = $list('furnishingType');
			if ($furnishingTypes !== []) {
				$query->whereIn('furnishing_types', $furnishingTypes);
			}

			// Sorting
			if (!empty($filters['sorting'])) {
				if ($filters['sorting'] == 'LowToHigh') {
					$query->orderBy('total_price', 'asc');
				} elseif ($filters['sorting'] == 'HighToLow') {
					$query->orderBy('total_price', 'desc');
				} elseif ($filters['sorting'] == 'NewestFirst') {
					$query->orderBy('created_at', 'desc');
				}
			} else {
				$query->latest();
			}

			// Pagination
			$properties = $query->paginate(6);
			// â”€â”€ Dynamic title 
			$totalResults = $properties->total();
			$titleParts   = [];

			if ($configurations !== []) {
				$formatted = array_map(function ($c) {
					return strtoupper(str_replace('_', ' ', $c));
				}, $configurations);
				$titleParts[] = implode(', ', $formatted);
			}

			if ($propertyTypes !== []) {
				$titleParts[] = implode(', ', $propertyTypes);
			}

			$locationLabels = $list('location');
			if ($locationLabels !== []) {
				$titleParts[] = 'in ' . implode(', ', $locationLabels);
			}

			if (!empty($filters['search'])) {
				$searchLabel = trim((string) $filters['search']);
				if ($searchLabel !== '') {
					$titleParts[] = '"' . $searchLabel . '"';
				}
			}

			$dynamicTitle = count($titleParts)
				? implode(' ', $titleParts)
				: 'All Properties';

			$totalResultsText = $totalResults . ' Result' . ($totalResults !== 1 ? 's' : '');
			// â”€â”€ end Dynamic title 

			// Filter UI Data
			$minPrice = Property::min('total_price') ?? 100000;
			$maxPrice = Property::max('total_price') ?? 0;
			$locations = Property::pluck('city')
				->filter()
				->map(function ($item) {
					return ucwords(strtolower(trim($item)));
				})
				->unique()
				->values()
				->all();

			return response()->json([
				'status' => true,
				'data' => $properties,
				'dynamicTitle'     => $dynamicTitle,        // â† add
				'totalResults'     => $totalResults,         // â† add
				'totalResultsText' => $totalResultsText,
				'filtersData' => [
					'locations' => $locations,
					'minPrice' => $minPrice,
					'maxPrice' => $maxPrice,
				],
				'pagination' => [
					'last_page' => $properties->lastPage(),
					'current_page' => $properties->currentPage(),
				]
			]);

		} catch (\Throwable $e) {
			report($e);

			return response()->json([
				'status' => false,
				'data' => [
					'data' => [],
					'current_page' => 1,
					'last_page' => 1,
					'total' => 0,
				],
				'dynamicTitle' => 'All Properties',
				'totalResults' => 0,
				'totalResultsText' => '0 Results',
				'filtersData' => [
					'locations' => [],
					'minPrice' => 0,
					'maxPrice' => 0,
				],
				'pagination' => [
					'last_page' => 1,
					'current_page' => 1,
				],
			], 200);
		}
	}
	
	 /**
     * Handle property custom links
     */	
	private function handlePropertyCustomLink($slug, $link, $title, $name, $description, $keywords)
	{
		$slugParts = explode('-', $slug);

		$bhk = null;
		$city = null;

	   //bhk wise filter
		for ($i = 0; $i < count($slugParts); $i++) {
			if (is_numeric($slugParts[$i]) && isset($slugParts[$i + 1]) && $slugParts[$i + 1] === 'bhk') {
				$bhk = $slugParts[$i] . ' BHK';
				break;
			}
		}

	   //city
		if (str_contains($slug, 'apartment-in-') || str_contains($slug, 'apartments-in-')) {
			$cityPart = str_contains($slug, 'apartment-in-')
				? explode('apartment-in-', $slug)[1] ?? null
				: explode('apartments-in-', $slug)[1] ?? null;

			if ($cityPart) {
				$city = trim(str_replace('-', ' ', $cityPart));
			}
		}

		//query
		$propertiesQuery = Property::query();

		// Always apartment type
		$propertiesQuery->where('property_type', 'apartment');

		if ($bhk) {
			$configValue = strtolower(str_replace(' ', '_', $bhk));
			$propertiesQuery->where('configuration', $configValue);
		}

		//FIX: Case-insensitive + partial match
		if ($city) {
			// Get exact matching city from DB (case insensitive)
			$matchedCity = Property::whereRaw("LOWER(city) LIKE ?", ['%' . strtolower($city) . '%'])
				->value('city');

			if ($matchedCity) {
				$initialFilters['location'] = [$matchedCity]; // exact DB value
			}
		}

		$properties = $propertiesQuery->paginate(12);

	   //filter with price
		$minPrice = Property::min('total_price') ?? 100000;
		$maxPrice = Property::max('total_price') ?? 10000000;

		//$locations = Property::pluck('city')->filter()->unique()->values()->all();
		$locations = Property::pluck('city')
			->filter()
			->map(function ($item) {
				return ucwords(strtolower(trim($item)));
			})
			->unique()
			->values()
			->all();
		$configurations = Property::pluck('configuration')->filter()->unique()->values()->all();
		$constructionStatuses = Property::where('status', 'approved')
			->pluck('construction_status')
			->filter()
			->unique()
			->values()
			->all();

		$furnishingTypes = Property::where('status', 'approved')
			->pluck('furnishing_types')
			->filter()
			->unique()
			->values()
			->all();

	   //filter
		$initialFilters = [];

		if (isset($configValue)) {
			$initialFilters['configuration'] = [$configValue];
		}

		if ($city) {
			$initialFilters['location'] = [$city];
		}

		$initialFilters['propertyType'] = ['apartment'];

		$links_description = $link->links_description ?? null;

		return view('frontend.property-listing', compact(
			'properties',
			'minPrice',
			'maxPrice',
			'locations',
			'configurations',
			'constructionStatuses',
            'furnishingTypes',
			'title',
			'name',
			'description',
			'keywords',
			'initialFilters',
			'links_description'
		));
	}
	
	public function test(){
		$links = CustomLink::get();
		
		foreach($links as $item){
			$slug = $item->slug;
			$originalUrl = $slug;
		

			// Add www.
			$updatedUrl = str_replace('/projects/filter/', '', $originalUrl);
			$obj = CustomLink::findOrFail($item->id);
			$obj->canonical = $updatedUrl;
			$obj->slug = $updatedUrl;
			//$obj->save();
		}
	}

	private function resolveListingSelection(Request $request, array $filters = []): array
	{
		$cleanList = static function ($value): array {
			return array_values(array_filter(array_map('strval', (array) $value), static function ($item) {
				return $item !== '';
			}));
		};

		$locations = $cleanList($request->input('location', []));
		$localities = $cleanList($request->input('locality', []));
		$types = $cleanList($request->input('type', []));
		$possession = $cleanList($request->input('possession', []));
		$developers = $cleanList($request->input('developer', []));
		$q = trim((string) $request->input('q', $request->input('keyword', '')));
		$sort = (string) $request->input('sort', 'newest');
		if (!in_array($sort, ['newest', 'price_asc', 'price_desc'], true)) {
			$sort = 'newest';
		}

		$minPrice = $request->input('min_price');
		$maxPrice = $request->input('max_price');

		if (empty($locations)) {
			if (!empty($filters['city'])) {
				$locations = [(string) $filters['city']];
			} elseif (!empty($filters['location'])) {
				$locations = $cleanList($filters['location']);
			}
		}

		if (empty($localities) && !empty($filters['locality'])) {
			$localities = $cleanList($filters['locality']);
		}

		if (empty($types)) {
			$type = $filters['typologyToRender'] ?? $filters['typology'] ?? null;
			if (!empty($type)) {
				$types = [(string) $type];
			}
		}

		if (empty($possession) && !empty($filters['project_status'])) {
			$possession = [(string) $filters['project_status']];
		}

		if (empty($developers) && !empty($filters['developer'])) {
			$developers = $cleanList($filters['developer']);
		}

		return [
			'location' => $locations,
			'locality' => $localities,
			'type' => $types,
			'possession' => $possession,
			'developer' => $developers,
			'q' => $q,
			'sort' => $sort,
			'min_price' => is_numeric($minPrice) ? (int) $minPrice : null,
			'max_price' => is_numeric($maxPrice) ? (int) $maxPrice : null,
		];
	}

	private function listingQueryParams(array $selected): array
	{
		$params = [];
		foreach (['location', 'locality', 'type', 'possession', 'developer'] as $key) {
			if (!empty($selected[$key])) {
				$params[$key] = array_values($selected[$key]);
			}
		}
		if (!empty($selected['q'])) {
			$params['q'] = $selected['q'];
		}
		if (!empty($selected['sort']) && $selected['sort'] !== 'newest') {
			$params['sort'] = $selected['sort'];
		}
		if ($selected['min_price'] !== null) {
			$params['min_price'] = $selected['min_price'];
		}
		if ($selected['max_price'] !== null) {
			$params['max_price'] = $selected['max_price'];
		}

		return $params;
	}

	private function applyProjectListingFilters($query, array $selected): void
	{
		$hasLocationId = Schema::hasColumn('projects', 'location_id');
		$hasSublocationId = Schema::hasColumn('projects', 'sublocation_id');

		if (!empty($selected['location'])) {
			$parentIds = collect();
			if ($hasLocationId) {
				$parentIds = Location::parents()
					->active()
					->where(function ($q) use ($selected) {
						foreach ($selected['location'] as $city) {
							$q->orWhereRaw('LOWER(TRIM(city)) = ?', [strtolower(trim($city))]);
						}
					})
					->pluck('id');
			}

			$query->where(function ($q) use ($selected, $parentIds, $hasLocationId) {
				foreach ($selected['location'] as $city) {
					$q->orWhereRaw('LOWER(TRIM(cities)) = ?', [strtolower(trim($city))]);
				}
				if ($hasLocationId && $parentIds->isNotEmpty()) {
					$q->orWhereIn('location_id', $parentIds);
				}
			});
		}

		if (!empty($selected['locality'])) {
			$childIds = collect();
			if ($hasSublocationId) {
				$childIds = Location::query()
					->whereNotNull('parent_id')
					->active()
					->where(function ($q) use ($selected) {
						foreach ($selected['locality'] as $locality) {
							$q->orWhereRaw('LOWER(TRIM(city)) = ?', [strtolower(trim($locality))]);
						}
					})
					->pluck('id');
			}

			$query->where(function ($q) use ($selected, $childIds, $hasSublocationId) {
				foreach ($selected['locality'] as $locality) {
					$q->orWhereRaw('LOWER(TRIM(location)) = ?', [strtolower(trim($locality))]);
				}
				if ($hasSublocationId && $childIds->isNotEmpty()) {
					$q->orWhereIn('sublocation_id', $childIds);
				}
			});
		}

		if (!empty($selected['type'])) {
			$query->where(function ($q) use ($selected) {
				foreach ($selected['type'] as $type) {
					$needle = trim((string) $type);
					if ($needle === '') {
						continue;
					}
					$like = '%' . addcslashes($needle === 'Shops' ? 'Shop' : $needle, '%_\\') . '%';
					$q->orWhere('typology', 'like', $like);
				}
			});
		}

		if (!empty($selected['possession'])) {
			$possession = array_map(function ($value) {
				return strtolower(str_replace(' ', '_', (string) $value));
			}, $selected['possession']);
			$query->whereIn('project_status', $possession);
		}

		if (!empty($selected['developer'])) {
			$developerIds = array_map('intval', $selected['developer']);
			$developerNames = Developer::whereIn('id', $developerIds)->pluck('developer_name');

			$query->where(function ($q) use ($developerIds, $developerNames) {
				foreach ($developerNames as $name) {
					$q->orWhereRaw('LOWER(TRIM(developer_name)) = ?', [strtolower(trim($name))]);
				}
				foreach ($developerIds as $id) {
					$q->orWhere('floor_plans_description', $id)
						->orWhere('floor_plans_description', (string) $id)
						->orWhere('floor_plans_description', json_encode($id));
				}
			});
		}

		if ($selected['q'] !== '') {
			$like = '%' . addcslashes($selected['q'], '%_\\') . '%';
			$query->where(function ($q) use ($like) {
				$q->where('project_name', 'like', $like)
					->orWhere('location', 'like', $like)
					->orWhere('cities', 'like', $like)
					->orWhere('developer_name', 'like', $like);
			});
		}

		if ($selected['min_price'] !== null) {
			$query->where('price', '>=', $selected['min_price']);
		}
		if ($selected['max_price'] !== null) {
			$query->where('price', '<=', $selected['max_price']);
		}

		match ($selected['sort']) {
			'price_asc' => $query->orderBy('price', 'asc')->orderBy('id', 'desc'),
			'price_desc' => $query->orderBy('price', 'desc')->orderBy('id', 'desc'),
			default => $query->orderBy('id', 'desc'),
		};
	}

	private function transformListedProjects($projects): void
	{
		$projects->getCollection()->transform(function ($project) {
			$project->logo_image = storageUrl($project->logo_image);

			return $project;
		});
	}
	
}
