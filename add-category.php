<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the category from the form
    $kategori = $_POST['kategori'];

    // Perform necessary validations and sanitization on the category

    // Insert the category into the database
    include 'conn-d.php';

    // Prepare the insert query
    $insertQuery = "INSERT INTO facebook_category (kategoria) VALUES ('$kategori')";

    // Execute the insert query
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        // Category inserted successfully
        // Redirect back to the main page or show a success message
        header("Location: vegla_facebook.php");
        exit;
    } else {
        // Error occurred while inserting the category
        // Redirect back to the main page or show an error message
        header("Location: vegla_facebook.php?error=1");
        exit;
    }

    mysqli_close($conn);
}
?>