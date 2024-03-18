<?php
session_start();
require 'vendor/autoload.php';
include 'conn-d.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $csrf_token = isset($_COOKIE['csrf_token']) ? $_COOKIE['csrf_token'] : '';
    if (!isset($_POST['csrf_token']) || !hash_equals($csrf_token, $_POST['csrf_token'])) {
        // Invalid CSRF token, handle accordingly
        echo "Error: CSRF token validation failed.";
        exit();
    }

    // Validate input data
    if (!isset($_POST['user_id']) || !isset($_POST['role_id'])) {
        // Handle missing input data
        echo "Error: Required fields are missing.";
        exit();
    }

    // Sanitize user input
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $role_id = filter_var($_POST['role_id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch user info and role name
    $user_info = fetchUserInfo($user_id, $conn);
    $role_name = fetchRoleName($role_id, $conn);

    // Check if user info and role name are fetched successfully
    if (!$user_info || !$role_name) {
        // Handle database fetch errors
        echo "Error: Failed to fetch user info or role name.";
        exit();
    }

    // Store selected user and role information in session variables
    $_SESSION['selected_user'] = $user_info;
    $_SESSION['selected_role_name'] = $role_name;
    $_SESSION['selected_user_id'] = $user_id;
    $_SESSION['selected_role_id'] = $role_id;

    // Insert user role into database
    $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $role_id);

    // Check if insertion is successful
    if ($stmt->execute()) {
        // Email sending code
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'egjini17@gmail.com';
            $mail->Password = 'rhydniijtqzijjdy';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('egjini17@gmail.com', 'Technical Information');
            $mail->addAddress($user_info['email'], $user_info['firstName'] . ' ' . $user_info['last_name']);
            $mail->addAddress('egjini17@gmail.com', 'Administrator');
            $mail->addReplyTo('egjini17@gmail.com', 'Technical Information');
            $mail->isHTML(true);
            $mail->Subject = '=?utf-8?B?' . base64_encode('Roli për përdoruesin ' . $user_info['firstName'] . ' ' . $user_info['last_name'] . ' është zgjedhur ' . $role_name) . '?=';
            $mail->Body = "
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        background-color: #fff;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    }
                    h1 {
                        color: #333;
                        text-align: center;
                    }
                    .content {
                        margin-top: 20px;
                        border-top: 2px solid #ddd;
                        padding-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1>Roli i përdoruesit është zgjedhur</h1>
                    <div class='content'>
                        <p>Përdoruesi: " . $user_info['firstName'] . " " . $user_info['last_name'] . "</p>
                        <p>Email: " . $user_info['email'] . "</p>
                        <p>Roli: " . $role_name . "</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            $mail->send();
        } catch (Exception $e) {
            // Handle email sending errors
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
        // Redirect to the roles.php page
        header("Location: roles.php");
        exit();
    } else {
        // Handle database insertion errors
        echo "Error: " . $stmt->error;
    }
}

// Function to fetch user information from the database
function fetchUserInfo($user_id, $conn)
{
    $sql = "SELECT id, firstName, last_name, email FROM googleauth WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
    return $user_info;
}

// Function to fetch role name from the database based on role ID
function fetchRoleName($role_id, $conn)
{
    $sql = "SELECT name FROM roles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $role = $result->fetch_assoc();
    return $role ? $role['name'] : null;
}
