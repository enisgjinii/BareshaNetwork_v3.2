<?php
// Start or resume the session
session_start();

// Replace 'your_secret_password' with your actual password
$expectedPassword = 'baresha_2023';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the entered password from the POST data
    $enteredPassword = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate the password
    if ($enteredPassword === $expectedPassword) {
        // Correct password
        $_SESSION['authenticated'] = time() + 500; // Set session expiration time (1 hour in this example)
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Incorrect password
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        exit;
    }
} else {
    // Handle non-POST requests
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
