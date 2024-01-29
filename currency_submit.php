<?php
session_start();
include 'conn-d.php';

function sanitizeInput($input)
{
    return mysqli_real_escape_string($GLOBALS['conn'], $input);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Token i pavlefshëm CSRF']);
        exit();
    }

    $selectedPeriod = sanitizeInput($_POST['periods']);
    $feeValue = sanitizeInput($_POST['add_currencyValue']);

    $updateSql = "UPDATE platformat_2 SET Fee = '$feeValue' WHERE AccountingPeriod = '$selectedPeriod'";
    if ($conn->query($updateSql) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Rreshtat u përditësuan me sukses!']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Gabim gjatë përditësimit të rreshtave: ' . $conn->error]);
    }

    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Metoda e kërkesës nuk është e vlefshme.']);
    exit();
}
