<?php
include 'conn-d.php';
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = $_POST['email_ads'];
    $adsID = $_POST['adsID'];
    $shteti = $_POST['shteti'];

    
    // Insert data into the database
    $sql = "INSERT INTO facebook_ads (email, ads_id, shteti) VALUES ('$email', '$adsID', '$shteti')";

    if ($conn->query($sql) === TRUE) {
        // Data inserted successfully, redirect to facebook.php
        header("Location: facebook.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
