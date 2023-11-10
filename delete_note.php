<?php
// Connect to the database
include 'conn-d.php';

// Get the ID of the note to be deleted from the URL
$note_id = $_GET['id'];

// Construct a SQL DELETE query to delete the note from the database
$sql = "DELETE FROM shenime WHERE id=$note_id";

// Execute the SQL query
if (mysqli_query($conn, $sql)) {
    // If the query was successful, redirect to the notes page
    header("Location: notes.php");
    exit();
} else {
    // If the query was not successful, display an error message
    echo "Error deleting note: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
