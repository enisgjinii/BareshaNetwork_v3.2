<?php
include 'conn-d.php';

// Initialize variables for pagination, sorting, and filtering
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderColumn = isset($_GET['columns'][$orderColumnIndex]['data']) ? $_GET['columns'][$orderColumnIndex]['data'] : 'id';
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'DESC';
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

// Get the total number of records
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM platforms";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecordsRow = mysqli_fetch_assoc($totalRecordsResult);
$totalRecords = $totalRecordsRow['total'];

// Get the total number of records with filtering
$filteredRecordsQuery = "SELECT COUNT(*) AS total FROM platforms WHERE platforma LIKE '%$searchValue%' OR titulli LIKE '%$searchValue%' OR pershkrimi LIKE '%$searchValue%' OR email_used LIKE '%$searchValue%'";
$filteredRecordsResult = mysqli_query($conn, $filteredRecordsQuery);
$filteredRecordsRow = mysqli_fetch_assoc($filteredRecordsResult);
$totalFilteredRecords = $filteredRecordsRow['total'];

// Fetch the data with pagination, sorting, and filtering
$query = "
    SELECT * FROM platforms 
    WHERE platforma LIKE '%$searchValue%' 
    OR titulli LIKE '%$searchValue%' 
    OR pershkrimi LIKE '%$searchValue%' 
    OR email_used LIKE '%$searchValue%' 
    ORDER BY $orderColumn $orderDir 
    LIMIT $start, $length
";
$result = mysqli_query($conn, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Prepare the response for DataTables
$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFilteredRecords,
    "data" => $data
);

echo json_encode($response);

// Close database connection
mysqli_close($conn);
