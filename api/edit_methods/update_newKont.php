<?php
include '../../conn-d.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    // Validate and sanitize inputs
    $id = filter_var($id, FILTER_VALIDATE_INT);
    $column = filter_var($column, FILTER_SANITIZE_STRING);
    $value = filter_var($value, FILTER_SANITIZE_STRING);

    // List of allowed columns to update
    $allowedColumns = ['invoice_date', 'description', 'category', 'company_name', 'vlera_faktura'];

    if ($id && in_array($column, $allowedColumns)) {
        $sql = "UPDATE invoices_kont SET $column = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $value, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
