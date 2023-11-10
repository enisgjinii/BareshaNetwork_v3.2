<?php

// Function to get the YouTube video title from the URL using the API
function getYouTubeVideoTitle($videoUrl, $apiKey)
{
    // Extract the video ID from the URL
    parse_str(parse_url($videoUrl, PHP_URL_QUERY), $videoId);

    // Make a request to the YouTube Data API to get video information
    $apiUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id={$videoId['v']}&key={$apiKey}";
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);

    // Get the video title from the API response
    if (isset($data['items'][0]['snippet']['title'])) {
        return $data['items'][0]['snippet']['title'];
    } else {
        return "Video title not available";
    }
}
