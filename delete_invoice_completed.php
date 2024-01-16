<?php
// Include your database connection here
include 'conn-d.php';

header('Content-Type: application/json');

if (isset($_POST['invoice_id'])) {
    $invoiceId = $_POST['invoice_id'];

    // Perform the delete operation
    $deleteSql = "DELETE FROM payments WHERE invoice_id = $invoiceId"; // Adjust this query according to your database structure

    if ($conn->query($deleteSql)) {
        // Successful deletion
        $response = array('success' => true);
    } else {
        // Error during deletion
        $response = array('success' => false, 'error' => $conn->error);
    }

    echo json_encode($response);
} else {
    echo json_encode(array('success' => false, 'error' => 'Missing invoice_id'));
}

$conn->close();
