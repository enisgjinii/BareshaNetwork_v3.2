<?php
// Assuming you have established a database connection
include 'conn-d.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the SQL query to fetch data based on the ID
    $query = "SELECT * FROM autori WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the data as an associative array
    $data = mysqli_fetch_assoc($result);

    // Return the fetched data as JSON
    echo json_encode($data);
} else {
    // Handle error if ID is not provided
    echo json_encode(['error' => 'ID is missing']);
}
?>
