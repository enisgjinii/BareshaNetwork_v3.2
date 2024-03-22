<?php
include 'conn-d.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the category with the given ID from the "facebook_category" table
    $deleteQuery = "DELETE FROM facebook_category WHERE id = '$id'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
        header("Location: vegla_facebook.php");
        exit;
    } else {
        echo "<p class='text-danger'>Failed to delete the category.</p>";
    }
}

mysqli_close($conn);
?>