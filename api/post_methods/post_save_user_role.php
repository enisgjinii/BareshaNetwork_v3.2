<?php
session_start();
require '../../vendor/autoload.php';
include '../../conn-d.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TCPDF;

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

    // Fetch user info, role name, and pages access
    $user_info = fetchUserInfo($user_id, $conn);
    $role_name = fetchRoleName($role_id, $conn);
    $pages_access = fetchPagesAccess($role_id, $conn);

    // Check if user info, role name, and pages access are fetched successfully
    if (!$user_info || !$role_name || !$pages_access) {
        // Handle database fetch errors
        echo "Error: Failed to fetch user info, role name, or pages access.";
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
        // Create PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdfContent = "<h1>Roli i përdoruesit është zgjedhur</h1>";
        $pdfContent .= "<p><strong>Përdoruesi:</strong> " . htmlspecialchars($user_info['firstName']) . " " . htmlspecialchars($user_info['last_name']) . "</p>";
        $pdfContent .= "<p><strong>Email:</strong> " . htmlspecialchars($user_info['email']) . "</p>";
        $pdfContent .= "<p><strong>Roli:</strong> " . htmlspecialchars($role_name) . "</p>";
        $pdfContent .= "<p><strong>Faqet ku keni akses:</strong></p><ul>";
        foreach ($pages_access as $page) {
            $pdfContent .= "<li>" . htmlspecialchars($page) . "</li>";
        }
        $pdfContent .= "</ul>";
        $pdf->writeHTML($pdfContent);
        $pdfOutput = $pdf->Output('', 'S');

        // Create Markdown
        $markdownContent = "# Roli i përdoruesit është zgjedhur\n";
        $markdownContent .= "**Përdoruesi:** " . htmlspecialchars($user_info['firstName']) . " " . htmlspecialchars($user_info['last_name']) . "\n\n";
        $markdownContent .= "**Email:** " . htmlspecialchars($user_info['email']) . "\n\n";
        $markdownContent .= "**Roli:** " . htmlspecialchars($role_name) . "\n\n";
        $markdownContent .= "**Faqet ku keni akses:**\n";
        foreach ($pages_access as $page) {
            $markdownContent .= "- " . htmlspecialchars($page) . "\n";
        }
        $markdownFile = 'role_info.md';
        file_put_contents($markdownFile, $markdownContent);

        // Email sending code
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'egjini@bareshamusic.com';
            $mail->Password = 'pazvpeihqiekpkiv';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('egjini@bareshamusic.com', 'Technical Information');
            $mail->addAddress($user_info['email'], $user_info['firstName'] . ' ' . $user_info['last_name']);
            $mail->addAddress('egjini@bareshamusic.com', 'Administrator');
            $mail->addReplyTo('egjini@bareshamusic.com', 'Technical Information');
            $mail->isHTML(true);
            $mail->Subject = '=?utf-8?B?' . base64_encode('Roli për përdoruesin ' . $user_info['firstName'] . ' ' . $user_info['last_name'] . ' është zgjedhur ' . $role_name) . '?=';
            $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;'>
                <div style='max-width: 600px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
                    <h1 style='color: #333; text-align: center; font-size: 24px; margin-bottom: 20px;'>Roli i përdoruesit është zgjedhur</h1>
                    <div style='border-top: 2px solid #ddd; padding-top: 20px;'>
                        <p style='color: #555; font-size: 16px; margin: 10px 0;'><strong>Përdoruesi:</strong> " . htmlspecialchars($user_info['firstName']) . " " . htmlspecialchars($user_info['last_name']) . "</p>
                        <p style='color: #555; font-size: 16px; margin: 10px 0;'><strong>Email:</strong> " . htmlspecialchars($user_info['email']) . "</p>
                        <p style='color: #555; font-size: 16px; margin: 10px 0;'><strong>Roli:</strong> " . htmlspecialchars($role_name) . "</p>
                        <p style='color: #555; font-size: 16px; margin: 10px 0;'><strong>Faqet ku keni akses:</strong></p>
                        <ul style='list-style-type: none; padding: 0;'>";
                        foreach ($pages_access as $page) {
                            $mail->Body .= "<li style='color: #555; font-size: 16px; margin: 5px 0;'>" . htmlspecialchars($page) . "</li>";
                        }
                        $mail->Body .= "</ul>
                    </div>
                    <div style='margin-top: 30px; text-align: center; color: #888; font-size: 14px;'>
                        <p>Ju faleminderit që përdorni shërbimet tona.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            $mail->addStringAttachment($pdfOutput, 'role_info.pdf');
            $mail->addAttachment($markdownFile);
            $mail->send();
        } catch (Exception $e) {
            // Handle email sending errors
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
        // Redirect to the roles.php page
        header("Location: ../../roles.php");
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

// Function to fetch pages access based on role ID
function fetchPagesAccess($role_id, $conn)
{
    $sql = "SELECT page FROM role_pages WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pages = [];
    while ($row = $result->fetch_assoc()) {
        $pages[] = $row['page'];
    }
    return $pages;
}
?>
