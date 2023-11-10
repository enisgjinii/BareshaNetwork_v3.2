<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email and id from the form
    $email = $_POST['email_edit'];
    $id = $_POST['id'];

    // Perform necessary validations and sanitization on the email and id

    // Update the email in the database
    include 'conn-d.php';

    // Prepare the update query
    $updateQuery = "UPDATE facebook_emails SET email = '$email' WHERE id = '$id'";

    // Execute the update query
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        // Email updated successfully
        // Redirect back to the main page or show a success message
        header("Location: facebook.php");
        exit;
    } else {
        // Error occurred while updating the email
        // Redirect back to the main page or show an error message
        header("Location: facebook.php?error=1");
        exit;
    }

    mysqli_close($conn);
}
?>