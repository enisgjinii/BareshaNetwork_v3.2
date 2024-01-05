<?php
// Include your database connection file
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM shpenzimep WHERE id = ?");
    foreach ($ids as $id) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    // Close the statement
    $stmt->close();

    // Close the database connection
    $conn->close();

    // You can return a response if needed
    echo json_encode(['success' => true]);
} else {
    // Invalid request
    http_response_code(400);
    echo 'Invalid request';
}
