<?php
$error = isset($_POST['error']) ? $_POST['error'] : '';

if (!empty($error)) {
    // Define the path to the JSON file
    $jsonFilePath = 'errors/errors.json';

    // Read existing errors from JSON file
    $existingErrors = [];
    if (file_exists($jsonFilePath)) {
        $existingErrors = json_decode(file_get_contents($jsonFilePath), true);
    }

    // Append the new error to the existing errors array
    $existingErrors[] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'error_message' => $error
    ];

    // Save the updated errors array back to the JSON file
    file_put_contents($jsonFilePath, json_encode($existingErrors, JSON_PRETTY_PRINT));

    // Return a success response
    echo "Error saved successfully.";
} else {
    // Return an error response if no error message was provided
    echo "Error message is empty.";
}
