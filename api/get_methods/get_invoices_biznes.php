<?php
include '../../conn-d.php';

// Set the table and primary key
$table = 'invoices';
$primaryKey = 'id';

// Define the columns
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

// Base SQL query
$sql = "SELECT i.id, i.invoice_number, i.item, i.customer_id, i.state_of_invoice, i.file_path, i.type,
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

// Corrected WHERE clause with proper parentheses
$sql .= " WHERE 
            (i.type != 'grupor' OR i.type IS NULL) AND
            (
                (i.total_amount_in_eur_after_percentage IS NOT NULL 
                 AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
                OR 
                (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
            )
            AND k.lloji_klientit = 'Biznes'";

// Apply search filter if present
$searchValue = isset($_REQUEST['search']['value']) ? mysqli_real_escape_string($conn, $_REQUEST['search']['value']) : '';
if (!empty($searchValue)) {
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
                    $searchConditions[] = "i." . mysqli_real_escape_string($conn, $column['db']) . " LIKE '%$searchValue%'";
                    break;
            }
        }
    }
    $sql .= implode(" OR ", $searchConditions);
    $sql .= ")";
}

// Count total records without filtering
$sqlCountTotal = "SELECT COUNT(*) as count FROM (
                    SELECT i.id
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
                    ) AS i_agg ON i.invoice_number = i_agg.invoice_number
                    WHERE 
                        (i.type != 'grupor' OR i.type IS NULL) AND
                        (
                            (i.total_amount_in_eur_after_percentage IS NOT NULL 
                             AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
                            OR 
                            (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
                        )
                        AND k.lloji_klientit = 'Biznes'
                ) AS totalTable";
$resultTotal = mysqli_query($conn, $sqlCountTotal);
if (!$resultTotal) {
    die("Error fetching total records: " . mysqli_error($conn));
}
$totalRecords = mysqli_fetch_assoc($resultTotal)['count'];

// Count total records with filtering
if (!empty($searchValue)) {
    $sqlCountFiltered = "SELECT COUNT(*) as count FROM ($sql) AS filteredTable";
    $resultFiltered = mysqli_query($conn, $sqlCountFiltered);
    if (!$resultFiltered) {
        die("Error fetching filtered records: " . mysqli_error($conn));
    }
    $recordsFiltered = mysqli_fetch_assoc($resultFiltered)['count'];
} else {
    $recordsFiltered = $totalRecords;
}

// Handle pagination
$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
$length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 10;

// Handle ordering
$orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? intval($_REQUEST['order'][0]['column']) : 0;
$orderDirection = isset($_REQUEST['order'][0]['dir']) && in_array(strtolower($_REQUEST['order'][0]['dir']), ['asc', 'desc']) ? strtoupper($_REQUEST['order'][0]['dir']) : 'ASC';

$orderColumn = isset($columns[$orderColumnIndex]['db']) ? $columns[$orderColumnIndex]['db'] : 'id';
$orderColumn = mysqli_real_escape_string($conn, $orderColumn); // Sanitize column name


$sql .= " ORDER BY id DESC LIMIT $start, $length";
// Execute the final query
$query = mysqli_query($conn, $sql);
if (!$query) {
    die("Error executing main query: " . mysqli_error($conn));
}

$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Prepare the response
$response = array(
    "draw" => isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $data
);

// Output the JSON response
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
?>
