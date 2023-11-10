<?php
// Connect to the database
include 'conn-d.php';

// Define the columns to be used for sorting
$columns = array('emri', 'monetizuar');

// Get the total number of records (without filtering)
$totalRecords = $conn->query("SELECT COUNT(*) as count FROM klientet WHERE monetizuar = 'PO'")->fetch_assoc()['count'];

// Paging
$limit = intval($_POST['length']);
$start = intval($_POST['start']);
$draw = intval($_POST['draw']);

// Ordering
$orderBy = $columns[intval($_POST['order'][0]['column'])];
$orderDir = $_POST['order'][0]['dir'];

// Filtering
$searchValue = $_POST['search']['value'];

// Prepare the WHERE clause for filtering
$whereClause = " WHERE monetizuar = 'PO'";
if (!empty($searchValue)) {
  $whereClause .= " AND (emri LIKE '%$searchValue%' OR monetizuar LIKE '%$searchValue%')";
}

// Perform the query with paging, ordering, and filtering
$query = "SELECT emri, monetizuar FROM klientet $whereClause ORDER BY $orderBy $orderDir LIMIT $start, $limit";
$result = $conn->query($query);

// Prepare the data for DataTables
$data = array();
while ($row = $result->fetch_assoc()) {
  $data[] = $row;
}

// Perform a separate query to get the total number of filtered records
$queryFiltered = "SELECT COUNT(*) as count FROM klientet $whereClause";
$resultFiltered = $conn->query($queryFiltered);
$totalFiltered = $resultFiltered->fetch_assoc()['count'];

// Close the database connection
$conn->close();

// Prepare the response
$response = array(
  "draw" => $draw,
  "recordsTotal" => $totalRecords,
  "recordsFiltered" => $totalFiltered,
  "data" => $data
);

// Return the data as JSON
echo json_encode($response);
