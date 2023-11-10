<?php
include 'conn-d.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the row with the given ID from the "facebook_ads" table
    $deleteQuery = "DELETE FROM facebook_ads WHERE id = '$id'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        // Redirect back to the original page after successful deletion
        header("Location: facebook.php");
        exit();
    } else {
        echo "Error deleting row: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>