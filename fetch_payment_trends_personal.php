<?php
// fetch_payment_trends_personal.php

include 'conn-d.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Retrieve filter parameters
$minDate = isset($_POST['minDate']) ? $_POST['minDate'] : '';
$maxDate = isset($_POST['maxDate']) ? $_POST['maxDate'] : '';
$bank = isset($_POST['bank']) ? $_POST['bank'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Initialize the base query
$baseQuery = "
    FROM payments
    JOIN invoices ON payments.invoice_id = invoices.id
    JOIN klientet ON invoices.customer_id = klientet.id
    WHERE (klientet.shtetsia IS NULL OR klientet.shtetsia = 'Kosova')
    AND (klientet.lloji_klientit = 'Personal' OR klientet.lloji_klientit IS NULL)
";

// Initialize an array for binding parameters
$bindings = [];
$types = "";

// Apply date filters
if (!empty($minDate)) {
    $baseQuery .= " AND payments.payment_date >= ?";
    $bindings[] = $minDate;
    $types .= "s";
}
if (!empty($maxDate)) {
    $baseQuery .= " AND payments.payment_date <= ?";
    $bindings[] = $maxDate;
    $types .= "s";
}

// Apply additional filters: Bank and Type of Payment
if (!empty($bank)) {
    $baseQuery .= " AND payments.bank_info = ?";
    $bindings[] = $bank;
    $types .= "s";
}
if (!empty($type)) {
    $baseQuery .= " AND payments.type_of_pay = ?";
    $bindings[] = $type;
    $types .= "s";
}

// Query to aggregate payments by month
$trendQuery = "SELECT DATE_FORMAT(payments.payment_date, '%Y-%m') AS month, SUM(payments.payment_amount) AS total " . $baseQuery . " GROUP BY month ORDER BY month ASC";

$trendStmt = $conn->prepare($trendQuery);
if (!empty($bindings)) {
    $trendStmt->bind_param($types, ...$bindings);
}
$trendStmt->execute();
$trendResult = $trendStmt->get_result();

$labels = [];
$data = [];
while ($row = $trendResult->fetch_assoc()) {
    $labels[] = $row['month'];
    $data[] = floatval($row['total']);
}

$response = [
    "labels" => $labels,
    "data" => $data
];

echo json_encode($response);
?>
