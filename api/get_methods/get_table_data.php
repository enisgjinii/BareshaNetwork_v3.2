<?php
include '../../conn-d.php';

$category = $_GET['category'] ?? 'all';

$query = "SELECT * FROM invoices_kont";
if ($category !== 'all') {
    $query .= " WHERE category = ?";
}

$stmt = $conn->prepare($query);
if ($category !== 'all') {
    $stmt->bind_param("s", $category);
}
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
