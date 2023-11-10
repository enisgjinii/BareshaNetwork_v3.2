<?php
include 'conn-d.php';

// Get the selected value from the AJAX request
$nameSurname = $_GET['nameSurname'];

// Fetch the "perqindja" value from the database based on the selected value
$query = "SELECT perqindja FROM facebook WHERE emri_mbiemri = '$nameSurname'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$perqindja = $row['perqindja'];

// Return the "perqindja" value as the response to the AJAX request
echo $perqindja;

// Close the database connection
mysqli_close($conn);
