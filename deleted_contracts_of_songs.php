<?php
// Include your database connection file
include 'conn-d.php';

try {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Define the columns which can be searched
    $searchableColumns = array('emri', 'mbiemri', 'numri_i_telefonit', 'numri_personal', 'vepra', 'data', 'shenim', 'nenshkrimi', 'kontrata_PDF', 'perqindja', 'klienti', 'klient_email', 'emriartistik', 'pdf_file', 'created_at');

    // Initialize variables for pagination
    $start = isset($_POST['start']) ? $_POST['start'] : 0;
    $length = isset($_POST['length']) ? $_POST['length'] : 10;

    // Set default order
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
    $orderColumnName = isset($_POST['columns'][$orderColumnIndex]['data']) ? $_POST['columns'][$orderColumnIndex]['data'] : 'id';
    $orderDirection = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    // Set search keyword
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

    // Construct the query
    $query = "SELECT id, emri, mbiemri, numri_i_telefonit, numri_personal, vepra, data, shenim, nenshkrimi, kontrata_PDF, perqindja, klienti, klient_email, emriartistik, pdf_file, created_at FROM kontrata_recovery WHERE 1";

    // Apply search filter
    if (!empty($searchValue)) {
        $query .= " AND (";
        foreach ($searchableColumns as $column) {
            $query .= " $column LIKE '%$searchValue%' OR";
        }
        $query = rtrim($query, 'OR') . ")";
    }

    // Execute the total records count query (without filtering)
    $totalRecordsQuery = "SELECT COUNT(*) AS total FROM kontrata_recovery";
    $totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
    if (!$totalRecordsResult) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    $totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

    // Execute the total records count query (after filtering)
    $totalFilteredQuery = "SELECT COUNT(*) AS totalFiltered FROM kontrata_recovery WHERE 1";

    if (!empty($searchValue)) {
        $totalFilteredQuery .= " AND (";
        foreach ($searchableColumns as $column) {
            $totalFilteredQuery .= " $column LIKE '%$searchValue%' OR";
        }
        $totalFilteredQuery = rtrim($totalFilteredQuery, 'OR') . ")";
    }

    $totalFilteredResult = mysqli_query($conn, $totalFilteredQuery);
    if (!$totalFilteredResult) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    $totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['totalFiltered'];

    // Append the ORDER BY clause to the query
    $query .= " ORDER BY created_at DESC";

    // Apply pagination
    $query .= " LIMIT $start, $length";

    // Execute the query
    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception("Error: " . mysqli_error($conn));
    }

    // Fetch the data into an array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Add the row to the data array
        $data[] = $row;
    }

    // Close the connection
    mysqli_close($conn);

    // Return the data as JSON
    echo json_encode([
        'draw' => intval($_POST['draw']),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalFiltered,
        'data' => $data
    ]);
} catch (Exception $e) {
    // Return error message as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
