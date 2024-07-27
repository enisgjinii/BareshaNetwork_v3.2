<?php
// Include the database connection file
include 'conn-d.php';

// Get parameters from DataTables
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$minDateBiz = isset($_POST['minDateBiz']) ? $_POST['minDateBiz'] : '';
$maxDateBiz = isset($_POST['maxDateBiz']) ? $_POST['maxDateBiz'] : '';

// Prepare base query to fetch data
$query = "
SELECT
    payments.payment_id,
    payments.invoice_id,
    payments.payment_amount,
    payments.payment_date,
    payments.bank_info,
    payments.type_of_pay,
    payments.description,
    klientet.emri AS client_name,
    invoices.total_amount_after_percentage AS total
FROM payments
JOIN invoices ON payments.invoice_id = invoices.id
JOIN klientet ON invoices.customer_id = klientet.id
WHERE (klientet.lloji_klientit = 'Biznes')
";

// Handle date filter
if (!empty($minDateBiz) && !empty($maxDateBiz)) {
    $query .= " AND payments.payment_date BETWEEN '$minDateBiz' AND '$maxDateBiz'";
} elseif (!empty($minDateBiz)) {
    $query .= " AND payments.payment_date >= '$minDateBiz'";
} elseif (!empty($maxDateBiz)) {
    $query .= " AND payments.payment_date <= '$maxDateBiz'";
}

// Handle search filter
if (!empty($search)) {
    $search = $conn->real_escape_string($search); // Sanitize search input
    $query .= " AND (
        payments.payment_id LIKE '%$search%' OR 
        payments.invoice_id LIKE '%$search%' OR 
        payments.payment_amount LIKE '%$search%' OR 
        payments.payment_date LIKE '%$search%' OR 
        payments.bank_info LIKE '%$search%' OR 
        payments.type_of_pay LIKE '%$search%' OR 
        payments.description LIKE '%$search%' OR 
        klientet.emri LIKE '%$search%' OR 
        invoices.total_amount_after_percentage LIKE '%$search%'
    )";
}

// Add ordering
$query .= " ORDER BY payments.payment_id DESC";

// Add pagination if not "All"
if ($length !== -1) {
    $query .= " LIMIT $start, $length";
}

// Execute the query to fetch paginated data
$result = $conn->query($query);

// Initialize total records count
$totalRecords = 0;
$totalRecordsFiltered = 0;

// Fetch total records for pagination
$totalRecordsQuery = "
SELECT COUNT(*) AS total
FROM payments
JOIN invoices ON payments.invoice_id = invoices.id
JOIN klientet ON invoices.customer_id = klientet.id
WHERE (klientet.lloji_klientit = 'Biznes')";

if (!empty($minDateBiz) && !empty($maxDateBiz)) {
    $totalRecordsQuery .= " AND payments.payment_date BETWEEN '$minDateBiz' AND '$maxDateBiz'";
} elseif (!empty($minDateBiz)) {
    $totalRecordsQuery .= " AND payments.payment_date >= '$minDateBiz'";
} elseif (!empty($maxDateBiz)) {
    $totalRecordsQuery .= " AND payments.payment_date <= '$maxDateBiz'";
}

if (!empty($search)) {
    $totalRecordsQuery .= " AND (
        payments.payment_id LIKE '%$search%' OR 
        payments.invoice_id LIKE '%$search%' OR 
        payments.payment_amount LIKE '%$search%' OR 
        payments.payment_date LIKE '%$search%' OR 
        payments.bank_info LIKE '%$search%' OR 
        payments.type_of_pay LIKE '%$search%' OR 
        payments.description LIKE '%$search%' OR 
        klientet.emri LIKE '%$search%' OR 
        invoices.total_amount_after_percentage LIKE '%$search%'
    )";
}

$totalRecordsResult = $conn->query($totalRecordsQuery);
if ($totalRecordsResult) {
    $totalRecordsRow = $totalRecordsResult->fetch_assoc();
    $totalRecords = $totalRecordsRow['total'];
    $totalRecordsFiltered = $totalRecords; // Adjust if needed
}

// Initialize an array to store the fetched data
$data = array();

// Loop through the result and store each row in the data array
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Prepare the output in JSON format for DataTables
$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFiltered,
    "data" => $data
);

// Send the output in JSON format
echo json_encode($output);
?>
