<?php
// update_newKont.php

include '../../conn-d.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    // Validate inputs as necessary

    $allowedColumns = ['invoice_date', 'description', 'category', 'company_name', 'vlera_faktura'];
    if (!in_array($column, $allowedColumns)) {
        $response['message'] = 'Invalid column specified.';
        echo json_encode($response);
        exit;
    }

    $sql = "UPDATE invoices_kont SET $column = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $response['message'] = 'Database error: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param('si', $value, $id);
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to update the record.';
    }
    $stmt->close();
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
