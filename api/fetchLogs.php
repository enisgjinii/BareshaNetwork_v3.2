<?php
// fetchLogs.php

// Include database connection
include '../conn-d.php';

// Retrieve filter parameters from POST request
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$staffName = isset($_POST['staffName']) ? $_POST['staffName'] : '';

// Base SQL query
$sql = "SELECT stafi, ndryshimi, koha FROM logs WHERE 1=1";

// Add conditions based on filters
$params = [];
$types = "";

if (!empty($startDate)) {
    $sql .= " AND koha >= ?";
    $params[] = $startDate . ' 00:00:00';
    $types .= "s";
}

if (!empty($endDate)) {
    $sql .= " AND koha <= ?";
    $params[] = $endDate . ' 23:59:59';
    $types .= "s";
}

if (!empty($staffName)) {
    $sql .= " AND stafi LIKE ?";
    $params[] = '%' . $staffName . '%';
    $types .= "s";
}

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute and fetch data
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'stafi' => htmlspecialchars($row['stafi']),
        'ndryshimi' => htmlspecialchars($row['ndryshimi']),
        'koha' => htmlspecialchars($row['koha'])
    ];
}

// Return JSON response
echo json_encode([
    "data" => $data
]);

$stmt->close();
$conn->close();
?>
