<?php
// Include your database connection here
include 'conn-d.php'; // Update this with your actual connection file

// Check if the 'id' GET parameter is set
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Perform the deletion query
  if ($conn->query("DELETE FROM yinc WHERE id='$id'")) {
    header("Location: yinc.php"); // Redirect to yinc.php
    exit();
  }
}

// Handle the case where the deletion was unsuccessful (optional)
// You can add an error message or additional logic here if needed
?>
