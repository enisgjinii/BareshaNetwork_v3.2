<?php
// convert_currency.php

if (isset($_GET['amount'])) {
    $amount = $_GET['amount'];
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.exconvert.com/convert?from=USD&to=EUR&amount=" . $amount . "&access_key=7ac9d0d8-2c2a1729-0a51382b-b85cd112",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo json_encode(["error" => "cURL Error #:" . $err]);
    } else {
        echo $response;
    }
} else {
    echo json_encode(["error" => "No amount provided"]);
}
