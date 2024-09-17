<?php
include '../../conn-d.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    // Validate inputs (you can add more specific validation if necessary)
    $allowedColumns = ['invoice_date','invoice_number', 'description', 'category', 'company_name', 'vlera_faktura'];
    if (!in_array($column, $allowedColumns)) {
        $response['message'] = 'Kolona e specifikuar është e pavlefshme.';
        echo json_encode($response);
        exit;
    }

    $sql = "UPDATE invoices_kont SET $column = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $response['message'] = 'Gabim në bazën e të dhënave: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param('si', $value, $id);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Dështoi përditësimi i rekordit. Gabimi: ' . $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Metoda e kërkesës është e pavlefshme.';
}

echo json_encode($response);
