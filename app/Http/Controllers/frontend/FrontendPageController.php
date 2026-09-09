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
		$cities = Project::select('cities')->distinct()->whereNotNull('cities')->pluck('cities');
		$pageData['cities'] = $cities;
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
		$projectsQuery = Project::query()->where('status', '1')->orderBy('id', 'DESC');

		$minPrice = (int) $projectsQuery->min('price');
		$maxPrice = (int) $projectsQuery->max('price');
		
		$localityCityMap = Project::select('location', 'cities')
		->groupBy('location', 'cities')
		->get()
		->pluck('cities', 'location')
		->toArray();
		$locations = Project::pluck('cities')->filter()->unique()->values()->all();
		$locality = Project::pluck('location')->filter()->unique()->values()->all();
		
        $developers = Developer::select('id', 'developer_name')->get();

		$projects = $projectsQuery->paginate(9);
        $projects->getCollection()->transform(function ($project) {

			$typologies = json_decode($project->typology, true);

			$project->typology_text = is_array($typologies)
				? implode(', ', $typologies)
				: 'N/A';

			$project->logo_image = $project->logo_image
				? url('storage/' . $project->logo_image)
				: url('uploads/project/default.png');

			return $project;
		});
		
		$latestProjects = Project::where('status', '1')->orderBy('id', 'DESC')->get();

		// Generate ItemList schema
		$itemList = [
			"@context" => "https://schema.org",
			"@type" => "ItemList",
			"name" => "Latest Real Estate Projects",
			"itemListElement" => [],
		];

		$productSchemas = [];
		if(!empty($latestProjects) && count($latestProjects)>0){
			foreach ($latestProjects as $index => $project) {
				$url = url('/projects/' . $project->slug);
				$image = $project->logo_image ? url('/storage/' . $project->logo_image) : asset('default-image.jpg'); // fallback
				$availability = $project->status == 1 ? "https://schema.org/PreOrder" : "https://schema.org/InStock";

				$itemList['itemListElement'][] = [
					"@type" => "ListItem",
					"position" => $index + 1,
					"url" => $url
				];

				
			}
		}

		$schema = '<script type="application/ld+json">' . json_encode($itemList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
		if(!empty($productSchemas) && count($productSchemas)>0){
			foreach ($productSchemas as $product) {
				$schema .= "\n<script type=\"application/ld+json\">" . json_encode($product, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>";
			}
		}
		
		$relatedCityLinks = $this->generateRelatedCityLinks();
		return view('frontend.listing', compact(
			'projects',
			'minPrice',
			'maxPrice',
			'locations',
			'developers',
			'schema',
			'locality',
			'localityCityMap',
			'relatedCityLinks'
		));
	}
	
	// filter for projects 

	
	public function applyFilters(Request $request)
	{

		$projects = Project::query();

		$projects->where('status', '1');

		if (isset($_GET['keyword'])) {

			$projects = $projects->where(
				'project_name',
				'LIKE',
				'%' . $_GET['keyword'] . '%'
			);
		}

		if ($request->filled('location')) {
			$projects->whereIn('cities', array_values($request->location));
		}

		if ($request->filled('locality')) {
			$projects->whereIn('location', array_values($request->locality));
		}

		// Sorting
		if (isset($request->filters['sorting'])) {

			$sorting = $request->filters['sorting'];

			switch ($sorting) {

				case 'LowToHigh':
					$projects = $projects->orderBy('price', 'ASC');
					break;

				case 'HighToLow':
					$projects = $projects->orderBy('price', 'DESC');
					break;

				case 'NewestFirst':
					$projects = $projects->orderBy('id', 'DESC');
					break;

				default:
					$projects = $projects->orderBy('id', 'DESC');
					break;
			}

		} else {

			$projects = $projects->orderBy('id', 'DESC');
		}

		// Budget Filter
		if (isset($request->filters['budget'])) {

			$projects = $projects->whereBetween('price', [
				$request->filters['budget']['min'],
				$request->filters['budget']['max']
			]);
		}

		// Search Params
		if (isset($request->filters['search_params'])) {

			$projects = $projects->where(
				'project_name',
				'LIKE',
				'%' . $request->filters['search_params'] . '%'
			);
		}

		// Possession Filter
		if (
			isset($request->filters['possession']) &&
			count($request->filters['possession']) > 0
		) {

			$possessionFilters = array_map(function ($value) {
				return strtolower(str_replace(' ', '_', $value));
			}, $request->filters['possession']);

			$projects = $projects->whereIn(
				'project_status',
				$possessionFilters
			);
		}

		// City Filter
		if (
			isset($request->filters['location']) &&
			count($request->filters['location']) > 0
		) {

			$projects = $projects->whereIn(
				'cities',
				$request->filters['location']
			);
		}

		// Locality Filter
		if (
			isset($request->filters['locality']) &&
			count($request->filters['locality']) > 0
		) {

			$projects = $projects->whereIn(
				'location',
				$request->filters['locality']
			);
		}

		// Developer Filter
		if (
			isset($request->filters['developer']) &&
			count($request->filters['developer']) > 0
		) {

			$projects = $projects->where(function ($query) use ($request) {

				foreach ($request->filters['developer'] as $developerId) {

					$query->orWhereJsonContains(
						'floor_plans_description',
						$developerId
					);
				}
			});
		}

		// Property Type Filter
		if (!empty($request->filters['propertyType'])) {

			$propertyTypes = $request->filters['propertyType'];

			$projects = $projects->where(function ($query) use ($propertyTypes) {

				foreach ($propertyTypes as $propertyType) {

					$query->orWhereRaw(
						"typology LIKE ?",
						['%' . $propertyType . '%']
					);
				}
			});
		}

		// Pagination
		$projects = $projects->paginate(9);

		// Modify Results
		foreach ($projects as $project) {

			$typologies = json_decode($project->typology, true);

			$project->typology = is_array($typologies)
				? implode(', ', $typologies)
				: 'N/A';

			$project->project_status = clean($project->project_status);

			if ($project->logo_image) {

				$project->logo_image = url('storage/' . $project->logo_image);

			} else {

				$project->logo_image = url('uploads/project/default.png');
			}
		}

		$totalResults = $projects->total();

		$nonProjectTypologies = ['plots', 'studio apartments', 'shops'];

		$titleParts = [];

		// Normalize selected property types
		$selectedTypes = array_map(
			'strtolower',
			$request->filters['propertyType'] ?? []
		);

		// Check if ALL selected types are non-flat types
		$isNonProjectType =
			!empty($selectedTypes) &&
			count(array_diff($selectedTypes, $nonProjectTypologies)) === 0;

		// Decide keyword
		$propertyKeyword = $isNonProjectType ? '' : 'Flats';

		// =========================
		// LUXURY CHECK FROM URL
		// =========================

		$currentUrl = url()->previous();

		$isLuxury = str_contains(
			strtolower($currentUrl),
			'luxury'
		);

		// STATUS FIRST
		if (!empty($request->filters['possession'])) {

			$titleParts[] = implode(
				', ',
				$request->filters['possession']
			);
		}

		// PROPERTY TYPE
		if (!empty($request->filters['propertyType'])) {

			$titleParts[] = implode(
				', ',
				$request->filters['propertyType']
			);
		}

		// LOCALITY
		if (!empty($request->filters['locality'])) {

			$titleParts[] =
				($propertyKeyword ? $propertyKeyword . ' in ' : 'in ') .
				implode(', ', $request->filters['locality']);

		} elseif (!empty($request->filters['location'])) {

			$titleParts[] =
				($propertyKeyword ? $propertyKeyword . ' in ' : 'in ') .
				implode(', ', $request->filters['location']);
		}

		$totalResultsText = $totalResults . ' Results';


/*
|--------------------------------------------------------------------------
| RELATED CITY LINKS
| Dynamic cities from Project table
| Existing BHK links ko touch nahi karta
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| RELATED CITY LINKS
| Dynamic cities from Project table
| Existing BHK links ko touch nahi karta
|--------------------------------------------------------------------------
*/

		$projectHtml = view('frontend.partials._project-list', [
			'projects' => $projects,
		])->render();

		return response()->json([
			'status' => true,
			'html' => $projectHtml,
			'dynamicTitle' => $dynamicTitle ?? null,
			'totalResultsText' => $totalResultsText,
			'pagination' => [
				'current_page' => $projects->currentPage(),
				'last_page' => $projects->lastPage(),
				'total' => $projects->total(),
			],
		]);

}
	
	public function showFilteredProjects(Request $request, $slug)
	{
		if (str_contains($slug, 'projects-in')) {
			$newSlug = str_replace('projects-in', 'flats-in', $slug);
			return redirect('/' . $newSlug, 301);
		}

		if (str_contains($slug, 'central-noida')) {
			$newSlug = str_replace('central-noida', 'noida', $slug);
			return redirect('/' . $newSlug, 301);
		}

		$url = $slug;

		$link = CustomLink::where('slug', $url)->first();

		// if (!$link) {
			// abort(404);
		// }

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

		$locations = Project::pluck('cities')->filter()->unique()->values()->all();
		$locality = Project::pluck('location')->filter()->unique()->values()->all();

		$developers = Developer::select('id', 'developer_name')->get();

		// Build locality => city map
		$localityCityMap = Project::whereNotNull('location')
			->whereNotNull('cities')
			->get(['location', 'cities'])
			->pluck('cities', 'location')
			->toArray();

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

			$project->logo_image = $project->logo_image
				? url('storage/' . $project->logo_image)
				: url('uploads/project/default.png');

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
			!empty($filters['project_status']);

		if (!$hasAnyValidFilter) {
			abort(404);
		}

		// Extra safety checks
		if (!empty($filters['city'])) {

			$cityExists = Project::where('cities', $filters['city'])->exists();

			if (!$cityExists) {
				abort(404);
			}
		}

		if (!empty($filters['locality'])) {

			$localityExists = Project::where('location', $filters['locality'])->exists();

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


		$relatedCityLinks = $this->generateRelatedCityLinks(
    $filters['city'] ?? null
);
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
			'relatedCityLinks'
		));
	}
			



