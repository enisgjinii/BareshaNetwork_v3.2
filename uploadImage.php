<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['emri'])) {
    header("Location: kycu_1.php");
    exit();
}

include "conn-d.php";

// Get the user ID
$user_id = $_SESSION['emri'];

// Check if the form was submitted
if (isset($_POST['upload'])) {

    // Get the uploaded file
    $file = $_FILES['profileImage'];

    // Check if a file was selected
    if ($file['name'] !== '') {

        // Check if the file is an image
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file_type !== 'jpg' && $file_type !== 'jpeg' && $file_type !== 'png' && $file_type !== 'gif') {
            $_SESSION['error'] = 'Only JPG, JPEG, PNG and GIF files are allowed.';
            header("Location: perditsoProfilin.php");
            exit();
        }

        // Set the file name and path
        $file_name =uniqid() . '.' . $file_type;
        $file_path = 'uploads/' . $file_name;

        // Move the file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $file_path)) {

            // Update the user's profile image in the database
            $sql = "UPDATE users SET profile_image = '$file_path' WHERE name = '$user_id'";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success'] = 'Profile image updated successfully.';
            } else {
                $_SESSION['error'] = 'Error updating profile image: ' . mysqli_error($conn);
            }
        } else {
            $_SESSION['error'] = 'Error uploading file. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Please select a file to upload.';
    }

    header("Location: perditsoProfilin.php");
    exit();
} else {
    header("Location: perditsoProfilin.php");
    exit();
}
