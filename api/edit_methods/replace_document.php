<?php
include '../../conn-d.php'; // Adjust the path as needed

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Check if 'id' and 'newDocument' are present
if (!isset($_POST['id']) || !isset($_FILES['newDocument'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$id = $_POST['id'];
$newDocument = $_FILES['newDocument'];

// Validate the uploaded file
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/svg+xml', 'application/pdf'];
if (!in_array($newDocument['type'], $allowedTypes)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type.'
    ]);
    exit;
}

// Handle file upload
$uploadDir = '../../uploads/documents/'; // Adjust the path as needed
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = basename($newDocument['name']);
$targetFilePath = $uploadDir . time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);

// Move the uploaded file to the target directory
if (move_uploaded_file($newDocument['tmp_name'], $targetFilePath)) {
    // Update the database with the new document path
    $sql = "UPDATE invoices_kont SET document_path = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $targetFilePath, $id);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Document replaced successfully.'
            ]);
        } else {
            // Failed to update database
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update database.'
            ]);
        }
        $stmt->close();
    } else {
        // Failed to prepare statement
        echo json_encode([
            'success' => false,
            'message' => 'Failed to prepare database statement.'
        ]);
    }
} else {
    // Failed to move uploaded file
    echo json_encode([
        'success' => false,
        'message' => 'Failed to upload file.'
    ]);
}

$conn->close();
