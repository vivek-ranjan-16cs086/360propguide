<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePassController extends Controller
{
    
public function changePassword()
{
    $title = 'Change Password';

    
    return view('admin.change.password', compact(
        'title'
    ));
}

public function updatePassword(Request $request)
{
    $request->validate([
        'old_password' => 'required',
        'new_password' => 'required|min:8',
        'new_password_confirmation' => 'required|same:new_password',
    ], [
        'old_password.required' => 'Please enter your old password.',
        'new_password.required' => 'Please enter your new password.',
        'new_password.min' => 'New password must be at least 8 characters.',
        'new_password_confirmation.required' => 'Please confirm your new password.',
        'new_password_confirmation.same' => 'New password and confirm password must match.',
    ]);

    $user = Auth::user();

    // Old password check
    if (!Hash::check($request->old_password, $user->password)) {
        return back()
            ->withErrors([
                'old_password' => 'Old password is incorrect.'
            ])
            ->withInput();
    }

    // New password save
    $user->password = Hash::make($request->new_password);

    // Session version increase
    $user->session_version = ($user->session_version ?? 1) + 1;

    $user->save();

    // Current user ko bhi logout kar do
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('login')
        ->with('success', 'Password changed successfully. Please login again with your new password.');
}


}
