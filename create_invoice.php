<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form has been submitted
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
    $total_amount_in_eur = isset($_POST["total_amount_in_eur"]) ? $_POST["total_amount_in_eur"] : 0;
    $total_amount_in_eur_after_percentage = isset($_POST["total_amount_after_percentage_in_eur"]) ? $_POST["total_amount_after_percentage_in_eur"] : 0;
    $created_date = isset($_POST["created_date"]) ? $_POST["created_date"] : date('Y-m-d');
    $status = $_POST["invoice_status"];  // Retrieve the invoice status from the form

    // Connect to the database
    require_once 'conn-d.php';

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, created_date, state_of_invoice, total_amount_in_eur, total_amount_in_eur_after_percentage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("sisddsddd", $invoice_number, $customer_id, $item, $total_amount, $total_amount_after_percentage, $created_date, $status, $total_amount_in_eur, $total_amount_in_eur_after_percentage);

    // Get name of client from table klientet based on customer_id
    $stmt2 = $conn->prepare("SELECT emri FROM klientet WHERE id = ?");

    // Check if the statement was prepared successfully
    if ($stmt2 === false) {
        die("Failed to prepare the SQL statement for client name: " . $conn->error);
    }

    $stmt2->bind_param("s", $customer_id);
    $stmt2->execute();

    // Check if the execution was successful
    if ($stmt2->error) {
        die("Error executing query: " . $stmt2->error);
    }

    $result = $stmt2->get_result();

    // Check if the result was retrieved successfully
    if ($result === false) {
        die("Error getting result: " . $stmt2->error);
    }

    $row = $result->fetch_assoc();
    $client_name = $row['emri'];

    if ($stmt->execute()) {
        // Close statements and connection
        $stmt->close();
        $stmt2->close();
        $conn->close();

        // Redirect to the invoice page
        header("Location: invoice.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "All required fields are not set.";
}
