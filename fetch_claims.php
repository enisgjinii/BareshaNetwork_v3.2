<?php

// Include necessary configurations and database connections here
include 'conn-d.php';

// Get user-defined parameters from the GET request
$show = isset($_POST['show']) ? $_POST['show'] : 10;
$pg = isset($_POST['pg']) ? $_POST['pg'] : 1;

// Define the base URL for the API endpoint
$baseUrl = 'https://bareshamusic.sourceaudio.com/api/contentid/claims';

// Define your request parameters
$params = array(
    'token' => '6636-66f549fbe813b2087a8748f2b8243dbc',
    'show' => $show, // Number of claims to show per page (e.g., 10 claims per page)
    'pg' => $pg,    // Page of claim data to retrieve (e.g., page 1)
    // You can add more parameters here as needed
);

// Build the final URL with query parameters
$url = $baseUrl . '?' . http_build_query($params);

// Make a GET request using file_get_contents()
$response = file_get_contents($url);

// Check if the response is valid
if ($response === false) {
    die('Error: Failed to retrieve data from the API');
}

// Decode JSON data
$data = json_decode($response, true);

// Return the JSON data
header('Content-Type: application/json');
echo json_encode(['claim' => $data['claim']]);
