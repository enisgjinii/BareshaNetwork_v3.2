<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set appropriate headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust as needed for security

// Function to send JSON responses
function send_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

// Retrieve and sanitize the 'amount' parameter
$amount = filter_input(INPUT_GET, 'amount', FILTER_VALIDATE_FLOAT);

if ($amount === false || $amount === null) {
    send_response(["error" => "Invalid or missing 'amount' parameter. It must be a numeric value."], 400);
}

// Optional: Define minimum and maximum allowed amounts
$min_amount = 0.01;
$max_amount = 1000000;

if ($amount < $min_amount || $amount > $max_amount) {
    send_response(["error" => "The 'amount' must be between $min_amount and $max_amount."], 400);
}

// **Hardcode your API key here**
$api_key = '7ac9d0d8-2c2a1729-0a51382b-b85cd112'; // Replace with your actual API key

if (empty($api_key)) {
    send_response(["error" => "Server configuration error: API key not set."], 500);
}

// Prepare the API request URL
$api_url = sprintf(
    "https://api.exconvert.com/convert?from=USD&to=EUR&amount=%.2f&access_key=%s",
    $amount,
    urlencode($api_key)
);

// Initialize cURL
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true, // Follow redirects
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'User-Agent: YourAppName/1.0' // Replace with your application's name and version
    ],
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_SSL_VERIFYPEER => true,
]);

// Execute the API request
$response = curl_exec($curl);
$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);

// Close cURL session
curl_close($curl);

// Handle cURL errors
if ($curl_error) {
    // Log the error message to a file or monitoring system
    error_log("cURL Error: " . $curl_error);
    send_response(["error" => "Failed to communicate with the currency conversion service."], 502);
}

// Handle HTTP errors
if ($http_status >= 400) {
    // Log the HTTP error
    error_log("API responded with status code $http_status. Response: $response");
    send_response(["error" => "Currency conversion service returned an error (HTTP $http_status)."], $http_status);
}

// Decode the API response
$decoded_response = json_decode($response, true);

// Handle JSON decoding errors
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON Decode Error: " . json_last_error_msg());
    send_response(["error" => "Invalid response format from the currency conversion service."], 502);
}

// Check for API-specific errors in the response
if (isset($decoded_response['error'])) {
    error_log("API Error: " . json_encode($decoded_response['error']));
    send_response(["error" => "Currency conversion service error: " . $decoded_response['error']['info']], 502);
}

// Successfully retrieved and decoded the conversion data
send_response($decoded_response);
?>
