<?php
include 'conn-d.php';

// Retrieve data from the "ascap" table
$sql = "SELECT * FROM ascap";
$result = $conn->query($sql);

$data = array(); // Initialize an empty array to store the data

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Add each row to the data array
    }
}

// Return the data as JSON
echo json_encode($data);

?>