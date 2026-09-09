<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class DeveloperController extends Controller
{
    public function index()
    {
        $title = 'Developers Page';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'developers.index' => 'Developers Page',
            'javascript:void(0);' => 'View'
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

        return view('admin.developers.list', compact('title', 'breadcrumbHtml'));
    }
	
	public function ajaxList(Request $request)
	{
		$draw = $request->draw;
		$start = $request->start;
		$length = $request->length;

		$order = $request->order;
		$columns = $request->columns;
		$search = $request->search['value'];

		$from_date = $request->from_date;
		$to_date = $request->to_date;

		$columnIndex = $order[0]['column'];
		$columnName = $columns[$columnIndex]['data'];
		$columnSortOrder = $order[0]['dir'];

		$allowedColumns = [
			'id',
			'developer_name',
			'developer_experience',
			'ongoing_project',
			'completed_projects',
			'created_at'
		];

		if (!in_array($columnName, $allowedColumns)) {
			$columnName = 'id';
		}

		$query = Developer::query();

		if (!empty($search)) {
			$query->where(function ($q) use ($search) {
				$q->where('developer_name', 'like', "%{$search}%")
					->orWhere('developer_experience', 'like', "%{$search}%")
					->orWhere('ongoing_project', 'like', "%{$search}%")
					->orWhere('completed_projects', 'like', "%{$search}%");
			});
		}

		if (!empty($from_date) && !empty($to_date)) {
			$query->whereBetween('created_at', [
				$from_date . ' 00:00:00',
				$to_date . ' 23:59:59'
			]);
		}

		$totalRecords = Developer::count();

		$totalRecordswithFilter = $query->count();

		$records = $query->orderBy('created_at', 'desc')
		->skip($start)
		->take($length)
		->get();

		$data_arr = [];
		$sno = $start + 1;

		foreach ($records as $record) {

			$id = base64_encode($record->id);

			$edit = "<a href='" . route('developers.edit', $id) . "' class='btn btn-xs btn-info'>
						<i class='fas fa-pen'></i>
					 </a>";

			$delete = "<a href='javascript:void(0)'
						onclick='return myConfirm(\"developers/delete/$id\")'
						class='btn btn-xs btn-danger'>
						<i class='fas fa-trash'></i>
					  </a>";

			if ($record->developer_logo != '') {

				$logo = "<img src='" . asset($record->developer_logo) . "' width='60' height='60' style='border-radius:5px;'>";

			} else {

				$logo = "<img src='" . asset('assets/images/NA.webp') . "' width='60'>";
			}

			$data_arr[] = [

				"id" => $sno++,

				"developer_logo" => $logo,

				"developer_name" => ucfirst($record->developer_name),

				"developer_experience" => $record->developer_experience,

				"ongoing_project" => $record->ongoing_project,

				"completed_projects" => $record->completed_projects,

				"uploaded_at" => formatDate($record->created_at),

				"action" => $edit . " " . $delete

			];
		}

		return response()->json([
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordswithFilter,
			"aaData" => $data_arr
		]);
	}

    public function add()
    {
        try {
            $title = 'Add New Developer';
            $breadcrumbs = [
                'dashboard' => 'Dashboard',
                'developers.index' => 'Developers Page',
                'javascript:void(0);' => 'Add New'
            ];
            $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
            
            $developerDetails = Developer::get();
            return view(
				'admin.developers.add',
				compact(
				'title',
				'breadcrumbHtml'
				)
				);
           
           
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
		$developer = new Developer();

		$developer->developer_name = $request->developer_name;

		$developer->developer_experience = $request->developer_experience;

		$developer->ongoing_project = $request->ongoing_project;

		$developer->completed_projects = $request->completed_projects;

		if($request->hasFile('developer_logo')){

			$path = uploadFile($request->file('developer_logo'),'developer-logo');

			$developer->developer_logo = $path;
		}

		$developer->save();

		return redirect()->route('developers.index')
				->with('success','Developer Added Successfully');
	}
   
    public function edit($id)
    {

        try {
            $title = 'Update Developers Details';
            $breadcrumbs = [
                'dashboard' => 'Dashboard',
                'developers.index' => 'Developers Page',
                'javascript:void(0);' => 'Edit developers Page'
            ];
            $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();

            $developers=  Developer::findOrFail(base64_decode($id));
            $developers->seo_data = json_decode($developers->seo_data, true);
			
            $developerDetails = Developer::get();
$developer = Developer::findOrFail(base64_decode($id));

return view('admin.developers.edit', compact(
    'title',
    'breadcrumbHtml',
    'developer'
));

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
  public function update(Request $request)
{
    try {

        $id = base64_decode($request->id); 

        $developer = Developer::findOrFail($id);

        $developer->developer_name = $request->developer_name;
        $developer->developer_experience = $request->developer_experience;
        $developer->ongoing_project = $request->ongoing_project;
        $developer->completed_projects = $request->completed_projects;

        if ($request->hasFile('developer_logo')) {

            $path = uploadFile(
                $request->file('developer_logo'),
                'developer-logo'
            );

            $developer->developer_logo = $path;
        }

        $developer->save();

        return redirect()
            ->route('developers.index')
            ->with('success', 'Developer updated successfully.');

    } catch (\Exception $e) {

        return back()->withErrors($e->getMessage());
    }
}
    // public function changeStatus($id)
    // {
    //     try {
    //         $id = base64_decode($id);
    //         // $developer=  Developer::findOrFail($id);
    //         // $developer->status = (!$developer->status);
    //         $developer->save();
    //         return response()->json([
    //             "message" => "Status changed successfully",
    //             "status" => true
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching: ' . $e->getMessage());
    //         return back()->withInput()->withErrors(['error' => 'Something went wrong.']);
    //     }
    // }

    public function moveToBin($id)
    {
        try {
            $developer=  Developer::findOrFail(base64_decode($id));
            if ($developer->delete()) {
                //Log::error('Model not found: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Developer deleted successfully.',
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

}
