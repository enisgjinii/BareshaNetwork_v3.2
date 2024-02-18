<?php
// delete_investimi.php

// Connect to the database
include 'conn-d.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare the SQL statement with a placeholder for the ID
    $sql = "DELETE FROM investimi WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter and execute the statement
    $stmt->bind_param("i", $id);

    $response = array();

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Të dhënat u fshinë me sukses.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error: ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Kërkesë e pavlefshme. Asnjë ID e specifikuar.';
}

// Close the connection
$conn->close();

// Convert the PHP array to JSON and return it as the response
header('Content-Type: application/json');
echo json_encode($response);
