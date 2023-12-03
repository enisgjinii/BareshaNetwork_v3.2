<?php
// Include your database connection file
include('conn-d.php');

// DataTables parameters
$draw = $_POST['draw'] ?? null;
$start = $_POST['start'] ?? null;
$length = $_POST['length'] ?? null;

// Order
$orderColumn = $_POST['order'][0]['column'] ?? null;
$orderDirection = $_POST['order'][0]['dir'] ?? null;
$columns = ['Emri', 'Artist', 'ReportingPeriod', 'AccountingPeriod', 'Release', 'Track', 'Country', 'RevenueUSD', 'RevenueShare', 'SplitPayShare'];
$orderBy = $columns[$orderColumn] ?? 'Emri';

// Search
$searchValue = $_POST['search']['value'] ?? null;

// Existing conditions
$conditions = '';

$lastItemId = $_POST['lastItemId'] ?? null;

$selectedClient = $_POST['selectedClient'] ?? null;
$reportingPeriod = $_POST['reportingPeriod'] ?? null;

// Add conditions for the new parameters
if (!empty($selectedClient)) {
    $conditions .= " AND Emri = '$selectedClient'";
}

if (!empty($reportingPeriod)) {
    $conditions .= " AND ReportingPeriod = '$reportingPeriod'";
}

if (!empty($searchValue)) {
    $conditions .= " AND (Emri LIKE '%$searchValue%' OR Artist LIKE '%$searchValue%' OR `Release` LIKE '%$searchValue%' OR Track LIKE '%$searchValue%')"; // Include columns in the OR clause
}

// Query to get total records count
$totalRecordsQuery = "SELECT COUNT(*) as count FROM platformat_2 WHERE 1=1 $conditions";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['count'];

// Query to fetch data
$query = "SELECT * FROM platformat_2 WHERE 1=1 $conditions ORDER BY $orderBy $orderDirection LIMIT $start, $length";
$resultSet = mysqli_query($conn, $query);

// Data array for DataTables
$data = [];
while ($row = mysqli_fetch_assoc($resultSet)) {
    $data[] = $row;
}

// Response array for DataTables
$response = [
    'draw' => intval($draw),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords, // for simplicity, assuming no filtering is applied
    'data' => $data,
];

// Send JSON response
echo json_encode($response);
