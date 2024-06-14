<?php
include 'conn-d.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $conn->autocommit(false); // Disable autocommit to start the transaction manually

        $query = "DELETE FROM expenses WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id); // 'i' indicates the variable is integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo 'success';
            $stmt->close(); // Close the prepared statement
            $conn->commit(); // Commit the transaction explicitly on success
        } else {
            $conn->rollback(); // Rollback the transaction if no record is found for deletion
            echo 'No record found for deletion.';
        }
    } catch (Exception $e) {
        $conn->rollback(); // Rollback the transaction if an error occurs
        echo 'Database error: ' . $e->getMessage();
    } finally {
        $conn->autocommit(true); // Restore autocommit to its initial state
    }
}
