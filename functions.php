<?php
// functions.php

/**
 * Fetch YouTube Data using cURL with Error Handling
 *
 * @param mysqli $conn        The MySQLi connection object.
 * @param string $channelId   The YouTube channel ID.
 * @param string $part        The part of the YouTube API to fetch.
 * @return array|null          The decoded JSON response or null on failure.
 */
function fetchYouTubeData($conn, $channelId, $part) {
    $api_key = "AIzaSyBrE0kFGTQJwn36FeR4NIyf4FEw2HqSSIQ"; // Ideally, store this securely

    $url = "https://www.googleapis.com/youtube/v3/channels?part={$part}&id={$channelId}&key={$api_key}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        curl_close($ch);
        return null;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        error_log("YouTube API responded with HTTP code {$http_code}");
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    $data = json_decode($response, true);
    return $data['items'][0] ?? null;
}

/**
 * Escape Output to Prevent XSS
 *
 * @param string $string The string to escape.
 * @return string        The escaped string.
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
