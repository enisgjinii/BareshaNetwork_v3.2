<?php
// Include your database connection code here
include 'conn-d.php';

// Query to get the total number of clients
$sql = "SELECT COUNT(*) AS count FROM klientet";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $clientCount = $row["count"];

    // Return the client count as JSON
    echo json_encode(["count" => $clientCount]);
} else {
    echo json_encode(["count" => 0]);
}

$conn->close();
?>
