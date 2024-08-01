<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn-d.php';
include 'send_email_to_employ.php';

$employee_id = $_POST['employee_id'];
$title = $_POST['title'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$status = $_POST['status'];

// Fetch employee details
$sql = "SELECT firstName, last_name, email FROM googleauth WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
    exit;
}

// Insert the leave request
$sql = "INSERT INTO leaves (title, start_date, end_date, status, user_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $title, $start_date, $end_date, $status, $employee_id);

if ($stmt->execute()) {
    // Send email to employee
    $subject = 'Kërkesë e re për leje';
    $body = "<div style='font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;'>
             <p style='background-color: #ffffff; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px;'>
                 Një kërkesë e re për pushim është paraqitur për ju:<br>
                 <strong>Titulli:</strong> {$title}<br>
                 <strong>Data e fillimit:</strong> {$start_date}<br>
                 <strong>Data e përfundimit:</strong> {$end_date}<br>
                 <strong>Statusi:</strong> {$status}
             </p>
         </div>";


    $emailSent = sendEmail($employee['email'], $subject, $body);

    if ($emailSent) {
        echo json_encode([
            'success' => true,
            'message' => 'New leave request added successfully and notification sent to employee'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'New leave request added successfully but failed to send notification to employee'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
