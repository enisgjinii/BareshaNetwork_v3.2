<?php

// Establish a connection to the database
include 'conn-d.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form data
    $supplierName = mysqli_real_escape_string($conn, $_POST["supplier_name"]);
    $invoiceNumber = mysqli_real_escape_string($conn, $_POST["invoice_number"]);
    $invoiceAmount = mysqli_real_escape_string($conn, $_POST["invoice_amount"]);
    $invoiceDate = mysqli_real_escape_string($conn, $_POST["invoice_date"]);
    $paymentStatus = mysqli_real_escape_string($conn, $_POST["payment_status"]);

    // Handle file upload
    $uploadDir = "investimet_e_objektit/";
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
            // Success message using JavaScript alert
            echo "<script>
                    alert('Investimi u regjistrua me sukses');
                    window.location.href = 'office_investments.php'; // Redirect to the form page
                  </script>";
        } else {
            // Error message using JavaScript alert
            echo "<script>
                    alert('Gabim gjate regjistrimit. Error: " . $stmt->error . "');
                    window.location.href = 'office_investments.php'; // Redirect to the form page
                  </script>";
        }

        // Close statement
        $stmt->close();
    } else {
        // Error message for file upload using JavaScript alert
        echo "<script>
                alert('Gabim gjate ngarkimit te fatures');
                window.location.href = 'your_form_page.php'; // Redirect to the form page
              </script>";
    }

    // Close connection
    $conn->close();
} else {
    // Redirect to the form page if accessed directly without submission
    header("Location: office_investments.php");
    exit();
}
