<?php
// Include your database connection logic (assuming you have a $conn variable)
include 'conn-d.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the employee ID from the form
    $employeeId = isset($_POST['id']) ? $_POST['id'] : null;

    // Validate and sanitize the input (you should perform more thorough validation)
    $employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

    if ($employeeId !== false && $employeeId !== null) {
        // Perform the deletion
        $deleteQuery = $conn->prepare("DELETE FROM googleauth WHERE id = ?");
        $deleteQuery->bind_param('i', $employeeId);

        if ($deleteQuery->execute()) {
            // Deletion successful
            $response = array('status' => 'success', 'message' => 'Punonjësi është fshirë me sukses.');
        } else {
            // Error occurred during deletion
            $response = array('status' => 'error', 'message' => 'Gabim gjatë fshirjes së punonjësit. Ju lutemi provoni përsëri.');
        }

        $deleteQuery->close();
    } else {
        // Invalid or missing employee ID
        $response = array('status' => 'error', 'message' => 'ID e pavlefshme e punonjësit.');
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If the form is not submitted, redirect to the main page or display an error
    echo "Kërkesë e pavlefshme.";
}
