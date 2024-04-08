<?php
// Function to get all videos from the channel with pagination
function getAllVideos($api_key, $channel_id)
{
    $videos = array();
    $nextPageToken = '';
    do {
        // Fetch videos from YouTube Data API
        $apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?key=' . $api_key . '&channelId=' . $channel_id . '&part=snippet,id&order=date&maxResults=50&pageToken=' . $nextPageToken);
        if ($apiData !== false) {
            $api_response_decoded = json_decode($apiData, true);
            if (!empty($api_response_decoded['items'])) {
                foreach ($api_response_decoded['items'] as $item) {
                    if (isset($item['id']['videoId'])) {
                        $video = array(
                            'id' => $item['id']['videoId'],
                            'title' => $item['snippet']['title'],
                            'description' => $item['snippet']['description'],
                            'thumbnail' => $item['snippet']['thumbnails']['high']['url']
                        );
                        // Fetch video statistics
                        $gc = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=statistics&id=' . $item['id']['videoId'] . '&key=' . $api_key);
                        $gcc = json_decode($gc, true);
                        if (!empty($gcc['items'])) {
                            $video['viewCount'] = $gcc['items'][0]['statistics']['viewCount'];
                            $video['likeCount'] = $gcc['items'][0]['statistics']['likeCount'];
                            $video['dislikeCount'] = $gcc['items'][0]['statistics']['dislikeCount'];
                            $video['commentCount'] = $gcc['items'][0]['statistics']['commentCount'];
                        }
                        $videos[] = $video;
                    }
                }
            }
            $nextPageToken = isset($api_response_decoded['nextPageToken']) ? $api_response_decoded['nextPageToken'] : '';
        } else {
            // Error handling for API request failure
            echo '<div class="alert alert-danger" role="alert">Failed to fetch videos from YouTube. Please try again later.</div>';
            include 'partials/footer.php';
            exit;
        }
    } while (!empty($nextPageToken));
    return $videos;
}
