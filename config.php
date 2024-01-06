<?php
include('vendor/autoload.php');

// Define constants for configuration
define('CONFIG_FILE', 'client.json');
define('LOCALHOST_URI', "http://localhost/BareshaNetwork_v3.2/kycu_1.php");
define('ONLINE_URI', "https://paneli.bareshaoffice.com/kycu_1.php");

// Check if configuration file exists
if (!file_exists(CONFIG_FILE) || !is_readable(CONFIG_FILE)) {
    throw new Exception("Configuration file missing or not readable");
}

// Cache client configuration
$clientConfig = json_decode(file_get_contents(CONFIG_FILE), true);
$clientId = $clientConfig['web']['client_id'];
$clientSecret = $clientConfig['web']['client_secret'];

$client = new Google_Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);

// Set redirect URI based on environment
$redirectUri = ($_SERVER['HTTP_HOST'] === 'localhost') ? LOCALHOST_URI : ONLINE_URI;
$client->setRedirectUri($redirectUri);

// Set scopes in a single call
$client->setScopes(["email", "profile"]);
