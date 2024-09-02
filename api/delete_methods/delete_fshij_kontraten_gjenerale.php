<?php
// Include your database connection file
include '../../conn-d.php';

// Get the ID from the URL
$id = $_GET['id'];

// Delete the record from the database
$query = "DELETE FROM kontrata_gjenerale WHERE id = $id";
mysqli_query($conn, $query);

// Redirect to the main page
$response = [
    'status' => 'success',
    'message' => 'The record has been deleted.'
];

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
