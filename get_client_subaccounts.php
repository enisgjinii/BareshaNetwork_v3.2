<?php
// get_clients_subaccounts.php

// Database connection
include 'conn-d.php';

$client_id = $_GET['client_id'];

$sql = "SELECT * FROM client_subaccounts WHERE client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();

$subaccounts = [];

while ($row = $result->fetch_assoc()) {
    $subaccounts[] = $row;
}

echo json_encode($subaccounts);
