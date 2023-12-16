<?php
// Assuming you have a database connection established\
include 'conn-d.php';

// Check if the 'id' parameter is set
if (isset($_POST['id'])) {
    // Sanitize the input to prevent SQL injection (you can use prepared statements for additional security)
    $rowId = $_POST['id'];

    $deleteQuery = "DELETE FROM rrogat WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $rowId);

    if ($stmt->execute()) {
        // Deletion successful
        $response = ['success' => true];
    } else {
        // Deletion failed
        $response = ['success' => false, 'error' => $stmt->error];
    }

    $stmt->close();
    $conn->close();
} else {
    // 'id' parameter not set
    $response = ['success' => false, 'error' => 'ID parameter not provided.'];
}

// Send JSON response back to the client
header('Content-Type: application/json');
echo json_encode($response);
