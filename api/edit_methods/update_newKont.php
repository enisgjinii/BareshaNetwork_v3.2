<?php
include '../../conn-d.php'; // Adjust the path as needed

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Check if 'id', 'column', and 'value' are present
if (!isset($_POST['id']) || !isset($_POST['column']) || !isset($_POST['value'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$id = $_POST['id'];
$column = $_POST['column'];
$value = $_POST['value'];

// Whitelist columns that can be updated to prevent SQL injection
$allowedColumns = ['invoice_date', 'invoice_number', 'description', 'category', 'company_name', 'vlera_faktura'];
if (!in_array($column, $allowedColumns)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid column specified.'
    ]);
    exit;
}

// Prepare the SQL statement
$sql = "UPDATE invoices_kont SET {$column} = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Bind parameters based on column type
    if ($column === 'vlera_faktura') {
        // Assuming 'vlera_faktura' is a decimal
        $stmt->bind_param("di", $value, $id);
    } else {
        // For other columns, bind as string
        $stmt->bind_param("si", $value, $id);
    }

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Record updated successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update record.'
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to prepare statement.'
    ]);
}

$conn->close();
