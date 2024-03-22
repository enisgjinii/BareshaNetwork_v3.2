<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the form
    $row_id = $_POST['row_id'];
    $email_ads_edit = $_POST['email_ads_edit'];
    $adsID_edit = $_POST['adsID_edit'];
    $shteti_edit = $_POST['shteti_edit'];

    // Perform the update in the database
    include 'conn-d.php';

    $sql = "UPDATE facebook_ads SET email = '$email_ads_edit', ads_id = '$adsID_edit', shteti = '$shteti_edit' WHERE id = '$row_id'";

    if (mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        header("Location: vegla_facebook.php");
        exit;
    } else {
        echo "Error updating row: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>