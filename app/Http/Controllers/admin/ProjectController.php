<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\AminityList;
use App\Models\Location;
use App\Http\Controllers\admin\CustomLinkController;
use App\Models\FcmToken;
use App\Services\FirebaseNotificationService; 
use App\Services\IndexNowService;
use App\Events\ProjectCreated;


class ProjectController extends Controller
{
    public function index()
    {
        $title = 'Projects Page';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'projects.index' => 'Projects Page',
            'javascript:void(0);' => 'View'
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.projects.list', compact('title', 'breadcrumbHtml'));
    }

	
	public function ajaxList(Request $request)
	{
		$draw = $request->get('draw');
		$start = $request->get("start");
		$rowperpage = $request->get("length"); 

		$columnIndex_arr = $request->get('order');
		$columnName_arr = $request->get('columns');
		$order_arr = $request->get('order');
		$search_arr = $request->get('search');

		$from_date = $request->get('from_date');
		$to_date = $request->get('to_date');

		// Cities Filter
		$cities = $request->get('cities');

		$columnIndex = $columnIndex_arr[0]['column'];
		$columnName = $columnName_arr[$columnIndex]['data'];
		$columnSortOrder = $order_arr[0]['dir'];

		$searchValue = $search_arr['value'];

		// Query
		$query = Project::query();

		// Search
		if (!empty($searchValue)) {

			$query->where(function ($q) use ($searchValue) {

				$q->where('projects.project_name', 'like', '%' . $searchValue . '%')
				  ->orWhere('projects.slug', 'like', '%' . $searchValue . '%')
				  ->orWhere('projects.location', 'like', '%' . $searchValue . '%')
				  ->orWhere('projects.cities', 'like', '%' . $searchValue . '%')
				  ->orWhere('projects.created_at', 'like', '%' . $searchValue . '%');
			});
		}

		// Date Filter
		if ($from_date && $to_date) {

			$query->whereBetween('projects.created_at', [
				$from_date . ' 00:00:00',
				$to_date . ' 23:59:59'
			]);
		}

		// Cities Filter
		if (!empty($cities)) {

			$query->where('projects.cities', $cities);
		}

		// Total Records
		$totalRecords = Project::count();
		$totalRecordswithFilter = $query->count();

		// Fetch Records
		$records = $query->orderBy('projects.created_at', 'desc')
						 ->orderBy($columnName, $columnSortOrder)
						 ->skip($start)
						 ->take($rowperpage)
						 ->get();

		// Data Format
		$data_arr = [];
		$sno = $start + 1;

		foreach ($records as $record) {

			$sqft_price = collect(json_decode($record->sqft_price, true))
							->pluck('value')
							->implode(', ');

			$hero_images = '<img src="/storage/' . $record->hero_images . '" style="height:100px">';

			$slug = '<iframe style="height:100px" src="' . $record->slug . '" frameborder="0" allowfullscreen></iframe>';

			$statusBtn = $record->status == 1 ? 'btn-info' : 'alert alert-info mb-0';

			$statusText = $record->status == 1 ? 'Active' : 'Inactive';

$url = 'projects/status/' . base64_encode($record->id);

$status = "<a href=\"javascript:void(0)\"
    class=\"btn btn-xs {$statusBtn}\"
    onclick=\"return myConfirm('{$url}')\">
    {$statusText}
</a>";
			$edit = "<a href='projects/edit/" . base64_encode($record->id) . "' class='btn btn-xs btn-info'><i class='fas fa-pen'></i></a>";

			$deleteConfirm = 'return myConfirm("projects/delete/' . base64_encode($record->id) . '")';

			$remove = "<a href='javascript:void(0)' class='btn btn-xs btn-danger' onclick='{$deleteConfirm}'><i class='fas fa-times'></i></a>";

			$data_arr[] = [

				"id" => $sno++,

				"project_name" => ucfirst($record->project_name),

				"location" => ucfirst($record->location),

				// Cities Column
				"cities" => ucwords(strtolower($record->cities)),

				"hero_images" => $hero_images,

				"sqft_price" => $sqft_price,

				"slug" => $slug,

				"status" => $status,

				"uploaded_at" => formatDate($record->created_at),

				"action" => $edit . ' ' . $remove
			];
		}

		// Response
		$response = [
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordswithFilter,
			"aaData" => $data_arr
		];

		return response()->json($response);
	}