private function generateRelatedCityLinks(?string $currentCity = null): array
{
$relatedCityLinks = [];

    $allCities = Project::query()
        ->where('status', 1)
        ->whereNotNull('cities')
        ->pluck('cities')
        ->map(fn($cityName) => trim($cityName))
        ->filter()
        ->unique(fn($cityName) => strtolower($cityName))
        ->values();

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

    /*
    |--------------------------------------------------------------------------
    | 1. MAIN CITY LINK
    |--------------------------------------------------------------------------
    */

    $citySlug = 'flats-in-' . Str::slug($projectCity);

    CustomLink::firstOrCreate(
        [
            'slug' => $citySlug,
        ],
        [
            'title' => 'Flats in ' . $projectCity,
            'name' => 'Flats in ' . $projectCity,
            'type' => 'city',
            'is_active' => 1,
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | 2. GET ALL LOCALITIES OF THIS CITY
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | 3. CREATE LOCALITY LINKS
    |--------------------------------------------------------------------------
    */

    foreach ($localities as $location) {

		$locationSlug = 'flats-in-' . Str::slug($location);
		$legacyLocationSlug = 'flats-in-' . Str::slug($projectCity) . '-' . Str::slug($location);

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


    /*
    |--------------------------------------------------------------------------
    | 4. GET CITY + LOCALITY LINKS FOR DISPLAY
    |--------------------------------------------------------------------------
    */

    $customLinks = CustomLink::where('is_active', 1)
        ->where(function ($query) use ($projectCity, $localities) {

            // Main city
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

            // Locality links
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
    $query = Project::query()->where('status', true);

    $results = [];

    if ($req->filled('keyword')) {

        $keyword = trim($req->keyword);

        /*
        |--------------------------------------------------------------------------
        | 1. Check City
        |--------------------------------------------------------------------------
        */
        $matchedCity = Project::where('status', true)
            ->whereNotNull('cities')
            ->where('cities', 'LIKE', '%' . $keyword . '%')
            ->value('cities');

        /*
        |--------------------------------------------------------------------------
        | 2. Create Custom Link for City if not exists
        |--------------------------------------------------------------------------
        */
        if ($matchedCity) {

			$citySlug = 'flats-in-' . Str::slug($matchedCity);

            $customLink = CustomLink::firstOrCreate(
                [
                    'slug' => $citySlug,
                ],
                [
                    'title' => 'Flats in ' . $matchedCity,
					'name' => 'Flats in ' . $matchedCity,
                    'type' => 'city',
                    'is_active' => 1,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Add City Custom Link to Search Result
            |--------------------------------------------------------------------------
            */
            $results[] = [
                'name' => $matchedCity,
                'slug' => $customLink->slug,
                'type' => 'custom',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Existing Custom Links
        |--------------------------------------------------------------------------
        */
        $customLinks = CustomLink::where('is_active', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('title', 'LIKE', '%' . $keyword . '%');
            })
            ->get();

        foreach ($customLinks as $link) {

            // Avoid duplicate city/custom links
            $alreadyExists = collect($results)->contains(function ($item) use ($link) {
                return $item['slug'] === $link->slug;
            });

            if (!$alreadyExists) {

                $results[] = [
                    'name' => $link->name ?: $link->title,
                    'slug' => $link->slug,
                    'type' => 'custom',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Search Projects by Project Name OR City
        |--------------------------------------------------------------------------
        */
        $query->where(function ($q) use ($keyword) {

            $q->where('project_name', 'LIKE', '%' . $keyword . '%')
              ->orWhere('cities', 'LIKE', '%' . $keyword . '%');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Location Filter
    |--------------------------------------------------------------------------
    */
    if ($req->filled('location')) {

        $query->where('cities', $req->location);

    }

    /*
    |--------------------------------------------------------------------------
    | BHK Filter
    |--------------------------------------------------------------------------
    */
    if ($req->filled('bhkType')) {

        $query->whereJsonContains('typology', $req->bhkType);

    }

    /*
    |--------------------------------------------------------------------------
    | Get Projects
    |--------------------------------------------------------------------------
    */
    $projects = $query->get();

    /*
    |--------------------------------------------------------------------------
    | Add Projects to Results
    |--------------------------------------------------------------------------
    */
    foreach ($projects as $project) {

        /*
        | Avoid duplicate project
        */
        $alreadyExists = collect($results)->contains(function ($item) use ($project) {
            return $item['type'] === 'project'
                && isset($item['id'])
                && $item['id'] == $project->id;
        });

        if (!$alreadyExists) {

            $results[] = [
                'name' => $project->project_name,
                'slug' => $project->slug,
                'id' => $project->id,
                'type' => 'project',
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Final Response
    |--------------------------------------------------------------------------
    */
    return response()->json([
        'status' => true,
        'data' => $results
    ]);
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

			$query = Property::with(['project', 'user'])
				->where('status', 'approved');

			// SEARCH 
			if (!empty($filters['search'])) {
				$search = trim($filters['search']);

				$query->where(function ($q) use ($search) {
					$q->where('title', 'LIKE', "%{$search}%")
					  ->orWhere('city', 'LIKE', "%{$search}%");
				});
			}

			//Listing Type
			if (!empty($filters['listingType']) && count($filters['listingType']) > 0) {
				$query->whereIn('listing_type', $filters['listingType']);
			}

			// Property Type
			if (!empty($filters['propertyType']) && count($filters['propertyType']) > 0) {
				$query->whereIn('property_type', $filters['propertyType']);
			}

			// Configuration
			if (!empty($filters['configuration']) && count($filters['configuration']) > 0) {
				$query->whereIn('configuration', $filters['configuration']);
			}

			// Location
			if (!empty($filters['location']) && count($filters['location']) > 0) {
				$query->whereIn('city', $filters['location']);
			}

			// Budget
			if (!empty($filters['budget']) && isset($filters['budget']['min'], $filters['budget']['max'])) {
				$query->whereBetween('total_price', [
					$filters['budget']['min'],
					$filters['budget']['max']
				]);
			}
			
			// Construction Status
			if (!empty($filters['constructionStatus']) && count($filters['constructionStatus']) > 0) {
				$query->whereIn('construction_status', $filters['constructionStatus']);
			}

			// Furnishing Type
			if (!empty($filters['furnishingType']) && count($filters['furnishingType']) > 0) {
				$query->whereIn('furnishing_types', $filters['furnishingType']);
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
			// ── Dynamic title 
			$totalResults = $properties->total();
			$titleParts   = [];

			// Configuration  (e.g. "2 BHK, 3 BHK")
			if (!empty($filters['configuration'])) {
				$formatted = array_map(function($c) {
					return strtoupper(str_replace('_', ' ', $c)); // "2_bhk" → "2 BHK"
				}, $filters['configuration']);
				$titleParts[] = implode(', ', $formatted);
			}

			// Property type
			if (!empty($filters['propertyType'])) {
				$titleParts[] = implode(', ', $filters['propertyType']);
			}

			// Location (city)
			if (!empty($filters['location'])) {
				$inPart = 'in ' . implode(', ', $filters['location']);
				$titleParts[] = $inPart;
			}

			// Fallback
			$dynamicTitle = count($titleParts)
				? implode(' ', $titleParts)
				: 'All Properties';

			$totalResultsText = $totalResults . ' Result' . ($totalResults !== 1 ? 's' : '');
			// ── end Dynamic title 

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
				'dynamicTitle'     => $dynamicTitle,        // ← add
				'totalResults'     => $totalResults,         // ← add
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

		} catch (\Exception $e) {

			//DEBUG RETURN (temporary)
			return response()->json([
				'status' => false,
				'error' => $e->getMessage(),
				'line' => $e->getLine()
			]);
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
	
}
