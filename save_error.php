<?php
// Get the error message from the AJAX request
$error_message = $_POST['error_message'];

// Define the path to the JSON file
$json_file = 'errors.json';

// Load existing errors from JSON file
$errors = json_decode(file_get_contents($json_file), true);

// Add the new error message
$errors[] = $error_message;

// Save the updated errors to the JSON file
file_put_contents($json_file, json_encode($errors));

// Respond to the AJAX request
echo 'Error message saved successfully.';
