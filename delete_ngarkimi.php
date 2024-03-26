<?php
// Include the database connection
include 'conn-d.php';

// Check if the ID parameter is set and is a valid integer
if(isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    // Sanitize the ID to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Construct the delete query
    $query = "DELETE FROM ngarkimi WHERE id = $id";

    // Execute the delete query
    if(mysqli_query($conn, $query)) {
        // If deletion is successful, return a success message
        echo json_encode(array('success' => true));
    } else {
        // If deletion fails, return an error message
        echo json_encode(array('error' => 'Error deleting record'));
    }
} else {
    // If ID parameter is missing or invalid, return an error message
    echo json_encode(array('error' => 'Invalid request'));
}
?>
