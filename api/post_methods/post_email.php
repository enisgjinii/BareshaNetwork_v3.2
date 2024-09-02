<?php
include '../../conn-d.php';

if (isset($_POST['submit'])) {
    // Get the submitted email
    $email = $_POST['email_facebook'];

    // Prepare and execute the SQL query to insert the email into the "facebook_emails" table
    $sql = "INSERT INTO facebook_emails (email) VALUES ('$email')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "Email successfully saved in the database.";
        // Redirect to vegla_facebook.php
        header("Location: ../../vegla_facebook.php");
        exit(); // Make sure to include this line after the redirect
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>