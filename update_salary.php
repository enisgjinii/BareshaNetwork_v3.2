<?php
// Include your database connection logic (assuming you have a $conn variable)
include 'conn-d.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the employee ID and new salary from the form
    $employeeId = isset($_POST['id']) ? $_POST['id'] : null;
    $newSalary = isset($_POST['salary']) ? $_POST['salary'] : null;

    // Validate and sanitize the input (you should perform more thorough validation)
    $employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);
    $newSalary = filter_var($newSalary, FILTER_SANITIZE_NUMBER_INT);

    if ($employeeId !== false && $employeeId !== null && $newSalary !== false && $newSalary !== null) {
        // Perform the salary update
        $updateQuery = $conn->prepare("UPDATE googleauth SET salary = ? WHERE id = ?");
        $updateQuery->bind_param('ii', $newSalary, $employeeId);

        if ($updateQuery->execute()) {
            // Update successful
            $response = array('status' => 'success', 'message' => 'Paga u përditësua me sukses.');
        } else {
            // Error occurred during update
            $response = array('status' => 'error', 'message' => 'Gabim gjatë përditësimit të pagës. Ju lutemi provoni përsëri.');
        }

        $updateQuery->close();
    } else {
        // Invalid or missing input
        $response = array('status' => 'error', 'message' => 'Të dhëna të pavlefshme.');
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If the form is not submitted, display an error
    echo "Kërkesë e pavlefshme.";
}
