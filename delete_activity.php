<?php
// delete_activity.php

session_start();

// Include the database connection
include 'conn-d.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $activity_id = intval($_GET['id']);

    // Delete the activity
    $stmt = $conn->prepare("DELETE FROM employee_activity WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $activity_id);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: aktiviteti.php?msg=Activity+deleted+successfully.");
            exit();
        } else {
            $error = "Error deleting activity: " . $stmt->error;
            $stmt->close();
        }
    } else {
        $error = "Failed to prepare statement.";
    }
} else {
    $error = "Invalid activity ID.";
}

$conn->close();

// Redirect back with error message
header("Location: aktiviteti.php?error=" . urlencode($error));
exit();
