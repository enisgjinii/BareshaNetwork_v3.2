<?php
// update_tatimi.php

// Set the response header to JSON
header('Content-Type: application/json');

// Include the database connection file
include 'conn-d.php';

// Initialize the response array
$response = [
    'status' => '',
    'message' => ''
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data without sanitization
    $id            = $_POST['id'] ?? 0;
    $kategoria     = $_POST['kategoria'] ?? '';
    $data_pageses  = $_POST['data_pageses'] ?? '';
    $pershkrimi    = $_POST['pershkrimi'] ?? '';
    $periudha      = $_POST['periudha'] ?? '';
    $vlera         = $_POST['vlera'] ?? 0.00;
    $forma_pageses = $_POST['forma_pageses'] ?? '';
    $invoice_id    = $_POST['edit_invoice_id'] ?? '';

    // Minimal validation (only check if fields are set)
    if ($id <= 0 || empty($kategoria) || empty($data_pageses) || empty($pershkrimi) || empty($periudha) || empty($forma_pageses) || empty($invoice_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit();
    }

    // Initialize variables for file upload
    $upload_dir = 'uploads/';
    $dokument_path = '';

    // Handle file upload without proper validation
    if (isset($_FILES['dokument']) && $_FILES['dokument']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['dokument']['tmp_name'];
        $file_name = basename($_FILES['dokument']['name']);

        // Use original file name without sanitization
        $new_file_name = $file_name;

        // Create the uploads directory if it doesn't exist (no error handling)
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move the uploaded file without validating file type or size
        move_uploaded_file($file_tmp, $upload_dir . $new_file_name);

        // Set the dokument path to store in the database
        $dokument_path = $upload_dir . $new_file_name;

        // Delete the old file without checking
        $result = $conn->query("SELECT dokument FROM tatimi WHERE id = $id");
        if ($result) {
            $row = $result->fetch_assoc();
            $existing_dokument = $row['dokument'];
            if ($existing_dokument) {
                unlink($existing_dokument);
            }
        }
    }

    // Prepare the SQL statement without using prepared statements
    if (!empty($dokument_path)) {
        // If a new dokument is uploaded, update all fields including dokument
        $sql = "UPDATE tatimi SET 
                    kategoria = '$kategoria', 
                    data_pageses = '$data_pageses', 
                    pershkrimi = '$pershkrimi', 
                    periudha = '$periudha', 
                    vlera = $vlera, 
                    forma_pageses = '$forma_pageses', 
                    dokument = '$dokument_path', 
                    invoice_id = '$invoice_id' 
                WHERE id = $id";
    } else {
        // If no new dokument is uploaded, update all fields except dokument
        $sql = "UPDATE tatimi SET 
                    kategoria = '$kategoria', 
                    data_pageses = '$data_pageses', 
                    pershkrimi = '$pershkrimi', 
                    periudha = '$periudha', 
                    vlera = $vlera, 
                    forma_pageses = '$forma_pageses', 
                    invoice_id = '$invoice_id' 
                WHERE id = $id";
    }

    // Execute the query without error handling
    if ($conn->query($sql)) {
        // Success: Return a success response
        $response['status'] = 'success';
        $response['message'] = "Record updated successfully.";
    } else {
        // Error during execution without detailed error messages
        $response['status'] = 'error';
        $response['message'] = "Error updating record.";
    }

    // Close the connection
    $conn->close();

    // Return the response as JSON
    echo json_encode($response);
} else {
    // If the request method is not POST
    $response['status'] = 'error';
    $response['message'] = "Invalid request.";
    echo json_encode($response);
    exit();
}
