<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OtpLoginController extends Controller
{


    /**
     * Show the OTP login form
     */
    public function showForm()
    {
		if (Auth::check() && Auth::user()->role_id == 2) {
			return redirect()->route('list'); // user dashboard
		}
        return view('frontend.auth.login'); // blade view
    }

    
	
	/**
     * consuming OTP from whitehat 
     */
	 
	 
	public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10',
        ]);

        try {
            $phone = $request->phone;

            // External API URL
            $apiUrl = 'https://whitehatrealty.in/api/v1/send-otp';

            // Data you send to external OTP API
            $postData = [
                'phone_number' => $phone,
                'app_name' => '360propguide',
            ];

            // cURL Setup
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                \Log::error('OTP API Error: ' . $error);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to send OTP at the moment.',
                ], 500);
            }

            $data = json_decode($response, true);
            
            
            // Create user if not exists
            $user = User::firstOrCreate(
                ['phone_number' => $phone],
                [
                    'name' => 'user',
                    'role_id' => 2,
                ]
            );

            // Extract OTP from external API
            $otp = $data['data'] ?? $data['otp'] ?? null;

            if (!$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP not received from server.',
                ], 400);
            }

            // Store OTP in local DB
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            if (!empty($data['status']) && $data['status'] === true) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['message'] ?? 'Failed to send OTP.',
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Send OTP Exception: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }


    /**
     * Verify OTP and log in the user
     */
    public function verifyOtp(Request $request)
	{
		$validated = $request->validate([
			'phone' => 'required|digits:10',
			'otp'   => 'required|digits:6',
		]);

		$user = User::where('phone_number', $validated['phone'])
					->where('otp', $validated['otp'])
					->where('otp_expires_at', '>=', now())
					->first();
	 
		if (!$user) {
			if ($request->expectsJson()) {
				return response()->json([
					'success' => false,
					'message' => 'Invalid or expired OTP.',
				], 422);
			}

			return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
		}

		// OTP is valid
		$user->update([
			'otp' => null,
			'otp_verified_at' => now(),
		]);

		//Auth::login($user);
		Auth::guard('web')->login($user);
        $request->session()->regenerate(); 
		
		if ($request->expectsJson()) {
			return response()->json([
				'success' => true,
				'message' => 'OTP verified successfully.',
				'redirect' => route('list'), // Change to your intended route
			]);
		}

		return redirect()->route('list')->with('success', 'Logged in successfully.');
	}



    /**
     * Logout frontend user
     */
    public function logout(Request $request)
    {
        Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
        return redirect()->route('frontend.login')->with('success', 'Logged out successfully.');
    }
}
