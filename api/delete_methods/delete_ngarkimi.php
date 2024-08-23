<?php
// Include the database connection
include '../../conn-d.php';

// Check if the ID parameter is set and is a valid integer
if (isset($_POST['id']) && filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
    // Sanitize the ID to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Retrieve the record to be deleted
    $select_query = "SELECT * FROM ngarkimi WHERE id = $id";
    $result = mysqli_query($conn, $select_query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $deleted_record = json_encode($row);

        // Construct the delete query
        $delete_query = "DELETE FROM ngarkimi WHERE id = $id";

        // Execute the delete query
        if (mysqli_query($conn, $delete_query)) {
            // Insert the deleted record into another table
            $insert_query = "INSERT INTO deleted_ngarkimi (deleted_record) VALUES ('$deleted_record')";

            if (mysqli_query($conn, $insert_query)) {
                // If deletion and insertion are successful, return a success message
                echo json_encode(array('success' => true));
            } else {
                // If insertion into the deleted_records table fails, return an error message
                echo json_encode(array('error' => 'Error inserting deleted record into deleted_records table'));
            }
        } else {
            // If deletion fails, return an error message
            echo json_encode(array('error' => 'Error deleting record'));
        }
    } else {
        // If no record found with the given ID, return an error message
        echo json_encode(array('error' => 'Record not found'));
    }
} else {
    // If ID parameter is missing or invalid, return an error message
    echo json_encode(array('error' => 'Invalid request'));
}
