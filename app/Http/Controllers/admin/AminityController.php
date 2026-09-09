<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AminityList;

class AminityController extends Controller
{
     public function store(Request $request){
		 //dd($request->all());
        $path = uploadFile($request->image, 'aminity-list');
        $aminityObj = new AminityList;
        $aminityObj->name = $request->name;
        $aminityObj->image = $path;
        $aminityObj->save();
        return response()->json([
            'message' => 'stored suceessfully.',
            'status' => true
        ]);
    }
}
