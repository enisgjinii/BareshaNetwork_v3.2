<?php
include 'conn-d.php';

// Check if expense ID is provided
if (isset($_GET['expense_id'])) {
    $expenseId = $_GET['expense_id'];

    // Prepare SQL query to fetch expense data
    $query = "SELECT * FROM expenses WHERE id = ?";

    // Prepare statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $expenseId);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch expense data
    $expense = $result->fetch_assoc();

    // Return JSON response
    echo json_encode($expense);
} else {
    // If expense ID is not provided, return error message
    echo json_encode(array("error" => "Expense ID is not provided"));
}
