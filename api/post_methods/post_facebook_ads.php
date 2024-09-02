<?php
session_start();
include '../../conn-d.php';

// Function to validate CSRF token
function validate_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

// Debugging
var_dump($_POST['csrf_token']); // Check the value of CSRF token received from the form
var_dump($_SESSION['csrf_token']); // Check the value of CSRF token stored in the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and CSRF token
    $email = $_POST['email_ads'];
    $adsID = $_POST['adsID'];
    $shteti = $_POST['shteti'];

    // Retrieve CSRF token from the form
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    // Validate CSRF token
    if (!validate_csrf_token($csrf_token)) {
        // Invalid CSRF token, handle the error (e.g., redirect or display an error message)
        $error_message = "CSRF token validation failed!";
        echo $error_message;
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO facebook_ads (email, ads_id, shteti) VALUES ('$email', '$adsID', '$shteti')";

    if ($conn->query($sql) === TRUE) {
        // Data inserted successfully, redirect to vegla_facebook.php
        header("Location: ../../vegla_facebook.php");
        exit;
    } else {
        // Error handling for database insertion failure
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
        echo $error_message;
    }

    // Close the database connection
    $conn->close();
}
