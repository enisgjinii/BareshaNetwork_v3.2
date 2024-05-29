<?php
include 'conn-d.php'; // Assuming this file contains your database connection setup

// Check if the ID parameter is set and use POST method
if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID is not set']);
    exit;
}

$id = $_POST['id'];

// Sanitize and validate the input 
$id = mysqli_real_escape_string($conn, $id); // Escape to prevent SQL injection
if (!is_numeric($id) || $id <= 0) { // Ensure it's a positive integer
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

// Prepare and execute the SQL statement (use prepared statements)
$stmt = mysqli_prepare($conn, "DELETE FROM platforms WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id); // "i" indicates an integer parameter

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['success' => true, 'message' => 'Platform deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No platform found with the given ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete platform: ' . mysqli_stmt_error($stmt)]);
}

// Close the statement and the connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
