<?php
// Start the session
session_start();

// Set the response header to JSON
header('Content-Type: application/json');

// Include database connection
require_once 'conn-d.php'; // Ensure this path is correct

// Initialize response array
$response = array('status' => '', 'message' => '', 'profile_pic' => '');

// Function to log errors (for server-side logging)
function log_error($error_message)
{
    error_log($error_message, 3, '/path/to/your/error.log'); // Update the path as needed
}

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    $response['status'] = 'error';
    $response['message'] = 'User is not logged in.';
    log_error("User attempted to upload a profile picture without being logged in.");
    echo json_encode($response);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method. Please use POST.';
    log_error("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode($response);
    exit;
}

// Check if file was uploaded without errors
if (!isset($_FILES['profile_pic'])) {
    $response['status'] = 'error';
    $response['message'] = 'No file was uploaded.';
    log_error("No file uploaded by user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

$fileError = $_FILES['profile_pic']['error'];

// Handle PHP file upload errors
switch ($fileError) {
    case UPLOAD_ERR_OK:
        // No error, continue processing
        break;
    case UPLOAD_ERR_INI_SIZE:
        $response['status'] = 'error';
        $response['message'] = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
        log_error("Upload error UPLOAD_ERR_INI_SIZE for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    case UPLOAD_ERR_FORM_SIZE:
        $response['status'] = 'error';
        $response['message'] = 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.';
        log_error("Upload error UPLOAD_ERR_FORM_SIZE for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    case UPLOAD_ERR_PARTIAL:
        $response['status'] = 'error';
        $response['message'] = 'The uploaded file was only partially uploaded.';
        log_error("Upload error UPLOAD_ERR_PARTIAL for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    case UPLOAD_ERR_NO_FILE:
        $response['status'] = 'error';
        $response['message'] = 'No file was uploaded.';
        log_error("Upload error UPLOAD_ERR_NO_FILE for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    case UPLOAD_ERR_NO_TMP_DIR:
        $response['status'] = 'error';
        $response['message'] = 'Missing a temporary folder.';
        log_error("Upload error UPLOAD_ERR_NO_TMP_DIR for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    case UPLOAD_ERR_CANT_WRITE:
        $response['status'] = 'error';
        $response['message'] = 'Failed to write file to disk.';
        log_error("Upload error UPLOAD_ERR_CANT_WRITE for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    case UPLOAD_ERR_EXTENSION:
        $response['status'] = 'error';
        $response['message'] = 'File upload stopped by a PHP extension.';
        log_error("Upload error UPLOAD_ERR_EXTENSION for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    default:
        $response['status'] = 'error';
        $response['message'] = 'Unknown upload error.';
        log_error("Unknown upload error (code $fileError) for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
}

// Proceed with file validation and processing
$fileTmpPath = $_FILES['profile_pic']['tmp_name'];
$fileName = $_FILES['profile_pic']['name'];
$fileSize = $_FILES['profile_pic']['size'];
$fileType = $_FILES['profile_pic']['type'];
$fileNameCmps = pathinfo($fileName);
$fileExtension = strtolower($fileNameCmps['extension']);

// 1. Check for file name length
if (strlen($fileName) > 255) {
    $response['status'] = 'error';
    $response['message'] = 'File name is too long.';
    log_error("File name too long for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 2. Sanitize file name
$sanitizedFileName = preg_replace("/[^A-Z0-9._-]/i", "_", $fileName);

// 3. Generate a unique file name to prevent collisions
$newFileName = 'profile_' . $_SESSION['id'] . '_' . time() . '.' . $fileExtension;

// 4. Allowed file extensions
$allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');

// 5. Validate file extension
if (!in_array($fileExtension, $allowedfileExtensions)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid file extension. Allowed types: ' . implode(', ', $allowedfileExtensions) . '.';
    log_error("Invalid file extension ($fileExtension) for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 6. Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fileTmpPath);
finfo_close($finfo);
$allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif');

if (!in_array($mimeType, $allowedMimeTypes)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid MIME type. Allowed types: ' . implode(', ', $allowedMimeTypes) . '.';
    log_error("Invalid MIME type ($mimeType) for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 7. Validate file size (max 2MB)
$maxFileSize = 2 * 1024 * 1024; // 2MB
if ($fileSize > $maxFileSize) {
    $response['status'] = 'error';
    $response['message'] = 'File size exceeds the maximum limit of 2MB.';
    log_error("File size ($fileSize bytes) exceeds limit for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 8. Validate that the file is a real image
$check = getimagesize($fileTmpPath);
if ($check === false) {
    $response['status'] = 'error';
    $response['message'] = 'Uploaded file is not a valid image.';
    log_error("Uploaded file is not a valid image for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 9. Define the upload directory
$uploadFileDir = 'uploads/profile_pics/';

// 10. Create the upload directory if it doesn't exist
if (!is_dir($uploadFileDir)) {
    if (!mkdir($uploadFileDir, 0755, true)) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to create upload directory.';
        log_error("Failed to create upload directory ($uploadFileDir) for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    }
}

// 11. Define the destination path
$dest_path = $uploadFileDir . $newFileName;

// 12. Move the uploaded file to the destination directory
if (!move_uploaded_file($fileTmpPath, $dest_path)) {
    $response['status'] = 'error';
    $response['message'] = 'There was an error moving the uploaded file.';
    log_error("Failed to move uploaded file to destination ($dest_path) for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 13. Optionally, delete the old profile picture if it's not the default one
$stmt = $conn->prepare("SELECT profile_pic FROM googleauth WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $_SESSION['id']);
    if ($stmt->execute()) {
        $stmt->bind_result($currentProfilePic);
        if ($stmt->fetch()) {
            // Define the default profile picture path
            $defaultPic = 'uploads/profile_pics/default_profile.png';
            if ($currentProfilePic && $currentProfilePic !== $defaultPic && file_exists($currentProfilePic)) {
                if (!unlink($currentProfilePic)) {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to delete the old profile picture.';
                    log_error("Failed to delete old profile picture ($currentProfilePic) for user ID: " . $_SESSION['id']);
                    echo json_encode($response);
                    exit;
                }
            }
        } else {
            // User not found in database
            $response['status'] = 'error';
            $response['message'] = 'User not found in the database.';
            log_error("User not found in database when fetching current profile picture for user ID: " . $_SESSION['id']);
            echo json_encode($response);
            exit;
        }
    } else {
        // Execution failed
        $response['status'] = 'error';
        $response['message'] = 'Failed to execute the profile picture retrieval query.';
        log_error("Failed to execute profile picture retrieval query for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    }
    $stmt->close();
} else {
    // Failed to prepare the statement
    $response['status'] = 'error';
    $response['message'] = 'Failed to prepare the profile picture retrieval statement.';
    log_error("Failed to prepare profile picture retrieval statement for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 14. Update the profile_pic path in the database
$stmt = $conn->prepare("UPDATE googleauth SET profile_pic = ? WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("si", $dest_path, $_SESSION['id']);
    if ($stmt->execute()) {
        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Profile picture updated successfully.';
            $response['profile_pic'] = $dest_path;
        } else {
            // No rows updated, possibly same file
            $response['status'] = 'warning';
            $response['message'] = 'No changes were made to your profile picture.';
            $response['profile_pic'] = $dest_path;
            log_error("No changes made to profile picture for user ID: " . $_SESSION['id']);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to update profile picture in the database.';
        log_error("Failed to execute profile picture update query for user ID: " . $_SESSION['id']);
        echo json_encode($response);
        exit;
    }
    $stmt->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to prepare the profile picture update statement.';
    log_error("Failed to prepare profile picture update statement for user ID: " . $_SESSION['id']);
    echo json_encode($response);
    exit;
}

// 15. Final success response
echo json_encode($response);
exit;
