<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user(); // Default guard, correct
        return view('frontend.properties.profile', compact('user'));
    }

    public function update(Request $request)
	{
		$user = Auth::user();

		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email,' . $user->id,
			'phone_number' => [
				'required',
				'regex:/^[0-9]{10}$/',
				'unique:users,phone_number,' . $user->id,
			],
		], [
			'name.required' => 'Name is required.',
			'email.required' => 'Email is required.',
			'email.email' => 'Enter a valid email address.',
			'email.unique' => 'This email is already registered.',
			'phone_number.required' => 'Phone number is required.',
			'phone_number.regex' => 'Phone number must be exactly 10 digits.',
			'phone_number.unique' => 'This phone number is already in use.',
		]);

		// Clean values
		$validated = array_map('trim', $validated);

		$user->update($validated);

		return redirect()->route('profile')->with('success', 'Profile updated successfully.');
	}

}
