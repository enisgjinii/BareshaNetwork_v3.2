<?php
include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceId = $_POST['invoice_id'];
    $descriptionOfFile = $_POST['descriptionOfFile']; // Retrieve the description
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is a PDF or DOC/DOCX
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo json_encode(["status" => "error", "message" => "Only PDF, DOC, and DOCX files are allowed."]);
        exit;
    }

    // Move uploaded file to the target directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        // Update the database record
        $stmt = $conn->prepare("UPDATE invoices SET file_path = ?, file_description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $targetFile, $descriptionOfFile, $invoiceId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "File uploaded/updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to move uploaded file."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
$conn->close();
?>
