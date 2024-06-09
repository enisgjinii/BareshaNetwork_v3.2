<?php
// get_conversion_rate.php

$amount = 1; // converting 1 USD to get the rate
$apiUrl = "https://api.exconvert.com/convert?from=USD&to=EUR&amount=" . $amount . "&access_key=YOUR_ACCESS_KEY";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['result']['EUR'])) {
        echo json_encode(['rate' => $data['result']['EUR']]);
    } else {
        echo json_encode(['error' => 'Could not fetch rate']);
    }
} else {
    echo json_encode(['error' => 'Request failed']);
}
