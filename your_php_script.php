<?php

// Include your database connection code here
include 'conn-d.php';


if (isset($_POST['ruaj'])) {

    // Check if the connection was successful
    if (!$conn) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    $stafi = mysqli_real_escape_string($conn, $_POST['stafi']);
    $shuma = mysqli_real_escape_string($conn, $_POST['shuma']);
    $data = mysqli_real_escape_string($conn, $_POST['data']);
    $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);

    // Perform the database insertion
    $query = "INSERT INTO yinc (kanali, shuma, pershkrimi, data) VALUES ('$stafi', '$shuma', '$pershkrimi', '$data')";

    if ($conn->query($query)) {
        echo json_encode(["success" => true]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error, "query" => $query]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}
