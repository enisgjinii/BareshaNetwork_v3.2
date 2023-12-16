<?php
// Include your database connection file
include 'conn-d.php';

// Check if the 'id' parameter is set
if (isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection (you can use prepared statements for additional security)
    $rowId = $_GET['id'];

    $selectQuery = "SELECT * FROM rrogat WHERE id = ?";
    $stmt = $conn->prepare($selectQuery);
    $stmt->bind_param('i', $rowId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    // Send JSON response back to the client
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // 'id' parameter not set
    $response = ['error' => 'ID parameter not provided.'];

    // Send JSON response back to the client
    header('Content-Type: application/json');
    echo json_encode($response);
}
