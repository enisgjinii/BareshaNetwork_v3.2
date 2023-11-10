<?php
// Include your database connection here (assuming you've already established a database connection)
include 'conn-d.php'; // Update this with your actual connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['shto'])) {
  // Validate and retrieve data from the form
  $stafi = mysqli_real_escape_string($conn, $_POST['stafi']);
  $shuma = mysqli_real_escape_string($conn, $_POST['shuma']);
  $data = mysqli_real_escape_string($conn, $_POST['data']);
  $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);

  // Perform database insertion
  $insertQuery = "INSERT INTO yinc (kanali, shuma, data, pershkrimi) VALUES ('$stafi', '$shuma', '$data', '$pershkrimi')";

  if (mysqli_query($conn, $insertQuery)) {
    // Insertion was successful
    header("Location: yinc.php"); // Redirect to a success page
    exit();
  } else {
    // Insertion failed
    echo "Gabim gjat&euml; futjes n&euml; baz&euml;n e t&euml; dh&euml;nave: " . mysqli_error($conn);
  }
}

// Close the database connection if necessary
mysqli_close($conn);
