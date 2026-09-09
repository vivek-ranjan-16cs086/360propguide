<?php

	use GeoIp2\Database\Reader;

    // function for date formatting
	if (!function_exists('formatDate')) {
       function formatDate($date) {
          return date('d M Y', strtotime($date));
        }
    }

    if (!function_exists('storageUrl')) {
        function storageUrl(?string $path, string $fallback = 'uploads/project/default.png'): string
        {
            $path = trim(str_replace('\\', '/', (string) $path));
            if ($path === '') {
                return url($fallback);
            }
            if (preg_match('#^https?://#i', $path)) {
                return $path;
            }

            $path = ltrim($path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            if (str_starts_with($path, 'app/public/')) {
                $path = substr($path, 11);
            }
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, 7);
            }
            $path = preg_replace('#/+#', '/', $path);

            $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));

            return url('storage/' . $encoded);
        }
    }

    if (!function_exists('storageFileCandidates')) {
        function storageFileCandidates(string $path): array
        {
            $path = ltrim(str_replace('\\', '/', urldecode($path)), '/');
            $path = preg_replace('#/+#', '/', $path);
            if ($path === '' || str_contains($path, '..')) {
                return [];
            }

            $withoutPublic = str_starts_with($path, 'public/') ? substr($path, 7) : $path;
            $withPublic = str_starts_with($path, 'public/') ? $path : 'public/' . $path;

            return array_values(array_unique([
                storage_path('app/public/' . $withoutPublic),
                storage_path('app/public/' . $path),
                storage_path('public/' . $withoutPublic),
                storage_path('public/' . $path),
                public_path('storage/' . $withoutPublic),
                public_path('storage/' . $path),
                public_path('storage/' . $withPublic),
                storage_path('app/' . $withPublic),
                storage_path('app/' . $path),
            ]));
        }
    }

    // for file uploading
    if(!function_exists('uploadFile')){
		function uploadFile($file, $location, $prefix = '360_prop_guide')
		{
			$fileName = $prefix . '_' . Str::random(10) . '_' . $file->getClientOriginalName();
			$url = $file->storeAs($location, $fileName, 'public');
			return $url;
		}
	}
	
	// for remove file
	if(!function_exists('removeFile')){
		function removeFile($filePath){
			try {
				if (Storage::disk('public')->exists($filePath)) {
					Storage::disk('public')->delete($filePath);
					return true;
				}
				return false;
			} catch (\Exception $e) {
				return false;
			}
		}
	}
	// for data sanitization
	if(!function_exists('clean')){
		function clean($string) {
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
       }
	}


	if(!function_exists('createSlug')){
		function createSlug($string) {
			// Convert the string to lowercase
			$slug = strtolower($string);

			// Remove any characters that are not alphanumeric, spaces, or hyphens
			$slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);

			// Replace multiple spaces or hyphens with a single hyphen
			$slug = preg_replace('/[\s-]+/', '-', $slug);

			// Trim hyphens from the beginning and end of the slug
			$slug = trim($slug, '-');

			return $slug;
		}
	}
	
	//function for format price
	function formatPrice($price) {
		if ($price < 100000) {
			return number_format($price / 1000, 2, '.', ',') . ' K';
		} elseif ($price < 10000000) {
			return number_format($price / 100000, 2, '.', ',') . ' Lakh';
		} else { 
			return number_format($price / 10000000, 2, '.', ',') . ' Cr';
		}
	}


// Youtube video (Reshu Verma)

	if (!function_exists('formatDuration')) {
		/**
		 * Convert an ISO 8601 duration string (PTxMxxS) to a formatted time string (MM:SS).
		 *
		 * @param string $duration
		 * @return string
		 */
		function formatDuration($duration)
		{
			// Extract hours, minutes and seconds using regex (all optional, so PT45S, PT3M, PT1H2M3S etc. all work)
			preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $duration, $matches);

			// Get hours, minutes and seconds from the regex match
			$hours   = isset($matches[1]) ? intval($matches[1]) : 0;
			$minutes = isset($matches[2]) ? intval($matches[2]) : 0;
			$seconds = isset($matches[3]) ? intval($matches[3]) : 0;

			// Return formatted time as H:MM:SS or MM:SS
			if ($hours > 0) {
				return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
			}

			return sprintf("%02d:%02d", $minutes, $seconds);
		}
	}
	if (!function_exists('formatViews')) {
		function formatViews($views)
		{
			if ($views >= 10000000) {
				return round($views / 10000000, 1) . 'Cr'; // Crores
			} elseif ($views >= 100000) {
				return round($views / 100000, 1) . 'L'; // Lakhs
			} elseif ($views >= 1000) {
				return round($views / 1000, 1) . 'K'; // Thousands
			}
			return $views; // Return as is for smaller numbers
		}
	}
	if (!function_exists('formatYoutubeDate')) {
		function formatYoutubeDate($isoDate)
		{
			// Convert ISO 8601 date to a DateTime object
			$date = new DateTime($isoDate);
			$now = new DateTime();

			// Check if the date is more than 1 year ago
			$isMoreThanOneYear = $now->diff($date)->y >= 1;

			// Format the date accordingly
			return $isMoreThanOneYear ? $date->format('d M Y') : $date->format('d M');
		}
	}
	if (!function_exists('clean')) {
		function clean($string)
		{
			return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
		}
	}


	if (!function_exists('formatBHK')) {
		function formatBHK($data)
		{
			$bhkList = json_decode($data, true);
			return collect($bhkList)
				->map(fn($item) => str_replace(' ', '', $item))
				->implode(', ');
		}
	}

	if (!function_exists('formatToString')) { 
		function formatToString($value)
		{
			// Handle arrays: recursively format and return as comma-separated string
			if (is_array($value)) {
				$formattedArray = array_map('formatToString', $value);
				return implode(', ', $formattedArray);
			}

			// Normalize BHK formats like "2BHK", "3 BHK", etc. → "2 BHK"
			if (preg_match('/\d+\s*bhk/i', $value)) {
				// Extract number part and "BHK" separately
				preg_match('/(\d+)\s*bhk/i', $value, $matches);
				return $matches[1] . ' BHK';
			}

			// Format snake_case → Title Case
			if (strpos($value, '_') !== false) {
				return ucwords(str_replace('_', ' ', strtolower($value)));
			}

			return $value;
		}
	}
