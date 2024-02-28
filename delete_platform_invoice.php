<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'conn-d.php';
    $invoice_id = $_POST['invoice_id'];

    // Perform the delete operation
    $delete_sql = "DELETE FROM platform_invoices WHERE id = $invoice_id";
    mysqli_query($conn, $delete_sql);

    // Redirect back to the original page after deletion
    header("Location: platform_invoices.php");
    exit();
}
?>
