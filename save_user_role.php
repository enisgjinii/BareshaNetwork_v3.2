<?php

include 'conn-d.php';
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected user ID and role ID from the form data
    $user_id = $_POST['user_id'];
    $role_id = $_POST['role_id'];

    // Prepare SQL statement to insert user ID and role ID into user_roles table
    $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");

    // Bind user ID and role ID parameters to the statement
    $stmt->bind_param("ii", $user_id, $role_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the roles.php page
        header("Location: roles.php");
        exit();
    } else {
        // Display an error message
        echo "Error: " . $stmt->error;
    }
}
?>
