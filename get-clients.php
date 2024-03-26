<?php
// Include your database connection code here
include 'conn-d.php';

// DataTables server-side processing
$requestData = $_POST;

// Initialize default values for length, start, and draw
$length = isset($requestData['length']) ? intval($requestData['length']) : 10;
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;
$draw = isset($requestData['draw']) ? intval($requestData['draw']) : 0;

// Fetch search value
$searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

// Column index to sort
$sortColumnIndex = isset($requestData['order'][0]['column']) ? intval($requestData['order'][0]['column']) : null;

// Sorting order
$sortDirection = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'desc'; // Default sorting order

// Prepare SQL query with search, sorting, and pagination
$sql = "SELECT emri, emriart, emailadd, dk, dks, monetizuar, id, youtube 
        FROM klientet 
        WHERE (aktiv IS NULL OR aktiv = 0)";

// Apply search filter
if (!empty($searchValue)) {
    $sql .= " AND (emri LIKE '%" . $searchValue . "%' 
                 OR emriart LIKE '%" . $searchValue . "%' 
                 OR emailadd LIKE '%" . $searchValue . "%')";
}

// Get total records count without filtering
$totalRecordsQuery = "SELECT COUNT(id) AS total FROM klientet WHERE aktiv IS NULL OR aktiv = 0";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Apply sorting
$sortColumn = null;
$columns = array('emri', 'emriart', 'emailadd', 'dk', 'dks', 'monetizuar', 'id', 'youtube');
if ($sortColumnIndex !== null && isset($columns[$sortColumnIndex])) {
    $sortColumn = $columns[$sortColumnIndex];
}
if ($sortColumn !== null) {
    $sql .= " ORDER BY " . $sortColumn . " " . $sortDirection;
} else {
    // Default sorting by id DESC if no sort column provided
    $sql .= " ORDER BY id DESC";
}

// Get filtered records count
$filterRecordsQuery = $sql;
$filterRecordsResult = $conn->query($filterRecordsQuery);
$filterRecords = $filterRecordsResult->num_rows;

// Apply pagination
$sql .= " LIMIT " . $length . " OFFSET " . $start;

// Execute the query
$result = $conn->query($sql);

// Prepare response data
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// JSON response
$response = array(
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($filterRecords),
    "data" => $data
);

echo json_encode($response);
