<?php
// get_investimi.php

// Connect to the database
include '../../conn-d.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare the SQL statement with a placeholder for the ID
    $sql = "SELECT * FROM investimi WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter and execute the statement
    $stmt->bind_param("i", $id);

    // Execute the query
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch the row data
    $row = $result->fetch_assoc();

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();

    // Convert the row data to JSON and return it as the response
    header('Content-Type: application/json');
    echo json_encode($row);
} else {
    // Invalid request if ID is not specified
    $response = array('error' => 'Invalid request. No ID specified.');
    header('Content-Type: application/json');
    echo json_encode($response);
}
