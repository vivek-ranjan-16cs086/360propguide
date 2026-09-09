<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\AminityList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class PropertiesController extends Controller
{
    public function index()
    {
        $title = 'Properties Page';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'properties.index' => 'Properties Page',
            'javascript:void(0);' => 'View'
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.properties.list', compact('title', 'breadcrumbHtml'));
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

        // Total records
        $totalRecords = Property::select('count(*) as allcount', 'properties');

        if (!empty($searchValue)) {
            $totalRecords->where('properties.title', 'like', '%' . $searchValue . '%');
            $totalRecords->orWhere('properties.slug', 'like', '%' . $searchValue . '%');
            $totalRecords->orWhere('properties.created_at', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = $totalRecords->count();

        $totalRecordswithFilter = Property::select('count(*) as allcount', 'properties');

        if (!empty($searchValue)) {
            $totalRecordswithFilter->where('properties.title', 'like', '%' . $searchValue . '%');
            $totalRecordswithFilter->orWhere('properties.slug', 'like', '%' . $searchValue . '%');
            $totalRecordswithFilter->orWhere('properties.created_at', 'like', '%' . $searchValue . '%');
        }

        $totalRecordswithFilter = $totalRecordswithFilter->count();

        // Fetch records
        $records = Property::orderBy('properties.created_at', 'desc');
        $records->orderBy($columnName, $columnSortOrder);
        if (!empty($searchValue)) {
            $records->where('properties.title', 'like', '%' . $searchValue . '%');
            $records->orWhere('properties.slug', 'like', '%' . $searchValue . '%');
            $records->orWhere('properties.created_at', 'like', '%' . $searchValue . '%');
        }

        $records->select('properties.*');
        $records->skip($start);
        $records->take($rowperpage);
        $records = $records->get();

        $data_arr = array();
        $sno = $start + 1;
        $i = 1;
        foreach ($records as $record) {
            $title = $record->title;
            $slno = $i++;
            $propertyDate = $record->created_at;
			$url = $record->slug;
            $action = array();
            $id = base64_encode($record->id);
            $deleteConfirm = 'return myConfirm("properties/delete/' . $id . '")';
            $statusConfirm = 'return myConfirm("properties/status/' . $id . '")';

            $view = "<a href='properties/view/" . base64_encode($record->id) . "' class='notPrintable btn btn-xs btn-info'><i class='fas fa-eye'></i></a>";


            $remove = "<a href='javascript: void(0)' class='notPrintable btn btn-xs btn-danger' onclick='$deleteConfirm'><i class='fas fa-times'></i></a>";



            $statusBtn = $record->active == 1 ? 'btn-info' : 'alert alert-info mb-0';
            $statusText = $record->active == 1 ? ' Active ' : ' Inactive ';

            $status = "<a href='javascript: void(0)' class='notPrintable btn btn-xs {$statusBtn}'  style='padding:0.5rem' onclick='$statusConfirm'>{$statusText}</a>";

            $action[] = $view . " " . $remove;
            $data_arr[] = array(
                "id" => $sno++,
                'title' => $title,
                'status' => $status,
				'url' => $url,
                "uploaded_at" => formatDate($propertyDate),
                'action' => $action
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }
    public function view($id)
    {
        $id = base64_decode($id);
        $property = Property::findOrFail($id);
        $amenities = AminityList::whereIn('id', $property->amenities ?? [])->get();
        $title = 'View Property';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'properties.index' => 'Properties Page',
            'javascript:void(0);' => 'View'
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.properties.view', compact('title', 'breadcrumbHtml', 'property', 'amenities'));
    }
     public function changeStatus($id)
    {
        try {
            $id = base64_decode($id);
            $property = Property::findOrFail($id);
            $property->active = (!$property->active);
            $property->save();
            return response()->json([
                "message" => "Status changed successfully",
                "status" => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Something went wrong.']);
        }
    }

    public function approveProperties($id)
    {
        $property = Property::findOrFail($id);
        $property->status = 'approved'; 
        $property->save();

        return redirect()->route('properties.index')
            ->with('success', 'Property approved successfully!');
    }

    public function rejectProperties($id)
    {
        $property = Property::findOrFail($id);
        $property->status = 'rejected';
        $property->save();

        return redirect()->route('properties.index')
            ->with('error', 'Property rejected successfully!');
    }


}
