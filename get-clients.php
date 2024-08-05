<?php
// Include database connection
include 'conn-d.php';
// Process DataTables server-side request
function processDataTablesRequest($conn)
{
    // Get request data
    $request = $_POST;
    // Set default values
    $length = isset($request['length']) ? intval($request['length']) : 10;
    $start = isset($request['start']) ? intval($request['start']) : 0;
    $draw = isset($request['draw']) ? intval($request['draw']) : 0;
    $searchValue = isset($request['search']['value']) ? $request['search']['value'] : '';
    $sortColumnIndex = isset($request['order'][0]['column']) ? intval($request['order'][0]['column']) : null;
    $sortDirection = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'desc';
    // Prepare base SQL query
    $sql = "SELECT emri, emriart, emailadd, dk, dks, monetizuar, id, youtube 
            FROM klientet 
            WHERE (aktiv IS NULL OR aktiv = 0)";
    // Apply search filter
    if (!empty($searchValue)) {
        $sql .= " AND (emri LIKE '%" . $conn->real_escape_string($searchValue) . "%' 
                     OR emriart LIKE '%" . $conn->real_escape_string($searchValue) . "%' 
                     OR emailadd LIKE '%" . $conn->real_escape_string($searchValue) . "%')";
    }
    // Get total records count
    $totalRecords = getTotalRecordsCount($conn);
    // Apply sorting
    $sql = applySorting($sql, $sortColumnIndex, $sortDirection);
    // Get filtered records count
    $filterRecords = getFilteredRecordsCount($conn, $sql);
    // Apply pagination
    $sql .= " LIMIT $length OFFSET $start";
    // Execute the query and fetch data
    $data = fetchData($conn, $sql);
    // Prepare and return the response
    return [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filterRecords,
        "data" => $data
    ];
}
// Get total records count
function getTotalRecordsCount($conn)
{
    $query = "SELECT COUNT(id) AS total FROM klientet WHERE aktiv IS NULL OR aktiv = 0";
    $result = $conn->query($query);
    return $result->fetch_assoc()['total'];
}
// Apply sorting to SQL query
function applySorting($sql, $sortColumnIndex, $sortDirection)
{
    $columns = ['emri', 'emriart', 'emailadd', 'dk', 'dks', 'monetizuar', 'id', 'youtube'];
    if ($sortColumnIndex !== null && isset($columns[$sortColumnIndex])) {
        $sortColumn = $columns[$sortColumnIndex];
        $sql .= " ORDER BY $sortColumn $sortDirection";
    } else {
        $sql .= " ORDER BY id DESC";
    }
    return $sql;
}
// Get filtered records count
function getFilteredRecordsCount($conn, $sql)
{
    $result = $conn->query($sql);
    return $result->num_rows;
}
// Fetch data from database
function fetchData($conn, $sql)
{
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}
// Process the request and echo JSON response
$response = processDataTablesRequest($conn);
echo json_encode($response);
