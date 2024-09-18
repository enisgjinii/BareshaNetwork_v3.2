<?php
// Database connection
include '../../conn-d.php';

// Get parameters from DataTables
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$start_date = isset($_GET['startDateBiznes']) ? $_GET['startDateBiznes'] : '';
$end_date = isset($_GET['endDateBiznes']) ? $_GET['endDateBiznes'] : '';

// Sanitize search input
$search = $conn->real_escape_string($search);

// Build search query
$searchQuery = '';
$dateRangeQuery = '';
$params = [];
$types = '';

if ($search) {
    $searchQuery = "AND klientet.emri LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if ($start_date && $end_date) {
    $dateRangeQuery = "AND payments.payment_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

// Base SQL query
$baseQuery = "FROM payments
              INNER JOIN invoices ON payments.invoice_id = invoices.id
              INNER JOIN klientet ON invoices.customer_id = klientet.id
              WHERE klientet.lloji_klientit = 'Biznes'
              $searchQuery
              $dateRangeQuery";

// SQL query for data
$sql = "SELECT invoices.id, invoices.customer_id, invoices.invoice_number,
               klientet.emri AS customer_name,klientet.emailadd,klientet.email_kontablist, MIN(payments.payment_id) AS payment_id, 
               payments.invoice_id, SUM(payments.payment_amount) AS total_payment_amount, 
               MIN(payments.payment_date) AS payment_date, MIN(payments.bank_info) AS bank_info, 
               MIN(payments.type_of_pay) AS type_of_pay, MIN(payments.description) AS description, 
               invoices.total_amount AS total_invoice_amount,
               invoices.total_amount_after_percentage AS total_amount_after_percentage,

               invoices.file_path AS file_path,
               invoices.file_description AS file_description
        $baseQuery
        GROUP BY invoices.id, invoices.customer_id, invoices.invoice_number, 
                 klientet.emri, invoices.total_amount_after_percentage
        ORDER BY payments.payment_id DESC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$params[] = $start;
$params[] = $length;
$types .= 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

// Get total records count for pagination
$totalRecordsQuery = "SELECT COUNT(DISTINCT invoices.id) AS total $baseQuery";
$totalStmt = $conn->prepare($totalRecordsQuery);

if ($search && $start_date && $end_date) {
    $totalStmt->bind_param('sss', $params[0], $params[1], $params[2]);
} elseif ($search) {
    $totalStmt->bind_param('s', $params[0]);
} elseif ($start_date && $end_date) {
    $totalStmt->bind_param('ss', $params[0], $params[1]);
}

$totalStmt->execute();
$totalRecordsResult = $totalStmt->get_result();
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Create response array
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
];

echo json_encode($response);

// Close database connection
$conn->close();
