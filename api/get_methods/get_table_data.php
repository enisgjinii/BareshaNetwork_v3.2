<?php
include '../../conn-d.php'; // Adjust the path as needed

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category === 'all') {
    $sql = "SELECT * FROM invoices_kont ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM invoices_kont WHERE category = ? ORDER BY id DESC";
}

$stmt = $conn->prepare($sql);

if ($category !== 'all') {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
