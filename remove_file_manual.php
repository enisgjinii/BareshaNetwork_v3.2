<?php
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceId = $_POST['invoice_id'];

    // Fetch the current file path
    $stmt = $conn->prepare("SELECT file_path FROM invoices WHERE id = ?");
    $stmt->bind_param("i", $invoiceId);
    $stmt->execute();
    $stmt->bind_result($filePath);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the server
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Update the database record to remove the file path and description
    $stmt = $conn->prepare("UPDATE invoices SET file_path = NULL, file_description = NULL WHERE id = ?");
    $stmt->bind_param("i", $invoiceId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "File and description removed successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed."]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
$conn->close();
