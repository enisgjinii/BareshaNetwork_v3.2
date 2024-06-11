<?php
include 'conn-d.php';

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

$sql = "SELECT i.id, i.invoice_number, i.item, i.customer_id, i.state_of_invoice,
                i_agg.total_amount,
                i_agg.total_amount_after_percentage,
                i.total_amount_in_eur,
                i.total_amount_in_eur_after_percentage,     
                i_agg.paid_amount,
                k.emri AS customer_name,
                k.emailadd AS customer_email,
                y.customer_loan_amount,
                y.customer_loan_paid
        FROM (
            SELECT id, invoice_number, item, customer_id, state_of_invoice,
                   total_amount_after_percentage, paid_amount, total_amount_in_eur, total_amount_in_eur_after_percentage
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

$sql .= " WHERE (
    (i.total_amount_in_eur_after_percentage IS NOT NULL 
     AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
    OR 
    (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
  )
  AND (k.lloji_klientit = 'Personal' OR k.lloji_klientit IS NULL)";

if (!empty($_REQUEST['search']['value'])) {
    $sql .= " AND (";
    $searchConditions = array();
    foreach ($columns as $column) {
        if ($column['searchable']) {
            if ($column['dt'] === 'customer_name') {
                $searchConditions[] = "k.emri LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            } elseif ($column['dt'] === 'customer_email') {
                $searchConditions[] = "k.emailadd LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            } else {
                $searchConditions[] = "i." . $column['db'] . " LIKE '%" . mysqli_real_escape_string($conn, $_REQUEST['search']['value']) . "%'";
            }
        }
    }
    $sql .= implode(" OR ", $searchConditions);
    $sql .= ")";
}

$sqlCount = "SELECT COUNT(*) as count FROM ($sql) AS countTable";
$totalRecords = mysqli_fetch_assoc(mysqli_query($conn, $sqlCount))['count'];

$start = $_REQUEST['start'];
$length = $_REQUEST['length'];
$orderColumn = $columns[$_REQUEST['order'][0]['column']]['db'];
$orderDirection = $_REQUEST['order'][0]['dir'];

$sql .= " ORDER BY id DESC LIMIT $start, $length";

$query = mysqli_query($conn, $sql);
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

$response = array(
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

echo json_encode($response);
$conn->close();
