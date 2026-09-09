<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Credential;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $title = 'Settings Page';
        $breadcrumbs = [
            'dashboard' => 'Dashboard',
            'settings.index' => 'Settings Page', 
            'javascript:void(0);' => 'View'
        ];
        $breadcrumbHtml = view('admin.partials.breadcrumbs', compact('breadcrumbs', 'title'))->render();
        $credentials = Credential::orderBy('id', 'DESC')->first();


        return view('admin.settings.view', compact('title', 'breadcrumbHtml', 'credentials'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        try {
            $credentials = new Credential;
            $credentials->api_key = $request->api_key;
            $credentials->channel_id = $request->channel_id;

            if ($credentials->save()) {
                return redirect()->route('settings.index')->with('success', 'Credentials Saved successfully!');
            } else {
                return redirect()->route('settings.index')->with('error', 'Credentials could not saved!');
            }
        } catch (\Exception $e) {
            Log::error('Error fetching: ' . $e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false
            ], 500);
        }

    }
}
