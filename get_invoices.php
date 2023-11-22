<?php
// Include your database connection here
include 'conn-d.php';

// Define your database table
$table = 'invoices';

// Define your primary key column
$primaryKey = 'id';

// Map your DataTables columns to your database columns
$columns = array(
    array('db' => 'id', 'dt' => 'id', 'searchable' => false),
    array('db' => 'invoice_number', 'dt' => 'invoice_number', 'searchable' => true),
    array('db' => 'customer_id', 'dt' => 'customer_id', 'searchable' => true),
    array('db' => 'item', 'dt' => 'item', 'searchable' => true),
    array('db' => 'total_amount_after_percentage', 'dt' => 'total_amount_after_percentage', 'searchable' => false),
    array('db' => 'paid_amount', 'dt' => 'paid_amount', 'searchable' => false),
    array('db' => 'k.emri AS customer_name', 'dt' => 'customer_name', 'searchable' => true),
    array('db' => '(SUM(y.shuma) - SUM(y.pagoi)) AS customer_loan', 'dt' => 'customer_loan', 'searchable' => false)
);

// Build the SQL query
$sql = "SELECT i.id, i.invoice_number, i.customer_id, i.item, i.state_of_invoice,
                i.total_amount,
                i.total_amount_after_percentage,
                SUM(i.paid_amount) AS paid_amount,
                k.emri AS customer_name,
                (SUM(y.shuma) - SUM(y.pagoi)) AS customer_loan
        FROM $table AS i
        JOIN klientet AS k ON i.customer_id = k.id
        LEFT JOIN yinc AS y ON i.customer_id = y.kanali
        GROUP BY i.invoice_number";

// Add a condition to filter out invoices with total_amount_after_percentage and paid_amount both equal to 0
$sql .= " HAVING SUM(i.total_amount_after_percentage - i.paid_amount) != 0";

// Apply filtering (search)
if (!empty($_REQUEST['search']['value'])) {
    $sql .= " AND (";
    $searchConditions = array();
    foreach ($columns as $column) {
        if ($column['searchable']) {
            if ($column['dt'] === 'customer_name' || $column['dt'] === 'customer_loan') {
                $searchConditions[] = "`" . $column['dt'] . "` LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            } else {
                $searchConditions[] = "`i`.`" . $column['db'] . "` LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            }
        }
    }
    $sql .= implode(" OR ", $searchConditions);
    $sql .= ")";
}

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
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Build the response for DataTables
$response = array(
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

// Return the JSON response
echo json_encode($response);
