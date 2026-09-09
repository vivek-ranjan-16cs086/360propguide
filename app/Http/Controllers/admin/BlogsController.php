<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\FcmToken;
use App\Services\FirebaseNotificationService;
use App\Services\IndexNowService;

class BlogsController extends Controller
{
    public function index(){
		$title = 'Blogs Page';
		$breadcrumbs = [
			'dashboard' => 'Dashboard',
			'blogs.index' => 'Blogs Page',
			'javascript:void(0);' => 'View'
		];
		$breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs','title'))->render();

        return view('admin.blogs.list',compact('title','breadcrumbHtml'));
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
    $from_date = $request->get('from_date');
    $to_date = $request->get('to_date');

    // Total records
    $totalRecords = Blog::query();
    if (!empty($searchValue)) {
        $totalRecords->where(function($query) use ($searchValue) {
            $query->where('blogs.title', 'like', '%' . $searchValue . '%')
                  ->orWhere('blogs.slug', 'like', '%' . $searchValue . '%')
                  ->orWhere('blogs.created_at', 'like', '%' . $searchValue . '%');
        });
    }

    if ($from_date && $to_date) {
        $totalRecords->whereBetween('blogs.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
    }

    $totalRecordsCount = $totalRecords->count();

    // Filtered record count
    $totalRecordswithFilter = Blog::query();
    if (!empty($searchValue)) {
        $totalRecordswithFilter->where(function($query) use ($searchValue) {
            $query->where('blogs.title', 'like', '%' . $searchValue . '%')
                  ->orWhere('blogs.slug', 'like', '%' . $searchValue . '%')
                  ->orWhere('blogs.created_at', 'like', '%' . $searchValue . '%');
        });
    }

    if ($from_date && $to_date) {
        $totalRecordswithFilter->whereBetween('blogs.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
    }

    $totalFilteredCount = $totalRecordswithFilter->count();

    // Fetch records
    $records = Blog::orderBy('blogs.created_at', 'desc');
	$records->orderBy($columnName,$columnSortOrder);
    if (!empty($searchValue)) {
        $records->where(function($query) use ($searchValue) {
            $query->where('blogs.title', 'like', '%' . $searchValue . '%')
                  ->orWhere('blogs.slug', 'like', '%' . $searchValue . '%')
                  ->orWhere('blogs.created_at', 'like', '%' . $searchValue . '%');
        });
    }

    if ($from_date && $to_date) {
        $records->whereBetween('blogs.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
    }

    $records->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage);

    $records = $records->get();

    $data_arr = array();
    $sno = $start + 1;
    $i = 1;
    foreach($records as $record){
        $title = $record->title;
        $featured_image = '<img src="/storage/' . $record->featured_image . '" style="height:150px">';
        $slug = '<iframe style="height:150px" class="iframe" src="' . $record->slug . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen=""></iframe>';
        $blogDate = $record->created_at;

        $id = base64_encode($record->id);
		$deleteConfirm = 'return myConfirm("blogs/delete/' . $id . '")';
        $statusConfirm = 'return myConfirm("blogs/status/' . $id . '")';
		
        $edit = "<a href='blogs/edit/{$id}' class='notPrintable btn btn-xs btn-info'><i class='fas fa-pen'></i></a>";
        $remove = "<a href='javascript: void(0)' class='notPrintable btn btn-xs btn-danger' onclick='return myConfirm(\"blogs/delete/{$id}\")'><i class='fas fa-times'></i></a>";
        $statusBtn = $record->status == 1 ? 'btn-info' : 'alert alert-info mb-0';
        $statusText = $record->status == 1 ? ' Active ' : ' Inactive ';

        $status = "<a href='javascript: void(0)' class='notPrintable btn btn-xs {$statusBtn}'  style='padding:0.5rem' onclick='$statusConfirm'>{$statusText}</a>"; 
        
        $action = $edit . " " . $remove;

        $data_arr[] = array(
            "id" => $sno++,
            "title" => ucfirst($title),
            "featured_image" => $featured_image,
            "slug" => $slug,
            'status' => $status,
            "uploaded_at" => formatDate($blogDate),
            'action' => $action
        );
    }

    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecordsCount,
        "iTotalDisplayRecords" => $totalFilteredCount,
        "aaData" => $data_arr
    );

    echo json_encode($response);
    exit;
  }

	public function add(){
		try{
			$title = 'Add New Blog';
			$breadcrumbs = [
				'dashboard' => 'Dashboard',
				'blogs.index' => 'Blogs Page',
				'javascript:void(0);' => 'Add New'
			];
			$breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs','title'))->render();
			return view('admin.blogs.add',compact('title','breadcrumbHtml'));
		}catch (\Exception $e) {
			Log::error('Error fetching: ' . $e->getMessage());
			return response()->json([
				'message' => 'Internal Server Error',
				'status' => false
			], 500);
		}
	}



	private function processBase64Images($content)
	{
		// Regular expression to match <img> tags with base64 images in the src attribute
		$pattern = '/<img[^>]+src=["\']data:image\/([^;]+);base64,([^"\']*)["\'][^>]*>/i';
		preg_match_all($pattern, $content, $matches);

		foreach ($matches[0] as $index => $base64Image) {
			// Extract the MIME type and base64 string
			$mimeType = $matches[1][$index]; // e.g., 'png', 'jpeg', etc.
			$base64String = $matches[2][$index];

			// Decode the base64 string
			$imageData = base64_decode($base64String);
			if (!$imageData) {
				continue; // Skip invalid base64 strings
			}

			// Generate a unique file name with the correct extension
			$extension = $mimeType === 'jpeg' ? 'jpg' : $mimeType;
			$fileName = 'img_' . Str::random(10) . '.' . $extension;
			$path = 'public/blogs/' . $fileName;

			// Save the image
			Storage::put($path, $imageData);
			$fileUrl = Storage::url($path);

			// Replace the base64 image with the URL
			$content = str_replace($base64Image, '<img style="width:100%" src="' . $fileUrl . '">', $content);
		}
		
		return $content;
	}



	public function edit($id){
		try{
			$title = 'Update Blog Details';
			$breadcrumbs = [
				'dashboard' => 'Dashboard',
				'blogs.index' => 'Blogs Page',
				'javascript:void(0);' => 'Edit Blog'
			];
			$breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs','title'))->render();

			$blog = Blog::findOrFail(base64_decode($id));
			$blog->seo_data = json_decode($blog->seo_data,true);
			return view('admin.blogs.edit',compact('title','blog','breadcrumbHtml'));
		}catch (ModelNotFoundException $e) {
			Log::error('Model not found: ' . $e->getMessage());
			return response()->json([
				'message' => 'Model not found.',
				'status' => false
			], 404);
		}catch (\Exception $e) {
			Log::error('Error fetching: ' . $e->getMessage());
			return response()->json([
				'message' => 'Internal Server Error',
				'status' => false
			], 500);
		}
	}

	public function moveToBin($id){
		try{
			$blog = Blog::findOrFail(base64_decode($id));
			if($blog->delete()){
				//Log::error('Model not found: ' . $e->getMessage());
				return response()->json([
					'message' => 'Blog deleted successfully.',
					'status' => true
				], 200);
			}
		}catch (ModelNotFoundException $e) {
			Log::error('Model not found: ' . $e->getMessage());
			return response()->json([
				'message' => 'Model not found.',
				'status' => false
			], 404);
		}catch (\Exception $e) {
			Log::error('Error fetching: ' . $e->getMessage());
			return response()->json([
				'message' => 'Internal Server Error',
				'status' => false
			], 500);
		}
	}

	public function permanentRemove($id){
		try{
			$blog = Blog::findOrFail(base64_decode($id));
			if($blog->forceDelete()){
				//Log::error('Model not found: ' . $e->getMessage());
				return response()->json([
					'message' => 'Blog deleted successfully.',
					'status' => true
				], 200);
			}
		}catch (ModelNotFoundException $e) {
			Log::error('Model not found: ' . $e->getMessage());
			return response()->json([
				'message' => 'Model not found.',
				'status' => false
			], 404);
		}catch (\Exception $e) {
			Log::error('Error fetching: ' . $e->getMessage());
			return response()->json([
				'message' => 'Internal Server Error',
				'status' => false
			], 500);
		}
	}

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/blogs/');
            return response()->json(asset('storage/' . substr($path, 7)));
        }
    }

    public function store1(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'slug' => 'required',
			'short_description' => 'required|string',
			'seo_data' => 'required',
			'featured_image' => 'required',
			'faqs_data' => 'required'
        ]);
		
		//Store the FAQs
		$faqData = [];
		if(!empty($request->faqs_data) && count($request->faqs_data)>0){
			foreach($request->faqs_data as $key => $currentFaqData){
				$faqData[$key]['question'] = $currentFaqData['question'];
				$faqData[$key]['answer'] = $currentFaqData['answer'];
			}
		}
		$totalFaqData = json_encode($faqData);
		
		$blog = new Blog;
		$blog->title = $request->title;
		$blog->user_id = Auth::user()->id;
		$blog->short_description = $request->short_description;
		$blog->seo_data = json_encode($request->seo_data);
		$blog->slug = $request->slug;
		$blog->faqs_data = $totalFaqData;
		$blog->description = $request->description;
		$blog->status = isset($request->status) ? true : false;
		if($request->hasFile('featured_image')){
			$blog->featured_image = uploadFile($request->file('featured_image'),'blogs');
		}
		
		if(!empty($request->faqs_data) && count($request->faqs_data)>0){
			$newFaqData = [];			
			foreach($request->faqs_data as $key => $faqData){
				$newFaqData[$key]['question'] = $faqData['question'];
				$newFaqData[$key]['answer'] = $faqData['answer'];
					
			}	
			$blog->faqs_data = json_encode($newFaqData);	
		}
		
		$blog->save();
		
        return redirect()->route('blogs.index')->with('success', 'Blog updated successfully!');
    }
	
	public function store(
        Request $request,
        FirebaseNotificationService $firebaseService, IndexNowService $indexNowService
    ) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'slug' => 'required',
            'short_description' => 'required|string',
            'seo_data' => 'required',
            'featured_image' => 'required',
            'faqs_data' => 'required'
        ]);

        // Store FAQs
        $faqData = [];

        if (!empty($request->faqs_data) && count($request->faqs_data) > 0) {
            foreach ($request->faqs_data as $key => $currentFaqData) {
                $faqData[$key]['question'] = $currentFaqData['question'];
                $faqData[$key]['answer'] = $currentFaqData['answer'];
            }
        }

        $blog = new Blog();
        $blog->title = $request->title; 
        $blog->user_id = Auth::id();
        $blog->short_description = $request->short_description;
        $blog->seo_data = json_encode($request->seo_data);
        $blog->slug = $request->slug;
        $blog->description = $request->description;
        $blog->faqs_data = json_encode($faqData);
        $blog->status = $request->has('status');

        if ($request->hasFile('featured_image')) {
            $blog->featured_image = uploadFile(
                $request->file('featured_image'), 
                'blogs'
            );
        }

        $blog->save();
         
		//index now service 
        try {
			$indexNowService->notifySearchEngines(
				route('blogs.details', $blog->slug)
			);
		} catch (\Exception $e) {
			\Log::error('IndexNow Error: ' . $e->getMessage());
		}


        // Send Firebase Notification
        try {

            $tokens = FcmToken::pluck('token')->toArray();

            if (!empty($tokens)) {

                $firebaseService->send(
                    $tokens,
                    'New Blog Published on 360PropGuide',
                    $blog->title,
                    route('blogs.details', $blog->slug)
                );
            }

        } catch (\Exception $e) {

            \Log::error('Firebase Notification Error: ' . $e->getMessage());
        }

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog added successfully!');
    }

    public function update(Request $request)
    {
		// dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
			'short_description' => 'required|string',
			'seo_data' => 'required',
			'featured_image' => 'nullable|image|mimes:webp',
        ]);

        
		$blog = Blog::findOrFail(base64_decode($request->id));
		$blog->title = $request->title;
		$blog->user_id = Auth::user()->id;
		$blog->slug = $request->slug;
		$blog->short_description = $request->short_description;
		$blog->seo_data = json_encode($request->seo_data); 
		$blog->description = $request->description;
		$blog->status = isset($request->status) ? true : false;
		if($request->hasFile('featured_image')){
			$blog->featured_image = uploadFile($request->file('featured_image'),'blogs');
		}
		
		if(!empty($request->faqs_data) && count($request->faqs_data)>0){
			$newFaqData = [];			
			foreach($request->faqs_data as $key => $faqData){
				$newFaqData[$key]['question'] = $faqData['question'];
				$newFaqData[$key]['answer'] = $faqData['answer'];
					
			}	
			$blog->faqs_data = json_encode($newFaqData);	
		}

		$blog->save();
		

        return redirect()->route('blogs.index')->with('success', 'Blog updated successfully!'); 
    }
	
	public function checkSlug(Request $request)
    {
        $slug = $request->input('slug');

        $exists = Blog::where('slug', $slug)->exists();

        return response()->json(['exists' => $exists]);
    }
	
	public function changeStatus($id)
    {
        try {
            $id = base64_decode($id);
            $blog = Blog::findOrFail($id);
            $blog->status = (!$blog->status);
            $blog->save();
            return response()->json([
                "message" => "Status changed successfully",
                "status" => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Something went wrong.']);
        }
    }

}
