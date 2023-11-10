<?php
// Include your database connection here

include 'conn-d.php';

// Define your database table
$table = 'invoices';

// Define your primary key column
$primaryKey = 'id';

// Map your DataTables columns to your database columns
$columns = array(
    array('db' => 'id', 'dt' => 'id', 'searchable' => true),
    array('db' => 'invoice_number', 'dt' => 'invoice_number', 'searchable' => true),
    array('db' => 'customer_id', 'dt' => 'customer_id', 'searchable' => true),
    array('db' => 'SUM(total_amount) as total_amount', 'dt' => 'total_amount', 'searchable' => false),
    array('db' => 'SUM(total_amount_after_percentage) as total_amount_after_percentage', 'dt' => 'total_amount_after_percentage', 'searchable' => false),
    array('db' => 'SUM(paid_amount) as paid_amount', 'dt' => 'paid_amount', 'searchable' => false)
);

// Define the SQL query to fetch data from the table and group by invoice_number
$sql = "SELECT " . implode(", ", array_map(function ($col) {
    return $col['db'];
}, $columns)) . " FROM $table GROUP BY invoice_number";

// Prepare an array to store search conditions
$searchConditions = array();

// Apply filtering (search)
if (!empty($_REQUEST['search']['value'])) {
    foreach ($columns as $column) {
        if ($column['searchable']) {
            $searchConditions[] = $column['db'] . " LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
        }
    }
}

// Combine search conditions
if (!empty($searchConditions)) {
    $sql .= " HAVING " . implode(" OR ", $searchConditions);
}
// Add a condition to filter out invoices with total_amount_after_percentage and paid_amount both equal to 0
$sql .= " HAVING SUM(total_amount_after_percentage - paid_amount) != 0";

// Execute the SQL query
$query = mysqli_query($conn, $sql);

// Total records without filtering
$totalRecords = mysqli_num_rows($query);

// Apply ordering
$orderColumn = $columns[$_REQUEST['order'][0]['column']]['db'];
$orderDirection = $_REQUEST['order'][0]['dir'];
$sql .= " ORDER BY $orderColumn $orderDirection";

// Limit and offset
$start = $_REQUEST['start'];
$length = $_REQUEST['length'];
$sql .= " LIMIT $start, $length";

// Execute the final SQL query
$query = mysqli_query($conn, $sql);

// Prepare the response data
$data = array();
while ($row = mysqli_fetch_array($query)) {
    $data[] = $row;
}

// Build the response for DataTables
$response = array(
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords, // For simplicity, set it to total records as we did not filter on the server
    "data" => $data
);


// Return the JSON response
echo json_encode($response);
