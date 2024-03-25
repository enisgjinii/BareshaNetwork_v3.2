<?php
include 'conn-d.php';

// Check if 'id' parameter is set in the URL
if (isset($_GET['id'])) {

    // Get the 'id' parameter from the URL and sanitize it
    $id_of_client = mysqli_real_escape_string($conn, $_GET['id']);

    // Define the passive status
    $passive_status = 1;

    // Prepare the SQL statement using a prepared statement
    $sql_query = "UPDATE klientet SET aktiv = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql_query)) {
        // Bind the parameters
        $stmt->bind_param("ii", $passive_status, $id_of_client);

        // Execute the statement
        if ($stmt->execute()) {
            // Operation successful, redirect to a success page
            header("Location: klient.php");
            exit(); // Terminate the script after redirection
        } else {
            // Error handling if execution fails
            echo "Error: Unable to execute the statement.";
            error_log("Execution error in update query: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error handling if the prepared statement fails
        echo "Error: Unable to prepare the statement.";
        error_log("Preparation error in update query: " . $conn->error);
    }

    // Close the database connection
    $conn->close();
} else {
    // If 'id' parameter is not set, redirect to an error page or handle as appropriate
    echo "Error: 'id' parameter is missing in the URL.";
}
