<?php
require 'vendor/autoload.php';
include 'conn-d.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log errors
function logError($message)
{
    error_log($message, 3, 'error_log.txt');
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = [
        'emri', 'mbiemri', 'numri_tel', 'numri_personal',
        'perqindja', 'klienti', 'vepra', 'data',
        'shenime', 'signatureData', 'email', 'emriartistik'
    ];

    $data = [];
    foreach ($fields as $field) {
        $data[$field] = isset($_POST[$field]) ? $_POST[$field] : '';
    }

    // Check if a PDF file has been uploaded
    $file_path = null;
    if (!empty($_FILES['pdf_file']['name']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $folder_path = "pdf_files/";
        $unique_pdf_name = time() . "_" . $_FILES['pdf_file']['name'];
        $file_path = $folder_path . $unique_pdf_name;

        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $file_path)) {
            logError('Error moving the PDF file.');
            echo 'Error moving the PDF file.';
            exit;
        }
    }

    // Create text document content
    $text_content = "Kontrata Information:\n";
    foreach ($data as $key => $value) {
        $text_content .= ucfirst($key) . ": " . $value . "\n";
    }
    if ($file_path) {
        $text_content .= "PDF File Path: " . $file_path . "\n";
    }

    // Ensure the directory exists before saving the text document
    $text_folder_path = "text_files/";
    if (!file_exists($text_folder_path)) {
        mkdir($text_folder_path, 0700, true);
    }

    // Save text document locally
    $text_file_path = $text_folder_path . time() . "_kontrata.txt";
    if (file_put_contents($text_file_path, $text_content) === false) {
        logError('Error saving text document.');
        echo 'Error saving text document.';
        exit;
    }

    // Insert data into the database, including the PDF file path
    try {
        $sql = "INSERT INTO kontrata (emri, mbiemri, numri_i_telefonit, numri_personal, vepra, data, shenim, nenshkrimi, pdf_file, perqindja, klienti, klient_email, emriartistik)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssss",
            $data['emri'],
            $data['mbiemri'],
            $data['numri_tel'],
            $data['numri_personal'],
            $data['vepra'],
            $data['data'],
            $data['shenime'],
            $data['signatureData'],
            $file_path,
            $data['perqindja'],
            $data['klienti'],
            $data['email'],
            $data['emriartistik']
        );

        $response = ['success' => $stmt->execute()];

        // Output response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (Exception $e) {
        logError('Database Error: ' . $e->getMessage());
        echo 'Error saving to the database: ' . $e->getMessage();
        exit;
    }
} else {
    echo 'Invalid request method.';
}
