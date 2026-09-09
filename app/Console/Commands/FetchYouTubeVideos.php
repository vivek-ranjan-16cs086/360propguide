<?php
/**
    * FetchYouTubeVideos Class
    *
    * @Author- Reshu Verma
    * @Contact No.- 8081503903
    * @email- vermareshu0401@gmail.com
    * @App-version 1.0
    * @description user controller
    *
    * @Functions- Command for fetch youtube videos 
    */
namespace App\Console\Commands;

use Illuminate\Console\Command; 
use App\Services\YouTubeService;
use App\Models\YouTubeVideo;
use Illuminate\Support\Facades\Log;

class FetchYouTubeVideos extends Command
{
    protected $signature = 'youtube:fetch-videos';
    protected $description = 'Fetch and update YouTube videos';

    private $youtubeService;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(YouTubeService $youtubeService)
    {
        try {
            // Fetch channel ID from the service
            $channelId = $youtubeService->getChannelId();

            // Get video data from YouTube API
            $videosData = $youtubeService->getChannelVideos($channelId);

            // Check if videos are returned
            if (!empty($videosData['items'])) {
                // Extract video IDs
                $videoIds = array_map(function ($item) {
                    // Ensure 'id' and 'videoId' exist before accessing them
                    if (isset($item['id']['videoId'])) {
                        return $item['id']['videoId'];
                    }
                    return null; // Return null if 'videoId' is not present
                }, $videosData['items']);

                // Clean up null values from video IDs
                $videoIds = array_filter($videoIds);
                $commaSeparatedIds = implode(',', $videoIds);
                \Log::info('youtube search API Response:'. $commaSeparatedIds);
                // Fetch the video details for the given video IDs
                $videos = $youtubeService->getVideosData($commaSeparatedIds);
				YouTubeVideo::query()->truncate();
                // If video data is available, update or create in the database
                if (!empty($videos['items']) && count($videos['items']) > 0) {
                    foreach ($videos['items'] as $item) {
                        if (isset($item['id'])) {
                            // YouTubeVideo::updateOrCreate( 
                                // ['video_id' => $item['id']],
                                // [
                                    // 'title' => $item['snippet']['title'],
                                    // 'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? null,
                                    // 'published_time' => $item['snippet']['publishedAt'] ?? null,
                                    // 'video_id' => $item['id'],
                                    // 'duration' => $item['contentDetails']['duration'] ?? null,
                                    // 'views' => $item['statistics']['viewCount'] ?? null,
                                // ]
                            // );
							$youtubeObj = new YouTubeVideo;

							$youtubeObj->title = $item['snippet']['title'] ?? null;
							$youtubeObj->thumbnail = $item['snippet']['thumbnails']['high']['url'] ?? null;
							$youtubeObj->published_time = $item['snippet']['publishedAt'] ?? null;
							$youtubeObj->video_id = $item['id'] ?? null;
							$youtubeObj->duration = $item['contentDetails']['duration'] ?? null;
							$youtubeObj->views = $item['statistics']['viewCount'] ?? null;

							$youtubeObj->save();
							
                        }
                    }
                    $this->info('YouTube videos updated successfully.');
					\Log::info("Youtube Cron is working fine!");
                } else {
                    $this->warn('No video details found for the provided IDs.');
                }
            } else {
                $this->warn('No videos found for the channel.');
            }
        } catch (\Exception $e) {
            // Log the exception message for debugging
            Log::error('Error fetching YouTube videos: ' . $e->getMessage());

            // Display a friendly error message to the console
            $this->error('An error occurred while fetching or updating YouTube videos. Please check the logs for more details.');
        }
    }

}
