<?php
require_once "../../conn-d.php";

if (isset($_POST['customer_id'])) {
    $customerId = $_POST['customer_id'];
    $sql = "SELECT client_id FROM clients_subaccounts WHERE client_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'client_id' => $row['client_id']]);
    } else {
        echo json_encode(['status' => 'not_found']);
    }

    $stmt->close();
}

$conn->close();
