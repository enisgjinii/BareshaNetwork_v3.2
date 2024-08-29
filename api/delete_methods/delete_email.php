<?php
include '../../conn-d.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the email from the "facebook_emails" table based on the provided ID
    $emailQuery = "SELECT email FROM facebook_emails WHERE id = '$id'";
    $emailResult = mysqli_query($conn, $emailQuery);
    $row = mysqli_fetch_assoc($emailResult);

    if ($row) {
        $email = $row['email'];

        // Delete the email with the given ID from the "facebook_emails" table
        $deleteQuery = "DELETE FROM facebook_emails WHERE id = '$id'";
        $deleteResult = mysqli_query($conn, $deleteQuery);

        if ($deleteResult) {
            header("Location: ../../vegla_facebook.php");
            exit(); // Make sure to include this line after the redirect
        } else {
            echo "<p class='text-danger'>Failed to delete email. Please try again.</p>";
        }
    } else {
        echo "<p class='text-danger'>Email not found.</p>";
    }
}

mysqli_close($conn);
?>