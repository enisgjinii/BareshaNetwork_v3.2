<?php

include 'conn-d.php';

// Check if the role ID is set
if (isset($_POST['role_id'])) {
    $roleId = $_POST['role_id'];



    // Check the connection
    if ($conn->connect_errno) {
        echo 'Failed to connect to MySQL: ' . $conn->connect_error;
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    // Delete the corresponding rows from the role_pages table
    $sql = "DELETE FROM role_pages WHERE role_id = $roleId";
    if ($conn->query($sql)) {
        // Delete the corresponding rows from the user_roles table
        $sql = "DELETE FROM user_roles WHERE role_id = $roleId";
        if ($conn->query($sql)) {
            // Delete the row from the roles table
            $sql = "DELETE FROM roles WHERE id = $roleId";
            if ($conn->query($sql)) {
                // Commit the transaction
                $conn->commit();
                // Return success
                echo 'success';
            } else {
                // Rollback the transaction
                $conn->rollback();
                // Return error
                echo 'error';
            }
        } else {
            // Rollback the transaction
            $conn->rollback();
            // Return error
            echo 'error';
        }
    } else {
        // Rollback the transaction
        $conn->rollback();
        // Return error
        echo 'error';
    }

    // Close the connection
    $conn->close();
}
