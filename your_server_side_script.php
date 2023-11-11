<?php

// Include your database connection
include('conn-d.php');

// Initialize variables for DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';

// Process the DataTables request
$sql = "SELECT p.invoice_id, p.payment_date, SUM(p.payment_amount) AS total_payment_amount, k.emri AS customer_name
        FROM payments p
        INNER JOIN invoices i ON p.invoice_id = i.id
        INNER JOIN klientet k ON i.customer_id = k.id
        GROUP BY p.invoice_id
        ORDER BY " . $orderColumn . " " . $orderDirection . "
        LIMIT " . $start . "," . $length;

// Get total records without filtering
$totalRecordsQuery = "SELECT COUNT(DISTINCT p.invoice_id) AS total
                     FROM payments p
                     INNER JOIN invoices i ON p.invoice_id = i.id
                     INNER JOIN klientet k ON i.customer_id = k.id";

$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

// Execute the SQL query
$query = mysqli_query($conn, $sql);

// Prepare the response for DataTables
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Build the response for DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => count($data), // Adjust based on your filter criteria
    "data" => $data
);

// Debug: Print the JSON response for inspection
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
