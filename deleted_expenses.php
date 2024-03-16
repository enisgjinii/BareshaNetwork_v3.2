<?php
// Include the database connection file
include 'conn-d.php';

// Define the columns available in your table
$columns = array(
    0 => 'id',
    1 => 'klienti', // This will be fetched from the klientet table
    2 => 'shuma',
    3 => 'data',
    4 => 'pershkrimi',
    5 => 'lloji',
    6 => 'pagoi',
    7 => 'linku_i_kenges'
);

// Set default sorting
$order = 'id DESC';

// Get parameters from DataTables
$start = $_POST['start'];
$length = $_POST['length'];
$searchValue = $_POST['search']['value'];
$columnIndex = $_POST['order'][0]['column'];
$columnName = $columns[$columnIndex];
$orderDirection = $_POST['order'][0]['dir'];

// Construct the query
$query = "SELECT r.*, (r.shuma - r.pagoi) AS obligim, k.emri AS klienti FROM recovery_yinc r ";
$query .= "LEFT JOIN klientet k ON r.kanali = k.id "; // Adjust the join condition according to your table structure

// If there's a search term, add a WHERE clause
if (!empty($searchValue)) {
    $query .= "WHERE (";
    $query .= "r.kanali LIKE '%" . $searchValue . "%' "; // Adjust this condition according to your requirements
    $query .= "OR r.shuma LIKE '%" . $searchValue . "%' ";
    $query .= "OR r.data LIKE '%" . $searchValue . "%' ";
    $query .= "OR r.pershkrimi LIKE '%" . $searchValue . "%' ";
    $query .= "OR r.lloji LIKE '%" . $searchValue . "%' ";
    $query .= "OR r.pagoi LIKE '%" . $searchValue . "%' ";
    $query .= "OR r.linku_i_kenges LIKE '%" . $searchValue . "%' ";
    $query .= "OR k.emri LIKE '%" . $searchValue . "%' "; // Searching by klienti name
    $query .= ") ";
}

// Add sorting
$query .= "ORDER BY " . $columnName . " " . $orderDirection . " ";

// Add limit and offset
$query .= "LIMIT " . $start . ", " . $length;

// Execute the query
$result = mysqli_query($conn, $query);

// Fetch data into array
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Format the linku_i_kenges field as a clickable link
    $row['linku_i_kenges'] = '<a href="' . $row['linku_i_kenges'] . '" target="_blank"> Linku </a>';
    $row['obligim'] = number_format($row['obligim'], 2); // Format obligim as needed
    $data[] = $row;
}

// Get total records count
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM recovery_yinc";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

// Get filtered records count
$filteredRecordsQuery = "SELECT COUNT(*) AS total FROM recovery_yinc r ";
$filteredRecordsQuery .= "LEFT JOIN klientet k ON r.kanali = k.id "; // Adjust the join condition according to your table structure
if (!empty($searchValue)) {
    $filteredRecordsQuery .= "WHERE (";
    $filteredRecordsQuery .= "r.kanali LIKE '%" . $searchValue . "%' "; // Adjust this condition according to your requirements
    $filteredRecordsQuery .= "OR r.shuma LIKE '%" . $searchValue . "%' ";
    $filteredRecordsQuery .= "OR r.data LIKE '%" . $searchValue . "%' ";
    $filteredRecordsQuery .= "OR r.pershkrimi LIKE '%" . $searchValue . "%' ";
    $filteredRecordsQuery .= "OR r.lloji LIKE '%" . $searchValue . "%' ";
    $filteredRecordsQuery .= "OR r.pagoi LIKE '%" . $searchValue . "%' ";
    $filteredRecordsQuery .= "OR r.linku_i_kenges LIKE '%" . $searchValue . "%' ";
    $filteredRecordsQuery .= "OR k.emri LIKE '%" . $searchValue . "%' "; // Searching by klienti name
    $filteredRecordsQuery .= ") ";
}
$filteredRecordsResult = mysqli_query($conn, $filteredRecordsQuery);
$filteredRecords = mysqli_fetch_assoc($filteredRecordsResult)['total'];

// Prepare data to send to DataTables
$response = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
);

// Send JSON response
echo json_encode($response);
