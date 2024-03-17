<?php
// Include your database connection code here
require_once('conn-d.php');

// Define the default parameters
$params = [
    'start' => isset($_GET['start']) ? intval($_GET['start']) : 0,
    'length' => isset($_GET['length']) ? intval($_GET['length']) : 10,
    'search' => isset($_GET['search']['value']) ? $_GET['search']['value'] : ''
];

// Construct the SQL query with pagination and search functionality
$query = "SELECT y.*, k.emri AS klienti_emri FROM yinc y JOIN klientet k ON y.kanali = k.id";

// If search value is provided, add search condition
if (!empty($params['search'])) {
    $query .= " WHERE y.id LIKE '%$params[search]%' OR y.shuma LIKE '%$params[search]%' OR y.pagoi LIKE '%$params[search]%' OR y.lloji LIKE '%$params[search]%' OR y.pershkrimi LIKE '%$params[search]%' OR y.data LIKE '%$params[search]%' OR k.emri LIKE '%$params[search]%'";
}

$query .= " ORDER BY y.id DESC LIMIT $params[start], $params[length]";

$result = $conn->query($query);

$data = array();

while ($row = $result->fetch_assoc()) {
    // Additional processing
    // Example: Format dates
    $row['formatted_date'] = date('Y-m-d', strtotime($row['data']));

    // Example: Calculate remaining balance
    $row['remaining_balance'] = $row['shuma'] - $row['pagoi'];

    // Example: Convert linku_i_kenges to an array of links
    $row['links'] = explode(',', $row['linku_i_kenges']);

    // Add the modified row to the data array
    $data[] = $row;
}

// Get total records count for pagination
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM yinc y JOIN klientet k ON y.kanali = k.id";
if (!empty($params['search'])) {
    $totalRecordsQuery .= " WHERE y.id LIKE '%$params[search]%' OR y.shuma LIKE '%$params[search]%' OR y.pagoi LIKE '%$params[search]%' OR y.lloji LIKE '%$params[search]%' OR y.pershkrimi LIKE '%$params[search]%' OR y.data LIKE '%$params[search]%' OR k.emri LIKE '%$params[search]%'";
}
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

// Return data as JSON with total records count
echo json_encode([
    'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords,
    'data' => $data
]);
