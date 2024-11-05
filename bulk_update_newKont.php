<?php
// File: api/edit_methods/bulk_update_newKont.php

header('Content-Type: application/json');

// Include database connection
include 'conn-d.php'; // Adjust the path as necessary

// Function to sanitize input
function sanitize($data, $conn) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metoda e kërkesës nuk është e lejuar.']);
    exit;
}

// Retrieve and sanitize inputs
$ids = isset($_POST['ids']) ? $_POST['ids'] : [];
$column = isset($_POST['column']) ? $_POST['column'] : '';
$value = isset($_POST['value']) ? $_POST['value'] : '';

// Validate inputs
if (empty($ids) || !is_array($ids)) {
    echo json_encode(['success' => false, 'message' => 'Lista e ID-ve është e pavlefshme.']);
    exit;
}

$allowed_columns = ['invoice_date', 'invoice_number', 'description', 'category', 'company_name', 'vlera_faktura'];
if (!in_array($column, $allowed_columns)) {
    echo json_encode(['success' => false, 'message' => 'Kolona e zgjedhur nuk është valide.']);
    exit;
}

if (empty($value)) {
    echo json_encode(['success' => false, 'message' => 'Vlera e re nuk mund të jetë bosh.']);
    exit;
}

// Sanitize the value
$sanitized_value = sanitize($value, $conn);

// Prepare placeholders for the IN clause
$placeholders = implode(',', array_fill(0, count($ids), '?'));

// Determine the data type for binding
$data_types = '';
$params = [];
foreach ($ids as $id) {
    $data_types .= 'i'; // Assuming IDs are integers
    $params[] = $id;
}

// Construct the SQL query
$sql = "UPDATE invoices_kont SET {$column} = ? WHERE id IN ($placeholders)";

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Gabim në përgatitjen e pyetjes SQL: ' . $conn->error]);
    exit;
}

// Bind parameters
$bind_types = 's' . $data_types; // 's' for the value, followed by 'i's for IDs
$stmt->bind_param($bind_types, $sanitized_value, ...$params);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Rekordet janë përditësuar me sukses.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gabim gjatë përditësimit të rekordit: ' . $stmt->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