    public function add()
    {
        try {
            $title = 'Add New Project';
            $breadcrumbs = [
                'dashboard' => 'Dashboard',
                'projects.index' => 'Projects Page',
                'javascript:void(0);' => 'Add New'
            ];
            $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
            $aminityLists = AminityList::get();
            $developerDetails = Developer::get();
            $projects = Project::where('status', false)->get();
            $parentLocations = Location::parents()->active()->orderBy('city')->get();
            return view('admin.projects.add', compact('title', 'breadcrumbHtml', 'aminityLists', 'developerDetails', 'projects', 'parentLocations'));
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false
            ], 500);
        }
    }
    public function store(Request $request)
    {
		


              $request->validate([
            'project_type' => 'required|in:residential,commercial,mixed',
        
            'faqs_data' => 'required',
            'floor_plans' => 'required|array',
        
            'location' => 'required',
            'cities' => 'required',
        
            'location_id' => 'nullable|exists:locations,id',
            'sublocation_id' => 'nullable|exists:locations,id',
        
            'rera_no' => 'required',
            'price' => 'required|numeric',
            ]);
        
        
        if (!empty($request->floor_plans) && is_array($request->floor_plans)) {
            $floorPlans = [];

            foreach ($request->floor_plans as $currentFloorPlan) {
                $processedPlan = [];

                foreach ($currentFloorPlan as $key => $value) {
                    if ($key === 'feature_image') {
                        if ($value instanceof \Illuminate\Http\UploadedFile && $value->isValid()) {
                            $processedPlan['image'] = uploadFile(
                                $value,
                                'projects/' . createSlug($request->project_name ?? $request->name) . '/BHKPlans'
                            );
                        }
                    } else {
                        $processedPlan[$key] = $value;
                    }
                }

                $processedPlan['image'] = $processedPlan['image'] ?? null;
                $floorPlans[] = $processedPlan;
            }

            // Save the JSON-encoded floor plans data to the database
            $floorPlanData = json_encode($floorPlans);

        }

        //Store the FAQs of projects
        $faqData = [];
        if (!empty($request->faqs_data) && count($request->faqs_data) > 0) {
            foreach ($request->faqs_data as $key => $currentFaqData) {
                $faqData[$key]['question'] = $currentFaqData['question'];
                $faqData[$key]['answer'] = $currentFaqData['answer'];
            }
        }
        $totalFaqData = json_encode($faqData);
		
		$availableBhkTypes = json_encode($request->typology ?? []);
		$encodedSqftPrice = json_encode($request->sqft_price);
		
		
		$reraData = [];

		if (!empty($request->rera_data) && is_array($request->rera_data)) {

			foreach ($request->rera_data as $key => $data) {

				$qrImagePath = null;

				if (isset($data['qr_image'])) {
					$qrImagePath = uploadFile(
						$data['qr_image'],
						'projects/' . Str::slug($request->project_name) . '/rera'
					);
				}

				$reraData[] = [
					'phase'    => $data['phase'],
					'rera_no'  => $data['rera_no'],
					'qr_image' => $qrImagePath,
				];
			}
		}

		$encodedReraData = json_encode($reraData);

        try {
            $project = new Project;
            $project->project_name = $request->project_name;
            $project_name = Str::slug($request->project_name);
            $project->slug = $project_name;
            $project->rera_no = $request->rera_no;
            $project->launch_date = $request->launch_date;
            $project->developer_name = $request->developer_name;
            $project->property_size = $request->property_size;
            $project->typology = $availableBhkTypes;
			$project->project_status = $request->project_status;
            $this->syncProjectLocation($project, $request);
            $project->project_type = $request->project_type;
           
			
			//sqft price
			$project->sqft_price = $encodedSqftPrice;
            $project->price = $request->price;
			$project->max_price = $request->max_price;
            $project->about_description = $request->about_description;
            $project->key_insights = $request->key_insights;
            $project->location_description = $request->location_description;
            $project->site_plans_description = $request->site_plans_description;
            $project->possession_description = $request->possession_description;
            // $project->amenities_description = json_encode(array_keys($request->aminities));
			$project->amenities_description = json_encode(array_values($request->input('amenities', [])));
            // $project->floor_plans_description = json_encode(array_keys($request->details));
			$project->floor_plans_description = json_encode(array_values($request->input('details', [])));
            $project->developer_background_dscp = $request->developer_background_dscp;
            $project->seo_data = json_encode($request->seo_data);
            $project->floor_plans_data = $floorPlanData;
			$project->rera_data = $encodedReraData; 
            $project->faqs_data = $totalFaqData;
            $project->youtube_links = $request->youtube_links;

            // to store the hero image
            if ($request->hasFile('hero_images')) {
                $heroImage = uploadFile($request->file('hero_images'), 'projects');
                $project->hero_images = $heroImage;
            }
            // to store amenities image
            if ($request->hasFile('amenities_images')) {
                $amenitiesImage = uploadFile($request->file('amenities_images'), 'projects');
                $project->amenities_images = $amenitiesImage;
            }
            // to store siteplan images in hero section
            if ($request->hasFile('site_plans_images')) {
                $sitePlanImages = uploadFile($request->file('site_plans_images'), 'projects');
                $project->site_plans_images = $sitePlanImages;
            }
            // to store the brochure
            if ($request->hasFile('floor_plans_images')) {
                $floorPlanImages = uploadFile($request->file('floor_plans_images'), 'projects');
                $project->floor_plans_images = $floorPlanImages;
            }
            // to store the location video
            if ($request->hasFile('location_video')) {
                $locationVideo = uploadFile($request->file('location_video'), 'projects');
                $project->location_video = $locationVideo;
            }
            // to store the price list
            if ($request->hasFile('price_list')) {
                $priceList = uploadFile($request->file('price_list'), 'projects');
                $project->price_list = $priceList;
            }
			
			// to store the sanctioned_map
            if ($request->hasFile('sanctioned_map')) {
                $sanctionedMap = uploadFile($request->file('sanctioned_map'), 'projects');
                $project->sanctioned_map = $sanctionedMap;
            }
			
			// to store the lease_deed
            if ($request->hasFile('lease_deed')) {
                $leaseDeed = uploadFile($request->file('lease_deed'), 'projects');
                $project->lease_deed = $leaseDeed;
            }
			
            // to store the developer background image
            if ($request->hasFile('developer_background_image')) {
                $developerBackgroundImage = uploadFile($request->file('developer_background_image'), 'projects');
                $project->developer_background_image = $developerBackgroundImage;
            }
            // to store the logo image
            if ($request->hasFile('logo_image')) {
                $logoImage = uploadFile($request->file('logo_image'), 'projects');
                $project->logo_image = $logoImage;
            }
            // to store the feature image
            if ($request->hasFile('feature_image')) {
                $featureImage = uploadFile($request->file('feature_image'), 'projects');
                $project->feature_image = $featureImage;
            }
            
           if ($project->save()) {

              // Project create hone ke baad event fire hoga
  
        event(new ProjectCreated($project));

        return redirect()
        ->route('projects.index')
        ->with(
            'success',
            'projects uploaded successfully!'
        );
           } else {

			return redirect()
				->route('projects.index')
				->with(
					'error',
					'projects could not uploaded!'
				);
		}
      } catch (\Exception $e) {

    Log::error('Project Store Error', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    return response()->json([
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'status' => false
    ], 500);
}
    }

    private function processBase64Images($content)
    {
        // Regular expression to match Base64 images
        $pattern = '/<img[^>]+src="data:image\/[^;]+;base64[^"]+"[^>]*>/i';
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[0] as $base64Image) {
            // Extract the base64 string
            $base64String = preg_replace('/^.*base64,/', '', $base64Image);
            $imageData = base64_decode($base64String);

            // Generate a unique file name and save the image
            $fileName = 'img_' . Str::random(10) . '.png';
            $path = 'public/blogs/' . $fileName;
            Storage::put($path, $imageData);
            $fileUrl = Storage::url($path);

            // Replace the Base64 image with the URL
            $content = str_replace($base64Image, '<img src="' . $fileUrl . '">', $content);
        }

        return $content;
    }
   public function edit($id)
{
    try {

        $title = 'Update Projects Details';

        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'projects.index' => 'projects Page',
            'javascript:void(0);' => 'Edit projects Page'
        ];

        $breadcrumbHtml = view(
            'admin.partials.breadcrumbs',
            compact('breadcrumbs', 'title')
        )->render();

        $projects = Project::findOrFail(base64_decode($id));

        $projects->seo_data = json_decode(
            $projects->seo_data,
            true
        );

        $aminityLists = AminityList::get();

        $developerDetails = Developer::get();

        $parentLocations = Location::parents()
            ->active()
            ->orderBy('city')
            ->get();

        $sublocations = $projects->location_id
            ? Location::where('parent_id', $projects->location_id)
                ->active()
                ->orderBy('city')
                ->get()
            : collect();

        return view(
            'admin.projects.edit',
            compact(
                'title',
                'projects',
                'breadcrumbHtml',
                'aminityLists',
                'developerDetails',
                'parentLocations',
                'sublocations'
            )
        );

    } catch (ModelNotFoundException $e) {

        Log::error(
            'Model not found: ' . $e->getMessage()
        );

        return response()->json([
            'message' => 'Model not found.',
            'status' => false
        ], 404);

    } catch (\Exception $e) {

        Log::error(
            'Error fetching: ' . $e->getMessage()
        );

        return response()->json([
            'message' => 'Internal Server Error',
            'status' => false
        ], 500);
    }
}


