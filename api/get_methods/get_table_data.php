<?php
// get_table_data.php

include '../../conn-d.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category == 'all') {
    $sql = "SELECT * FROM invoices_kont";
} else {
    $sql = "SELECT * FROM invoices_kont WHERE category = ?";
}

$stmt = $conn->prepare($sql);

if ($category != 'all') {
    $stmt->bind_param('s', $category);
}

$stmt->execute();
$result = $stmt->get_result();

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
