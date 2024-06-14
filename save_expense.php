<?php
include 'conn-d.php';

function handle_error($message)
{
    // Log the error
    error_log($message, 0);

    // Redirect user to an error page or display a generic error message
    header('Location: error_page.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $registruesi = $_POST['recipient-name'];
        $pershkrimi = $_POST['message'];
        $shuma = $_POST['amount'];
        $dokumenti = $_FILES['file']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($dokumenti);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Input validation to prevent SQL injection attacks
        $registruesi = mysqli_real_escape_string($conn, $registruesi);
        $pershkrimi = mysqli_real_escape_string($conn, $pershkrimi);
        $shuma = mysqli_real_escape_string($conn, $shuma);
        $dokumenti = mysqli_real_escape_string($conn, $dokumenti);

        // Centralized file upload validations
        if ($_FILES["file"]["size"] > 500000) {
            throw new Exception("Skedari juaj është shumë i madh.");
        }

        if (file_exists($target_file)) {
            throw new Exception("Skedari ekziston tashmë.");
        }

        // Whitelist approach for allowed file types
        $allowed_file_types = array("jpg", "jpeg", "png", "gif", "bmp", "svg");
        if (!in_array($imageFileType, $allowed_file_types)) {
            throw new Exception("Vetëm formatet JPG, JPEG, PNG, GIF, BMP dhe SVG janë të lejuara.");
        }

        // Move the uploaded file after all checks pass
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            throw new Exception("Ndodhi një problem gjatë ngarkimit të skedarit.");
        }

        // Insert data into the database only when upload is successful
        $query = "INSERT INTO expenses (registruesi, pershkrimi, shuma, dokumenti) VALUES ('$registruesi', '$pershkrimi', '$shuma', '$dokumenti')";
        if (!$conn->query($query)) {
            throw new Exception("Gabim gjatë përpjekjes për të shtuar të dhënat: " . $conn->error);
        }

        // Close the database connection
        $conn->close();

        // Redirect upon successful upload
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (Exception $e) {
        handle_error($e->getMessage());
    }
}
