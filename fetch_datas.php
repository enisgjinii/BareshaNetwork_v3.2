<?php
// fetch_datas.php
// Check if type parameter is set and not empty
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = $_GET['type'];
    include 'conn-d.php'; // Ensure this file includes your database connection

    // Check if 'id' parameter is set and not empty
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];

        // Prepare SQL query based on the type
        if ($type === 'individual') {
            // Assuming 'klientet' table has 'perqindja_e_klientit' column for individual percentage
            $sql = "SELECT *, perqindja AS percentage FROM klientet WHERE id = ?";
            // Assuming you will bind parameters to prevent SQL injection
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode($row);
            } else {
                echo json_encode([]); // Return empty JSON if no data found
            }
        } elseif ($type === 'group') {
            // Assuming 'client_subaccounts' table has 'percentage' column for group percentage
            $sql = "SELECT * FROM client_subaccounts WHERE client_id = ?";
            // Assuming you will bind parameters to prevent SQL injection
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row; // Collect each row as an array element
            }
            echo json_encode($rows); // Return JSON array of rows
        } else {
            echo json_encode(['error' => 'Invalid type']); // Handle unknown type
        }
    } else {
        echo json_encode(['error' => 'Missing id']); // Handle missing id parameter
    }

    $stmt->close();
    $conn->close();
} else {
    // Handle case where type parameter is missing or empty
    echo json_encode(['error' => 'Invalid request']);
}
