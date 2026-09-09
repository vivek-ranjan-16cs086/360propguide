<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\AminityList;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyPostController extends Controller
{
    protected $steps = [
        1 => 'property_details',
        2 => 'advanced_details',
        3 => 'amenities',
        4 => 'galleries',
        5 => 'verify',
    ];

	private function authorizePropertyOwner(Property $property)
	{
		if ($property->user_id !== auth()->id()) {
			abort(403, 'Unauthorized action.');
		}
	}

    protected function getProperty($id)
    {
        return Property::where('property_uid', $id)
            ->where('user_id', Auth()->id())
            ->firstOrFail();
    }

    protected function redirectToStep(Property $property, $step = null)
    {
        $targetStep = $step ?? ($property->step_id ?? 1);
        return redirect()->route('postproperty.edit.' . $this->steps[$targetStep], $property->property_uid);
    }

    protected function updateStatus(Property $property)
    {
        if (in_array($property->status, ['active', 'rejected'])) {
            $property->status = 'under_review';
        }
    }

    // Initial property creation (Step 0)
    public function create()
    {
        $cities = Project::pluck('cities')->unique();
        $currentStep = 0; // Initial step
        return view('frontend.properties.create', compact('cities', 'currentStep'));
    }

    public function saveCreate(Request $request)
    {
        $validated = $request->validate([
            'cities' => 'required|string',
            'listing_type' => 'required|in:sale,rent',
            'property_type' => 'required|in:apartment,plots',
        ]);

        $property = Property::create([
            'user_id' => Auth()->id(),
            'city' => $validated['cities'],
            'listing_type' => $validated['listing_type'],
            'property_type' => $validated['property_type'],
            'step_id' => 1,
            'status' => 'draft',
            'is_complete' => false,
        ]);
        return $this->redirectToStep($property, 1);
    }

    // Step 1: Property Details
    public function propertyDetails(Property $property)
    {
		$this->authorizePropertyOwner($property);
        if ($property->step_id < 1) return $this->redirectToStep($property);
        $data = $property ?? [];
        $currentStep = 1;
        $projectName = optional($property->project)->project_name; // Will return null safely if project is missing
        // dd($data);
        if (request()->ajax()) {
            return view('frontend.properties.steps.partials.property_partial', compact('property', 'data', 'projectName', 'currentStep'))->render();
        }
        return view('frontend.properties.steps.property_details', compact('property', 'data', 'projectName', 'currentStep'));
    }

    public function savePropertyDetails(Request $request, Property $property)
{
	$this->authorizePropertyOwner($property);
    // Determine if property is a plot
    $isPlot = $property->property_type === 'plots';

    $rules = [
        'project_id' => 'required|integer',
        'configuration' => 'required|string',
        'area' => 'required|numeric',
        'area_unit' => 'required|string',
        'total_price' => 'required|numeric',
    ];

    // Only require furnishing_types if it's not a plot
    if (!$isPlot) {
        $rules['furnishing_types'] = 'required|string';
        $rules['construction_status'] = 'required|string';
    }

    $validated = $request->validate($rules);

    $property->project_id = $validated['project_id'];
    $property->configuration = $validated['configuration'];
    $property->area = $validated['area'];
    $property->area_unit = $validated['area_unit'];
    $property->total_price = $validated['total_price'];
	
	$property->property_details = $request->property_details ?? [];
    

    // Only assign furnishing if it exists in validated data
    if (isset($validated['furnishing_types'])) {
        $property->furnishing_types = $validated['furnishing_types'];
    } else {
        $property->furnishing_types = null; // optional: clear it if not required
    } if (isset($validated['construction_status'])) {
        $property->construction_status = $validated['construction_status'];
    } else {
        $property->construction_status = null; // optional: clear it if not required
    }

    $property->step_id = max(2, $property->step_id);
    $this->updateStatus($property);
    $property->save();

    $this->generateTitleSlugAndSeo($property);

    $nextStep = 2;
    $currentStep = $nextStep;
    $data = $property->advanced_details ?? [];

    if ($request->ajax()) {
        $html = view("frontend.properties.steps.partials.advanced_partial", compact('property', 'data', 'currentStep'))->render();
        $url = route('postproperty.edit.advanced_details', $property->property_uid);
        return response()->json([
            'html' => $html,
            'redirect_url' => $url,
            'step' => $nextStep
        ]);
    }

    return $this->redirectToStep($property, 2);
}


    // Step 2: advanced details
    public function localityDetails(Property $property) 
    {
		$this->authorizePropertyOwner($property);
        if ($property->step_id < 2) return $this->redirectToStep($property);
        $data = $property->advanced_details ?? [];
        // dd($property);
        $currentStep = 2;
        if (request()->ajax()) {
            return view('frontend.properties.steps.partials.advanced_partial', compact('property', 'data', 'currentStep'))->render();
        }
        return view('frontend.properties.steps.advanced_details', compact('data', 'property', 'currentStep'));
    }

    public function saveLocalityDetails(Request $request, Property $property)
    {
		$this->authorizePropertyOwner($property);
        $validated = $request->validate([
            'advanced_details.property_age' => 'nullable|numeric',
            'advanced_details.bathroom' => 'nullable|numeric',
            'advanced_details.balcony' => 'nullable|numeric',
            'advanced_details.parking' => 'nullable|numeric',
            'advanced_details.length' => 'nullable|numeric',
            'advanced_details.width' => 'nullable|numeric',
            'advanced_details.flat_no' => 'nullable|string|max:50',
            'advanced_details.plot_no' => 'nullable|string|max:50',
            'advanced_details.floor_no' => 'nullable|string|max:50',
            'advanced_details.total_floors' => 'nullable|string|max:50',
            'advanced_details.facing' => 'nullable|string',
            'advanced_details.description' => 'nullable|string',
        ]);

        $property->advanced_details = $validated['advanced_details'];
        $property->step_id = max(3, $property->step_id);
        $this->updateStatus($property);
        $property->save();
		$this->generateTitleSlugAndSeo($property);
        $nextStep = 3;
        $currentStep = $nextStep;
        $data = $property->amenities ?? [];
        $amenities = AminityList::all();
        if ($request->ajax()) {
            $html = view("frontend.properties.steps.partials.amenities_partial", compact('property', 'data', 'currentStep', 'amenities'))->render();
            $url = route('postproperty.edit.amenities', $property->property_uid);
            return response()->json([
                'html' => $html,
                'redirect_url' => $url,
                'step' => $nextStep
            ]);
        }
        return $this->redirectToStep($property, 3);
    }


    // Step 4: Amenities
    public function amenitiesDetails(Property $property)
    {
		$this->authorizePropertyOwner($property);
        if ($property->step_id < 3) return $this->redirectToStep($property);
        $data = $property->amenities ?? [];
        $amenities = AminityList::all();
        $currentStep = 3;
        if (request()->ajax()) {
            return view('frontend.properties.steps.partials.amenities_partial', compact('property', 'amenities','data', 'currentStep'))->render();
        }
        return view('frontend.properties.steps.amenities', compact('data', 'property', 'amenities', 'currentStep'));
    }

    public function saveAmenitiesDetails(Request $request, Property $property)
    {
		$this->authorizePropertyOwner($property);
        $validated = $request->validate([
            'amenities' => 'required|array',
        ]);

        $property->amenities = $validated['amenities'];
        $property->step_id = max(4, $property->step_id);
        $this->updateStatus($property);
        $property->save();
		$this->generateTitleSlugAndSeo($property);
        $nextStep = 4;
        $currentStep = $nextStep;
        $data = $property->galleries ?? [];
        if ($request->ajax()) {
            $html = view("frontend.properties.steps.partials.galleries_partial", compact('property', 'data', 'currentStep'))->render();
            $url = route('postproperty.edit.galleries', $property->property_uid);
            return response()->json([
                'html' => $html,
                'redirect_url' => $url,
                'step' => $nextStep
            ]);
        }
        return $this->redirectToStep($property, 4);
    }

    // Step 5: Galleries
    public function galleries(Property $property)
    {
		$this->authorizePropertyOwner($property);
        if ($property->step_id < 4) return $this->redirectToStep($property);
        $data = $property->galleries ?? [];
        $currentStep = 4;
        if (request()->ajax()) {
            return view('frontend.properties.steps.partials.galleries_partial', compact('property', 'data', 'currentStep'))->render();
        }
        return view('frontend.properties.steps.galleries', compact('data', 'property', 'currentStep'));
    }

	
	public function deleteImageFromJson(Request $request)
	{
		$request->validate([
			'image' => 'required|string',
			'property_id' => 'required|integer',
		]);

		$property = Property::find($request->property_id);

		if (!$property) {
			return response()->json(['success' => false, 'message' => 'Property not found'], 404);
		}

		// Now it's safe to authorize
		$this->authorizePropertyOwner($property);

		// Verify ownership again (optional if authorizePropertyOwner already checks it)
		if ($property->user_id !== auth()->id()) {
			return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
		}

		$galleries = $property->galleries;

		// Remove the image path
		$updated = array_filter($galleries, function ($img) use ($request) {
			return $img !== $request->image;
		});

		// Delete file from storage
		if (Storage::exists('public/' . $request->image)) {
			Storage::delete('public/' . $request->image);
		}

		$property->galleries = array_values($updated); // Reset keys
		$property->save();

		return response()->json(['success' => true]);
	}


    public function saveGalleries(Request $request, Property $property)
    {
		$this->authorizePropertyOwner($property);
        if ($property->step_id < 4) return $this->redirectToStep($property);

        $validated = $request->validate([
            'galleries.*' => 'image',
        ]);

        $existingImages = $property->galleries ?? [];

        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $image) {
                $path = $image->store('properties/gallery', 'public'); // stores to /storage/app/public/properties/gallery
                $existingImages[] = $path;
            }
        }

        $property->galleries = $existingImages;
        $property->step_id = max(5, $property->step_id);
        $property->save();
		$this->generateTitleSlugAndSeo($property);
        $nextStep = 5;
        $currentStep = $nextStep;
        $data = $property ?? [];
        $amenities = AminityList::whereIn('id', $property->amenities ?? [])->get();
        if ($request->ajax()) {
            $html = view("frontend.properties.steps.partials.verify_partial", compact('property', 'data', 'currentStep', 'amenities'))->render();
            $url = route('postproperty.edit.verify', $property->property_uid);
            return response()->json([
                'html' => $html,
                'redirect_url' => $url,
                'step' => $nextStep
            ]);
        }
        return $this->redirectToStep($property, 5);
    }

    // Verify property
    public function verify(Property $property)
    {
		$this->authorizePropertyOwner($property);
        $amenities = AminityList::whereIn('id', $property->amenities ?? [])->get();
        if ($property->step_id < 5) return $this->redirectToStep($property);
        $data = $property ?? [];
        $currentStep = 5;
		if (request()->ajax()) {
            return view('frontend.properties.steps.partials.verify_partial', compact('property', 'data', 'currentStep', 'amenities'))->render();
        }
        return view('frontend.properties.steps.verify', compact('property', 'data', 'currentStep', 'amenities'));
    }
	
	public function submit(Request $request, Property $property)
	{
		$this->authorizePropertyOwner($property);
		$this->generateTitleSlugAndSeo($property);

		return redirect()->route('list')->with('success', 'Property submitted successfully!');
	}

	private function generateTitleSlugAndSeo8(Property $property)
	{
		if ($property->step_id >= 5) {
			// Fetch associated data
			$projectName = optional($property->project)->project_name;
			$config = strtoupper(str_replace('_', ' ', $property->configuration));
			$city = $property->city;
			$type = ucfirst($property->property_type);
			$area = $property->area . ' ' . strtoupper($property->area_unit);
			$price = number_format($property->total_price);
			$furnish = ucwords(str_replace('_', ' ', $property->furnishing_types));

			// Generate title
			$titleParts = [];
			if ($config) $titleParts[] = $config;
			if ($type != 'Plots') $titleParts[] = $type;
			if ($projectName) $titleParts[] = "in $projectName";
			$titleParts[] = "at $city";
			$title = implode(' ', $titleParts);

			// Generate base slug
			$baseSlug = Str::slug($title);

			// Generate unique slug with random 10-digit number
			do {
				$randomNumber = random_int(1000000000, 9999999999);
				$slug = $baseSlug . '-' . $randomNumber;
			} while (
				Property::where('slug', $slug)->where('id', '!=', $property->id)->exists()
			);

			// SEO meta
			$metaTitle = $title . ' for Sale';
			$metaDescription = "Find $config $type at $projectName in $city with area $area and price ₹$price. $furnish. Best deals available now.";

			// Assign values
			$property->title = $title;
			$property->slug = $slug;
			$property->seo_data = [
				'meta_title' => $metaTitle,
				'meta_description' => $metaDescription,
			];
			$property->status = 'under_review';
			$property->step_id = max(6, $property->step_id);
			$property->save();
		}
	}
	

	private function generateTitleSlugAndSeo(Property $property)
	{
		if ($property->step_id < 5) {
			return;
		}

		// Project Data
		$project = optional($property->project);

		$projectName = $project->project_name ?? '';
		$location    = $project->location ?? '';

		// Property Data
		$bhk         = strtoupper(str_replace('_', ' ', $property->configuration));
		$type        = ucfirst($property->property_type);
		$area        = $property->area;
		$city        = $property->city;
		$furnishing  = ucfirst(str_replace('_', '-', strtolower($property->furnishing_types)));

		// Advanced Details
		$advancedDetails = $property->advanced_details ?? [];

		$floor  = $advancedDetails['floor'] ?? '';
		$facing = $advancedDetails['facing'] ?? '';

		// Price in Crore
		$priceCr = number_format($property->total_price / 10000000, 2);

		$titleParts = [];

		if ($bhk) {
			$titleParts[] = $bhk;
		}

		if ($type != 'Plots') {
			$titleParts[] = $type;
		}

		if ($projectName) {
			$titleParts[] = "in {$projectName}";
		}

		if ($city) {
			$titleParts[] = "at {$city}";
		}

		$title = implode(' ', $titleParts);

        //meta title
		$metaTitle = "{$bhk} {$type} in {$projectName} {$location} - Resale at ₹{$priceCr} Cr";

       //slug
		$baseSlug = Str::slug($title);

		do {
			$slug = $baseSlug . '-' . random_int(1000000000, 9999999999);
		} while (
			Property::where('slug', $slug)
				->where('id', '!=', $property->id)
				->exists()
		);

       //description
		$metaDescription = "{$bhk} " . strtolower($type) . " in {$projectName} {$location} - {$area} sq ft";

		if (!empty($facing)) {
			$metaDescription .= ", {$facing}-facing";
		}

		if (!empty($floor)) {
			$metaDescription .= ", {$floor}th floor";
		}

		$metaDescription .= ". {$furnishing} resale at ₹{$priceCr} Cr. No brokerage. Enquire now!";
        // save
		$property->title = $title;
		$property->slug = $slug;

		$property->seo_data = [
			'meta_title' => $metaTitle,
			'meta_description' => $metaDescription,
		];

		$property->status = 'under_review';
		$property->step_id = max(6, $property->step_id);

		$property->save();
	}

}
