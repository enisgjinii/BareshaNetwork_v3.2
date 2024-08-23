<?php
// Establish a connection to the database
include '../../conn-d.php';

// Create a response array
$response = array();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form data
    $supplierName = mysqli_real_escape_string($conn, $_POST["supplier_name"]);
    $invoiceNumber = mysqli_real_escape_string($conn, $_POST["invoice_number"]);
    $invoiceAmount = mysqli_real_escape_string($conn, $_POST["invoice_amount"]);
    $invoiceDate = mysqli_real_escape_string($conn, $_POST["invoice_date"]);
    $paymentStatus = mysqli_real_escape_string($conn, $_POST["payment_status"]);

    // Handle file upload
    $uploadDir = "../../investimet_e_objektit/";
    $uploadFile = $uploadDir . basename($_FILES["invoice_scan"]["name"]);

    // Validate file type and size
    $allowedFileTypes = ['pdf', 'jpg', 'jpeg', 'png'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    $fileExtension = pathinfo($_FILES["invoice_scan"]["name"], PATHINFO_EXTENSION);

    if (
        in_array($fileExtension, $allowedFileTypes) &&
        $_FILES["invoice_scan"]["size"] <= $maxFileSize &&
        move_uploaded_file($_FILES["invoice_scan"]["tmp_name"], $uploadFile)
    ) {
        // File uploaded successfully

        // Insert data into the database using prepared statement
        $sql = "INSERT INTO investments (supplier_name, invoice_number, invoice_amount, invoice_date, payment_status, invoice_scan_path)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsss", $supplierName, $invoiceNumber, $invoiceAmount, $invoiceDate, $paymentStatus, $uploadFile);

        if ($stmt->execute()) {
            // Success message in the response
            $response['status'] = 'success';
            $response['message'] = 'Investimi u regjistrua me sukses';
        } else {
            // Error message in the response
            $response['status'] = 'error';
            $response['message'] = "Gabim gjate regjistrimit. Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        // Error message for file upload in the response
        $response['status'] = 'error';
        $response['message'] = 'Gabim gjate ngarkimit te fatures';
    }

    // Close connection
    $conn->close();

    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Redirect to the form page if accessed directly without submission
    header("Location: office_investments.php");
    exit();
}
