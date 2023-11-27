<?php

include 'conn-d.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Get the invoice_number from the form
    $invoice_number = $_POST['invoice_number'];

    // Perform the deletion in the database
    $deleteSql = "DELETE FROM invoices WHERE id = $invoice_number";
    $deleteResult = mysqli_query($conn, $deleteSql);

    if ($deleteResult) {
        // Deletion successful
        header("Location: $_SERVER[HTTP_REFERER]"); // Redirect back to the previous page
        exit();
    } else {
        // Handle deletion failure
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // Redirect to an error page or handle the situation where delete button was not clicked
    header("Location: error_page.php");
    exit();
}
