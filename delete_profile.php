<?php
// Include the database connection file
include "conn-d.php";

// Check if a profile ID was provided
if (isset($_POST['profile_id'])) {
    $profile_id = $_POST['profile_id'];

    // Delete the user's record from the user_roles table
    $stmt1 = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
    $stmt1->bind_param("i", $profile_id);
    $stmt1->execute();

    // Prepare and execute the delete query
    $stmt2 = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt2->bind_param("i", $profile_id);
    $stmt2->execute();

    // Destroy all sessions and redirect the user back to the login page
    session_start();
    session_destroy();
    header('Location: kycu_1.php');
    exit();
} else {
    // If no profile ID was provided, redirect the user to the previous page
    header('Location: perditsoProfilin.php');
    exit();
}

?>

<!-- https://www.uplabs.com/posts/profile-settings-page-ui-design -->