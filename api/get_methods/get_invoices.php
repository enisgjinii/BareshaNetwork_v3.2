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

// Krijo SQL-in fillestar me bashkim dhe grupim
$sql = "SELECT 
            i.id, 
            i.invoice_number, 
            i.item, 
            i.customer_id, 
            i.state_of_invoice, 
            i.type,
            i.total_amount,
            i.total_amount_after_percentage,
            i.paid_amount,
            i.total_amount_in_eur,
            i.total_amount_in_eur_after_percentage,
            k.emri AS customer_name,
            k.emailadd AS customer_email,
            k.email_kontablist AS email_of_contablist,
            y.customer_loan_amount,
            y.customer_loan_paid
        FROM (
            SELECT 
                invoice_number, 
                MAX(id) AS id, 
                MAX(item) AS item,
                MAX(customer_id) AS customer_id,
                MAX(state_of_invoice) AS state_of_invoice,
                MAX(type) AS type,
                SUM(total_amount) AS total_amount,
                SUM(total_amount_after_percentage) AS total_amount_after_percentage,
                SUM(paid_amount) AS paid_amount,
                SUM(total_amount_in_eur) AS total_amount_in_eur,
                SUM(total_amount_in_eur_after_percentage) AS total_amount_in_eur_after_percentage
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
        ) AS y ON i.customer_id = y.kanali";

// Filloni kushtet e WHERE
$sql .= " WHERE (
    (i.total_amount_in_eur_after_percentage IS NOT NULL 
     AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
    OR 
    (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
  )
  AND (k.lloji_klientit = 'Personal' OR k.lloji_klientit IS NULL)
  AND (k.aktiv IS NULL OR k.aktiv = 0)";

// Kontrolloni nëse është bërë një kërkim
$isSearch = isset($_REQUEST['search']['value']) && !empty($_REQUEST['search']['value']);

// Nëse nuk është bërë një kërkim, përjashto faturat me shumë prej 10 euro ose më pak
if (!$isSearch) {
    $sql .= " AND (
        i.total_amount_after_percentage > 10 
        AND i.total_amount_in_eur_after_percentage > 10
    )";
}

// Filtrimi i muajit nëse është zgjedhur
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $selectedMonth = mysqli_real_escape_string($conn, $_GET['month']);
    $sql .= " AND i.item LIKE '%$selectedMonth%'";
}

// Filtrimi i shumës nëse është futur (në rast se përdoruesi ka specifikuar një shumë të caktuar)
if (isset($_GET['amount']) && !empty($_GET['amount'])) {
    $enteredAmount = mysqli_real_escape_string($conn, $_GET['amount']);
    // Mund të rregulloni operatorin sipas nevojës
    $sql .= " AND (
        i.total_amount_after_percentage <= $enteredAmount 
        OR i.total_amount_in_eur_after_percentage <= $enteredAmount
    )";
}

// Menaxhimi i funksionalitetit të kërkimit
if ($isSearch) {
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

// Krijimi i query për numërimin e rekordeve totale pa ORDER BY dhe LIMIT
$sqlCount = "SELECT COUNT(*) as count FROM (
    SELECT 
        i.id
    FROM (
        SELECT 
            invoice_number, 
            MAX(id) AS id, 
            MAX(item) AS item,
            MAX(customer_id) AS customer_id,
            MAX(state_of_invoice) AS state_of_invoice,
            MAX(type) AS type,
            SUM(total_amount) AS total_amount,
            SUM(total_amount_after_percentage) AS total_amount_after_percentage,
            SUM(paid_amount) AS paid_amount,
            SUM(total_amount_in_eur) AS total_amount_in_eur,
            SUM(total_amount_in_eur_after_percentage) AS total_amount_in_eur_after_percentage
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
    WHERE (
        (i.total_amount_in_eur_after_percentage IS NOT NULL 
         AND (i.total_amount_in_eur_after_percentage - i.paid_amount) > 1)
        OR 
        (COALESCE(i.total_amount_in_eur_after_percentage, i.total_amount_after_percentage) - i.paid_amount) > 1
      )
      AND (k.lloji_klientit = 'Personal' OR k.lloji_klientit IS NULL)
      AND (k.aktiv IS NULL OR k.aktiv = 0)";

// Nëse nuk është bërë një kërkim, përjashto faturat me shumë prej 10 euro ose më pak
if (!$isSearch) {
    $sqlCount .= " AND (
        i.total_amount_after_percentage > 10 
        AND i.total_amount_in_eur_after_percentage > 10
    )";
}

// Filtrimi i muajit nëse është zgjedhur
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $selectedMonth = mysqli_real_escape_string($conn, $_GET['month']);
    $sqlCount .= " AND i.item LIKE '%$selectedMonth%'";
}

// Filtrimi i shumës nëse është futur
if (isset($_GET['amount']) && !empty($_GET['amount'])) {
    $enteredAmount = mysqli_real_escape_string($conn, $_GET['amount']);
    $sqlCount .= " AND (
        i.total_amount_after_percentage <= $enteredAmount 
        OR i.total_amount_in_eur_after_percentage <= $enteredAmount
    )";
}

// Menaxhimi i funksionalitetit të kërkimit
if ($isSearch) {
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
    $sqlCount .= " AND (" . implode(" OR ", $searchConditions) . ")";
}

$sqlCount .= ") AS countTable";

// Ekzekuto query-n për numërimin e rekordeve totale
$resultCount = mysqli_query($conn, $sqlCount);
if (!$resultCount) {
    die(json_encode(array(
        "error" => mysqli_error($conn)
    )));
}
$totalRecords = mysqli_fetch_assoc($resultCount)['count'];

// Apliko renditjen
$orderColumnIndex = isset($_REQUEST['order'][0]['column']) ? (int)$_REQUEST['order'][0]['column'] : 0;
$orderDirection = isset($_REQUEST['order'][0]['dir']) && $_REQUEST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';
$orderColumn = $columns[$orderColumnIndex]['db'];

$sql .= " ORDER BY $orderColumn $orderDirection";

// Apliko paginimin
$start = isset($_REQUEST['start']) ? (int)$_REQUEST['start'] : 0;
$length = isset($_REQUEST['length']) && $_REQUEST['length'] != -1 ? (int)$_REQUEST['length'] : 10;
$sql .= " LIMIT $start, $length";

// Ekzekuto query-n final
$query = mysqli_query($conn, $sql);

if (!$query) {
    die(json_encode(array(
        "error" => mysqli_error($conn)
    )));
}

// Marrja e të dhënave
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

// Përgatitja e përgjigjes
$response = array(
    "draw" => isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

// Shfaqja e përgjigjes në format JSON
echo json_encode($response);
