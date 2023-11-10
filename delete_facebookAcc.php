<?php
include 'conn-d.php';

// Check if the ID parameter is present
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete the row with the given ID from the "facebook" table
    $sql = "DELETE FROM facebook WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "success"; // Return a success message if the deletion was successful
    } else {
        echo "error"; // Return an error message if the deletion failed
    }
}

// Close the database connection
mysqli_close($conn);
