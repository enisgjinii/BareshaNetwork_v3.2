<?php
// delete_kontrata_gjenerale.php

header('Content-Type: application/json');
session_start();
include('../../conn-d.php'); // Adjust the path as necessary

// Check for CSRF token in headers
$headers = getallheaders();
if (!isset($headers['X-CSRF-Token']) || $headers['X-CSRF-Token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit();
}

// Get the ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM kontrata_gjenerale WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Kontrata u fshinë me sukses!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gabim gjatë fshirjes së kontratës: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Gabim në përgatitjen e kërkesës SQL.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID nuk është i vlefshëm.']);
}
