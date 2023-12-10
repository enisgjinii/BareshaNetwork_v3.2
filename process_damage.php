<?php

// Connect to the database
include 'conn-d.php';

// Ensure that the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    // Get form data
    $damageType = mysqli_real_escape_string($conn, $_POST['damageType']);
    $damageDescription = mysqli_real_escape_string($conn, $_POST['damageDescription']);
    $damageDate = $_POST['damageDate'];
    $reporterName = mysqli_real_escape_string($conn, $_POST['reporterName']);;

    // Prepare and execute the SQL statement
    $sql = "INSERT INTO office_damages (damage_type, damage_description, damage_date, reporter_name)
            VALUES ('$damageType', '$damageDescription', '$damageDate', '$reporterName')";

    if ($conn->query($sql) === TRUE) {
        echo "Damage report submitted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    // If the form is not submitted, redirect or handle accordingly
    header("Location: index.php");
    exit();
}
?>
