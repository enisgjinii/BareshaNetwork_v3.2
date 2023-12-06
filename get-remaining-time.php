<?php
session_start();

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] > time()) {
    $remainingTime = max(0, $_SESSION['authenticated'] - time());
    echo json_encode(['remainingTime' => $remainingTime]);
} else {
    echo json_encode(['remainingTime' => 0]);
}
