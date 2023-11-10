<?php
// Establish a database connection
include 'conn-d.php';

// Get the filter value from the request
$filter = mysqli_real_escape_string($conn, $_GET['filter']);

// Fetch the filtered options from the database
$query = "SELECT * FROM klientet WHERE blocked='0' AND emri LIKE '%$filter%'";
$result = mysqli_query($conn, $query);

// Generate the option elements
$options = '';
while ($row = mysqli_fetch_assoc($result)) {
  $options .= '<option value="' . $row['id'] . '">' . $row['emri'] . '</option>';
}

// Return the generated options as the response
echo $options;

// Close the database connection
mysqli_close($conn);
