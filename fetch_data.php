<?php
// fetch_data.php
if (isset($_POST['month'])) {
    $month = $_POST['month'];
    
    include 'conn-d.php';
    $sql = "SELECT COUNT(*) as total_count, SUM(payment_amount) as total_sum FROM employee_payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);

    $stmt->close();
    $conn->close();
}
