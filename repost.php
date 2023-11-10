<?php
ob_start();

include 'partials/header.php';


?>
<?php

// Replace these with your actual credentials
$clientID = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$redirectURI = 'YOUR_REDIRECT_URI';

// Step 1: Redirect users to SoundCloud's authorization endpoint
if (!isset($_GET['code'])) {
    $authorizationUrl = 'https://soundcloud.com/connect' .
        '?client_id=' . urlencode($clientID) .
        '&redirect_uri=' . urlencode($redirectURI) .
        '&response_type=code' .
        '&scope=non-expiring';
    header('Location: ' . $authorizationUrl);
    exit();
}

// Step 2: Exchange authorization code for an access token
$authorizationCode = $_GET['code'];
$accessTokenUrl = 'https://api.soundcloud.com/oauth2/token';
$accessTokenParams = array(
    'client_id' => $clientID,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectURI,
    'code' => $authorizationCode,
    'grant_type' => 'authorization_code'
);

$ch = curl_init($accessTokenUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $accessTokenParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$accessTokenData = json_decode($response, true);

// Step 3: Make API request to retrieve earnings statements
$apiUrl = 'https://api.soundcloud.com/me/statements';
$headers = array(
    'Authorization: Bearer ' . $accessTokenData['access_token'],
    'Accept: application/json',
);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Step 4: Process and display the earnings statements
$earningsStatements = json_decode($response, true);

// Display the statements
if (isset($earningsStatements['collection'])) {
    foreach ($earningsStatements['collection'] as $statement) {
        // Process and display the statement data
        echo 'Statement ID: ' . $statement['id'] . '<br>';
        echo 'Period Start: ' . $statement['period']['start'] . '<br>';
        echo 'Period End: ' . $statement['period']['end'] . '<br>';
        // Display other relevant information
        echo '<br>';
    }
} else {
    echo 'No earnings statements found.';
}

// Flush the output buffer and turn off output buffering
ob_flush();
ob_end_clean();
?>
<?php include 'partials/footer.php' ?>