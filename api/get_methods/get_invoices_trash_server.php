<?php
// Connect to the database
require_once '../../conn-d.php';

// Define DataTables request parameters
$start = $_POST['start'];
$length = $_POST['length'];
$draw = $_POST['draw'];
$columns = $_POST['columns'];

// Set the table name
$table = 'invoice_trash';

// Build the initial SQL query
$sql = "SELECT * FROM invoice_trash";

// Execute the initial query
$resultInitial = mysqli_query($conn, $sql);

// Get the total number of records
$totalRecords = mysqli_num_rows($resultInitial);

// Apply DataTables search
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
if (!empty($searchValue)) {
    $sqlFiltered = $sql . " WHERE invoice_trash.invoice_number LIKE '%$searchValue%' OR invoice_trash.customer_id LIKE '%$searchValue%' OR invoice_trash.item LIKE '%$searchValue%' OR invoice_trash.total_amount LIKE '%$searchValue%' OR invoice_trash.total_amount_after_percentage LIKE '%$searchValue%' OR invoice_trash.paid_amount LIKE '%$searchValue%' OR invoice_trash.created_date LIKE '%$searchValue%'";
} else {
    $sqlFiltered = $sql;
}

// Get the filtered number of records
$resultFiltered = mysqli_query($conn, $sqlFiltered);
$totalFiltered = mysqli_num_rows($resultFiltered);

// Apply DataTables ordering and pagination
$orderColumn = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$column = $columns[$orderColumn]['data'];
$dir = $orderDir;

$sqlFinal = $sqlFiltered . " ORDER BY $column $dir LIMIT $start, $length";

// Execute the final query
$resultFinal = mysqli_query($conn, $sqlFinal);

// Fetch records into an associative array
$invoices = [];
while ($row = mysqli_fetch_assoc($resultFinal)) {
    // Fetch the client name from customer id in table klientet
    $customer_id = $row['customer_id'];
    $sqlClient = "SELECT emri FROM klientet WHERE id = $customer_id";
    $resultClient = mysqli_query($conn, $sqlClient);
    $rowClient = mysqli_fetch_assoc($resultClient);

    // Add the client name to the row
    $row['client_name'] = $rowClient['emri'];

    $invoices[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Prepare the response
$response = [
    'draw' => intval($draw),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data' => $invoices,
];

// Output the JSON response
echo json_encode($response);
