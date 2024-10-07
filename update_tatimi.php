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
    // Retrieve and sanitize form data
    $id            = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $kategoria     = isset($_POST['kategoria']) ? $conn->real_escape_string($_POST['kategoria']) : '';
    $data_pageses  = isset($_POST['data_pageses']) ? $conn->real_escape_string($_POST['data_pageses']) : '';
    $pershkrimi    = isset($_POST['pershkrimi']) ? $conn->real_escape_string($_POST['pershkrimi']) : '';
    $periudha      = isset($_POST['periudha']) ? $conn->real_escape_string($_POST['periudha']) : '';
    $vlera         = isset($_POST['vlera']) ? floatval($_POST['vlera']) : 0.00;
    $forma_pageses = isset($_POST['forma_pageses']) ? $conn->real_escape_string($_POST['forma_pageses']) : '';

    // Validate required fields
    if ($id <= 0 || empty($kategoria) || empty($data_pageses) || empty($pershkrimi) || empty($periudha) || empty($forma_pageses)) {
        $response['status'] = 'error';
        $response['message'] = 'Ju lutem plotësoni të gjitha fushat e kërkuara.';
        echo json_encode($response);
        exit();
    }

    // Initialize variables for file upload
    $upload_dir = 'uploads/';
    $dokument_path = '';

    // Handle file upload if a new file is provided
    if (isset($_FILES['dokument']) && $_FILES['dokument']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['dokument']['tmp_name'];
        $file_name = basename($_FILES['dokument']['name']);
        $file_size = $_FILES['dokument']['size'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Define allowed file types
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        // Validate file type
        if (!in_array($file_ext, $allowed)) {
            $response['status'] = 'error';
            $response['message'] = "Lloji i skedarit nuk lejohet. Lejohen: " . implode(', ', $allowed) . ".";
            echo json_encode($response);
            exit();
        }

        // Validate file size (max 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            $response['status'] = 'error';
            $response['message'] = "Madhësia e skedarit nuk duhet të tejkalojë 5MB.";
            echo json_encode($response);
            exit();
        }

        // Generate a unique file name to prevent overwriting
        $new_file_name = uniqid('dokument_', true) . '.' . $file_ext;

        // Create the uploads directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            $response['status'] = 'error';
            $response['message'] = "Dështoi ngarkimi i skedarit.";
            echo json_encode($response);
            exit();
        }

        // Set the dokument path to store in the database
        $dokument_path = $upload_dir . $new_file_name;

        // Fetch the existing dokument path to delete the old file
        $stmt = $conn->prepare("SELECT dokument FROM tatimi WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($existing_dokument);
            $stmt->fetch();
            $stmt->close();

            // Delete the old file if it exists
            if (!empty($existing_dokument) && file_exists($existing_dokument)) {
                unlink($existing_dokument);
            }
        }
    }

    // Prepare the SQL statement
    if (!empty($dokument_path)) {
        // If a new dokument is uploaded, update all fields including dokument
        $stmt = $conn->prepare("UPDATE tatimi SET kategoria = ?, data_pageses = ?, pershkrimi = ?, periudha = ?, vlera = ?, forma_pageses = ?, dokument = ? WHERE id = ?");
    } else {
        // If no new dokument is uploaded, update all fields except dokument
        $stmt = $conn->prepare("UPDATE tatimi SET kategoria = ?, data_pageses = ?, pershkrimi = ?, periudha = ?, vlera = ?, forma_pageses = ? WHERE id = ?");
    }

    if ($stmt === false) {
        $response['status'] = 'error';
        $response['message'] = "Gabim në përgatitjen e pyetjes së SQL: " . $conn->error;
        echo json_encode($response);
        exit();
    }

    // Bind parameters to the SQL statement
    if (!empty($dokument_path)) {
        // Include dokument in the update
        $stmt->bind_param("ssssdssi", $kategoria, $data_pageses, $pershkrimi, $periudha, $vlera, $forma_pageses, $dokument_path, $id);
    } else {
        // Exclude dokument from the update
        $stmt->bind_param("ssssdsi", $kategoria, $data_pageses, $pershkrimi, $periudha, $vlera, $forma_pageses, $id);
    }

    // Execute the statement
    if ($stmt->execute()) {
        // Success: Return a success response
        $response['status'] = 'success';
        $response['message'] = "Rekordi u përditësua me sukses.";
    } else {
        // Error during execution
        $response['status'] = 'error';
        $response['message'] = "Gabim gjatë përditësimit të të dhënave: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Return the response as JSON
    echo json_encode($response);
} else {
    // If the request method is not POST
    $response['status'] = 'error';
    $response['message'] = "Kërkesa e pavlefshme.";
    echo json_encode($response);
    exit();
}
