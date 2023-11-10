<?php
include 'conn-d.php';

// Define the columns that DataTables will display
$columns = array(
    'emri_kanalit',
    'id_kanalit',
    'data',
    'revenue',
);

// Define the SQL query
$sql = "SELECT emri_kanalit, id_kanalit, data, revenue FROM monetizimi_youtube";

// Execute the query and fetch the results
$result = $conn->query($sql);
$data = array();
while ($row = $result->fetch_assoc()) {
    // Convert the revenue to a float with 2 decimal places
    $row['revenue'] = round(floatval($row['revenue']), 2);
    // Add the row to the data array
    $data[] = $row;
}

// Close the database connection
$conn->close();

// Convert the data to JSON format and return it
echo json_encode(array('data' => $data, 'columns' => $columns));
?>
