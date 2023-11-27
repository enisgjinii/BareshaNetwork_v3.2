<?php
// Set error reporting to maximum for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log errors to a file
function logError($errorMessage)
{
    $logFilePath = 'error_log.txt'; // Set the path to your log file

    // Create or append to the log file
    if ($fileHandle = fopen($logFilePath, 'a')) {
        $logEntry = date('Y-m-d H:i:s') . ': ' . $errorMessage . PHP_EOL;
        fwrite($fileHandle, $logEntry);
        fclose($fileHandle);
    } else {
        // Handle the situation where the log file cannot be opened
        echo "Error: Unable to open or create the log file.";
    }
}

// Example: Triggering an error for testing
$exampleError = "This is a test error.";
logError($exampleError);

// In your main application, you can include this file and call logError() with appropriate messages.
