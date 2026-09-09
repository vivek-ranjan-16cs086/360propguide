<?php

namespace App\Http\Controllers\frontend;
use Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\ContactMail;
use App\Models\Query;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Jobs\SendContactEmailJob;

class MailController extends Controller
{

	public function SendContactMail(Request $request) 
	{
		// reCAPTCHA validation
		$recaptchaResponse = $request->input('g-recaptcha-response');
		$response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
			'secret' => config('services.recaptcha.secret_key'),
			'response' => $recaptchaResponse,
		]);

		$responseBody = $response->json();
		Log::info('reCAPTCHA Response:', $response->json());

		if (!$responseBody['success']) {
			return response()->json([
				'errors' => ['recaptcha' => 'reCAPTCHA validation failed.']
			], 422);
		}

		
		$validator = Validator::make($request->all(), [
			'name' => 'required|regex:/^[a-zA-Z\s]+$/',
			'mobile' => 'required|digits:10',
			'email' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}

		// Prepare mail data
		$mailData = $request->all();
		$mailData['submittedVia'] = $request->headers->get('referer');

		$query = Query::create($mailData);
		        try {
				   Http::timeout(25)
					->withHeaders([
						'x-api-key' => config('services.erp.key') // must match ERP
					])
					->post(config('services.erp.url').'/api/v1/leads/store', [
						'external_id' => $query->id,
						'source_website' => '360propguide.com',
						'name' => $query->name,
						'phone' => $query->mobile,
						'email' => $query->email,
						'message' => $query->message,
					]);

				} catch (\Exception $e) {
					// Do NOT break user flow
					\Log::error('ERP Sync Failed: ' . $e->getMessage());
				}

		if ($query) {
			// Dispatch Job for User
			SendContactEmailJob::dispatch($mailData, $request->email);

			// Dispatch Job for Admins
			$mailData['reciever'] = 'admin';

			 SendContactEmailJob::dispatch($mailData, 'rohit.mis.360@gmail.com');
			 

			// Store form name in session
			session(['form_submitted' => $request->formName]);

			
			if ($request->ajax()) {
				return response()->json([
					'success' => true,
					'redirect_url' => route('thankyou')
				]);
			} else {
				return redirect()->route('thankyou');
			}

		} else {
			return response()->json([
				'message' => 'Something went wrong',
				'status' => false
			], 200);
		}
	}

		
	public function popupDownload(Request $request)
	{
		// reCAPTCHA check
		$recaptchaResponse = $request->input('g-recaptcha-response');
		$response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
			'secret'   => config('services.recaptcha.secret_key'),
			'response' => $recaptchaResponse,
		]);
		$responseBody = $response->json();
		
		Log::info('reCAPTCHA Response:', $response->json());
		
		if (!$responseBody['success']) {
			return response()->json([
				'errors' => ['recaptcha' => 'reCAPTCHA validation failed.'],
				'status' => 422
			], 422);
		}
		
		// Validate request
		
		$validator = Validator::make($request->all(), [
			'name'          => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
			'mobile'        => 'required|digits:10',
			'email'         => 'nullable|email',
			'download_type' => 'required|in:brochure,price_list,sanctioned_map,lease_deed',
			'project_id'    => 'required|integer'
		]);
		
		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}
		
		// Prepare mail data
		$mailData = $request->all();
		$mailData['submittedVia'] = $request->headers->get('referer');
		
		// Save query
		$data = Query::create($mailData);
		
		if ($data) {
			// Dispatch Job for USER
			if (!empty($request->email)) {
				SendContactEmailJob::dispatch($mailData, $request->email);
			}

			// Dispatch Job for ADMIN
			$mailData['reciever'] = 'admin';

			SendContactEmailJob::dispatch($mailData, 'rohit.mis.360@gmail.com');
			
			// Store form name in session
			session(['form_submitted' => 'popup']);
			
			// File handling
			$project = Project::findOrFail($request->project_id);
			$fileField = null;
			
			switch ($request->download_type) { 
				case 'brochure':
					$fileField = $project->floor_plans_images ?? null;
					break;
				case 'price_list':
					$fileField = $project->price_list ?? null;
					break;
				case 'sanctioned_map':
					$fileField = $project->sanctioned_map ?? null;
					break;
				case 'lease_deed':
					$fileField = $project->lease_deed ?? null;
					break;
				default:
					$fileField = null;
			}
			
			$filePath = $fileField ? storage_path('app/public/' . $fileField) : null;
			
			if ($filePath && file_exists($filePath)) {
				session([
					'form' => 'popup',
					'download_file' => $fileField
				]);
			} else {
				session([
					'form' => 'popup',
					'message' => 'We do not have the file right now, but keep in touch!'
				]);
			}
			
			
			return response()->json(['success' => true, 'redirect_url' => route('thankyou')]);
			
		} else {
			return response()->json(['message' => 'Something Went wrong', 'status' => false], 200);
		}
	}
}

