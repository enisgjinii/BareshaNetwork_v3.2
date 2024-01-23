<?php
require_once 'vendor/autoload.php';
session_start();

$config = require_once 'second_config.php';

$client = initializeGoogleClient($config);

if (isset($_GET['code'])) {
    handleAuthentication($client);
}

// If the user is not authenticated, display the authentication link
echo '<a href="' . $client->createAuthUrl() . '">Click here to authenticate</a>';

function initializeGoogleClient($config)
{
    $client = new Google_Client();
    $client->setClientId($config['client_id']);
    $client->setClientSecret($config['client_secret']);
    $client->setRedirectUri($config['redirect_uri']);
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');

    $client->addScope([
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/youtubepartner',
        'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
        'https://www.googleapis.com/auth/yt-analytics.readonly'
    ]);

    return $client;
}

function handleAuthentication($client)
{
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        $youtube = new Google_Service_YouTube($client);
        $channels = $youtube->channels->listChannels('snippet', ['mine' => true]);
        $channel = $channels->items[0];
        $channelId = $channel->id;
        $channelName = $channel->snippet->title;

        if (isset($token['refresh_token'])) {
            $refreshToken = $token['refresh_token'];
            storeRefreshTokenInDatabase($refreshToken, $channelId, $channelName);
        }

        $_SESSION['refresh_token'] = $refreshToken;

        echo "<script>console.log('Refresh Token: " . json_encode($refreshToken) . "');</script>";
        echo "<script>console.log('Channel ID: $channelId');</script>";
        echo "<script>console.log('Channel Name: $channelName');</script>";

        // Redirect to a different page after authentication
        header('Location: invoice.php');
        exit;
    } catch (Google\Service\Exception $e) {
        echo '<pre>';
        print_r(json_decode($e->getMessage()));
        echo '</pre>';
    }
}

function storeRefreshTokenInDatabase($refreshToken, $channelId, $channelName)
{
    $config = require 'second_config.php';
    $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $refreshToken = $conn->real_escape_string($refreshToken);
    $channelId = $conn->real_escape_string($channelId);
    $channelName = $conn->real_escape_string($channelName);

    $sql = "INSERT INTO refresh_tokens (token, channel_id, channel_name) VALUES ('$refreshToken', '$channelId', '$channelName')";
    $conn->query($sql);

    $conn->close();
}
