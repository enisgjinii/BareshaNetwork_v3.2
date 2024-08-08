<?php
session_start();
include 'conn-d.php';

$action = $_POST['action'] ?? null;

switch ($action) {
    case 'fetch':
        // Fetch allowed IPs data
        $query = "SELECT * FROM allowed_ips";
        $result = $conn->query($query);
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['data' => $data]);
        break;

    case 'add':
        $ip = $conn->real_escape_string($_POST['ip_address']);
        $type = $conn->real_escape_string($_POST['type']);
        $query = "INSERT INTO allowed_ips (ip_address, type) VALUES ('$ip', '$type')";
        $conn->query($query);
        echo json_encode(['status' => 'success']);
        break;

    case 'edit':
        $id = $conn->real_escape_string($_POST['id']);
        $ip = $conn->real_escape_string($_POST['ip_address']);
        $type = $conn->real_escape_string($_POST['type']);
        $query = "UPDATE allowed_ips SET ip_address='$ip', type='$type' WHERE id=$id";
        $conn->query($query);
        echo json_encode(['status' => 'success']);
        break;

    case 'delete':
        $id = $conn->real_escape_string($_POST['id']);
        $query = "DELETE FROM allowed_ips WHERE id=$id";
        $conn->query($query);
        echo json_encode(['status' => 'success']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