public function update(Request $request)
{
    try {

        // =====================================================
        // VALIDATION
        // =====================================================

        $request->validate([
            'project_type' => 'required|in:residential,commercial,mixed',
           
            'floor_plans' => 'required|array',
        ]);


        // =====================================================
        // PROJECT
        // =====================================================

        $id = base64_decode($request->id);

        $project = Project::findOrFail($id);


        // =====================================================
        // PROJECT TYPE
        // =====================================================

        $project->project_type = $request->project_type;


        // =====================================================
        // AREA TYPE
        // Commercial = NULL
        // Residential / Mixed = apartment / plots
        // =====================================================

      


        // =====================================================
        // BASIC PROJECT DETAILS
        // =====================================================

        $project->project_name = $request->project_name;

        $project_name = Str::slug(
            $request->project_name
        );

        $project->slug = $project_name;

        $project->rera_no = $request->rera_no;

        $project->launch_date = $request->launch_date;

        $project->developer_name =
            $request->developer_name;

        $project->property_size =
            $request->property_size;

        $project->typology = json_encode(
            $request->typology ?? []
        );

        $project->project_status =
            $request->project_status;


        // =====================================================
        // LOCATION
        // =====================================================

        $this->syncProjectLocation(
            $project,
            $request
        );


        // =====================================================
        // SQFT PRICE
        // =====================================================

        $project->sqft_price = json_encode(
            $request->sqft_price ?? []
        );

        $project->price =
            $request->price;

        $project->max_price =
            $request->max_price;


        // =====================================================
        // DESCRIPTIONS
        // =====================================================

        $project->about_description =
            $request->about_description;

        $project->key_insights =
            $request->key_insights;

        $project->location_description =
            $request->location_description;

        $project->site_plans_description =
            $request->site_plans_description;

        $project->possession_description =
            $request->possession_description;

        $project->developer_background_dscp =
            $request->developer_background_dscp;


        // =====================================================
        // AMENITIES
        // =====================================================

        $project->amenities_description =
            json_encode(
                array_values(
                    $request->input(
                        'amenities',
                        []
                    )
                )
            );


        // =====================================================
        // FLOOR PLANS DESCRIPTION
        // =====================================================

        $project->floor_plans_description =
            json_encode(
                array_values(
                    $request->input(
                        'details',
                        []
                    )
                )
            );


        // =====================================================
        // SEO
        // =====================================================

        $project->seo_data =
            json_encode(
                $request->seo_data
            );


        // =====================================================
        // YOUTUBE
        // =====================================================

        $project->youtube_links =
            $request->youtube_links;


        // =====================================================
        // FAQ DATA
        // =====================================================

        if (
            !empty($request->faqs_data) &&
            count($request->faqs_data) > 0
        ) {

            $newFaqData = [];

            foreach (
                $request->faqs_data
                as $key => $faqData
            ) {

                $newFaqData[$key]['question'] =
                    $faqData['question'];

                $newFaqData[$key]['answer'] =
                    $faqData['answer'];
            }

            $project->faqs_data =
                json_encode($newFaqData);
        }


        // =====================================================
        // FLOOR PLANS
        // =====================================================

        $oldFloorData = json_decode(
            $project->floor_plans_data,
            true
        ) ?? [];

        $newFloorData = [];

        $requestFloorPlans =
            $request->input(
                'floor_plans',
                []
            );


        foreach (
            $requestFloorPlans
            as $index => $floorPlan
        ) {

            $tempPlan = [];


            foreach (
                $floorPlan
                as $key => $value
            ) {

                // ---------------------------------------------
                // FLOOR PLAN IMAGE
                // ---------------------------------------------

                if ($key === 'feature_image') {

                    if (
                        $value instanceof
                        \Illuminate\Http\UploadedFile
                        &&
                        $value->isValid()
                    ) {

                        // Remove old image
                        if (
                            !empty(
                                $oldFloorData[$index]['image']
                            )
                        ) {

                            removeFile(
                                $oldFloorData[$index]['image']
                            );
                        }


                        // Upload new image
                        $tempPlan['image'] =
                            uploadFile(
                                $value,
                                'projects/' .
                                createSlug(
                                    $request->project_name
                                    ?? $request->name
                                ) .
                                '/BHKPlans'
                            );
                    }

                } else {

                    // -----------------------------------------
                    // NORMAL FLOOR PLAN DATA
                    // -----------------------------------------

                    $tempPlan[$key] = $value;
                }
            }


            // ---------------------------------------------
            // KEEP OLD IMAGE
            // If user didn't upload a new image
            // ---------------------------------------------

            if (
                !isset($tempPlan['image']) &&
                !empty(
                    $oldFloorData[$index]['image']
                )
            ) {

                $tempPlan['image'] =
                    $oldFloorData[$index]['image'];
            }


            // ---------------------------------------------
            // IMAGE DEFAULT
            // ---------------------------------------------

            $tempPlan['image'] =
                $tempPlan['image'] ?? null;


            // ---------------------------------------------
            // ADD PLAN
            // ---------------------------------------------

            $newFloorData[] = $tempPlan;
        }


        // =====================================================
        // REMOVE OLD DELETED FLOOR PLAN IMAGES
        // =====================================================

        if (
            count($oldFloorData) >
            count($requestFloorPlans)
        ) {

            for (
                $i = count($requestFloorPlans);
                $i < count($oldFloorData);
                $i++
            ) {

                if (
                    !empty(
                        $oldFloorData[$i]['image']
                    )
                ) {

                    removeFile(
                        $oldFloorData[$i]['image']
                    );
                }
            }
        }


        // =====================================================
        // SAVE FLOOR PLANS
        // =====================================================

        $project->floor_plans_data =
            json_encode($newFloorData);


        // =====================================================
        // HERO IMAGE
        // =====================================================

        if (
            $request->hasFile('hero_images')
        ) {

            $oldPath =
                $project->hero_images;

            if (removeFile($oldPath)) {

                $heroImage =
                    uploadFile(
                        $request->file(
                            'hero_images'
                        ),
                        'projects/' .
                        createSlug(
                            $request->project_name
                        ) .
                        '/'
                    );

                $project->hero_images =
                    $heroImage;
            }
        }


        // =====================================================
        // AMENITIES IMAGE
        // =====================================================

        if (
            $request->hasFile('amenities_images')
        ) {

            $oldPath =
                $project->amenities_images;

            if (removeFile($oldPath)) {

                $amenitiesImage =
                    uploadFile(
                        $request->file(
                            'amenities_images'
                        ),
                        'projects/' .
                        createSlug(
                            $request->project_name
                        ) .
                        '/'
                    );

                $project->amenities_images =
                    $amenitiesImage;
            }
        }


        // =====================================================
        // SITE PLAN IMAGES
        // =====================================================

        if (
            $request->hasFile(
                'site_plans_images'
            )
        ) {

            $sitePlanImages =
                uploadFile(
                    $request->file(
                        'site_plans_images'
                    ),
                    'projects'
                );

            $project->site_plans_images =
                $sitePlanImages;
        }


        // =====================================================
        // FLOOR PLAN IMAGES
        // =====================================================

        if (
            $request->hasFile(
                'floor_plans_images'
            )
        ) {

            $floorPlanImages =
                uploadFile(
                    $request->file(
                        'floor_plans_images'
                    ),
                    'projects'
                );

            $project->floor_plans_images =
                $floorPlanImages;
        }


        // =====================================================
        // LOCATION VIDEO
        // =====================================================

        if (
            $request->hasFile(
                'location_video'
            )
        ) {

            $locationVideo =
                uploadFile(
                    $request->file(
                        'location_video'
                    ),
                    'projects'
                );

            $project->location_video =
                $locationVideo;
        }


        // =====================================================
        // PRICE LIST
        // =====================================================

        if (
            $request->hasFile(
                'price_list'
            )
        ) {

            $priceList =
                uploadFile(
                    $request->file(
                        'price_list'
                    ),
                    'projects'
                );

            $project->price_list =
                $priceList;
        }


        // =====================================================
        // SANCTIONED MAP
        // =====================================================

        if (
            $request->hasFile(
                'sanctioned_map'
            )
        ) {

            $sanctionedMap =
                uploadFile(
                    $request->file(
                        'sanctioned_map'
                    ),
                    'projects'
                );

            $project->sanctioned_map =
                $sanctionedMap;
        }


        // =====================================================
        // LEASE DEED
        // =====================================================

        if (
            $request->hasFile(
                'lease_deed'
            )
        ) {

            $leaseDeed =
                uploadFile(
                    $request->file(
                        'lease_deed'
                    ),
                    'projects'
                );

            $project->lease_deed =
                $leaseDeed;
        }


        // =====================================================
        // FEATURE IMAGE
        // =====================================================

        if (
            $request->hasFile(
                'feature_image'
            )
        ) {

            $featureImage =
                uploadFile(
                    $request->file(
                        'feature_image'
                    ),
                    'projects'
                );

            $project->feature_image =
                $featureImage;
        }


        // =====================================================
        // LOGO IMAGE
        // =====================================================

        if (
            $request->hasFile(
                'logo_image'
            )
        ) {

            $oldPath =
                $project->logo_image;

            if (removeFile($oldPath)) {

                $logoImage =
                    uploadFile(
                        $request->file(
                            'logo_image'
                        ),
                        'projects/' .
                        createSlug(
                            $request->project_name
                        ) .
                        '/'
                    );

                $project->logo_image =
                    $logoImage;
            }
        }


        // =====================================================
        // DEVELOPER BACKGROUND IMAGE
        // =====================================================

        if (
            $request->hasFile(
                'developer_background_image'
            )
        ) {

            $developerBackgroundImage =
                uploadFile(
                    $request->file(
                        'developer_background_image'
                    ),
                    'projects'
                );

            $project->developer_background_image =
                $developerBackgroundImage;
        }


        // =====================================================
        // UPDATE RERA DATA
        // =====================================================

        $oldReraData = json_decode(
            $project->rera_data,
            true
        ) ?? [];

        $newReraData = [];


        if (
            !empty($request->rera_data) &&
            is_array($request->rera_data)
        ) {

            foreach (
                $request->rera_data
                as $index => $rera
            ) {

                // Existing QR
                $qrImage =
                    $oldReraData[$index]['qr_image']
                    ?? null;


                // ---------------------------------------------
                // New QR uploaded
                // ---------------------------------------------

                if (
                    isset($rera['qr_image']) &&
                    $rera['qr_image']
                    instanceof \Illuminate\Http\UploadedFile &&
                    $rera['qr_image']->isValid()
                ) {

                    // Remove old QR
                    if ($qrImage) {

                        removeFile(
                            $qrImage
                        );
                    }


                    // Upload new QR
                    $qrImage =
                        uploadFile(
                            $rera['qr_image'],
                            'projects/' .
                            createSlug(
                                $request->project_name
                            ) .
                            '/rera'
                        );
                }


                $newReraData[] = [

                    'phase' =>
                        $rera['phase'] ?? null,

                    'rera_no' =>
                        $rera['rera_no'] ?? null,

                    'qr_image' =>
                        $qrImage,
                ];
            }
        }


        // =====================================================
        // REMOVE DELETED RERA QR IMAGES
        // =====================================================

        if (
            count($oldReraData) >
            count($newReraData)
        ) {

            for (
                $i = count($newReraData);
                $i < count($oldReraData);
                $i++
            ) {

                if (
                    !empty(
                        $oldReraData[$i]['qr_image']
                    )
                ) {

                    removeFile(
                        $oldReraData[$i]['qr_image']
                    );
                }
            }
        }


        // =====================================================
        // SAVE RERA DATA
        // =====================================================

        $project->rera_data =
            json_encode($newReraData);


        // =====================================================
        // SAVE PROJECT
        // =====================================================

        if ($project->save()) {

            return redirect()
                ->route('projects.index')
                ->with(
                    'success',
                    'Projects updated successfully!'
                );

        } else {

            return redirect()
                ->route('projects.index')
                ->with(
                    'error',
                    'Projects could not updated!'
                );
        }


    } catch (\Exception $e) {

        Log::error(
            'Error fetching: ' .
            $e->getMessage(),
            [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]
        );

        return response()->json([
            'message' =>
                'Internal Server Error',

            'status' =>
                false,

            'error' =>
                $e->getMessage(),

        ], 500);
    }
}




    public function changeStatus1($id)
    {
        try {
            $id = base64_decode($id);
            $project = Project::findOrFail($id);
            $project->status = (!$project->status);
            $project->save();
            return response()->json([
                "message" => "Status changed successfully",
                "status" => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Something went wrong.']);
        }
    }
	
	public function changeStatus2($id, FirebaseNotificationService $firebaseService,
    IndexNowService $indexNowService )
	{
		try {

			$id = base64_decode($id);
			$project = Project::findOrFail($id);

			$oldStatus = $project->status;

			$project->status = !$project->status;
			$project->save();

			// Notification only when status changes 0 -> 1
			if ($oldStatus == 0 && $project->status == 1) {

				$tokens = FcmToken::pluck('token')->toArray();

				if (!empty($tokens)) {

					$firebaseService->send(
						$tokens,
						'New Project Launched',
						$project->project_name,
						url('/projects/' . $project->slug)
					);
				} 
				
				// IndexNow
				try {
					$indexNowService->notifySearchEngines(
						route('projects.details', $project->slug)
					);
				} catch (\Exception $e) {
					\Log::error('IndexNow Error: ' . $e->getMessage());
				}
			}

			return response()->json([
				"message" => "Status changed successfully",
				"status" => true
			]);

		} catch (\Exception $e) {

			Log::error($e->getMessage());

			return back()->withInput()->withErrors([
				'error' => 'Something went wrong.'
			]);
		}
	}
	
	public function changeStatus($id)
{
    try {

        $decodedId = base64_decode($id);

        $project = Project::findOrFail($decodedId);

        $oldStatus = $project->status;

        $project->status = !$project->status;

        $project->save();

        return response()->json([
            'message' => 'Status changed successfully',
            'status' => true,
            'old_status' => $oldStatus,
            'new_status' => $project->status,
        ]);

    } catch (\Exception $e) {

        Log::error('Project Status Error: ' . $e->getMessage(), [
            'id' => $id,
            'decoded_id' => base64_decode($id),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'message' => 'Something went wrong.',
            'status' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}
    public function moveToBin($id)
    {
        try {
            $project = Project::findOrFail(base64_decode($id));
            if ($project->delete()) {
                //Log::error('Model not found: ' . $e->getMessage());	
                return response()->json([
                    'message' => 'Project deleted successfully.',
                    'status' => true
                ], 200);
            }
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

    private function syncProjectLocation(Project $project, Request $request): void
    {
        $parent = $request->location_id
            ? Location::find($request->location_id)
            : null;
        $child = $request->sublocation_id
            ? Location::find($request->sublocation_id)
            : null;

        $project->location_id = $parent?->id;
        $project->sublocation_id = $child?->id;
        $project->cities = $parent?->city ?: $request->cities;
        $project->location = $child?->city ?: ($request->location ?: $parent?->city);
    }

}
