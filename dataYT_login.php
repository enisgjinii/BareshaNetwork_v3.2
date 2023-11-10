<?php

// Load the Google API client library.
require_once __DIR__ . '/google-api/vendor/autoload.php';

// Create a Google API client object.
$client = new Google_Client();

// Set the application name.
$client->setApplicationName('API code samples');

// Set the OAuth 2.0 scopes.
$client->setScopes([
    'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/yt-analytics.readonly',
    'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
]);

// Redirect the user to the OAuth 2.0 authorization page.
$authUrl = $client->createAuthUrl();

?>

<html>

<head>
    <title>Log in with Google</title>
</head>

<body>
    <h1>Log in with Google</h1>
    <p>
        <a href="<?php echo $authUrl; ?>">Log in with Google</a>
    </p>
</body>

</html>