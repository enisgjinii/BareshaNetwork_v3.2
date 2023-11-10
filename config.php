<?php
include('vendor/autoload.php');

// Read the client configuration from client.json
$clientConfig = json_decode(file_get_contents('client.json'), true);

// Extract client ID and client secret from the configuration
$clientId = $clientConfig['web']['client_id'];
$clientSecret = $clientConfig['web']['client_secret'];

$client = new Google_Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);

// Determine the environment (online server or localhost)
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // If running on localhost, use the localhost URI
    $redirectUri = "http://localhost/BareshaNetwork_v3.2/kycu_1.php";
} else {
    // If running on a server, use the online URI
    $redirectUri = "https://paneli.bareshaoffice.com/kycu_1.php";
}

$client->setRedirectUri($redirectUri);

// Add the required scopes
$client->addScope("email");
$client->addScope("profile");
