<?php
// Include your database connection file
include 'conn-d.php';

// Check if the ID parameter is set and is a valid integer
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    // Sanitize the ID to prevent SQL injection
    $id = $_POST['id'];

    // Prepare a SQL statement to delete the record with the given ID
    $sql = "DELETE FROM refresh_tokens WHERE id = ?";

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("i", $id);

        // Execute the statement
        if ($stmt->execute()) {
            // Return a success message
            echo "Record deleted successfully.";
        } else {
            // Return an error message if execution fails
            echo "Error: Unable to execute SQL statement.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Return an error message if preparation fails
        echo "Error: Unable to prepare SQL statement.";
    }
} else {
    // Return an error message if ID parameter is missing or invalid
    echo "Error: Invalid request.";
}

// Close the database connection
$conn->close();
