<?php
// Database connection
include '../../conn-d.php';
// Get parameters from DataTables
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$start_date = isset($_GET['start_date1']) ? $_GET['start_date1'] : '';
$end_date = isset($_GET['end_date1']) ? $_GET['end_date1'] : '';
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$invoice_status = isset($_GET['invoice_status']) ? $_GET['invoice_status'] : '';
// Sanitize and validate inputs
$search = $conn->real_escape_string($search);
// Initialize variables for query construction
$searchQuery = '';
$dateRangeQuery = '';
$customerFilterQuery = '';
$statusFilterQuery = '';
$params = [];
$types = '';
// Apply search filter if provided
if (!empty($search)) {
    $searchQuery = " AND klientet.emri LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}
// Apply date range filter if both start_date and end_date are provided
if (!empty($start_date) && !empty($end_date)) {
    $dateRangeQuery = " AND payments.payment_date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}
// Apply customer ID filter if provided
if (!empty($customer_id) && is_numeric($customer_id)) {
    $customerFilterQuery = " AND klientet.id = ?";
    $params[] = intval($customer_id);
    $types .= 'i';
}
// Apply invoice status filter if provided and valid
$valid_statuses = ['Rregullt', 'Parregullt'];
if (!empty($invoice_status) && in_array($invoice_status, $valid_statuses)) {
    $statusFilterQuery = " AND invoices.invoice_status = ?";
    $params[] = $invoice_status;
    $types .= 's';
}
// Base SQL query with dynamic filters
$baseQuery = "FROM payments
             INNER JOIN invoices ON payments.invoice_id = invoices.id
             INNER JOIN klientet ON invoices.customer_id = klientet.id
             WHERE (klientet.lloji_klientit = 'Personal' OR klientet.lloji_klientit IS NULL)
             $searchQuery
             $dateRangeQuery
             $customerFilterQuery
             $statusFilterQuery";
// SQL query for data retrieval with LIMIT directly injected
$sql = "SELECT 
            invoices.id, 
            invoices.customer_id, 
            invoices.invoice_number, 
            klientet.emri AS customer_name, 
            klientet.emailadd, 
            klientet.email_kontablist, 
            MIN(payments.payment_id) AS payment_id, 
            payments.invoice_id, 
            SUM(payments.payment_amount) AS total_payment_amount, 
            MIN(payments.payment_date) AS payment_date, 
            MIN(payments.bank_info) AS bank_info, 
            MIN(payments.type_of_pay) AS type_of_pay, 
            MIN(payments.description) AS description, 
            invoices.total_amount AS total_invoice_amount,
            invoices.total_amount_after_percentage AS total_amount_after_percentage
        $baseQuery
        GROUP BY 
            invoices.id, 
            invoices.customer_id, 
            invoices.invoice_number, 
            klientet.emri, 
            invoices.total_amount
        ORDER BY payments.payment_id DESC
        LIMIT $start, $length"; // Directly inject LIMIT parameters
// Prepare the main statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die(json_encode([
        "error" => "Failed to prepare statement: " . $conn->error
    ]));
}
// Bind parameters dynamically (only filter parameters, excluding LIMIT)
if (!empty($types)) {
    // mysqli requires parameters to be passed by reference
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}
// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
// Close the main statement
$stmt->close();
// SQL query for total records count (without LIMIT)
$totalRecordsQuery = "SELECT COUNT(DISTINCT invoices.id) AS total $baseQuery";
$totalStmt = $conn->prepare($totalRecordsQuery);
if ($totalStmt === false) {
    die(json_encode([
        "error" => "Failed to prepare count statement: " . $conn->error
    ]));
}
// Prepare parameters for the count query (exclude LIMIT parameters)
$count_params = $params; // All filter parameters are already in $params
$count_types = $types;   // All filter types are already in $types
if (!empty($count_types)) {
    // mysqli requires parameters to be passed by reference
    $bind_names_count[] = $count_types;
    for ($i = 0; $i < count($count_params); $i++) {
        $bind_name = 'count_bind' . $i;
        $$bind_name = $count_params[$i];
        $bind_names_count[] = &$$bind_name;
    }
    call_user_func_array([$totalStmt, 'bind_param'], $bind_names_count);
}
// Execute the count statement
$totalStmt->execute();
$totalRecordsResult = $totalStmt->get_result();
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
// Close the count statement
$totalStmt->close();
// Create response array
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
];
// Return JSON response
echo json_encode($response);
// Close database connection
$conn->close();
?>
