<?php
include 'conn-d.php';

// Check if expense ID is provided
if (isset($_GET['expense_id'])) {
    $expenseId = $_GET['expense_id'];

    try {
        // Prepare SQL query to fetch expense data
        $query = "SELECT * FROM expenses WHERE id = ?";

        // Prepare statement
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Preparation of statement failed.");
        }

        // Bind parameters
        $stmt->bind_param("i", $expenseId);

        // Execute statement
        if (!$stmt->execute()) {
            throw new Exception("Execution of statement failed.");
        }

        // Get result
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Getting result failed.");
        }

        // Check if at least one row is fetched
        if ($result->num_rows > 0) {
            // Fetch expense data
            $expense = $result->fetch_assoc();

            // Close the statement to release associated memory resources
            $stmt->close();

            // Return JSON response
            http_response_code(200); // OK
            echo json_encode($expense);
        } else {
            http_response_code(404); // Not Found
            throw new Exception("Expense with the provided ID does not exist.");
        }
    } catch (Exception $e) {
        // Return error message
        http_response_code(500); // Internal Server Error
        echo json_encode(array("error" => $e->getMessage()));
    }
} else {
    // If expense ID is not provided, return error message
    http_response_code(400); // Bad Request
    echo json_encode(array("error" => "Expense ID is not provided"));
}
