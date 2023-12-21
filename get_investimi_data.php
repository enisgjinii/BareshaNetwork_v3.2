<?php
// get_investimi_data.php

// Connect to the database
include 'conn-d.php';

// Retrieve data from the "investimi" table
$sql = "SELECT * FROM investimi ORDER BY id DESC";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close the connection
$conn->close();

// Convert the PHP array to JSON and return it as the response
header('Content-Type: application/json');
echo json_encode($data);
