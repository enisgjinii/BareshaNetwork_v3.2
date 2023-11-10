<?php

// your_server_side_script.php

// Include your database connection
include('conn-d.php');

$columns = array(
    array('db' => 'k.emri', 'dt' => 0, 'field' => 'customer_name'),
    array('db' => 'i.invoice_id', 'dt' => 1, 'field' => 'invoice_id'),
    array('db' => 'i.total_amount_after_percentage', 'dt' => 2, 'field' => 'total_payment_amount'),
    array('db' => 'i.latest_payment_date', 'dt' => 3, 'field' => 'latest_payment_date')
);

// Add 'field' key for each column
foreach ($columns as &$column) {
    if (!isset($column['field'])) {
        $column['field'] = '';
    }
}

// Define the primary table and joins
$table = 'invoices i';
$joins = 'JOIN klientet k ON i.customer_id = k.id';

// Initialize variables for DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
$orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

// Process the DataTables request
$sql = "SELECT " . implode(", ", array_map(function ($col) {
    return $col['db'] . ' AS ' . $col['field'];
}, $columns)) . " FROM $table $joins";

// Get total records without filtering
$totalRecordsQuery = "SELECT COUNT(DISTINCT i.id) AS total FROM $table $joins";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

// Handle ordering
$orderColumn = $columns[$orderColumn]['db'];
$sql .= " ORDER BY $orderColumn $orderDirection";

// Execute the SQL query
$query = mysqli_query($conn, $sql);

// Prepare the response for DataTables
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    // Check if the total paid amount matches the total amount after percentage
    if (isInvoiceTotallyCompleted($conn, $row['invoice_id'])) {
        $data[] = $row;
    }
}

// Build the response for DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => count($data), // Adjust based on your filter criteria
    "data" => $data
);

// Return the JSON response
echo json_encode($response);

// Function to check if an invoice is totally completed
function isInvoiceTotallyCompleted($conn, $invoiceId)
{
    $sql = "SELECT total_amount_after_percentage, SUM(payment_amount) as total_paid_amount FROM payments WHERE invoice_id = $invoiceId";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return ($row['total_amount_after_percentage'] - $row['total_paid_amount'] == 0);
    }

    return false;
}

// Debug: Print the SQL query for inspection
error_log("SQL Query: " . $sql);
?>
