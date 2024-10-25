<?php
include '../../conn-d.php'; // Adjust the path as needed

// Retrieve 'category' from POST data instead of GET
$category = isset($_POST['category']) ? $_POST['category'] : 'all';

// Initialize response array
$response = [
    'success' => true,
    'data' => []
];

try {
    if ($category === 'all') {
        $sql = "SELECT * FROM invoices_kont ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT * FROM invoices_kont WHERE category = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("s", $category);
    }

    // Execute the prepared statement
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute statement: " . $stmt->error);
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Set the header to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
