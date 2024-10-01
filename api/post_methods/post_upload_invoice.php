<?php
include '../../conn-d.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $invoice_id = $_POST['invoice_id'];
    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['file']['name']);
    $target_file = $upload_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file type is allowed
    $allowed_types = array('pdf', 'doc', 'docx');
    if (!in_array($file_type, $allowed_types)) {
        echo "Error: Only PDF, DOC, and DOCX files are allowed.";
        exit;
    }

    // Check if file was uploaded without errors
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        // Update database with file path
        $sql = "UPDATE invoices SET file_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $target_file, $invoice_id);

        if ($stmt->execute()) {
            echo "File uploaded and database updated successfully.";
            header("Location: ../../invoice.php");
            exit;
        } else {
            echo "Error updating database: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}

$conn->close();
