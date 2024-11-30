<?php
header('Content-Type: application/json');
require_once 'conn-d.php';

// Enable error logging (disable display in production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'data/error_log.txt');

// Function to send JSON responses
function sendResponse($success, $data = [], $message = '')
{
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    exit;
}

// Function to log errors
function logError($message)
{
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[{$timestamp}] {$message}\n";
    file_put_contents('data/error_log.txt', $formattedMessage, FILE_APPEND);
}

// Function to fetch YouTube video details using cURL
function fetchYouTubeVideoDetails($videoId, $youtubeApiKey)
{
    $youtubeApiUrl = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id={$videoId}&key={$youtubeApiKey}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $youtubeApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout

    $youtubeResponse = curl_exec($ch);
    if ($youtubeResponse === FALSE) {
        $error = curl_error($ch);
        curl_close($ch);
        logError("cURL Error during YouTube API request: {$error}");
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        logError("YouTube API request failed with HTTP Code: {$httpCode}");
        return false;
    }

    $youtubeData = json_decode($youtubeResponse, true);
    if (empty($youtubeData['items'])) {
        return false;
    }

    return $youtubeData['items'][0]['snippet'];
}

// Function to search Spotify
function searchSpotify($artist, $title)
{
    // Spotify API Credentials (replace with your actual credentials)
    $clientId = '143109635b69494895d4201d185dac16';
    $clientSecret = '0522b85e172644d39f22b0289f75c967';

    // Get access token
    $tokenUrl = 'https://accounts.spotify.com/api/token';
    $authHeader = base64_encode("{$clientId}:{$clientSecret}");
    $headers = [
        "Authorization: Basic {$authHeader}",
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $postFields = 'grant_type=client_credentials';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout

    $tokenResponse = curl_exec($ch);
    if ($tokenResponse === FALSE) {
        $error = curl_error($ch);
        curl_close($ch);
        logError("cURL Error during Spotify token request: {$error}");
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        logError("Spotify token request failed with HTTP Code: {$httpCode}");
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    $tokenData = json_decode($tokenResponse, true);
    if (!isset($tokenData['access_token'])) {
        logError("Spotify token response missing access_token: " . $tokenResponse);
        return false;
    }

    $accessToken = $tokenData['access_token'];

    // Refine search query
    $query = urlencode("track:\"{$title}\" artist:\"{$artist}\"");
    $searchUrl = "https://api.spotify.com/v1/search?q={$query}&type=track&limit=1";

    $searchHeaders = [
        "Authorization: Bearer {$accessToken}"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $searchHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set timeout

    $searchResponse = curl_exec($ch);
    if ($searchResponse === FALSE) {
        $error = curl_error($ch);
        curl_close($ch);
        logError("cURL Error during Spotify search request: {$error}");
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        logError("Spotify search request failed with HTTP Code: {$httpCode}");
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    $searchData = json_decode($searchResponse, true);
    if (isset($searchData['tracks']['items']) && count($searchData['tracks']['items']) > 0) {
        $track = $searchData['tracks']['items'][0];
        return [
            'name' => 'Spotify',
            'url' => $track['external_urls']['spotify'] ?? '#',
            'logo' => 'assets/logos/spotify.jpg', // Ensure this path is correct
            'description' => 'Spotify is a digital music service that gives you access to millions of songs.'
        ];
    }

    return false;
}

// Function to search other platforms
function searchOtherPlatforms($artist, $title)
{
    $platforms = [];

    // iTunes
    $iTunesUrl = "https://itunes.apple.com/search?term=" . urlencode("{$artist} {$title}") . "&media=music&limit=1";
    $iTunesResponse = file_get_contents($iTunesUrl);
    if ($iTunesResponse !== FALSE) {
        $iTunesData = json_decode($iTunesResponse, true);
        if (isset($iTunesData['results'][0])) {
            $platforms[] = [
                'name' => 'iTunes',
                'url' => $iTunesData['results'][0]['trackViewUrl'] ?? '#',
                'logo' => 'assets/logos/itunes.png',
                'description' => 'iTunes is a media player, media library, and mobile device management application.'
            ];
        }
    }

    // Add other platforms similarly if desired...

    return $platforms;
}

// Function to search all platforms
function searchAllPlatforms($artist, $title)
{
    $platformDetails = [];

    // Spotify
    $spotifyData = searchSpotify($artist, $title);
    if ($spotifyData) {
        $platformDetails[] = $spotifyData;
    }

    // YouTube Music (construct search URL)
    $youtubeMusicUrl = "https://music.youtube.com/search?q=" . urlencode("{$artist} {$title}");
    $platformDetails[] = [
        'name' => 'YouTube Music',
        'url' => $youtubeMusicUrl,
        'logo' => 'assets/logos/youtube-music.jpg',
        'description' => 'YouTube Music is a music streaming service developed by YouTube.'
    ];

    // Other platforms
    $otherPlatforms = searchOtherPlatforms($artist, $title);
    $platformDetails = array_merge($platformDetails, $otherPlatforms);

    return $platformDetails;
}

// Validate input
if (!isset($_POST['linku']) || empty(trim($_POST['linku']))) {
    sendResponse(false, [], 'No link provided.');
}

$link = trim($_POST['linku']);

if (!filter_var($link, FILTER_VALIDATE_URL)) {
    sendResponse(false, [], 'Invalid URL.');
}

$parsedUrl = parse_url($link);
if (!isset($parsedUrl['host'])) {
    sendResponse(false, [], 'Invalid URL format.');
}

$host = $parsedUrl['host'];

$songTitle = '';
$artistName = '';
$thumbnail = '';
$videoId = '';
$videoDescription = '';
$channelName = '';
$channelID = '';
$publishDate = '';

// Fetch YouTube Video Details
if (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) {
    if (strpos($host, 'youtu.be') !== false) {
        $videoId = ltrim($parsedUrl['path'], '/');
    } else {
        parse_str($parsedUrl['query'], $queryParams);
        $videoId = $queryParams['v'] ?? '';
    }

    if (empty($videoId)) {
        sendResponse(false, [], 'Unable to extract video ID from YouTube link.');
    }

    // Replace with your actual YouTube API Key
    $youtubeApiKey = 'AIzaSyCRFtIfiEyeYmCrCZ8Bvy8Z4IPBy1v2iwo';

    $videoSnippet = fetchYouTubeVideoDetails($videoId, $youtubeApiKey);
    if (!$videoSnippet) {
        sendResponse(false, [], 'Unable to fetch video details from YouTube.');
    }

    $videoTitle = $videoSnippet['title'] ?? '';
    $thumbnail = $videoSnippet['thumbnails']['high']['url'] ?? '';
    $videoDescription = $videoSnippet['description'] ?? '';
    $channelName = $videoSnippet['channelTitle'] ?? '';
    $channelID = $videoSnippet['channelId'] ?? '';
    $publishDateRaw = $videoSnippet['publishedAt'] ?? '';

    if (!empty($publishDateRaw)) {
        $publishDate = date('Y-m-d', strtotime($publishDateRaw));
    }

    // Extract artist name and song title
    if (strpos($videoTitle, ' - ') !== false) {
        list($artistName, $songTitle) = explode(' - ', $videoTitle, 2);
    } else {
        $artistName = $channelName; // Default to channel name
        $songTitle = $videoTitle;
    }

    $artistName = trim($artistName);
    $songTitle = trim($songTitle);

    // Clean song title (remove text within parentheses or brackets)
    $songTitle = preg_replace('/\s*\(.*?\)\s*/', '', $songTitle); // Remove text within parentheses
    $songTitle = preg_replace('/\s*\[.*?\]\s*/', '', $songTitle); // Remove text within brackets
    $songTitle = trim($songTitle);

    if (empty($songTitle)) {
        sendResponse(false, [], 'Unable to extract song title from the link.');
    }
} else {
    sendResponse(false, [], 'Unsupported platform. Please provide a YouTube link.');
}

// Search all platforms
$platformDetails = searchAllPlatforms($artistName, $songTitle);

// Prepare response data
$responseData = [
    'title' => $songTitle,
    'channelID' => $channelID,
    'artist' => $artistName,
    'channel' => $channelName,
    'publishedDate' => $publishDate,
    'thumbnail' => $thumbnail,
    'description' => $videoDescription,
    'platforms' => $platformDetails
];

// Fetch client information based on YouTube channel ID
if (!empty($channelID)) {
    $stmt = $conn->prepare("SELECT id, emri FROM klientet WHERE youtube = ?");
    if ($stmt) {
        $stmt->bind_param("s", $channelID);
        $stmt->execute();
        $stmt->bind_result($id, $emri);
        if ($stmt->fetch()) {
            $responseData['client'] = [
                'id' => $id,
                'name' => $emri
            ];
        }
        $stmt->close();
    }
}

// Send JSON response
sendResponse(true, $responseData);
?>
