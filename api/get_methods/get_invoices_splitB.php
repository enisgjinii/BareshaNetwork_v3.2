<?php
// Include the database connection file
include '../../conn-d.php';
// Define table and primary key
$table = 'invoices';
$primaryKey = 'id';
// Define the columns for DataTables
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
// Construct the main SQL query
$sql = "SELECT 
            i.id, 
            i.invoice_number, 
            i.item, 
            i.customer_id, 
            i.state_of_invoice, 
            i.type, 
            i.subaccount_name,
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
            SELECT 
                kanali, 
                SUM(shuma) AS customer_loan_amount,
                SUM(pagoi) AS customer_loan_paid
            FROM yinc
            GROUP BY kanali
        ) AS y ON i.customer_id = y.kanali
        LEFT JOIN (
            SELECT 
                invoice_number,  
                SUM(total_amount) AS total_amount,
                SUM(total_amount_after_percentage) AS total_amount_after_percentage,
                SUM(paid_amount) AS paid_amount
            FROM invoices
            GROUP BY invoice_number
        ) AS i_agg ON i.invoice_number = i_agg.invoice_number";
// **Revised WHERE Clause: Strictly Enforce i.type = 'grupor'**
$sql .= " WHERE 
            i.type = 'grupor' AND (
                (i.total_amount_in_eur_after_percentage IS NOT NULL 
                 AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
                OR 
                (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
            )
                AND (k.lloji_klientit = 'Biznes' OR k.lloji_klientit IS NULL)
            AND (k.aktiv IS NULL OR k.aktiv = 0)";
// **Apply the Month Filter (Optional)**
if (isset($_GET['month']) && !empty($_GET['month'])) {
    // Use prepared statements to prevent SQL injection
    $selectedMonth = mysqli_real_escape_string($conn, $_GET['month']);
    $sql .= " AND i.item LIKE '%$selectedMonth%'";
}
// **Apply the Amount Filter (Optional)**
if (isset($_GET['amount']) && !empty($_GET['amount'])) {
    $enteredAmount = floatval($_GET['amount']); // Ensure it's a number
    $sql .= " AND (
                i.total_amount_after_percentage > $enteredAmount 
                OR i.total_amount_in_eur_after_percentage > $enteredAmount
              )";
}
// **Handle Search Functionality**
if (!empty($_REQUEST['search']['value'])) {
    $searchValue = mysqli_real_escape_string($conn, $_REQUEST['search']['value']);
    $sql .= " AND (";
    $searchConditions = array();
    foreach ($columns as $column) {
        if ($column['searchable']) {
            switch ($column['dt']) {
                case 'customer_name':
                    $searchConditions[] = "k.emri LIKE '%$searchValue%'";
                    break;
                case 'customer_email':
                    $searchConditions[] = "k.emailadd LIKE '%$searchValue%'";
                    break;
                default:
                    // Escape column names to prevent SQL injection
                    $safeColumn = mysqli_real_escape_string($conn, $column['db']);
                    $searchConditions[] = "i.$safeColumn LIKE '%$searchValue%'";
                    break;
            }
        }
    }
    // Combine search conditions with OR
    $sql .= implode(" OR ", $searchConditions);
    $sql .= ")";
}
// **Get Total Records Count Before Pagination**
$sqlCount = "SELECT COUNT(*) as count FROM (
                SELECT i.id
                FROM (
                    SELECT *
                    FROM invoices
                    GROUP BY invoice_number
                ) AS i
                JOIN klientet AS k ON i.customer_id = k.id
                LEFT JOIN (
                    SELECT 
                        kanali, 
                        SUM(shuma) AS customer_loan_amount,
                        SUM(pagoi) AS customer_loan_paid
                    FROM yinc
                    GROUP BY kanali
                ) AS y ON i.customer_id = y.kanali
                LEFT JOIN (
                    SELECT 
                        invoice_number,  
                        SUM(total_amount) AS total_amount,
                        SUM(total_amount_after_percentage) AS total_amount_after_percentage,
                        SUM(paid_amount) AS paid_amount
                    FROM invoices
                    GROUP BY invoice_number
                ) AS i_agg ON i.invoice_number = i_agg.invoice_number
                WHERE 
                    i.type = 'grupor' AND (
                        (i.total_amount_in_eur_after_percentage IS NOT NULL 
                         AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
                        OR 
                        (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
                    )
                    AND (k.lloji_klientit = 'Biznes' OR k.lloji_klientit IS NULL)
                    AND (k.aktiv IS NULL OR k.aktiv = 0)";
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $sqlCount .= " AND i.item LIKE '%$selectedMonth%'";
}
if (isset($_GET['amount']) && !empty($_GET['amount'])) {
    $sqlCount .= " AND (
                    i.total_amount_after_percentage > $enteredAmount 
                    OR i.total_amount_in_eur_after_percentage > $enteredAmount
                  )";
}
if (!empty($_REQUEST['search']['value'])) {
    $sqlCount .= " AND (";
    $sqlCount .= implode(" OR ", $searchConditions);
    $sqlCount .= ")";
}
$sqlCount .= ") AS countTable";
// Execute the count query
$resultCount = mysqli_query($conn, $sqlCount);
if (!$resultCount) {
    die(json_encode(array(
        "error" => mysqli_error($conn)
    )));
}
$totalRecords = mysqli_fetch_assoc($resultCount)['count'];
// **Apply Ordering**
$orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? (int)$_REQUEST['order'][0]['column'] : 0;
$orderDirection = (isset($_REQUEST['order'][0]['dir']) && strtolower($_REQUEST['order'][0]['dir']) === 'desc') ? 'DESC' : 'ASC';
// Ensure the order column index is within bounds
$orderColumn = isset($columns[$orderColumnIndex]['db']) ? $columns[$orderColumnIndex]['db'] : 'id';
$orderColumn = mysqli_real_escape_string($conn, $orderColumn); // Prevent SQL injection
$sql .= " ORDER BY $orderColumn $orderDirection";
// **Apply Pagination**
$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
$length = (isset($_REQUEST['length']) && $_REQUEST['length'] != -1) ? (int)$_REQUEST['length'] : 10;
$sql .= " LIMIT $start, $length";
// **Execute the Final Query**
$query = mysqli_query($conn, $sql);
if (!$query) {
    die(json_encode(array(
        "error" => mysqli_error($conn)
    )));
}
// **Fetch Data**
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}
// **Prepare the Response**
$response = array(
    "draw" => isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);
// **Output the Response in JSON Format**
header('Content-Type: application/json');
echo json_encode($response);
?>
