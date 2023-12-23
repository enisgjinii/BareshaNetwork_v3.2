<?php
// Include your database connection code here
include 'conn-d.php';

// Define the specific columns to display
$columnsToDisplay = array(
    'emri',
    'emriart',
    'emailadd',
    'dk',
    'dks',
    'monetizuar', 'id'
);

// DataTables server-side processing
$requestData = $_GET;
$columns = array(
    'emri',
    'emriart',
    'emailadd',
    'dk',
    'dks',
    'monetizuar', 'id'
);

// Construct the SQL query based on DataTables request
$sql = "SELECT ";
$sql .= implode(", ", $columnsToDisplay); // Join the columns with a comma
$sql .= " FROM klientet ORDER BY id DESC";
$sqlTotal = $sql; // Total query (without paging)
$sqlFiltered = $sql; // Query for data filtering

// Check if the 'order' key exists and is not empty
if (isset($requestData['order']) && !empty($requestData['order'])) {
    $orderByColumn = $columns[$requestData['order'][0]['column']];
    $orderDirection = $requestData['order'][0]['dir'];
    $sqlFiltered .= " ORDER BY $orderByColumn $orderDirection";
}

// Apply search filter if a search term is provided
if (!empty($requestData['search']['value'])) {
    $sqlFiltered .= " WHERE (";
    for ($i = 0; $i < count($columns); $i++) {
        $sqlFiltered .= $columns[$i] . " LIKE '%" . $requestData['search']['value'] . "%'";
        if ($i < count($columns) - 1) {
            $sqlFiltered .= " OR ";
        }
    }
    $sqlFiltered .= ")";
}

// Execute the filtered query to get the data count (before pagination)
$totalRecords = $conn->query($sqlFiltered)->num_rows;

// Apply length (limit) and offset for pagination
$length = intval($requestData['length']);
$start = intval($requestData['start']);
$sqlFiltered .= " LIMIT $start, $length";

// Execute the filtered and ordered query to get the data
$result = $conn->query($sqlFiltered);
$data = array();
while ($row = $result->fetch_assoc()) {
    $rowData = array();
    foreach ($columnsToDisplay as $column) {
        $rowData[$column] = $row[$column];
    }

    // Process your data here if needed

    $data[] = $rowData;
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
