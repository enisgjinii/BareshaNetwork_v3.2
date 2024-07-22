<?php
// Include your database connection file
include 'conn-d.php';

// Check if the ID parameter is set and is a valid integer
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    // Sanitize the ID to prevent SQL injection
    $id = (int) $_POST['id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Fetch the record to be deleted
        $select_sql = "SELECT * FROM refresh_tokens WHERE id = $id";
        $result = $conn->query($select_sql);

        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();

            // Insert the record into the backup table
            $backup_sql = "INSERT INTO backup_refresh_tokens (id, token, channel_id, channel_name, expiry_date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($backup_sql);
            if ($stmt === false) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $bind = $stmt->bind_param("issss", $record['id'], $record['token'], $record['channel_id'], $record['channel_name'], $record['expiry_date']);
            if ($bind === false) {
                throw new Exception('Bind failed: ' . $stmt->error);
            }
            $exec = $stmt->execute();
            if ($exec === false) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }

            // Prepare a SQL statement to delete the record with the given ID
            $delete_sql = "DELETE FROM refresh_tokens WHERE id = $id";

            // Execute the SQL statement
            if ($conn->query($delete_sql) === TRUE) {
                // Commit the transaction
                $conn->commit();
                echo "Record deleted successfully and backed up.";
            } else {
                // Rollback the transaction in case of error
                $conn->rollback();
                echo "Error: Unable to execute SQL statement. " . $conn->error;
            }
        } else {
            echo "Error: Record not found.";
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // Return an error message if ID parameter is missing or invalid
    echo "Error: Invalid request.";
}

// Close the database connection
$conn->close();
