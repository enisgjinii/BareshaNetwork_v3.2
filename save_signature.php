<?php
// Get the signature data from the POST request
$signatureData = $_POST['signature'];

// Save the signature data to the MySQL database
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database_name";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape the signature data to prevent SQL injection attacks
$escapedSignatureData = $conn->real_escape_string($signatureData);

// Save the signature data to the database
$sql = "INSERT INTO table_name (nenshkrimi) VALUES ('$escapedSignatureData')";
if ($conn->query($sql) === TRUE) {
    echo "Signature saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>