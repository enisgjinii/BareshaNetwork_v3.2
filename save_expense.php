<?php
include 'conn-d.php';

function handle_error($message, $status_code = 500)
{
    http_response_code($status_code);
    error_log($message, 0);
    header('Location: error_page.php', true, 302);
    exit;
}

function close_database_connection($conn)
{
    $conn->close();
}

// Prevent direct access to the script
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    handle_error("Invalid request", 400);
}

try {
    // Implementing output buffering to prevent "Headers already sent" issues
    ob_start();

    $registruesi = trim($_POST['recipient-name'] ?? '');
    $pershkrimi = trim($_POST['message'] ?? '');
    $shuma = trim($_POST['amount'] ?? '');
    $dokumenti = $_FILES['file']['name'] ?? '';
    $dokumenti_tmp = $_FILES['file']['tmp_name'] ?? '';
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($dokumenti);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Input validation to prevent SQL injection attacks
    if (empty($registruesi) || empty($pershkrimi) || empty($shuma) || empty($dokumenti)) {
        throw new Exception("Të gjitha fushat duhet të plotësohen.");
    }

    // Ensure that the uploaded file is of an allowed type
    $allowed_file_types = array("jpg", "jpeg", "png", "gif", "bmp", "svg");
    if (!in_array($imageFileType, $allowed_file_types)) {
        throw new Exception("Formati i skedarit nuk është i lejuar. Vetëm formatet JPG, JPEG, PNG, GIF, BMP dhe SVG janë të lejuara.");
    }

    // Check if the file was uploaded without errors
    if (!is_uploaded_file($dokumenti_tmp)) {
        throw new Exception("Skedari nuk u ngarkua me sukses.");
    }

    // Generate a unique file name to prevent overwriting existing files
    $new_filename = uniqid('file_', true) . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;

    // Move the uploaded file after all checks pass
    if (!move_uploaded_file($dokumenti_tmp, $target_file)) {
        throw new Exception("Ndodhi një problem gjatë ngarkimit të skedarit.");
    }

    // Database operations within a try-catch block
    try {
        $conn->begin_transaction();

        $query = "INSERT INTO expenses (registruesi, pershkrimi, shuma, dokumenti) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $registruesi, $pershkrimi, $shuma, $new_filename);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        throw new Exception("Gabim gjatë përpjekjes për të shtuar të dhënat: " . $e->getMessage());
    }

    // Close the database connection
    close_database_connection($conn);

    // Redirect upon successful upload
    header('Location: success_page.php');
    exit;
} catch (Exception $e) {
    // Clear the output buffer
    ob_end_clean();
    handle_error($e->getMessage());
}
