<?php
if (isset($_POST["id"])) {
    $id = $_POST["id"];

    // Connect to the database
    require_once 'conn-d.php';

    // Perform the restore action (move the record from invoice_trash back to invoices)
    // Exclude the primary key column (id) during the insertion
    $sql = "INSERT INTO invoices (invoice_number, customer_id, item, total_amount, total_amount_after_percentage, paid_amount, created_date)
            SELECT invoice_number, customer_id, item, total_amount, total_amount_after_percentage, paid_amount, created_date
            FROM invoice_trash
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Remove the record from invoice_trash
        $deleteSql = "DELETE FROM invoice_trash WHERE id = $id";
        mysqli_query($conn, $deleteSql);

        mysqli_close($conn);

        // Use SweetAlert2 for success message
        echo json_encode(array('success' => true, 'message' => 'Fatura u rikthye me sukses'));
    } else {
        echo json_encode(array('success' => false, 'message' => "Error: " . $sql . "<br>" . mysqli_error($conn)));
    }
} else {
    echo 'No valid ID provided';
}
