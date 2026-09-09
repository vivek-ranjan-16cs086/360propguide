<?php
/**
    * YouTubeService Class
    *
    * @Author- Reshu Verma
    * @Contact No.- 8081503903
    * @email- vermareshu0401@gmail.com
    * @App-version 1.0
    * @description user controller
    *
    * @Functions- Youtube Services file 
    */
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Credential;

class YouTubeService
{
    private $apiKey;
    private $channelId;

    public function __construct()
    {
        //$this->apiKey = env('YOUTUBE_API_KEY');
        $this->loadCredentials();
    }

    /**
     * Load credentials from database
     */
    private function loadCredentials()
    {
	  try
		{
          $credentials = Credential::firstOrFail();
          $this->apiKey = $credentials->api_key;
          $this->channelId = $credentials->channel_id;
		}
		catch(\Exception $e){
			\Log::error('Error loading credentials: ' . $e->getMessage());
		}
    }


    /**
     * Fetch channel videos using cURL.
     */
    public function getChannelVideos($channelId, $order = 'date', $maxResults = 20)
    {
        $url = 'https://www.googleapis.com/youtube/v3/search';
        $params = [
            'part' => 'id',
            'channelId' => $this->channelId,
            'maxResults' => $maxResults,
            'order' => $order,
            'key' => $this->apiKey,
        ];

        return $this->makeCurlRequest($url, $params);
    }

    /**
     * Fetch video data using cURL.
     */
    public function getVideosData($id)
    {
        $url = 'https://www.googleapis.com/youtube/v3/videos';
        $params = [
            'part' => 'snippet,contentDetails,statistics',
            'id' => $id,
            'key' => $this->apiKey,
        ];

        return $this->makeCurlRequest($url, $params);
    }

    /**
     * Make a cURL GET request.
     */
    protected function makeCurlRequest($url, $params)
    {
        // Build the query string.
        $queryString = http_build_query($params);
        $urlWithParams = $url . '?' . $queryString;

        // Initialize cURL.
        $ch = curl_init();

        // Set cURL options.
        curl_setopt($ch, CURLOPT_URL, $urlWithParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request.
        $response = curl_exec($ch);

        // Check for errors.
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => $error];
        }

        // Close cURL and decode the response.
        curl_close($ch);
        return json_decode($response, true);
    }
    public function getChannelId()
    {
        return $this->channelId;
    }

}
