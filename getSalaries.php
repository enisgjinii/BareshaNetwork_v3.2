<?php

// Establish a connection to the database
include 'conn-d.php';

// Define an array representing database columns
$columns = array(
    'firstName',
    'muaji',
    'viti',
    'shuma',
    'kontributi',
    'kontributi2',
    'tatimi',
    'neto',
    'data',
    'pagesa'
);

// Capture parameters from the DataTables request
$start = isset($_GET['start']) ? $_GET['start'] : 0;
$length = isset($_GET['length']) ? $_GET['length'] : 10;
$orderColumn = isset($_GET['order'][0]['column']) ? $columns[$_GET['order'][0]['column']] : '';
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : '';
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$draw = isset($_GET['draw']) ? $_GET['draw'] : '';

// Formulate the SQL query
$query = "SELECT r.*, g.firstName, g.last_name FROM rrogat r LEFT JOIN googleauth g ON r.stafi = g.id";

// Integrate the search condition
if (!empty($searchValue)) {
    $query .= " WHERE firstName LIKE '%" . $searchValue . "%'"; // Adjust as needed to search in other columns
}

// Integrate the ordering condition
if (!empty($orderColumn)) {
    $query .= " ORDER BY " . $orderColumn . " " . $orderDir;
}

// Integrate the pagination condition
$query .= " LIMIT " . intval($start) . ", " . intval($length);

// Execute the query
$result = $conn->query($query);

// Capture the data
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Retrieve the total number of records
$result = $conn->query("SELECT COUNT(*) AS total FROM rrogat");
$total = $result->fetch_assoc()['total'];

// Present the result as JSON
echo json_encode(array(
    'draw' => intval($draw),
    'recordsTotal' => intval($total),
    'recordsFiltered' => intval($total), // Adjust if server-side filtering is implemented
    'data' => $data
));
