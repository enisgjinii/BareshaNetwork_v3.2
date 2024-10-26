<?php
// fetch_filter_options.php

// Include the database connection file
include 'conn-d.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Get the type of payments to fetch ('personal' or 'biznes')
$type = isset($_GET['type']) ? $_GET['type'] : 'personal';

// Initialize the base query
$baseQuery = "
    FROM payments
    JOIN invoices ON payments.invoice_id = invoices.id
    JOIN klientet ON invoices.customer_id = klientet.id
    WHERE 1=1
";

// Modify the query based on the type
if ($type === 'personal') {
    $baseQuery .= "
        AND (klientet.shtetsia IS NULL OR klientet.shtetsia = 'Kosova')
        AND (klientet.lloji_klientit = 'Personal' OR klientet.lloji_klientit IS NULL)
    ";
} elseif ($type === 'biznes') {
    $baseQuery .= "
        AND klientet.lloji_klientit = 'Biznes'
    ";
}

// Fetch unique client names
$clientNamesQuery = "SELECT DISTINCT klientet.emri AS client_name " . $baseQuery . " ORDER BY klientet.emri ASC";
$clientNamesResult = $conn->query($clientNamesQuery);
$clientNames = [];
if ($clientNamesResult) {
    while ($row = $clientNamesResult->fetch_assoc()) {
        $clientNames[] = $row['client_name'];
    }
}

// Fetch unique bank info
$bankInfosQuery = "SELECT DISTINCT payments.bank_info " . $baseQuery . " ORDER BY payments.bank_info ASC";
$bankInfosResult = $conn->query($bankInfosQuery);
$bankInfos = [];
if ($bankInfosResult) {
    while ($row = $bankInfosResult->fetch_assoc()) {
        $bankInfos[] = $row['bank_info'];
    }
}

// Fetch unique payment types
$paymentTypesQuery = "SELECT DISTINCT payments.type_of_pay " . $baseQuery . " ORDER BY payments.type_of_pay ASC";
$paymentTypesResult = $conn->query($paymentTypesQuery);
$paymentTypes = [];
if ($paymentTypesResult) {
    while ($row = $paymentTypesResult->fetch_assoc()) {
        $paymentTypes[] = $row['type_of_pay'];
    }
}

// Prepare the response array
$response = [
    'client_names' => $clientNames,
    'bank_infos' => $bankInfos,
    'payment_types' => $paymentTypes
];

// Output the JSON response
echo json_encode($response);
?>
