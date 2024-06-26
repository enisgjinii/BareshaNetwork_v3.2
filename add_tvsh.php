<?php
// Connect to database (assuming 'conn-d.php' contains database connection code)
include 'conn-d.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $date = $description = $period = $document_path = $forma ='';
    $value = 0;

    // Retrieve and sanitize data from POST request
    if (isset($_POST['datetvsh'])) {
        $date = $_POST['datetvsh'];
    }
    if (isset($_POST['text'])) {
        $description = $_POST['text'];
    }
    if (isset($_POST['periodtvsh'])) {
        $period = $_POST['periodtvsh'];
    }
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    if (isset($_POST['formatvsh'])) {
        $forma = $_POST['formatvsh'];
    }

    // Check if file was uploaded
    if (isset($_FILES['file'])) {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $temp_file = $_FILES['file']['tmp_name'];
            $upload_dir = 'tvsh/'; // Directory to store uploaded files

            // Generate a unique filename to prevent overwriting
            $upload_file = $upload_dir . uniqid('file_', true) . '_' . basename($_FILES['file']['name']);

            // Move uploaded file to the upload directory
            if (move_uploaded_file($temp_file, $upload_file)) {
                $document_path = $upload_file;
            } else {
                echo "Error uploading file.";
                // Handle error - file upload failed
            }
        } elseif ($_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            echo "Error uploading file: " . $_FILES['file']['error'];
            // Handle error - file upload error other than UPLOAD_ERR_NO_FILE
        }
    } else {
        echo "No file uploaded.";
        // Handle case where no file was uploaded
    }

    // Insert data into database if all required fields are valid
    if (!empty($date) && !empty($description) && !empty($period) && !empty($value)) {
        if ($document_path === '') {
            $document_path = NULL; // Set to NULL if no file was uploaded
        }

        $sql = "INSERT INTO tvsh (date, description, period, value, document_path, payment_method ) VALUES (?, ?, ?, ?, ?, ?)";

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
    } else {
        echo "Please fill in all required fields.";
        // Handle error - required fields not filled
    }
}

// Close database connection
$conn->close();
