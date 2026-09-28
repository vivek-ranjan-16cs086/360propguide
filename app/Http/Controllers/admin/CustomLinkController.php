<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\CustomLink;
use App\Models\Project;
use App\Models\Property;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Services\ProjectLinksService;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomLinkController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$title = 'Custom Links';
		$breadcrumbs = [
			'dashboard' => 'Dashboard',
			'custom-links.index' => 'Custom Links',
			'javascript:void(0);' => 'List'
		];

		$breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

		return view('admin.custom-links.list', compact('title', 'breadcrumbHtml'));
	}

	public function ajaxList(Request $request)
	{

		$draw = $request->get('draw');
		$start = $request->get("start");
		$rowperpage = $request->get("length"); // Rows display per page

		$columnIndex_arr = $request->get('order');
		$columnName_arr = $request->get('columns');
		$order_arr = $request->get('order');
		$search_arr = $request->get('search');

		$columnIndex = $columnIndex_arr[0]['column']; // Column index
		$columnName = $columnName_arr[$columnIndex]['data']; // Column name
		$columnSortOrder = $order_arr[0]['dir']; // asc or desc

		$searchValue = $search_arr['value']; // Search value

		$totalRecords = CustomLink::select('count(*) as allcount', 'custom_links');

		if (!empty($searchValue)) {
			$totalRecords->where('custom_links.title', 'like', '%' . $searchValue . '%');
			$totalRecords->orWhere('custom_links.slug', 'like', '%' . $searchValue . '%');
			$totalRecords->orWhere('custom_links.description', 'like', '%' . $searchValue . '%');
			$totalRecords->orWhere('custom_links.created_at', 'like', '%' . $searchValue . '%');
		}

		$totalRecords = $totalRecords->count();
		$totalRecordswithFilter = CustomLink::select('count(*) as allcount', 'custom_links');
		if (!empty($searchValue)) {
			$totalRecordswithFilter->where('custom_links.title', 'like', '%' . $searchValue . '%');
			$totalRecordswithFilter->orWhere('custom_links.slug', 'like', '%' . $searchValue . '%');
			$totalRecordswithFilter->orWhere('custom_links.description', 'like', '%' . $searchValue . '%');
			$totalRecordswithFilter->orWhere('custom_links.created_at', 'like', '%' . $searchValue . '%');
		}

		$totalRecordswithFilter = $totalRecordswithFilter->count();

		// Fetch records
		$records = CustomLink::orderBy($columnName, $columnSortOrder);

		if (!empty($searchValue)) {
			$records->where('custom_links.title', 'like', '%' . $searchValue . '%');
			$records->orWhere('custom_links.slug', 'like', '%' . $searchValue . '%');
			$records->orWhere('custom_links.description', 'like', '%' . $searchValue . '%');
			$records->orWhere('custom_links.created_at', 'like', '%' . $searchValue . '%');
		}

		$records->select('custom_links.*');
		$records->skip($start);
		$records->take($rowperpage);
		$records = $records->get();

		$data_arr = array();
		$sno = $start + 1;
		$i = 1;
		foreach ($records as $record) {

			$id 		= base64_encode($record->id);
			$title 		= $record->title;
			$slug 		= $record->slug;
			$messageDate = $record->created_at;
			$status 	= $record->is_active;
			$status 	= $record->is_active;
			$statusBtn 	= '<button class="btn ' . ($status ? 'btn-success' : 'btn-warning') . '">'
				. ($status ? 'Active' : 'Inactive') .
				'</button>';

			$statusConfirm = 'return myConfirm("custom-links/status/' . $id . '")';
			$statusBtn = $record->is_active ? 'btn-info' : 'alert alert-info mb-0';
			$statusText = $record->is_active ? ' Active ' : ' Inactive ';
			$status = "<a href='javascript: void(0)' class='notPrintable btn btn-xs {$statusBtn}'  style='padding:0.5rem' onclick='$statusConfirm'>{$statusText}</a>";



			$deleteConfirm = 'return myConfirm("custom-links/delete/' . $id . '")';
			$description = $record->description;
			$edit		= "<a href='custom-links/edit/" . base64_encode($record->id) . "' class='notPrintable btn btn-xs btn-info'><i class='fas fa-pen'></i></a>";
			$remove 	= "<a href='javascript: void(0)' class='notPrintable btn btn-xs btn-danger' onclick='$deleteConfirm'><i class='fas fa-times'></i></a>";
			$slno 		= $i++;
			$action 	= array();
			$action[] 	= $edit . " " . $remove;
			$data_arr[] = array(
				"id" 		=> $sno++,
				"title"	 	=> ucfirst($title),
				"slug" 		=> $slug,
				"description" => $description,
				'status' 	=> $status,
				'action' 	=> $action
			);
		}
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordswithFilter,
			"aaData" => $data_arr
		);

		echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		exit;
	}

	public function create()
	{
		try {
			$title = 'Add New Link';
			$breadcrumbs = [
				'dashboard' => 'Dashboard',
				'custom-links.index' => 'Custom Links Page',
				'javascript:void(0);' => 'Add New'
			];
			$breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

			$customLink = customLink::get();
			return view('admin.custom-links.add', compact('title', 'breadcrumbHtml', 'customLink'));
		} catch (\Exception $e) {
			Log::error('Error fetching: ' . $e->getMessage());
			return response()->json([
				'message' => 'Internal Server Error',
				'status' => false
			], 500);
		}
	}



	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' 	  => 'nullable|string|max:255',
			'keywords' 	  => 'nullable|string|max:255',
			'canonical'   => 'nullable|string|max:255',
			'slug' 		  => 'required|string|unique:custom_links,slug|max:255',
			'description' => 'nullable|string',
			'links_description' => 'nullable|string'
		]);

		$validated['is_active'] = $request->has('is_active');

		CustomLink::create($validated);

		return redirect()->route('custom-links.index')->with('success', 'Link created successfully.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(string $id)
	{
		try {
			$title = 'Update Custom Link';
			$breadcrumbs = [
				'dashboard' => 'Dashboard',
				'blogs.index' => 'Custom Link Page',
				'javascript:void(0);' => 'Edit Custom Links'
			];
			$breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

			$customLink = CustomLink::findOrFail(base64_decode($id));

			return view('admin.custom-links.edit', compact('title', 'customLink', 'breadcrumbHtml'));
		} catch (ModelNotFoundException $e) {
			Log::error('Model not found: ' . $e->getMessage());
			return response()->json([
				'message' => 'Model not found.',
				'status' => false
			], 404);
		} catch (\Exception $e) {
			Log::error('Error fetching: ' . $e->getMessage());
			return response()->json([
				'message' => 'Internal Server Error',
				'status' => false
			], 500);
		}
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{
		$customLink = CustomLink::findOrFail($id);

		$validated = $request->validate([
			'title'       => 'nullable|string|max:255',
			'slug' 		  => 'required|string|max:255|unique:custom_links,slug,' . $customLink->id,
			'keywords' 	  => 'nullable|string|max:255',
			'canonical'   => 'nullable|string|max:255',
			'description' => 'nullable|string',
			'links_description' => 'nullable|string'
		]);

		$validated['is_active'] = $request->has('is_active');

		$customLink->update($validated);

		return redirect()->route('custom-links.index')->with('success', 'Link updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		try {
			$record = CustomLink::findOrFail(base64_decode($id));
			$record->delete();

			return response()->json([
				'status' => true,
				'message' => 'Record deleted successfully.'
			], 200);
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return response()->json([
				'status' => false,
				'message' => 'Record not found.'
			], 404);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'An error occurred while deleting the record.',
				'error' => $e->getMessage()
			], 500);
		}
	}


	public function toggleStatus(string $id)
	{
		try {
			$record = CustomLink::findOrFail(base64_decode($id));
			$record->is_active = !$record->is_active;
			$record->save();

			return response()->json([
				'status' => true,
				'message' => 'Status updated successfully.',
				'new_status' => $record->is_active
			], 200);
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return response()->json([
				'status' => false,
				'message' => 'Record not found.'
			], 404);
		} catch (\Exception $e) {
			return response()->json([
				'status' => false,
				'message' => 'An error occurred while updating the status.',
				'error' => $e->getMessage()
			], 500);
		}
	}



	public function test()
	{
		$projectCount = Project::where('cities', 'LIKE', 'noida')
			->where('typology', 'LIKE', "%1 BHK%")
			->count();
		// dd($projectCount); 
	}

	// public function generatePropertyLinks()
	// {
	// 	// STEP 1: Delete only property links
	// 	//CustomLink::where('type', 'property')->delete();

	// 	//bhk+city links
	// 	$properties = Property::select('city', 'configuration')
	// 		->whereNotNull('city')
	// 		->whereNotNull('configuration')
	// 		->get()
	// 		->unique(function ($item) {
	// 			return strtolower(trim($item->city)) . '|' . trim($item->configuration);
	// 		})
	// 		->values();

	// 	foreach ($properties as $item) {

	// 		if (empty(trim($item->configuration))) continue;

	// 		$bhk = strtoupper(str_replace('_', ' ', $item->configuration));
	// 		$cityName = ucwords(strtolower(trim($item->city)));

	// 		$slugBHK = strtolower(str_replace(' ', '-', $bhk));
	// 		$slugCity = strtolower(str_replace(' ', '-', $cityName));

	// 		$slug = "{$slugBHK}-apartment-in-{$slugCity}";
	// 		$legacySlug = "{$slugBHK}-flats-in-{$slugCity}";
	// 		$name = "{$bhk} Apartments in {$cityName}";

	// 		CustomLink::where('slug', $legacySlug)
	// 			->where('type', 'property')
	// 			->delete();

	// 		$minPrice = Property::where('city', $item->city)
	// 			->where('configuration', $item->configuration)
	// 			->min('total_price') ?? 0;

	// 		$priceInCr = $minPrice > 0 ? round($minPrice / 10000000, 2) : 0;

	// 		//Meta data
	// 		$seoTitle = "{$bhk} Apartments in {$cityName} | Premium Homes from ₹{$priceInCr} Cr";

	// 		$seoDescription = "Explore {$bhk} apartments in {$cityName} featuring modern layouts, premium amenities, excellent connectivity, and strong future appreciation potential.";

	// 		$linksDescription = "Find your ideal home with well-planned {$bhk} apartments in {$cityName}, offering spacious layouts, premium amenities, and a modern community environment designed for comfortable family living. These residences start from ₹{$priceInCr} Cr, giving you a wide range of options that perfectly balance lifestyle and value. Enjoy seamless connectivity to schools, hospitals, shopping centers, and major road networks, making everyday life convenient and stress-free. Experience a refined urban lifestyle with thoughtfully designed homes in one of {$cityName}'s most promising locations.";

	// 		$keywords = [
	// 			"{$bhk} apartments in {$cityName}",
	// 			"{$bhk} flats in {$cityName}",
	// 			"luxury {$bhk} apartments in {$cityName}",
	// 			"buy {$bhk} in {$cityName}",
	// 			"property in {$cityName}",
	// 			"{$slugBHK} apartments in {$slugCity}"
	// 		];

	// 		CustomLink::updateOrCreate(
	// 			['slug' => $slug],
	// 			[
	// 				'name' => $name,
	// 				'title' => $seoTitle,
	// 				'canonical' => $slug,
	// 				'type' => 'property',
	// 				'description' => $seoDescription,
	// 				'links_description' => $linksDescription,
	// 				'keywords' => implode(', ', $keywords)
	// 			]
	// 		);
	// 	}

	// 	//city only links
	// 	$cities = Property::select('city')
	// 		->whereNotNull('city')
	// 		->distinct()
	// 		->pluck('city');

	// 	foreach ($cities as $city) {

	// 		if (empty(trim($city))) continue;

	// 		$cityName = ucwords(strtolower(trim($city)));
	// 		$slugCity = strtolower(str_replace(' ', '-', $cityName));

	// 		$slug = "apartments-in-{$slugCity}";
	// 		$name = "Apartments in {$cityName}";

	// 		$minPrice = Property::where('city', $city)->min('total_price') ?? 0;
	// 		$priceInCr = $minPrice > 0 ? round($minPrice / 10000000, 2) : 0;

	// 		// seo data
	// 		$seoTitle = "Apartments in {$cityName} | Homes from ₹{$priceInCr} Cr";

	// 		$seoDescription = "Discover apartments in {$cityName} with modern amenities, prime locations, and excellent connectivity. Choose from a wide range of homes designed for comfortable and upscale living.";

	// 		$linksDescription = "Browse through premium apartments in {$cityName} offering modern designs, quality construction, and excellent lifestyle amenities. With prices starting from ₹{$priceInCr} Cr, these homes are ideal for families and investors alike. Enjoy easy access to schools, hospitals, shopping hubs, and major transportation routes, ensuring a convenient and well-connected lifestyle in {$cityName}.";

	// 		$keywords = [
	// 			"apartments in {$cityName}",
	// 			"flats in {$cityName}",
	// 			"property in {$cityName}",
	// 			"real estate in {$cityName}",
	// 			"buy apartment in {$cityName}"
	// 		];

	// 		CustomLink::updateOrCreate(
	// 			['slug' => $slug],
	// 			[
	// 				'name' => $name,
	// 				'title' => $seoTitle,
	// 				'canonical' => $slug,
	// 				'type' => 'property',
	// 				'description' => $seoDescription,
	// 				'links_description' => $linksDescription,
	// 				'keywords' => implode(', ', $keywords)
	// 			]
	// 		);
	// 	}

	// 	return "PROPERTY LINKS GENERATED";
	// }

	//generate project links



	public function loadMore2(Request $request, ProjectLinksService $service)
	{
		$type   = $request->query('type');
		$offset = (int) $request->query('offset', 0);
		$path   = strtolower($request->query('path', ''));
		$limit  = 10;

		if ($type === 'property') {
			$links = $service->getPropertyLinks();
		} else {
			$grouped = $service->getGroupedLinksForPath($path);
			$links = $grouped[$type] ?? [];
		}

		$slice = array_map(function ($link) {
			$link['url'] = url($link['url']);
			return $link;
		}, array_slice($links, $offset, $limit));

		return response()->json([
			'links'      => $slice,
			'hasMore'    => ($offset + $limit) < count($links),
			'nextOffset' => $offset + count($slice),
		]);
	}

	public function loadMore(Request $request, ProjectLinksService $service)
	{
		$type    = $request->query('type');
		$page    = (int) $request->query('page', 1);
		$path    = strtolower($request->query('path', ''));
		$perPage = ($type === 'property') ? 16 : 32;

		if ($type === 'property') {
			$links = $service->getPropertyLinks();
		} elseif (in_array($type, ['footer', 'general'], true)) {
			$links = $service->getFooterCustomLinks($path);
		} else {
			$links = [];
		}

		// URL ko absolute bana do
		$links = array_map(function ($link) {
			$link['url'] = url($link['url']);
			return $link;
		}, $links);

		$offset = ($page - 1) * $perPage;

		$paginator = new LengthAwarePaginator(
			array_slice($links, $offset, $perPage),
			count($links),
			$perPage,
			$page,
			[
				'path' => $request->url(),
				'query' => $request->query(),
			]
		);

		return response()->json([
			'links'       => $paginator->items(),
			'currentPage' => $paginator->currentPage(),
			'lastPage'    => $paginator->lastPage(),
			'hasMore'     => $paginator->hasMorePages(),
			'total'       => $paginator->total(),
		]);
	}
}
