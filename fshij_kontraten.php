<?php
// Include your database connection file
include 'conn-d.php';

// Get the ID from the URL
$id = $_GET['id'];

// Delete the record from the database
$query = "DELETE FROM kontrata WHERE id = $id";
mysqli_query($conn, $query);

// Redirect to the main page
header('Location: lista_kontratave.php');
?>