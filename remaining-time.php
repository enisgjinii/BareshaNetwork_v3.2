<?php
session_start();

$response = array('remainingTime' => 0);

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] > time()) {
    $remainingTime = max(0, $_SESSION['authenticated'] - time());
    $response['remainingTime'] = $remainingTime;
}

header('Content-Type: application/json');
echo json_encode($response);
