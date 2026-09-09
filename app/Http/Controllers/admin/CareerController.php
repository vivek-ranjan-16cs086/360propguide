<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CareerController extends Controller
{
    public function index()
    {
        $title = 'Career Page';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'career.index' => 'Career Page',
            'javascript:void(0);' => 'View'
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
        return view('admin.career.list', compact('title', 'breadcrumbHtml'));
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
        $totalRecords = Career::select('count(*) as allcount', 'career');

        if (!empty($searchValue)) {
            $totalRecords->where('careers.position', 'like', '%' . $searchValue . '%');
            $totalRecords->orWhere('careers.slug', 'like', '%' . $searchValue . '%');
            $totalRecords->orWhere('careers.created_at', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = $totalRecords->count();

        $totalRecordswithFilter = Career::select('count(*) as allcount', 'careers');
        if (!empty($searchValue)) {
            $totalRecordswithFilter->where('careers.position', 'like', '%' . $searchValue . '%');
            $totalRecordswithFilter->orWhere('careers.slug', 'like', '%' . $searchValue . '%');
            $totalRecordswithFilter->orWhere('careers.created_at', 'like', '%' . $searchValue . '%');
        }

        $totalRecordswithFilter = $totalRecordswithFilter->count();

        // Fetch records
        $records = Career::orderBy($columnName, $columnSortOrder);
        if (!empty($searchValue)) {
            $records->where('carees.position', 'like', '%' . $searchValue . '%');
            $records->orWhere('careers.slug', 'like', '%' . $searchValue . '%');
            $records->orWhere('careers.created_at', 'like', '%' . $searchValue . '%');
        }

        $records->select('careers.*');
        $records->skip($start);
        $records->take($rowperpage);
        $records = $records->get();

        $data_arr = array();
        $sno = $start + 1;
        $i = 1;
        foreach ($records as $record) {
            $position = $record->position;
            $experience = $record->experience;
            $slug = '<iframe style="height:150px" class="iframe" src="' . $record->slug . '" position="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen=""></iframe>';
            $location = $record->location;
            $slno = $i++;
            $action = array();
            $id = base64_encode($record->id);
            $deleteConfirm = 'return myConfirm("career/delete/' . $id . '")';
            $trendingConfirm = 'return myConfirm("career/trending/' . $id . '")';
            $statusConfirm = 'return myConfirm("career/status/' . $id . '")';

            $edit = "<a href='career/edit/" . base64_encode($record->id) . "' class='notPrintable btn btn-xs btn-info'><i class='fas fa-pen'></i></a>";


            $remove = "<a href='javascript: void(0)' class='notPrintable btn btn-xs btn-danger' onclick='$deleteConfirm'><i class='fas fa-times'></i></a>";



            $statusBtn = $record->status == 1 ? 'btn-info' : 'alert alert-info mb-0';
            $statusText = $record->status == 1 ? ' Active ' : ' Active ';

            $status = "<a href='javascript: void(0)' class='notPrintable btn btn-xs {$statusBtn}'  style='padding:0.5rem' onclick='$statusConfirm'>{$statusText}</a>";

            $action[] = $edit . " " . $remove;
            $data_arr[] = array(
                "id" => $sno++,
                "position" => ucfirst($position),
                "experience" => $experience,
                "slug" => $slug,
                'status' => $status,
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
    public function add()
    {
        try {
            $title = 'Add New Career';
            $breadcrumbs = [
                'dashboard' => 'Dashboard',
                'career.index' => 'Career Page',
                'javascript:void(0);' => 'Add New'
            ];
            $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
            return view('admin.career.add', compact('title', 'breadcrumbHtml'));
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
         //dd($request->all());
        try {
            $job_description = $request->input('job_description');
            $job_description = $this->processBase64Images($job_description);
            $career = new Career;
            // $career->user_id = Auth::user()->id;
            $career->position = $request->position;
            $career->location = $request->location;
            //dd($career->location);
            $career->open_positions = $request->open_positions;
            $career->experience = $request->experience;
            $career->date_posted = $request->date_posted;
            $career->job_description = $job_description;
            if ($request->hasFile('company_logo')) {
                $companyLogo = uploadFile($request->file('company_logo'), 'career');
                //dd($companyLogo);
                $career->company_logo = $companyLogo;
            }
            $career->save();
            return redirect()->route('career.index')->with('success', 'Job added successfully!');
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false
            ], 500);
        }
    }

    private function processBase64Images($content)
    {	//Regular expression to match <img> tags with base64 images in the src attribute
        $pattern = '/<img[^>]+src=["\']data:image\/[^;]+;base64,[^"\']*["\'][^>]*>/i';
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[0] as $base64Image) {
            //Extract the base64 string
            $base64String = preg_replace('/^.*base64,/', '', $base64Image);
            $imageData = base64_decode($base64String);

            //Generate a unique file name and save the image
            $fileName = 'img_' . Str::random(10) . '.jpg';
            $path = 'public/career/' . $fileName;
            Storage::put($path, $imageData);
            $fileUrl = Storage::url($path);

            //Replace the Base64 image with the URL
            $content = str_replace($base64Image, '<img style="width:100%" src="' . $fileUrl . '">', $content);
        }
        return $content;
    }

    public function edit($id)
    {
        try {
            $title = 'Update Career Details';
            $breadcrumbs = [
                'dashboard' => 'Dashboard',
                'career.index' => 'Career Page',
                'javascript:void(0);' => 'Edit Career Page'
            ];
            $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

            $career = Career::findOrFail(base64_decode($id));
            return view('admin.career.edit', compact('title', 'career', 'breadcrumbHtml'));
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

    //for update the jobs

    public function update(Request $request)
    {
        //dd($request->all());
        try {
            $job_description = $request->input('job_description');
            $job_description = $this->processBase64Images($job_description);
            $id = base64_decode($request->id);
            //dd($request->id);
            $career = Career::findOrFail($id);
            $career->position = $request->position;
            $career->location = $request->location;
            //dd($career->location);
            $career->open_positions = $request->open_positions;
            $career->experience = $request->experience;
            $career->date_posted = $request->date_posted;
            $career->job_description = $job_description;
            if ($request->hasFile('company_logo')) {
                $companyLogo = uploadFile($request->file('company_logo'), 'career');
                $career->company_logo = $companyLogo;
            }
            $career->save();
            return redirect()->route('career.index')->with('success', 'Job Updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false
            ], 500);
        }
    }
}



