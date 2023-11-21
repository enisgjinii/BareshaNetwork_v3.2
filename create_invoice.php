<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoice_number = $_POST["invoice_number"];
    $customer_id = $_POST["customer_id"];
    $item = $_POST["item"];
    $total_amount = isset($_POST["total_amount"]) ? $_POST["total_amount"] : 0;
    $total_amount_after_percentage = isset($_POST["total_amount_after_percentage"]) ? $_POST["total_amount_after_percentage"] : 0;
    $created_date = isset($_POST["created_date"]) ? $_POST["created_date"] : date('Y-m-d');
    $status = $_POST["invoice_status"];  // Retrieve the invoice status from the form

    // Connect to the database
    require_once 'conn-d.php';

    // SQL query to insert a new invoice into the database
    $sql = "INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, created_date, state_of_invoice) VALUES ('$invoice_number', '$customer_id', '$item', $total_amount, $total_amount_after_percentage, '$created_date', '$status')";

    if (mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        header("Location: invoice.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
