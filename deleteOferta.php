<?php

include 'conn-d.php';
// First, check if the id parameter was passed
if (isset($_GET['id'])) {
    // Connect to the database

    // Escape the id parameter to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete the row from the database
    $query = "DELETE FROM ofertat WHERE id = '$id'";
    mysqli_query($conn, $query);

    // Close the database connection
    mysqli_close($conn);

    // Redirect back to the page where the delete button was clicked
    header("Location: ofertat.php");
    exit();
} else {
    // If no id parameter was passed, show an error message
    echo "Error: No id parameter was passed.";
}
?>