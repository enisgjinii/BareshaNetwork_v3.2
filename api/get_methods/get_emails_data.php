<?php
include '../../conn-d.php'; // Ensure you have a separate file for DB connection

$query = "SELECT * FROM emails ORDER BY id DESC";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'email' => $row['email'],
        // Add action buttons with data attributes for edit and delete
        'actions' => '
            <button class="input-custom-css px-3 py-2 btn-edit" data-id="' . $row['id'] . '" data-email="' . $row['email'] . '"><i class="fi fi-rr-edit"></i></button>
            <button class="input-custom-css px-3 py-2 btn-delete" data-id="' . $row['id'] . '"><i class="fi fi-rr-trash"></i></button>
        '
    ];
}

echo json_encode(['data' => $data]);
