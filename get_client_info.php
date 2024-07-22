<?php
// get_client_info.php

// Assuming $conn is your database connection
include 'conn-d.php'; // Replace with your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $clientId = $_POST['clientId'];

    // Prepare and execute query to fetch client and subaccount information
    $response = array();

    // Fetch client information
    $sqlClient = "SELECT * FROM klientet WHERE id = ?";
    $stmtClient = $conn->prepare($sqlClient);
    $stmtClient->bind_param('i', $clientId);
    $stmtClient->execute();
    $resultClient = $stmtClient->get_result();

    if ($resultClient->num_rows > 0) {
        $rowClient = $resultClient->fetch_assoc();
        $clientInfo = array(
            'emri' => $rowClient['emri'],
            'perqindja' => $rowClient['perqindja'],
            'perqindja_e_klientit' => $rowClient['perqindja_e_klientit']
        );
        $response['client'] = $clientInfo;
    }

    // Fetch subaccounts information
    $sqlSubaccounts = "SELECT * FROM client_subaccounts WHERE client_id = ?";
    $stmtSubaccounts = $conn->prepare($sqlSubaccounts);
    $stmtSubaccounts->bind_param('i', $clientId);
    $stmtSubaccounts->execute();
    $resultSubaccounts = $stmtSubaccounts->get_result();

    $subaccounts = array();
    while ($rowSubaccount = $resultSubaccounts->fetch_assoc()) {
        $subaccountInfo = array(
            'name' => $rowSubaccount['name'],
            'percentage' => $rowSubaccount['percentage']
            // 'channel_id' => $rowSubaccount['channel_id']
        );
        $subaccounts[] = $subaccountInfo;
    }
    $response['subaccounts'] = $subaccounts;

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle invalid requests
    http_response_code(400);
    echo json_encode(array('message' => 'Invalid request'));
}
?>
