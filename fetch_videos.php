<?php
// fetch_videos.php

// Start the session and include the database connection
session_start();
include "conn-d.php"; // Adjust the path if necessary

// Define YouTube API Key
$youtubeApiKey = 'AIzaSyBQD3hhckJv5uxPcbRk3b8nlNogG9781Lk'; // Replace with your actual YouTube API key

// Define cache parameters
define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_LIFETIME', 3600); // 1 hour

if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}

// Function to get uploads playlist ID
function getUploadsPlaylistId($channelId, $apiKey)
{
    $apiUrl = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id={$channelId}&key={$apiKey}";
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $apiResponse = curl_exec($ch);
    if ($apiResponse === false) {
        curl_close($ch);
        return ['error' => 'Failed to fetch channel details from YouTube API.'];
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode($apiResponse, true);

    if ($httpCode !== 200) {
        if (isset($data['error']['errors'][0]['reason']) && $data['error']['errors'][0]['reason'] === 'quotaExceeded') {
            return ['error' => 'YouTube Data API quota has been exceeded. Please try again later.'];
        } else {
            $errorMessage = isset($data['error']['message']) ? $data['error']['message'] : 'An unknown error occurred.';
            return ['error' => 'YouTube Data API Error: ' . htmlspecialchars($errorMessage)];
        }
    }

    if (isset($data['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
        return ['uploadsPlaylistId' => $data['items'][0]['contentDetails']['relatedPlaylists']['uploads']];
    } else {
        return ['error' => 'Uploads playlist not found for this channel.'];
    }
}

// Function to fetch all playlist videos
function fetchAllPlaylistVideos($playlistId, $apiKey)
{
    $allVideos = [];
    $nextPageToken = '';
    $maxResultsPerPage = 50;

    while (true) {
        $apiUrl = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId={$playlistId}&maxResults={$maxResultsPerPage}&pageToken={$nextPageToken}&key={$apiKey}";
        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $apiResponse = curl_exec($ch);
        if ($apiResponse === false) {
            curl_close($ch);
            return ['error' => 'Failed to fetch videos from YouTube API.'];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($apiResponse, true);

        if ($httpCode !== 200) {
            if (isset($data['error']['errors'][0]['reason']) && $data['error']['errors'][0]['reason'] === 'quotaExceeded') {
                return ['error' => 'YouTube Data API quota has been exceeded. Please try again later.'];
            } else {
                $errorMessage = isset($data['error']['message']) ? $data['error']['message'] : 'An unknown error occurred.';
                return ['error' => 'YouTube Data API Error: ' . htmlspecialchars($errorMessage)];
            }
        }

        if (!empty($data['items'])) {
            foreach ($data['items'] as $video) {
                if (isset($video['contentDetails']['videoId'])) {
                    $allVideos[] = [
                        'videoId' => $video['contentDetails']['videoId'],
                        'title' => $video['snippet']['title'],
                        'thumbnail' => $video['snippet']['thumbnails']['medium']['url'],
                        'publishedAt' => $video['snippet']['publishedAt']
                    ];
                }
            }
        }

        if (isset($data['nextPageToken'])) {
            $nextPageToken = $data['nextPageToken'];
        } else {
            break;
        }
    }

    return ['items' => $allVideos];
}

// Function to get cache
function get_cache($channelId)
{
    $cacheFile = CACHE_DIR . '/' . $channelId . '.json';
    if (file_exists($cacheFile)) {
        $fileTime = filemtime($cacheFile);
        if ((time() - $fileTime) < CACHE_LIFETIME) {
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }
    }
    return false;
}

// Function to set cache
function set_cache($channelId, $data)
{
    $cacheFile = CACHE_DIR . '/' . $channelId . '.json';
    file_put_contents($cacheFile, json_encode($data));
}

// Handle AJAX requests for fetching videos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch_videos') {
    // Ensure content is JSON
    header('Content-Type: application/json');

    $channelId = isset($_POST['channelId']) ? trim($_POST['channelId']) : '';
    if (empty($channelId)) {
        echo json_encode(['error' => 'Invalid channel ID.']);
        exit;
    }

    // Check cache
    $videosData = get_cache($channelId);
    if ($videosData === false) {
        // Fetch uploads playlist ID
        $playlistData = getUploadsPlaylistId($channelId, $youtubeApiKey);
        if (isset($playlistData['error'])) {
            echo json_encode(['error' => $playlistData['error']]);
            exit;
        }

        $uploadsPlaylistId = $playlistData['uploadsPlaylistId'];
        $videosData = fetchAllPlaylistVideos($uploadsPlaylistId, $youtubeApiKey);
        if (!isset($videosData['error'])) {
            set_cache($channelId, $videosData);
        }
    }

    if (isset($videosData['error'])) {
        echo json_encode(['error' => $videosData['error']]);
        exit;
    }

    // Get channel name from DB
    $channelIdEscaped = mysqli_real_escape_string($conn, $channelId);
    $sqlChannel = "SELECT emri FROM klientet WHERE youtube = '$channelIdEscaped' LIMIT 1";
    $resultChannel = mysqli_query($conn, $sqlChannel);

    if (!$resultChannel || mysqli_num_rows($resultChannel) === 0) {
        echo json_encode(['error' => 'Channel name not found.']);
        exit;
    }

    $rowChannel = mysqli_fetch_assoc($resultChannel);
    $channelName = $rowChannel['emri'];
    $channelNameEscaped = mysqli_real_escape_string($conn, $channelName);

    // Fetch inserted song titles from 'ngarkimi' table
    $sqlEmri = "SELECT emri FROM ngarkimi WHERE klienti = '$channelNameEscaped'";
    $resultEmri = mysqli_query($conn, $sqlEmri);
    $insertedTitles = [];
    if ($resultEmri) {
        while ($rowEmri = mysqli_fetch_assoc($resultEmri)) {
            // Store titles in lowercase for case-insensitive comparison
            $insertedTitles[] = strtolower($rowEmri['emri']);
        }
    }

    // Alternatively, if you prefer to match using the 'muzika' field:

    $sqlMuzika = "SELECT muzika FROM ngarkimi WHERE klienti = '$channelNameEscaped'";
    $resultMuzika = mysqli_query($conn, $sqlMuzika);
    $insertedTitles = [];
    if ($resultMuzika) {
        while ($rowMuzika = mysqli_fetch_assoc($resultMuzika)) {
            // Store titles in lowercase for case-insensitive comparison
            $insertedTitles[] = strtolower($rowMuzika['muzika']);
        }
    }


    // **Optional:** If you want to match both 'emri' and 'muzika' fields:
    /*
    $sqlCombined = "SELECT emri, muzika FROM ngarkimi WHERE klienti = '$channelNameEscaped'";
    $resultCombined = mysqli_query($conn, $sqlCombined);
    $insertedTitles = [];
    if ($resultCombined) {
        while ($rowCombined = mysqli_fetch_assoc($resultCombined)) {
            if (!empty($rowCombined['emri'])) {
                $insertedTitles[] = strtolower($rowCombined['emri']);
            }
            if (!empty($rowCombined['muzika'])) {
                $insertedTitles[] = strtolower($rowCombined['muzika']);
            }
        }
    }
    */

    // Build HTML output
    $html = "<table class='table table-bordered table-hover'>
                <thead class='table-secondary'>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Published At</th>
                        <th>Inserted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";

    if (!empty($videosData['items'])) {
        foreach ($videosData['items'] as $video) {
            $videoId = htmlspecialchars($video['videoId']);
            $title = htmlspecialchars($video['title']);
            $thumbnail = htmlspecialchars($video['thumbnail']);
            $publishedAt = date('F j, Y', strtotime($video['publishedAt']));
            $videoUrl = "https://www.youtube.com/watch?v={$videoId}";

            // **Matching Logic:**
            // 1. Exact Match (Case-Insensitive)
            $isInserted = in_array(strtolower($video['title']), $insertedTitles);

            // 2. Partial Match (Optional)
            // Uncomment the following block if you prefer partial matching
            /*
            $isInserted = false;
            foreach ($insertedTitles as $insertedTitle) {
                if (stripos($video['title'], $insertedTitle) !== false) {
                    $isInserted = true;
                    break;
                }
            }
            */

            $insertedMark = $isInserted ? "<span class='text-success'>&#10004;</span>" : "<span class='text-danger'>&#10008;</span>";

            $html .= "<tr>
                        <td><a href='{$videoUrl}' target='_blank'><img src='{$thumbnail}' alt='{$title}' class='img-thumbnail'></a></td>
                        <td>{$title}</td>
                        <td>{$publishedAt}</td>
                        <td class='text-center'>{$insertedMark}</td>
                        <td><a href='{$videoUrl}' target='_blank' class='btn btn-secondary input-custom-css px-3 py-2'>Watch Video</a></td>
                    </tr>";
        }
    } else {
        $html .= "<tr><td colspan='5' class='text-center'>No videos found for this channel.</td></tr>";
    }

    $html .= "</tbody></table>";

    echo json_encode(['html' => $html, 'channelName' => htmlspecialchars($channelName)]);
    exit;
}

// If the script reaches here without processing AJAX, return an error
echo json_encode(['error' => 'Invalid request.']);
exit;
