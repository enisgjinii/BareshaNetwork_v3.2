<?php
include "conn-d.php";
session_start();

// Get the user ID
$user_id = $_SESSION['emri'];

// Retrieve the profile image data from the database
$sql = "SELECT profile_image FROM users WHERE name = '$user_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Delete the profile image from the database
$sql = "UPDATE users SET profile_image = NULL WHERE name = '$user_id'";
if (mysqli_query($conn, $sql)) {
  // Delete the image file from the server
  if ($row['profile_image']) {
    unlink($row['profile_image']);
  }
  header("Location:perditsoProfilin.php");
  exit();
} else {
  echo "Error deleting profile image: " . mysqli_error($conn);
}


mysqli_close($conn);
