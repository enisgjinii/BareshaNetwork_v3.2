<?php
include('./config.php');
include('conn-d.php');
session_start();
date_default_timezone_set('Europe/Tirane');

if (isset($_SESSION['token'])) {
    $token = $_SESSION['token'];

    // Fetch user data from the Google authentication
    $client->setAccessToken($token);
    $google_oauth = new Google_Service_Oauth2($client);
    $user_info = $google_oauth->userinfo->get();

    // Replace these variables with the appropriate Google authentication data
    $uid = trim($user_info['id']);
    $shikoban1 = [/* Replace with data from your database */];

    // Check if the user's account is disabled
    if ($shikoban1['ban'] == 1) {
        die("<center><h2>Disabled</h2></center><script>alert('Llogaria juaj nuk është aktive.');</script>");
    }

    // Continue with any other checks or actions you need
    $men = $conn->query("SELECT * FROM tiketa WHERE stafi='$uid' AND lexuar='0'");
    $men2 = mysqli_num_rows($men);

    $mes = $conn->query("SELECT * FROM rrogat WHERE stafi='$uid' AND lexuar='0'");
    $mes2 = mysqli_num_rows($mes);
} else {
    header("Location: kycu_1.php");
}
