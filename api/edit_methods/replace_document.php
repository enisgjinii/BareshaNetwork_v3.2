<?php
// replace_document.php

include '../../conn-d.php'; // Adjust the path if necessary

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Check if a file was uploaded without errors
    if (isset($_FILES['newDocument']) && $_FILES['newDocument']['error'] == 0) {
        // Fetch the old document path from the database
        $sql = "SELECT document_path FROM invoices_kont WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $response['message'] = 'Database error: ' . $conn->error;
            echo json_encode($response);
            exit;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($oldDocumentPath);
        if ($stmt->fetch()) {
            $stmt->close();

            // Delete the old document file
            if (file_exists('../../' . $oldDocumentPath)) {
                unlink('../../' . $oldDocumentPath);
            }

            // Handle the new file upload
            $targetDir = '../../uploads/'; // Ensure this directory exists and is writable
            $fileName = basename($_FILES['newDocument']['name']);
            $targetFilePath = $targetDir . $fileName;

            // Avoid filename collisions
            $i = 1;
            $fileBaseName = pathinfo($fileName, PATHINFO_FILENAME);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            while (file_exists($targetFilePath)) {
                $fileName = $fileBaseName . '_' . $i . '.' . $fileExtension;
                $targetFilePath = $targetDir . $fileName;
                $i++;
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['newDocument']['tmp_name'], $targetFilePath)) {
                // Update the database with the new document path
                $newDocumentPath = 'uploads/' . $fileName; // Adjust as needed

                $updateSql = "UPDATE invoices_kont SET document_path = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                if (!$updateStmt) {
                    $response['message'] = 'Database error: ' . $conn->error;
                    echo json_encode($response);
                    exit;
                }
                $updateStmt->bind_param('si', $newDocumentPath, $id);
                if ($updateStmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Failed to update the database.';
                }
                $updateStmt->close();
            } else {
                $response['message'] = 'Failed to upload the new file.';
            }
        } else {
            $response['message'] = 'Record not found.';
            $stmt->close();
        }
    } else {
        $response['message'] = 'No file uploaded or an error occurred during the upload.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
