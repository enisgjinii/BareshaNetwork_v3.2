<?php
// fetch_payments_personal.php

// Include the database connection file
include 'conn-d.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Retrieve DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$minDate = isset($_POST['minDate']) ? $_POST['minDate'] : '';
$maxDate = isset($_POST['maxDate']) ? $_POST['maxDate'] : '';

// Retrieve additional filtering parameters
$clientName = isset($_POST['clientName']) ? $_POST['clientName'] : '';
$bankInfo = isset($_POST['bankInfo']) ? $_POST['bankInfo'] : '';
$paymentType = isset($_POST['paymentType']) ? $_POST['paymentType'] : '';

// Define the columns for ordering
$columns = [
    0 => 'klientet.emri',
    1 => 'payments.invoice_id',
    2 => 'payments.payment_amount',
    3 => 'payments.payment_date',
    4 => 'payments.bank_info',
    5 => 'payments.type_of_pay',
    6 => 'payments.description',
    7 => 'invoices.total_amount_after_percentage'
];

// Initialize the base query
$baseQuery = "
    FROM payments
    JOIN invoices ON payments.invoice_id = invoices.id
    JOIN klientet ON invoices.customer_id = klientet.id
    WHERE (klientet.shtetsia IS NULL OR klientet.shtetsia = 'Kosova')
    AND (klientet.lloji_klientit = 'Personal' OR klientet.lloji_klientit IS NULL)
";

// Initialize an array for binding parameters
$bindings = [];
$types = "";

// Apply date filters
if (!empty($minDate)) {
    $baseQuery .= " AND payments.payment_date >= ?";
    $bindings[] = $minDate;
    $types .= "s";
}
if (!empty($maxDate)) {
    $baseQuery .= " AND payments.payment_date <= ?";
    $bindings[] = $maxDate;
    $types .= "s";
}

// Apply additional filters
if (!empty($clientName)) {
    $baseQuery .= " AND klientet.emri = ?";
    $bindings[] = $clientName;
    $types .= "s";
}

if (!empty($bankInfo)) {
    $baseQuery .= " AND payments.bank_info = ?";
    $bindings[] = $bankInfo;
    $types .= "s";
}

if (!empty($paymentType)) {
    $baseQuery .= " AND payments.type_of_pay = ?";
    $bindings[] = $paymentType;
    $types .= "s";
}

// Apply search filter
if (!empty($search)) {
    $baseQuery .= " AND (
        payments.payment_id LIKE CONCAT('%', ?, '%') OR 
        payments.invoice_id LIKE CONCAT('%', ?, '%') OR 
        payments.payment_amount LIKE CONCAT('%', ?, '%') OR 
        payments.payment_date LIKE CONCAT('%', ?, '%') OR 
        payments.bank_info LIKE CONCAT('%', ?, '%') OR 
        payments.type_of_pay LIKE CONCAT('%', ?, '%') OR 
        payments.description LIKE CONCAT('%', ?, '%') OR 
        klientet.emri LIKE CONCAT('%', ?, '%') OR 
        invoices.total_amount_after_percentage LIKE CONCAT('%', ?, '%')
    )";
    // Bind search parameter for each searchable column
    for ($i = 0; $i < 9; $i++) {
        $bindings[] = $search;
        $types .= "s";
    }
}

// Total records without filtering
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM payments 
    JOIN invoices ON payments.invoice_id = invoices.id 
    JOIN klientet ON invoices.customer_id = klientet.id 
    WHERE (klientet.shtetsia IS NULL OR klientet.shtetsia = 'Kosova') 
    AND (klientet.lloji_klientit = 'Personal' OR klientet.lloji_klientit IS NULL)";
$totalRecordsStmt = $conn->prepare($totalRecordsQuery);
$totalRecordsStmt->execute();
$totalRecordsResult = $totalRecordsStmt->get_result();
$totalRecordsRow = $totalRecordsResult->fetch_assoc();
$recordsTotal = $totalRecordsRow['total'];

