<?php
// Your YouTube Data API key
$api_key = 'AIzaSyCjlRRPMTbGcM_QE081YCy4zHKI9sUaZTg';

// Get the YouTube channel ID from the AJAX request
$channel_id = $_POST['yt']; // Make sure to validate and sanitize user input

// Make a request to the YouTube Data API to get channel information
$url = "https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id=$channel_id&key=$api_key";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and store the response
$response = curl_exec($ch);

// Check if the request was successful
if ($response !== false) {
    // Decode the JSON response
    $data = json_decode($response, true);

    if ($data && isset($data['items'][0])) {
        // Extract channel information
        $channel_info = $data['items'][0]['snippet'];
        $channel_statistics = $data['items'][0]['statistics'];

        // Display the channel information
        echo '<h1>' . $channel_info['title'] . '</h1>';
        echo '<p>Channel Name: ' . $channel_info['title'] . '</p>';
        echo '<p>Channel Description: ' . $channel_info['description'] . '</p>';
        echo '<p>Number of Subscribers: ' . $channel_statistics['subscriberCount'] . '</p>';
        echo '<img src="' . $channel_info['thumbnails']['default']['url'] . '" alt="' . $channel_info['title'] . '">';
    } else {
        echo 'Error: Unable to fetch channel information.';
    }
} else {
    echo 'Error: cURL request failed.';
}

// Close cURL session
curl_close($ch);
?>
