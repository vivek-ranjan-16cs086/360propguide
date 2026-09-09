<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FcmToken;

class NotificationController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        FcmToken::updateOrCreate([
            'token' => $request->token
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}
