<?php

// Establish a connection to the database
include 'conn-d.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form data
    $descriptionOfRequest = mysqli_real_escape_string($conn, $_POST["description_of_the_requirement"]);
    $expectedDate = mysqli_real_escape_string($conn, $_POST["expected_date"]);

    // Insert data into the database using prepared statement
    $sql = "INSERT INTO requirements (description_of_the_requirement, expected_date)
                VALUES (?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $descriptionOfRequest, $expectedDate);

    if ($stmt->execute()) {
        // Success message using JavaScript alert
        echo "<script>
                    alert('Kërkesa u regjistrua me sukses');
                    window.location.href = 'office_requirements.php'; // Redirect to the form page
                  </script>";
    } else {
        // Error message using JavaScript alert
        echo "<script>
                    alert('Gabim gjate regjistrimit. Error: " . $stmt->error . "');
                    window.location.href = 'office_requirements.php'; // Redirect to the form page
                  </script>";
    }

    // Close statement
    $stmt->close();
    // Close connection
    $conn->close();
} else {
    // Redirect to the form page if accessed directly without submission
    header("Location: office_requirements.php");
    exit();
}
