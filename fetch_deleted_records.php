<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish a connection to the database
include 'conn-d.php';

// Specify the table name
$tableName = 'deleted_ngarkimi';

// Define the primary key field
$primaryKeyField = 'id';

// Define the column structure
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'deleted_record', 'dt' => 1),
    array('db' => 'deleted_at', 'dt' => 2)
);

// Define the indexed column for efficient querying
$indexedColumn = 'id';

// Prepare an array to store the response data
$responseData = array();

// Retrieve the total count of records without any filters
$countQuery = "SELECT COUNT($primaryKeyField) as count FROM $tableName";
$countResult = mysqli_query($conn, $countQuery) or die(mysqli_error($conn));
$countRow = mysqli_fetch_assoc($countResult);
$totalRecords = $countRow['count'];

// Apply filtering conditions if necessary
$whereClause = '';
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
if (!empty($searchValue)) {
    $whereClause = "WHERE deleted_record LIKE '%$searchValue%'";
}

// Retrieve the total count of records after applying filters
$filterCountQuery = "SELECT COUNT($primaryKeyField) as count FROM $tableName $whereClause";
$filterCountResult = mysqli_query($conn, $filterCountQuery) or die(mysqli_error($conn));
$filterCountRow = mysqli_fetch_assoc($filterCountResult);
$totalFiltered = $filterCountRow['count'];

// Fetch the records
$query = "SELECT * FROM $tableName $whereClause ORDER BY $primaryKeyField DESC";

$startIndex = isset($_POST['start']) ? $_POST['start'] : 0;
$length = isset($_POST['length']) ? $_POST['length'] : 10; // Default to 10 or adjust as needed
$query .= " LIMIT $startIndex, $length";

$queryResult = mysqli_query($conn, $query) or die(mysqli_error($conn));

// Iterate through the fetched data and format it for DataTables
$formattedData = array();
while ($row = mysqli_fetch_assoc($queryResult)) {
    $formattedData[] = array_values($row);
}

// Construct the response
$response = array();
$response['draw'] = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$response['recordsTotal'] = $totalRecords;
$response['recordsFiltered'] = $totalFiltered;
$response['data'] = $formattedData;

// Output the response as JSON
echo json_encode($response);
