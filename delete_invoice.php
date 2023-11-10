<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    
    // Connect to the database
    require_once 'conn-d.php';

    // SQL query to delete the record
    $sql = "DELETE FROM invoices WHERE invoice_number = $id";

    if (mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        header("Location: invoice.php"); // Replace with the actual page name
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    header("Location: invoice.php"); // Redirect to the invoice list if no ID is provided
}
