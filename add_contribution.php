<?php

// Connect to database (assuming 'conn-d.php' contains database connection code)
include 'conn-d.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $date = $description = $period = $document_path = $forma = NULL;
    $value = 0;

    // Retrieve and sanitize data from POST request
    if (!empty($_POST['date'])) {
        $date = $_POST['date'];
    }
    if (!empty($_POST['text'])) {
        $description = $_POST['text'];
    }
    if (!empty($_POST['periodk'])) {
        $period = $_POST['periodk'];
    }
    if (!empty($_POST['value'])) {
        $value = $_POST['value'];
    }
    if (!empty($_POST['formak'])) {
        $forma = $_POST['formak'];
    }

    // Check if file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $temp_file = $_FILES['file']['tmp_name'];
            $upload_dir = 'contributions/'; // Directory to store uploaded files

            // Generate a unique filename to prevent overwriting
            $upload_file = $upload_dir . uniqid('file_', true) . '_' . basename($_FILES['file']['name']);

            // Move uploaded file to the upload directory
            if (move_uploaded_file($temp_file, $upload_file)) {
                $document_path = $upload_file;
            } else {
                echo "Error uploading file.";
                // Handle error - file upload failed
            }
        } else {
            echo "Error uploading file: " . $_FILES['file']['error'];
            // Handle error - file upload error other than UPLOAD_ERR_NO_FILE
        }
    }

    // Insert data into database
    $sql = "INSERT INTO contributions (date, description, period, value, document_path, payment_method ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("sssdss", $date, $description, $period, $value, $document_path, $forma);

    if ($stmt->execute()) {
        echo "New contribution added successfully.";
        // Handle success
        header("Location: ttatimi.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Close database connection
$conn->close();
