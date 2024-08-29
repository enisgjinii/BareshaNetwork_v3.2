<?php
if (isset($_POST["ids"]) && is_array($_POST["ids"])) {
    // Validate and sanitize the array of IDs
    $ids = array_map('intval', $_POST["ids"]);
    // Connect to the database
    require_once '../../conn-d.php';
    // Begin a transaction
    mysqli_begin_transaction($conn);
    try {
        // Use a prepared statement with an array of IDs to move records to invoice_trash
        $moveToTrashSql = "INSERT INTO invoice_trash (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, paid_amount, created_date)
                           SELECT invoice_number, customer_id, item, total_amount, total_amount_after_percentage, paid_amount, created_date
                           FROM invoices WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
        $moveToTrashStmt = mysqli_prepare($conn, $moveToTrashSql);
        // Bind parameters for move to trash statement
        $types = str_repeat('i', count($ids));  // Assuming 'id' column is an integer
        mysqli_stmt_bind_param($moveToTrashStmt, $types, ...$ids);
        // Execute the move to trash statement
        if (!mysqli_stmt_execute($moveToTrashStmt)) {
            throw new Exception("Error moving records to trash");
        }
        mysqli_stmt_close($moveToTrashStmt);
        // Now, use another prepared statement to delete records from the invoices table
        $deleteSql = "DELETE FROM invoices WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        // Bind parameters for delete statement
        mysqli_stmt_bind_param($deleteStmt, $types, ...$ids);
        // Execute the delete statement
        if (!mysqli_stmt_execute($deleteStmt)) {
            throw new Exception("Error deleting records from invoices");
        }
        mysqli_stmt_close($deleteStmt);
        // If both operations were successful, commit the transaction
        mysqli_commit($conn);
        mysqli_close($conn);
        echo 'Artikujt u fshinë me sukses'; // Send a response to the client
    } catch (Exception $e) {
        // If an error occurred, rollback the transaction
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
} else {
    echo 'No valid IDs provided';
}
