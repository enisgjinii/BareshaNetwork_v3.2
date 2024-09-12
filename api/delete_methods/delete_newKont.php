<?php
include '../../conn-d.php';

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
    exit;
}

if ($action === 'fetch') {
    // Fetch all information
    $stmt = $conn->prepare("SELECT * FROM invoices_kont WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    echo json_encode($data);
    exit;
} elseif ($action === 'delete') {
    // Delete the record
    $stmt = $conn->prepare("DELETE FROM invoices_kont WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
