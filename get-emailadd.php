<?php

// Get the user ID from the query string
$userId = $_GET['id'];

include 'conn-d.php';

// Debugging: print the GET parameters
// print_r($_GET);

// Prepare a query to retrieve the email address for the user with the given ID
$query = "SELECT * FROM kontrata WHERE id = $userId";

// Debugging: print the query string
// echo $query;

$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['emailadd'];
        echo $email;
    } else {
        echo 'User not found: no rows returned';
    }
} else {
    echo 'Error executing query: ' . $conn->error;
}

// Close the connection
$conn->close();