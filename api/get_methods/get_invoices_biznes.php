<?php
include '../../conn-d.php';

$table = 'invoices';
$primaryKey = 'id';

$columns = array(
    array('db' => 'id', 'dt' => 'id', 'searchable' => false),
    array('db' => 'invoice_number', 'dt' => 'invoice_number', 'searchable' => true),
    array('db' => 'customer_id', 'dt' => 'customer_id', 'searchable' => true),
    array('db' => 'item', 'dt' => 'item', 'searchable' => true),
    array('db' => 'total_amount_after_percentage', 'dt' => 'total_amount_after_percentage', 'searchable' => false),
    array('db' => 'paid_amount', 'dt' => 'paid_amount', 'searchable' => false),
    array('db' => 'customer_name', 'dt' => 'customer_name', 'searchable' => true),
    array('db' => 'customer_loan', 'dt' => 'customer_loan', 'searchable' => false),
    array('db' => 'customer_email', 'dt' => 'customer_email', 'searchable' => true)
);

$sql = "SELECT i.id, i.invoice_number, i.item, i.customer_id, i.state_of_invoice, i.type, i.subaccount_name,
                i_agg.total_amount,
                i_agg.total_amount_after_percentage,
                i.total_amount_in_eur,
                i.total_amount_in_eur_after_percentage,
                i_agg.paid_amount,
                k.emri AS customer_name,
                k.emailadd AS customer_email,
                k.email_kontablist AS email_of_contablist,
                y.customer_loan_amount,
                y.customer_loan_paid
        FROM (
            SELECT *
            FROM invoices
            GROUP BY invoice_number
        ) AS i
        JOIN klientet AS k ON i.customer_id = k.id
        LEFT JOIN (
            SELECT kanali, 
                SUM(shuma) AS customer_loan_amount,
                SUM(pagoi) AS customer_loan_paid
            FROM yinc
            GROUP BY kanali
        ) AS y ON i.customer_id = y.kanali
        LEFT JOIN (
            SELECT invoice_number,  
                SUM(total_amount) AS total_amount,
                SUM(total_amount_after_percentage) AS total_amount_after_percentage,
                SUM(paid_amount) AS paid_amount
            FROM invoices
            GROUP BY invoice_number
        ) AS i_agg ON i.invoice_number = i_agg.invoice_number";

// Adding the condition for `i.type != 'grupor' OR i.type IS NULL`
$sql .= " WHERE 
  (i.type != 'grupor' OR i.type IS NULL) AND (
    (i.total_amount_in_eur_after_percentage IS NOT NULL 
     AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
    OR 
    (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
  )
  AND (k.lloji_klientit = 'Biznes' OR k.lloji_klientit IS NULL)
  AND (k.aktiv IS NULL OR k.aktiv = 0)";

// Apply the month filter if selected
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $selectedMonth = mysqli_real_escape_string($conn, $_GET['month']);
    $sql .= " AND i.item LIKE '%$selectedMonth%'";
}

// Apply the amount filter if entered
if (isset($_GET['amount']) && !empty($_GET['amount'])) {
    $enteredAmount = mysqli_real_escape_string($conn, $_GET['amount']);
    $sql .= " AND (
        i.total_amount_after_percentage > $enteredAmount 
        OR i.total_amount_in_eur_after_percentage > $enteredAmount
    )";
}

// Handle search functionality
if (!empty($_REQUEST['search']['value'])) {
    $sql .= " AND (";
    $searchConditions = array();
    $searchValue = mysqli_real_escape_string($conn, $_REQUEST['search']['value']);

    foreach ($columns as $column) {
        if ($column['searchable']) {
            if ($column['dt'] === 'customer_name') {
                $searchConditions[] = "k.emri LIKE '%$searchValue%'";
            } elseif ($column['dt'] === 'customer_email') {
                $searchConditions[] = "k.emailadd LIKE '%$searchValue%'";
            } else {
                $searchConditions[] = "i." . $column['db'] . " LIKE '%$searchValue%'";
            }
        }
    }
    $sql .= implode(" OR ", $searchConditions);
    $sql .= ")";
}

// Get total records count before applying pagination
$sqlCount = "SELECT COUNT(*) as count FROM ($sql) AS countTable";
$resultCount = mysqli_query($conn, $sqlCount);
$totalRecords = $resultCount ? mysqli_fetch_assoc($resultCount)['count'] : 0;

// Apply ordering
$orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? (int)$_REQUEST['order'][0]['column'] : 0;
$orderDirection = isset($_REQUEST['order'][0]['dir']) && $_REQUEST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
$orderColumn = $columns[$orderColumnIndex]['db'];

$sql .= " ORDER BY id DESC";

// Apply pagination
$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
$length = isset($_REQUEST['length']) && $_REQUEST['length'] != -1 ? (int)$_REQUEST['length'] : 10;
$sql .= " LIMIT $start, $length";

// Execute the final query
$query = mysqli_query($conn, $sql);

if (!$query) {
    die(json_encode(array(
        "error" => mysqli_error($conn)
    )));
}

// Fetch data
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Prepare the response
$response = array(
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

// Output response in JSON format
echo json_encode($response);
