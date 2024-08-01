<?php
require 'vendor/autoload.php';
include 'conn-d.php';

use Google\Client;
use Google\Service\Drive;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log errors
function logError($message)
{
    error_log($message, 3, 'error_log.txt');
}

// Initialize Google Client
function getClient()
{
    try {
        $client = new Client();
        $client->setAuthConfig('client.json');
        $client->addScope(Drive::DRIVE_FILE);
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Retrieve the refresh token from cookie
        if (isset($_COOKIE['refreshToken'])) {
            $refreshToken = $_COOKIE['refreshToken'];
            $client->fetchAccessTokenWithRefreshToken($refreshToken);
            $accessToken = $client->getAccessToken();
            $client->setAccessToken($accessToken);

            // Save the new access token to a file (optional)
            $tokenPath = 'token.json';
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            throw new Exception('Refresh token not found in cookies.');
        }
    } catch (Exception $e) {
        logError('Google Client Initialization Error: ' . $e->getMessage());
        throw $e;
    }

    return $client;
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

    try {
        // Get Google Client
        $client = getClient();
        $service = new Drive($client);

        // Folder ID
        $folderId = '1HLVc7GzZZZp0EyfPU1zpD0xOwJjOSmoT'; // Replace with the correct folder ID

        // Check if the folder exists and is accessible
        try {
            $folder = $service->files->get($folderId, ['fields' => 'id']);
        } catch (Exception $e) {
            // If the folder is not found, create a new one
            if ($e->getCode() == 404) {
                $folderMetadata = new Drive\DriveFile([
                    'name' => 'Kontrata Files',
                    'mimeType' => 'application/vnd.google-apps.folder'
                ]);
                $folder = $service->files->create($folderMetadata, ['fields' => 'id']);
                $folderId = $folder->id;
                logError('Created new folder with ID: ' . $folderId);
            } else {
                throw $e; // Re-throw if it's a different error
            }
        }

        $fileMetadata = new Drive\DriveFile([
            'name' => basename($text_file_path),
            'parents' => [$folderId]
        ]);
        $content = file_get_contents($text_file_path);
        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'text/plain',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
        logError('File ID: ' . $file->id);
    } catch (Exception $e) {
        logError('Google Drive API Error: ' . $e->getMessage());
        echo 'Error uploading to Google Drive: ' . $e->getMessage();
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
