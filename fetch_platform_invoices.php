<?php
include 'conn-d.php';

$start = $_GET['start'];
$length = $_GET['length'];
$orderColumnIndex = $_GET['order'][0]['column'];
$orderDirection = $_GET['order'][0]['dir'];
$searchValue = $_GET['search']['value'];

// Define an array of columns and their corresponding names
$columns = array(
    1 => 'id',
    2 => 'klient_emri',
    3 => 'platform',
    4 => 'platform_income',
    5 => 'platform_income_after_percentage',
    6 => 'date',
    // Add more columns as needed
);

// Set the order column using the array
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'id';

$query = "SELECT pi.*, k.emri as klient_emri
          FROM platform_invoices pi
          JOIN klientet k ON pi.client_id = k.id
          WHERE k.emri LIKE '%$searchValue%'
          ORDER BY id DESC
          LIMIT $start, $length";


$result = mysqli_query($conn, $query);

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$totalRecordsQuery = "SELECT COUNT(*) as count FROM platform_invoices";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['count'];

$filteredRecordsQuery = "SELECT COUNT(*) as count
                        FROM platform_invoices pi
                        JOIN klientet k ON pi.client_id = k.id
                        WHERE k.emri LIKE '%$searchValue%'";
$filteredRecordsResult = mysqli_query($conn, $filteredRecordsQuery);
$filteredRecords = mysqli_fetch_assoc($filteredRecordsResult)['count'];

$output = array(
    "draw" => intval($_GET['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
);

echo json_encode($output);

mysqli_close($conn);
