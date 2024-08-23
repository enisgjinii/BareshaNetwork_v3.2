<?php
// Establish a connection to the database
include '../../conn-d.php';
// Create a response array
$response = array();
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form data
    $descriptionOfRequest = mysqli_real_escape_string($conn, $_POST["description_of_the_requirement"]);
    $expectedDate = mysqli_real_escape_string($conn, $_POST["expected_date"]);
    // Insert data into the database using prepared statement
    $sql = "INSERT INTO requirements (description_of_the_requirement, expected_date)
                VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $descriptionOfRequest, $expectedDate);
    if ($stmt->execute()) {
        // Success message in the response
        $response['status'] = 'success';
        $response['message'] = 'Kërkesa u regjistrua me sukses';
    } else {
        // Error message in the response
        $response['status'] = 'error';
        $response['message'] = "Gabim gjate regjistrimit. Error: " . $stmt->error;
    }
    // Close statement
    $stmt->close();
    // Close connection
    $conn->close();
    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Redirect to the form page if accessed directly without submission
    header("Location: office_requirements.php");
    exit();
}
