<?php
ob_start(); // Start output buffering

include 'conn-d.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$emri = isset($_POST['emri']) ? $_POST['emri'] : '';
$mbiemri = isset($_POST['mbiemri']) ? $_POST['mbiemri'] : '';
$numri_tel = isset($_POST['numri_tel']) ? $_POST['numri_tel'] : '';
$numri_personal = isset($_POST['numri_personal']) ? $_POST['numri_personal'] : '';
$perqindja = isset($_POST['perqindja']) ? $_POST['perqindja'] : '';
$klienti = isset($_POST['klienti']) ? $_POST['klienti'] : '';
$vepra = isset($_POST['vepra']) ? $_POST['vepra'] : '';
$data = isset($_POST['data']) ? $_POST['data'] : '';
$shenime = isset($_POST['shenime']) ? $_POST['shenime'] : '';
$signatureData = isset($_POST['signatureData']) ? $_POST['signatureData'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$emri_artistik = isset($_POST['emriartistik']) ? $_POST['emriartistik'] : '';

// Check if a PDF file has been uploaded
if (!empty($_FILES['pdf_file']['name'])) {
    // Check if the PDF file was uploaded successfully
    if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        // Get the uploaded file name and extension
        $pdf_name = $_FILES['pdf_file']['name'];

        // Set the folder path where the PDF will be stored on the server
        $folder_path = "pdf_files/";

        // Create a unique file name (e.g., timestamp + original name)
        $unique_pdf_name = time() . "_" . $pdf_name;

        // Set the file path where the PDF will be saved on the server
        $file_path = $folder_path . $unique_pdf_name;

        // Move the uploaded file to the desired path
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $file_path)) {
            // PDF file uploaded successfully
        } else {
            // If moving the file failed, show an error message
            echo 'Error moving the PDF file.';
        }
    } else {
        // If the PDF file upload failed, show an error message
        echo 'PDF file upload failed.';
    }
} else {
    // No PDF file was uploaded, so set $file_path to null
    $file_path = null;
}

// Insert data into the database, including the PDF file path
$sql = "INSERT INTO kontrata (emri, mbiemri, numri_i_telefonit, numri_personal, vepra, data, shenim, nenshkrimi, pdf_file, perqindja, klienti, klient_email, emriartistik)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Create a prepared statement
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param(
    "sssssssssssss",
    $emri,
    $mbiemri,
    $numri_tel,
    $numri_personal,
    $vepra,
    $data,
    $shenime,
    $signatureData,
    $file_path, // This may be  null if no PDF file was uploaded
    $perqindja,
    $klienti,
    $email,
    $emri_artistik
);

// Execute the statement
if ($stmt->execute()) {
    // Insertion successful, you can redirect or show a success message here
    header("Location: lista_kontratave.php");
    exit();
} else {
    // Insertion failed, show an error message
    echo '<script>alert("There was an error submitting the signature");</script>';
}

ob_end_flush(); // Flush the output buffer
?>
