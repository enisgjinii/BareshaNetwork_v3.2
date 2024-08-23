<?php
// Check if the backup file name is provided
if (isset($_GET['backupFile'])) {
    // Define the directory where the backup files are stored
    $backupDirectory = '../../backups';

    // Get the backup file name from the request
    $backupFile = $_GET['backupFile'];

    // Construct the full path to the backup file
    $backupFilePath = $backupDirectory . '/' . $backupFile;

    // Check if the backup file exists
    if (file_exists($backupFilePath)) {
        // Attempt to delete the backup file
        if (unlink($backupFilePath)) {
            // Return a success message
            echo 'Backup file deleted successfully.';
        } else {
            // Return an error message
            echo 'Failed to delete the backup file.';
        }
    } else {
        // Return an error message if the backup file does not exist
        echo 'Backup file does not exist.';
    }
} else {
    // Return an error message if the backup file name is not provided
    echo 'Backup file name is required.';
}
?>