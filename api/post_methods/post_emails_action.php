<?php
header('Content-Type: application/json');
include '../../conn-d.php'; // Ensure you have a separate file for DB connection

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'insert') {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $query = "INSERT INTO emails (email) VALUES ('$email')";
            if ($conn->query($query)) {
                $response['success'] = true;
                $response['message'] = 'Regjistrimi u shtua me sukses!';
            } else {
                $response['message'] = 'Shtimi i regjistrimit dështoi.';
            }
        } elseif ($action === 'update') {
            $edit_id = intval($_POST['edit_id']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $query = "UPDATE emails SET email='$email' WHERE id='$edit_id'";
            if ($conn->query($query)) {
                $response['success'] = true;
                $response['message'] = 'Regjistrimi u përditësua me sukses!';
            } else {
                $response['message'] = 'Përditësimi i regjistrimit dështoi.';
            }
        } elseif ($action === 'delete') {
            $delid = intval($_POST['id']);
            $stmt = $conn->prepare("DELETE FROM emails WHERE id = ?");
            $stmt->bind_param("i", $delid);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Regjistrimi është fshirë me sukses!';
            } else {
                $response['message'] = 'Fshirja e regjistrimit dështoi.';
            }
        }
    }
}

echo json_encode($response);
