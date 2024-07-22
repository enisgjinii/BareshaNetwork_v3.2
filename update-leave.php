<?php
include 'conn-d.php';

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$status = $_POST['status'] ?? null;

// Validate input
if (!$id || !$title || !$start_date || !$end_date || !$status) {
    die(json_encode(['success' => false, 'message' => 'Missing required fields']));
}

// Update the leave request
$sql = "UPDATE leaves SET title = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $title, $start_date, $end_date, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Update query failed: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
