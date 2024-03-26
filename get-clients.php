<?php
// Include your database connection code here
include 'conn-d.php';
// Define the specific columns to display
$columnsToDisplay = array(
    'k.emri', // Specifying the table alias
    'k.emriart',
    'k.emailadd',
    'k.dk',
    'k.dks',
    'kg.data_e_krijimit',
    'kg.kohezgjatja',
    'k.monetizuar',
    'k.id'
);
// DataTables server-side processing
$requestData = $_GET;
// Initialize default values for length, start, and draw
$length = isset($requestData['length']) ? intval($requestData['length']) : 10; // Default length of 10
$start = isset($requestData['start']) ? intval($requestData['start']) : 0; // Default start position of 0
$draw = isset($requestData['draw']) ? intval($requestData['draw']) : 0; // Default draw counter of 0
$sql = "SELECT k.emri, k.emriart, k.emailadd,k.dk, k.dks ,kg.data_e_krijimit, kg.kohezgjatja, k.monetizuar, k.id ";
$sql .= "FROM klientet k ";
$sql .= "LEFT JOIN kontrata_gjenerale kg ON CONCAT(kg.emri, ' ', kg.mbiemri) = k.emri ";
$sql .= "OR SUBSTRING_INDEX(kg.artisti, '||', 1) = k.emri ";
$sql .= "OR (kg.youtube_id IS NOT NULL AND kg.youtube_id != '' AND kg.youtube_id = k.youtube) ";
$sql .= "OR kg.artisti LIKE CONCAT('%', k.emri, '%')";
$sql .= "OR CONCAT(kg.emri, ' ', kg.mbiemri) LIKE CONCAT('%', k.emri, '%')";
$sql .= " WHERE k.aktiv IS NULL OR k.aktiv = 0"; // Condition for aktiv column
$sqlTotal = $sql; // Total query (without paging)
$sqlFiltered = $sql; // Query for data filtering
// Apply search filter if a search term is provided
if (!empty($requestData['search']['value'])) {
    $searchValue = $conn->real_escape_string($requestData['search']['value']);
    $sqlFiltered .= " AND (";
    foreach ($columnsToDisplay as $column) {
        $sqlFiltered .= "$column LIKE '%$searchValue%' OR "; // Corrected concatenation
    }
    $sqlFiltered = rtrim($sqlFiltered, "OR "); // Remove the last "OR" from the query
    $sqlFiltered .= ")";
}
// Execute the filtered query to get the data count (before pagination)
$totalRecords = $conn->query($sqlFiltered)->num_rows;
// Apply length (limit) and offset for pagination
$length = intval($requestData['length']);
$start = intval($requestData['start']);
$sqlFiltered .= " ORDER BY id DESC"; // Example sorting by the first column
$sqlFiltered .= " LIMIT $start, $length";
// Execute the filtered and ordered query to get the data
$result = $conn->query($sqlFiltered);
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
// Prepare the response JSON
$response = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data,
);
// Output the JSON response
echo json_encode($response);
