<?php
if (
    isset($_POST["invoice_number"]) &&
    isset($_POST["customer_id"]) &&
    isset($_POST["item"]) &&
    isset($_POST["total_amount"]) &&
    isset($_POST["total_amount_after_percentage"]) &&
    isset($_POST["created_date"]) &&
    isset($_POST["invoice_status"])
) {
    // Form values are set, proceed with processing the form data

    $invoice_number = $_POST["invoice_number"];
    $customer_id = $_POST["customer_id"];
    $item = $_POST["item"];
    $total_amount = isset($_POST["total_amount"]) ? $_POST["total_amount"] : 0;
    $total_amount_after_percentage = isset($_POST["total_amount_after_percentage"]) ? $_POST["total_amount_after_percentage"] : 0;
    $created_date = isset($_POST["created_date"]) ? $_POST["created_date"] : date('Y-m-d');
    $status = $_POST["invoice_status"];  // Retrieve the invoice status from the form

    // Connect to the database
    require_once 'conn-d.php';

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, created_date, state_of_invoice) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssdsss", $invoice_number, $customer_id, $item, $total_amount, $total_amount_after_percentage, $created_date, $status);

    if ($stmt->execute()) {
        $stmt->close();
        mysqli_close($conn);
        header("Location: invoice.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
