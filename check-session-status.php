<?php
session_start();
$authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] > time();
echo json_encode(['authenticated' => $authenticated]);
?>
