<?php
include 'conn-d.php';

// ORDER this by ID DESC
$sql = "SELECT office_damages.*, googleauth.firstName, googleauth.last_name
        FROM office_damages
        LEFT JOIN googleauth ON office_damages.reporter_name = googleauth.id ORDER BY id DESC";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
