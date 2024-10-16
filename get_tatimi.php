<?php
header('Content-Type: application/json');

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn-d.php';

$response = [];

// Check if 'id' is set and not empty
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Validate that 'id' is a positive integer
    if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        echo json_encode(['status' => 'error', 'message' => 'ID e pavlefshme.']);
        exit;
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM tatimi WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            // Handle potential NULL values (e.g., 'shteti')
            foreach ($data as $key => $value) {
                if (is_null($value)) {
                    $data[$key] = '';
                }
            }
            $response = ['status' => 'success', 'data' => $data];
        } else {
            $response = ['status' => 'error', 'message' => 'Rekordi nuk u gjet.'];
        }

        $stmt->close();
    } else {
        // Handle SQL statement preparation error
        $response = ['status' => 'error', 'message' => 'Gabim në përgatitjen e pyetjes SQL.'];
    }
} else {
    // Fetch all records if 'id' is not set
    $query = "SELECT * FROM tatimi ORDER BY id DESC";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Handle potential NULL values
            foreach ($row as $key => $value) {
                if (is_null($value)) {
                    $row[$key] = '';
                }
            }
            $data[] = $row;
        }
        $response = $data; // DataTables expects a plain array
    } else {
        $response = ['status' => 'error', 'message' => 'Gabim në ekzekutimin e pyetjes SQL.'];
    }
}

echo json_encode($response);
