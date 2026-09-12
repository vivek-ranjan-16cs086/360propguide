<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        $title = 'Locations Page';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'locations.index' => 'Locations Page',
            'javascript:void(0);' => 'View',
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.locations.list', compact('title', 'breadcrumbHtml'));
    }

    public function ajaxList(Request $request)
    {
        $draw = $request->draw;
        $start = $request->start;
        $length = $request->length;
        $search = $request->search['value'] ?? '';
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = Location::with('parent');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (!empty($from_date) && !empty($to_date)) {
            $query->whereBetween('created_at', [
                $from_date . ' 00:00:00',
                $to_date . ' 23:59:59',
            ]);
        }

        $totalRecords = Location::count();
        $totalRecordswithFilter = $query->count();

        $records = $query->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $data_arr = [];
        $sno = $start + 1;

        foreach ($records as $record) {
            $id = base64_encode($record->id);

            $edit = "<a href='" . route('locations.edit', $id) . "' class='btn btn-xs btn-info'>
                        <i class='fas fa-pen'></i>
                     </a>";

            $delete = "<a href='javascript:void(0)'
                        onclick='return myConfirm(\"locations/delete/$id\")'
                        class='btn btn-xs btn-danger'>
                        <i class='fas fa-trash'></i>
                      </a>";

            $type = $record->parent_id ? 'Sublocation' : 'Location';
            $status = $record->status
                ? "<span class='badge badge-success'>Active</span>"
                : "<span class='badge badge-secondary'>Inactive</span>";

            $data_arr[] = [
                'id' => $sno++,
                'city' => ucfirst($record->city),
                'type' => $type,
                'parent' => $record->parent?->city ? ucfirst($record->parent->city) : '-',
                'state' => $record->state ?: '-',
                'status' => $status,
                'uploaded_at' => formatDate($record->created_at),
                'action' => $edit . ' ' . $delete,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ]);
    }

    public function add()
    {
        $title = 'Add New Location';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'locations.index' => 'Locations Page',
            'javascript:void(0);' => 'Add New',
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
        $parentLocations = Location::parents()->active()->orderBy('city')->get();

        return view('admin.locations.add', compact('title', 'breadcrumbHtml', 'parentLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:locations,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'slug' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $location = new Location();
        $location->country = $request->country;
        $location->state = $request->state;
        $location->city = $request->city;
        $location->parent_id = $request->parent_id ?: null;
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;

        $location->status = $request->status;
        $slug = $request->filled('slug')
            ? $request->slug
            : $request->city;

        $location->slug = $this->uniqueSlug($slug);

        if ($request->hasFile('image')) {
            $location->image = uploadFile($request->file('image'), 'locations');
        }

        $location->save();

        return redirect()->route('locations.index')->with('success', 'Location added successfully');
    }

    public function edit($id)
    {
        try {
            $title = 'Update Location Details';
            $breadcrumbs = [
                'dashboard' => 'Dashboard',
                'locations.index' => 'Locations Page',
                'javascript:void(0);' => 'Edit Location',
            ];
            $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
            $location = Location::findOrFail(base64_decode($id));
            $parentLocations = Location::parents()
                ->active()
                ->where('id', '!=', $location->id)
                ->orderBy('city')
                ->get();

            return view('admin.locations.edit', compact('title', 'breadcrumbHtml', 'location', 'parentLocations'));
        } catch (ModelNotFoundException $e) {
            return back()->withErrors('Location not found.');
        }
    }

    public function update(Request $request)
    {
        $id = base64_decode($request->id);
        $location = Location::findOrFail($id);

        $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                Rule::exists('locations', 'id'),
                Rule::notIn([$location->id]),
            ],
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|max:2048',
        ]);

        $location->country = $request->country;
        $location->state = $request->state;
        $location->city = $request->city;
        $location->parent_id = $request->parent_id ?: null;
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->status = $request->status;

        if ($location->isDirty('city')) {
            $location->slug = $this->uniqueSlug($request->city, $location->id);
        }

        if ($request->hasFile('image')) {
            $location->image = uploadFile($request->file('image'), 'locations');
        }

        $location->save();

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function moveToBin($id)
    {
        try {
            $location = Location::findOrFail(base64_decode($id));
            Location::where('parent_id', $location->id)->update(['parent_id' => null]);
            $location->delete();

            return response()->json([
                'message' => 'Location deleted successfully.',
                'status' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Location not found.',
                'status' => false,
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting location: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false,
            ], 500);
        }
    }

    public function children($id)
    {
        $children = Location::where('parent_id', $id)
            ->active()
            ->orderBy('city')
            ->get(['id', 'city']);

        return response()->json($children);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i = 1;

        while (
            Location::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
