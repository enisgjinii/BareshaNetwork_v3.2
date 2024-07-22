<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form has been submitted and all required fields are set
if (
    isset(
        $_POST["invoice_number"],
        $_POST["customer_id"],
        $_POST["item"],
        $_POST["total_amount"],
        $_POST["total_amount_after_percentage"],
        $_POST["created_date"],
        $_POST["invoice_status"],
        $_POST["type"]
    )
) {
    // Sanitize and validate form data
    $invoice_number = filter_input(INPUT_POST, "invoice_number", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $customer_id = filter_input(INPUT_POST, "customer_id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $item = filter_input(INPUT_POST, "item", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $total_amount = filter_input(INPUT_POST, "total_amount", FILTER_VALIDATE_FLOAT) ?: 0;
    $total_amount_after_percentage = filter_input(INPUT_POST, "total_amount_after_percentage", FILTER_VALIDATE_FLOAT) ?: 0;
    $total_amount_in_eur = filter_input(INPUT_POST, "total_amount_in_eur", FILTER_VALIDATE_FLOAT) ?: 0;
    $total_amount_in_eur_after_percentage = filter_input(INPUT_POST, "total_amount_after_percentage_in_eur", FILTER_VALIDATE_FLOAT) ?: 0;
    $created_date = filter_input(INPUT_POST, "created_date", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: date('Y-m-d');
    $status = filter_input(INPUT_POST, "invoice_status", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Check if a file is uploaded
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file_upload']['name']);
        $file_tmp = $_FILES['file_upload']['tmp_name'];
        $file_destination = 'uploads/' . $file_name;

        // Move uploaded file to destination directory
        if (!move_uploaded_file($file_tmp, $file_destination)) {
            die("Failed to move uploaded file.");
        }
    } else {
        $file_destination = null; // No file uploaded
    }

    // Connect to the database
    require_once 'conn-d.php';

    // Check the database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, created_date, state_of_invoice, total_amount_in_eur, total_amount_in_eur_after_percentage, type, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("sisddsdddss", $invoice_number, $customer_id, $item, $total_amount, $total_amount_after_percentage, $created_date, $status, $total_amount_in_eur, $total_amount_in_eur_after_percentage, $type, $file_destination);

    // Get client name from table klientet based on customer_id
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

    if ($type == "grupor") {
        // Insert first invoice
        if (!$stmt->execute()) {
            error_log("Error inserting first invoice: " . $stmt->error);
            die("An error occurred while processing the invoice.");
        }

        // Generate new invoice number for the second invoice
        $new_invoice_number = $invoice_number . "-2";

        // Bind new invoice number for the second invoice
        $stmt->bind_param("sisddsddds", $new_invoice_number, $customer_id, $item, $total_amount, $total_amount_after_percentage, $created_date, $status, $total_amount_in_eur, $total_amount_in_eur_after_percentage, $type);

        // Insert second invoice
        if (!$stmt->execute()) {
            error_log("Error inserting second invoice: " . $stmt->error);
            die("An error occurred while processing the invoice.");
        }
    } else {
        if ($stmt->execute()) {
            // Close statements and connection
            $stmt->close();
            $stmt2->close();
            $conn->close();
            // Redirect to the invoice page
            header("Location: invoice.php");
            exit;
        } else {
            error_log("Error inserting invoice: " . $stmt->error);
            die("An error occurred while processing the invoice.");
        }
    }
} else {
    die("All required fields are not set.");
}
