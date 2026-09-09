<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function create()
    {
        $cities = Project::pluck('cities')
			->filter()
			->map(function ($item) {
				return ucfirst(strtolower(trim($item)));
			})
			->unique()
			->values()
			->all(); // Get unique cities
        return view('frontend.properties.create', compact('cities'));
    }

    public function index()
    {
        $userId = auth()->id(); // Default auth, no custom guard

        $properties = Property::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        return view('frontend.properties.list', compact('properties'));
    }

    public function store(Request $request)
    {
        // Add validation and logic to handle posting
        // If you’re auto-creating a user by phone, do it using the `users` table now
    }

    public function destroy(Property $property)
    {
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        $property->delete();

        return redirect()->back()->with('success', 'Property deleted successfully.');
    }
}
