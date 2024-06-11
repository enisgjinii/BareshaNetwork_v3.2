<?php
// Include your database connection here
include 'conn-d.php';

// Define the columns
$columns = array(
    'customer_name',
    'invoice_id',
    'total_payment_amount',
    'payment_date',
    'bank_info',
    'type_of_pay',
    'description',
    'total_invoice_amount',
    'payment_id' // Action column
);

// Define the main query
$sql = "SELECT 
            invoices.id,
            invoices.customer_id,
            invoices.invoice_number,
            klientet.emri AS customer_name,
            MIN(payments.payment_id) AS payment_id,
            payments.invoice_id,
            SUM(payments.payment_amount) AS total_payment_amount,
            MIN(payments.payment_date) AS payment_date,
            MIN(payments.bank_info) AS bank_info,
            MIN(payments.type_of_pay) AS type_of_pay,
            MIN(payments.description) AS description,
            invoices.total_amount_after_percentage AS total_invoice_amount
        FROM payments
        INNER JOIN invoices ON payments.invoice_id = invoices.id
        INNER JOIN klientet ON invoices.customer_id = klientet.id
        WHERE klientet.lloji_klientit = 'Personal' OR klientet.lloji_klientit IS NULL"; // Adding condition here

// Apply search
$searchValue = $_POST['search']['value'];
if (!empty($searchValue)) {
    $sql .= " AND (
                klientet.emri LIKE '%$searchValue%' OR  
                payments.payment_id LIKE '%$searchValue%' OR
                invoices.invoice_number LIKE '%$searchValue%' OR  
                payments.payment_date LIKE '%$searchValue%' OR  
                payments.bank_info LIKE '%$searchValue%' OR  
                payments.type_of_pay LIKE '%$searchValue%' OR  
                payments.description LIKE '%$searchValue%' OR  
                invoices.total_amount_after_percentage LIKE '%$searchValue%'
            )";
}

// Apply date range filter
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];
if (!empty($startDate) && !empty($endDate)) {
    $sql .= " AND payments.payment_date BETWEEN '$startDate' AND '$endDate'";
}

// Group by clause
$sql .= " GROUP BY invoices.customer_id, invoices.invoice_number, klientet.emri, payments.invoice_id";

// Subquery to count total records
$sqlCount = "SELECT COUNT(*) as count FROM ($sql) AS subquery";
$totalRecords = mysqli_fetch_assoc(mysqli_query($conn, $sqlCount))['count'];

// Apply ordering
if (!empty($_POST['order'])) {
    $orderByColumn = $columns[$_POST['order'][0]['column']];
    $orderDirection = $_POST['order'][0]['dir'];
    $sql .= " ORDER BY $orderByColumn $orderDirection";
}

// Apply pagination
$start = $_POST['start'];
$length = $_POST['length'];
if ($length != -1) { // Check if "All" option is selected
    $sql .= " LIMIT $start, $length";
}

// Execute the final query
$result = $conn->query($sql);

if (!$result) {
    // Handle query error
    die("Query failed: " . $conn->error);
}

// Process the data for DataTables
$data = array();

while ($row = $result->fetch_assoc()) {
    $dataRow = array();

    foreach ($columns as $column) {
        $dataRow[$column] = $row[$column];
    }

    // Add custom action link
    // Add custom action link
    $dataRow['action'] = '<a target="_blank" class="btn btn-primary btn-sm rounded-5 shadow-sm px-2 text-white" style="padding: 0.3rem 0.25rem; font-size: 0.7rem; text-decoration: none; text-transform: none;" href="print_invoice.php?id=' . $row['invoice_number'] . '"><i class="fi fi-rr-print"></i></a><br><br>' .
        '<button class="btn btn-danger btn-sm delete-btn text-white rounded-5 px-2" data-invoice-id="' . $row['invoice_id'] . '"><i class="fi fi-rr-trash"></i></button>';

    $dataRow['action'] .= '<a href="#" style="text-decoration:none;" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark send-invoice" ' .
        'data-id="' . $row['id'] . '">' .
        '<i class="fi fi-rr-file-export"></i></a>';


    $data[] = $dataRow;
}

// Convert data to JSON
$output = array(
    "draw" => intval($_POST["draw"]),
    "recordsTotal" => $totalRecords, // Total records without filtering
    "recordsFiltered" => $totalRecords, // Total records with filtering
    "data" => $data
);

echo json_encode($output);

// Close the database connection
$conn->close();
