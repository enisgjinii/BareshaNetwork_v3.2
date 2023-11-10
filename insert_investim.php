<?php
// Connect to the database
include 'conn-d.php';

// Get data from the form
$emri = $_POST['emri'];
$mbiemri = $_POST['mbiemri'];
$emri_i_kenges = $_POST['emri_i_kenges'];

// Handle "shenim" field content (encoding and escaping)
$shenim = $_POST['shenim']; // Assume TinyMCE editor content is directly sent in the POST data

// Prepare the SQL statement with placeholders
$sql = "INSERT INTO investimi (emri, mbiemri, emri_i_kenges, shenim) VALUES (?, ?, ?, ?)";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind the parameters and execute the statement
$stmt->bind_param("ssss", $emri, $mbiemri, $emri_i_kenges, $shenim);

$response = array();

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'T&euml; dh&euml;nat u regjistruan me sukses.';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Gabim: ' . $stmt->error;
}

// Close the statement
$stmt->close();

$conn->close();

// Convert the PHP array to JSON and return it as the response
header('Content-Type: application/json');
echo json_encode($response);
