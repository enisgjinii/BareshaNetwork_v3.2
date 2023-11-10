<?php
// Connect to the database
include_once 'conn-d.php';
// Get the ID of the entry to be deleted
$id = $_GET['id'];

// Delete the entry from the database
$sql = "DELETE FROM filet WHERE id = $id";
$conn->query($sql);

// Redirect the user back to the page with the table
header("Location: filet.php");
?>
