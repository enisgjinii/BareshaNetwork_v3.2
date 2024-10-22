<?php
header('Content-Type: application/json');

// Check if 'video_id' parameter is set
if (!isset($_GET['video_id']) || empty($_GET['video_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No video ID provided.'
    ]);
    exit;
}

$video_id = htmlspecialchars($_GET['video_id']);
$api_key = "AIzaSyBQeKTHOfJHUc92IYtvHzQvj-vFXysqMqQ"; // Replace with your API key

// Function to fetch comments for a video with caching
function getComments($api_key, $video_id)
{
    $cache_file = 'cache/comments_' . $video_id . '.json';
    $cache_time = 3600; // 1 hour

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        $data = file_get_contents($cache_file);
    } else {
        $comments = [];
        $nextPageToken = '';
        $maxComments = 20; // Limit to first 20 comments to reduce load time

        do {
            $apiUrl = 'https://www.googleapis.com/youtube/v3/commentThreads?key=' . $api_key . '&textFormat=plainText&part=snippet&videoId=' . $video_id . '&maxResults=100&pageToken=' . $nextPageToken;
            $response = @file_get_contents($apiUrl);
            if ($response === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch comments.'
                ];
            }

            $data = json_decode($response, true);
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $comment = [
                        'author' => htmlspecialchars($item['snippet']['topLevelComment']['snippet']['authorDisplayName']),
                        'text' => htmlspecialchars($item['snippet']['topLevelComment']['snippet']['textDisplay']),
                        'publishedAt' => date('F j, Y, g:i a', strtotime($item['snippet']['topLevelComment']['snippet']['publishedAt']))
                    ];
                    $comments[] = $comment;
                    if (count($comments) >= $maxComments) {
                        break 2; // Exit both foreach and do-while
                    }
                }
            }

            $nextPageToken = isset($data['nextPageToken']) ? $data['nextPageToken'] : '';
        } while (!empty($nextPageToken) && count($comments) < $maxComments);

        // Cache the comments data
        file_put_contents($cache_file, json_encode($comments));
        $data = json_encode($comments);
    }

    return [
        'success' => true,
        'comments' => json_decode($data, true)
    ];
}

$result = getComments($api_key, $video_id);
echo json_encode($result);
?>
