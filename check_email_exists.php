<?php

// Include config file
include "conn-d.php";
// Get the SQL query from the URL parameter
$sql = $_GET['sql'];
// Execute the SQL query
$result = mysqli_query($conn, $sql);
// Check if the query returned any rows
if (mysqli_num_rows($result) > 0) {
    // Email exists in the database
    echo "exists";
} else {
    // Email not found in the database
    echo "";
}
// Close the database connection
mysqli_close($conn);
?>