// Total records with filtering
$totalFilteredQuery = "SELECT COUNT(*) AS total " . $baseQuery;
$totalFilteredStmt = $conn->prepare($totalFilteredQuery);
if (!empty($bindings)) {
    $totalFilteredStmt->bind_param($types, ...$bindings);
}
$totalFilteredStmt->execute();
$totalFilteredResult = $totalFilteredStmt->get_result();
$totalFilteredRow = $totalFilteredResult->fetch_assoc();
$recordsFiltered = $totalFilteredRow['total'];

// Fetch the actual data with ordering and pagination
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) && in_array(strtoupper($_POST['order'][0]['dir']), ['ASC', 'DESC']) ? strtoupper($_POST['order'][0]['dir']) : 'DESC';
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'payments.payment_id';

$dataQuery = "SELECT
        payments.payment_id,
        payments.invoice_id,
        payments.payment_amount,
        payments.payment_date,
        payments.bank_info,
        payments.type_of_pay,
        payments.description,
        klientet.emri AS client_name,
        invoices.total_amount_after_percentage AS total
    " . $baseQuery . "
    ORDER BY $orderColumn $orderDir
    LIMIT ?, ?";

// Append pagination parameters
$bindingsWithPagination = $bindings;
$typesWithPagination = $types . "ii";
$bindingsWithPagination[] = $start;
$bindingsWithPagination[] = $length;

// Prepare and execute the data query
$dataStmt = $conn->prepare($dataQuery);
if (!empty($bindingsWithPagination)) {
    $dataStmt->bind_param($typesWithPagination, ...$bindingsWithPagination);
}
$dataStmt->execute();
$dataResult = $dataStmt->get_result();

// Initialize an array to store the fetched data
$data = [];

// Fetch data and prepare for DataTables
while ($row = $dataResult->fetch_assoc()) {
    $data[] = [
        "client_name" => htmlspecialchars($row['client_name']),
        "invoice_id" => htmlspecialchars($row['invoice_id']),
        "payment_amount" => number_format($row['payment_amount'], 2, '.', ''),
        "payment_date" => htmlspecialchars($row['payment_date']),
        "bank_info" => htmlspecialchars($row['bank_info']),
        "type_of_pay" => htmlspecialchars($row['type_of_pay']),
        "description" => htmlspecialchars($row['description']),
        "total" => number_format($row['total'], 2, '.', '')
    ];
}

// Calculate total payments and total transactions based on current filters
$totalPaymentQuery = "SELECT SUM(payments.payment_amount) AS total_payment, COUNT(*) AS total_transactions " . $baseQuery;
$totalPaymentStmt = $conn->prepare($totalPaymentQuery);
if (!empty($bindings)) {
    $totalPaymentStmt->bind_param($types, ...$bindings);
}
$totalPaymentStmt->execute();
$totalPaymentResult = $totalPaymentStmt->get_result();
$totalPaymentRow = $totalPaymentResult->fetch_assoc();
$totalPayment = $totalPaymentRow['total_payment'] ? $totalPaymentRow['total_payment'] : 0;
$totalTransactions = $totalPaymentRow['total_transactions'] ? $totalPaymentRow['total_transactions'] : 0;

// Prepare chart data
$chartDataQuery = "
    SELECT DATE(payments.payment_date) AS payment_date, SUM(payments.payment_amount) AS total_amount
    " . $baseQuery . "
    GROUP BY DATE(payments.payment_date)
    ORDER BY DATE(payments.payment_date) ASC
";
$chartDataStmt = $conn->prepare($chartDataQuery);
if (!empty($bindings)) {
    $chartDataStmt->bind_param($types, ...$bindings);
}
$chartDataStmt->execute();
$chartDataResult = $chartDataStmt->get_result();

// Initialize arrays to store chart data
$chartDates = [];
$chartAmounts = [];

while ($row = $chartDataResult->fetch_assoc()) {
    $chartDates[] = $row['payment_date'];
    $chartAmounts[] = (float)$row['total_amount'];
}

// Prepare the JSON response
$response = [
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data,
    "totalPayments" => number_format($totalPayment, 2, '.', ''),
    "totalTransactions" => $totalTransactions,
    "chartData" => [
        "dates" => $chartDates,
        "amounts" => $chartAmounts
    ]
];

// Output the JSON response
echo json_encode($response);
?>
