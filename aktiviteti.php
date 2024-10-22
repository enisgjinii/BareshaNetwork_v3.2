<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'partials/header.php';
// Initialize messages
$mesazhi_sukses = $mesazhi_error = "";
// SMTP Configuration Constants
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'egjini@bareshamusic.com'); // Replace with your actual email
define('SMTP_PASSWORD', 'snsrboaehroabrbt'); // Replace with your actual password or App Password
define('SMTP_SECURE', 'tls');
define('SMTP_PORT', 587);
define('SMTP_FROM_EMAIL', 'egjini@bareshamusic.com'); // Replace with your actual email
define('SMTP_FROM_NAME', 'Departamenti HR');
/**
 * Helper function to configure and return a PHPMailer instance
 */
function getMailer()
{
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = SMTP_HOST; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME; // SMTP username
        $mail->Password = SMTP_PASSWORD; // SMTP password
        $mail->SMTPSecure = SMTP_SECURE; // Encryption
        $mail->Port = SMTP_PORT; // TCP port to connect to
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME); // Sender info
        $mail->isHTML(true); // Set email format to HTML
        return $mail;
    } catch (Exception $e) {
        // Handle exception or log error
        return false;
    }
}
// Check if user is authenticated via email cookie
if (isset($_COOKIE['email'])) {
    // Retrieve email from cookie
    $user_email = $_COOKIE['email'];
} else {
    // Redirect to login page if no cookie is present
    header("Location: login.php");
    exit();
}
// Validate email
if (empty($user_email)) {
    header("Location: login.php");
    exit();
}
// Retrieve user details
$stmt = $conn->prepare("SELECT id, firstName, last_name FROM googleauth WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    // Destroy session and redirect to login if user not found
    session_destroy();
    header("Location: login.php");
    exit();
}
$user = $result->fetch_assoc();
$user_employee_id = $user['id'];
$is_admin = ($user_email === 'egjini17@gmail.com') ? true : false;
// Handle POST Requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assign Activity
    if (isset($_POST['assign_activity'])) {
        // For admins, allow assigning activities to any employee
        // For other users, assign only to themselves
        if ($is_admin) {
            $employee_id = intval($_POST['employee_id']);
        } else {
            $employee_id = $user_employee_id;
        }
        $status = $_POST['status'];
        $reason = trim($_POST['reason']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $allowed_statuses = ['leave', 'rest', 'sick_leave', 'vacation', 'personal_leave'];
        // Validate inputs
        if (empty($status) || empty($start_date) || empty($end_date)) {
            $mesazhi_error = "Të gjitha fushat përveç arsyeve janë të nevojshme.";
        } elseif (!in_array($status, $allowed_statuses)) {
            $mesazhi_error = "Statusi i zgjedhur është i pavlefshëm.";
        } elseif ($start_date > $end_date) {
            $mesazhi_error = "Data e fillimit nuk mund të jetë pas datës së mbarimit.";
        } else {
            // Check for overlapping activities
            $overlap_stmt = $conn->prepare("SELECT * FROM employee_activity WHERE employee_id=? AND (start_date<=? AND end_date>=?)");
            $overlap_stmt->bind_param("iss", $employee_id, $end_date, $start_date);
            $overlap_stmt->execute();
            $overlap_result = $overlap_stmt->get_result();
            if ($overlap_result->num_rows > 0) {
                $mesazhi_error = "Punonjësi ka aktivitete që mbivijojnë gjatë kësaj periudhe.";
            } else {
                // Insert new activity with 'pending' approval status
                $stmt = $conn->prepare("INSERT INTO employee_activity (employee_id, status, reason, start_date, end_date, approval_status) VALUES (?,?,?,?,?, 'pending')");
                if ($stmt) {
                    $stmt->bind_param("issss", $employee_id, $status, $reason, $start_date, $end_date);
                    if ($stmt->execute()) {
                        // Retrieve employee details for email
                        $emp_stmt = $conn->prepare("SELECT firstName, last_name, email FROM googleauth WHERE id=?");
                        if ($emp_stmt) {
                            $emp_stmt->bind_param("i", $employee_id);
                            $emp_stmt->execute();
                            $emp_result = $emp_stmt->get_result();
                            if ($emp_result->num_rows > 0) {
                                $employee = $emp_result->fetch_assoc();
                                // Send notification email
                                $mail_sent = sendEmailNotification($employee['email'], $employee['firstName'], $employee['last_name'], $status, $start_date, $end_date, $reason, 'assigned');
                                $mesazhi_sukses = $mail_sent ? "Aktiviteti caktohet dhe njoftimi me email dërgohet me sukses." : "Aktiviteti caktohet me sukses, por dërgimi i njoftimit me email dështoi.";
                            } else {
                                $mesazhi_error = "Punonjësi nuk u gjet.";
                            }
                            $emp_stmt->close();
                        } else {
                            $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
                        }
                    } else {
                        $mesazhi_error = "Gabim: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
            $overlap_stmt->close();
        }
    }
    // Edit Activity
    elseif (isset($_POST['edit_activity'])) {
        $activity_id = intval($_POST['activity_id']);
        // For admins, allow editing any activity
        // For other users, allow editing only their own activities
        if ($is_admin) {
            $employee_id = intval($_POST['employee_id']);
        } else {
            $employee_id = $user_employee_id;
        }
        $status = $_POST['status'];
        $reason = trim($_POST['reason']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $allowed_statuses = ['leave', 'rest', 'sick_leave', 'vacation', 'personal_leave'];
        // Validate inputs
        if (empty($status) || empty($start_date) || empty($end_date)) {
            $mesazhi_error = "Të gjitha fushat përveç arsyeve janë të nevojshme.";
        } elseif (!in_array($status, $allowed_statuses)) {
            $mesazhi_error = "Statusi i zgjedhur është i pavlefshëm.";
        } elseif ($start_date > $end_date) {
            $mesazhi_error = "Data e fillimit nuk mund të jetë pas datës së mbarimit.";
        } else {
            // Check for overlapping activities excluding the current one
            $overlap_stmt = $conn->prepare("SELECT * FROM employee_activity WHERE employee_id=? AND id!=? AND (start_date<=? AND end_date>=?)");
            $overlap_stmt->bind_param("iiss", $employee_id, $activity_id, $end_date, $start_date);
            $overlap_stmt->execute();
            $overlap_result = $overlap_stmt->get_result();
            if ($overlap_result->num_rows > 0) {
                $mesazhi_error = "Punonjësi ka aktivitete që mbivijojnë gjatë kësaj periudhe.";
            } else {
                // Update activity and reset approval status to 'pending'
                $stmt = $conn->prepare("UPDATE employee_activity SET employee_id=?, status=?, reason=?, start_date=?, end_date=?, approval_status='pending' WHERE id=?");
                if ($stmt) {
                    $stmt->bind_param("issssi", $employee_id, $status, $reason, $start_date, $end_date, $activity_id);
                    if ($stmt->execute()) {
                        // Retrieve employee details for email
                        $emp_stmt = $conn->prepare("SELECT firstName, last_name, email FROM googleauth WHERE id=?");
                        if ($emp_stmt) {
                            $emp_stmt->bind_param("i", $employee_id);
                            $emp_stmt->execute();
                            $emp_result = $emp_stmt->get_result();
                            if ($emp_result->num_rows > 0) {
                                $employee = $emp_result->fetch_assoc();
                                // Send update notification email
                                $mail_sent = sendEmailNotification($employee['email'], $employee['firstName'], $employee['last_name'], $status, $start_date, $end_date, $reason, 'updated');
                                $mesazhi_sukses = $mail_sent ? "Aktiviteti përditësua dhe njoftimi me email dërgohet me sukses." : "Aktiviteti përditësua me sukses, por dërgimi i njoftimit me email dështoi.";
                            } else {
                                $mesazhi_error = "Punonjësi nuk u gjet.";
                            }
                            $emp_stmt->close();
                        } else {
                            $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
                        }
                    } else {
                        $mesazhi_error = "Gabim: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
            $overlap_stmt->close();
        }
    }
}
// Handle GET Requests for Approval, Rejection, and Deletion
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Only admins can approve, reject, or delete
    if ($is_admin) {
        // Handle Approval
        if (isset($_GET['approve_id'])) {
            $approve_id = intval($_GET['approve_id']);
            // Update approval_status to 'approved'
            $stmt = $conn->prepare("UPDATE employee_activity SET approval_status='approved' WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("i", $approve_id);
                if ($stmt->execute()) {
                    // Retrieve activity and employee details for email
                    $emp_stmt = $conn->prepare("SELECT ga.firstName, ga.last_name, ga.email, ea.status, ea.start_date, ea.end_date, ea.reason FROM employee_activity ea JOIN googleauth ga ON ea.employee_id=ga.id WHERE ea.id=?");
                    if ($emp_stmt) {
                        $emp_stmt->bind_param("i", $approve_id);
                        $emp_stmt->execute();
                        $emp_result = $emp_stmt->get_result();
                        if ($emp_result->num_rows === 1) {
                            $activity = $emp_result->fetch_assoc();
                            // Send approval email
                            $mail_sent = sendApprovalNotification($activity['email'], $activity['firstName'], $activity['last_name'], $activity['status'], $activity['start_date'], $activity['end_date'], $activity['reason']);
                            $mesazhi_sukses = $mail_sent ? "Aktiviteti aprovua dhe njoftimi me email dërgohet me sukses." : "Aktiviteti aprovua me sukses, por dërgimi i njoftimit me email dështoi.";
                        } else {
                            $mesazhi_error = "Aktiviteti nuk u gjet.";
                        }
                        $emp_stmt->close();
                    } else {
                        $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
                    }
                } else {
                    $mesazhi_error = "Gabim në aprovimin e aktivitetit: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $mesazhi_error = "Dështoi përgatitja e pyetjes për aprovimin.";
            }
        }
        // Handle Rejection
        if (isset($_GET['reject_id'])) {
            $reject_id = intval($_GET['reject_id']);
            // Update approval_status to 'rejected'
            $stmt = $conn->prepare("UPDATE employee_activity SET approval_status='rejected' WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("i", $reject_id);
                if ($stmt->execute()) {
                    // Retrieve activity and employee details for email
                    $emp_stmt = $conn->prepare("SELECT ga.firstName, ga.last_name, ga.email, ea.status, ea.start_date, ea.end_date, ea.reason FROM employee_activity ea JOIN googleauth ga ON ea.employee_id=ga.id WHERE ea.id=?");
                    if ($emp_stmt) {
                        $emp_stmt->bind_param("i", $reject_id);
                        $emp_stmt->execute();
                        $emp_result = $emp_stmt->get_result();
                        if ($emp_result->num_rows === 1) {
                            $activity = $emp_result->fetch_assoc();
                            // Send rejection email
                            $mail_sent = sendRejectionNotification($activity['email'], $activity['firstName'], $activity['last_name'], $activity['status'], $activity['start_date'], $activity['end_date'], $activity['reason']);
                            $mesazhi_sukses = $mail_sent ? "Aktiviteti refuzua dhe njoftimi me email dërgohet me sukses." : "Aktiviteti refuzua me sukses, por dërgimi i njoftimit me email dështoi.";
                        } else {
                            $mesazhi_error = "Aktiviteti nuk u gjet.";
                        }
                        $emp_stmt->close();
                    } else {
                        $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
                    }
                } else {
                    $mesazhi_error = "Gabim në refuzimin e aktivitetit: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $mesazhi_error = "Dështoi përgatitja e pyetjes për refuzimin.";
            }
        }
        // Handle Deletion
        if (isset($_GET['delete_id'])) {
            $delete_id = intval($_GET['delete_id']);
            // Retrieve activity and employee details before deletion
            $emp_stmt = $conn->prepare("SELECT ga.firstName, ga.last_name, ga.email, ea.status, ea.start_date, ea.end_date, ea.reason FROM employee_activity ea JOIN googleauth ga ON ea.employee_id=ga.id WHERE ea.id=?");
            if ($emp_stmt) {
                $emp_stmt->bind_param("i", $delete_id);
                $emp_stmt->execute();
                $emp_result = $emp_stmt->get_result();
                if ($emp_result->num_rows === 1) {
                    $activity = $emp_result->fetch_assoc();
                    // Delete the activity
                    $del_stmt = $conn->prepare("DELETE FROM employee_activity WHERE id=?");
                    if ($del_stmt) {
                        $del_stmt->bind_param("i", $delete_id);
                        if ($del_stmt->execute()) {
                            // Send deletion email
                            $mail_sent = sendDeletionNotification($activity['email'], $activity['firstName'], $activity['last_name'], $activity['status'], $activity['start_date'], $activity['end_date'], $activity['reason']);
                            $mesazhi_sukses = $mail_sent ? "Aktiviteti fshiua dhe njoftimi me email dërgohet me sukses." : "Aktiviteti fshiua me sukses, por dërgimi i njoftimit me email dështoi.";
                        } else {
                            $mesazhi_error = "Gabim në fshirjen e aktivitetit: " . $del_stmt->error;
                        }
                        $del_stmt->close();
                    } else {
                        $mesazhi_error = "Dështoi përgatitja e pyetjes për fshirjen.";
                    }
                } else {
                    $mesazhi_error = "Aktiviteti nuk u gjet.";
                }
                $emp_stmt->close();
            } else {
                $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
            }
        }
    }
}
/**
 * Email Sending Functions Using the Helper Function
 */
/**
 * Sends notification emails for assigning and updating activities
 */
function sendEmailNotification($to_email, $firstName, $lastName, $status, $start_date, $end_date, $reason, $action)
{
    $mail = getMailer();
    if (!$mail) {
        return false;
    }
    try {
        $mail->addAddress($to_email, "$firstName $lastName");
        if ($action === 'assigned') {
            $mail->Subject = 'Njoftim për Caktimin e Aktivitetit';
            $body = "<p>I nderuar {$firstName} {$lastName},</p>
                     <p>Keni qenë caktuar me një aktivitet të ri me detajet e mëposhtme:</p>
                     <ul>
                         <li><strong>Lloji i Aktivitetit:</strong> " . ucwords(str_replace('_', ' ', $status)) . "</li>
                         <li><strong>Data e Fillimit:</strong> {$start_date}</li>
                         <li><strong>Data e Mbarimit:</strong> {$end_date}</li>";
            if (!empty($reason)) {
                $body .= "<li><strong>Arsyeja:</strong> {$reason}</li>";
            }
            $body .= "</ul>
                      <p>Aktiviteti është aktualisht <strong>Përshtatur për Aprovim</strong>.</p>
                      <p>Ju lutemi pritni konfirmimin nga departamenti HR.</p>
                      <p>Me respekt,<br>Departamenti HR</p>";
        } elseif ($action === 'updated') {
            $mail->Subject = 'Njoftim për Përditësimin e Aktivitetit';
            $body = "<p>I nderuar {$firstName} {$lastName},</p>
                     <p>Aktiviteti juaj është përditësuar me detajet e mëposhtme:</p>
                     <ul>
                         <li><strong>Lloji i Aktivitetit:</strong> " . ucwords(str_replace('_', ' ', $status)) . "</li>
                         <li><strong>Data e Fillimit:</strong> {$start_date}</li>
                         <li><strong>Data e Mbarimit:</strong> {$end_date}</li>";
            if (!empty($reason)) {
                $body .= "<li><strong>Arsyeja:</strong> {$reason}</li>";
            }
            $body .= "</ul>
                      <p>Aktiviteti i përditësuar është aktualisht <strong>Përshtatur për Aprovim</strong>.</p>
                      <p>Ju lutemi pritni konfirmimin nga departamenti HR.</p>
                      <p>Me respekt,<br>Departamenti HR</p>";
        } else {
            // Default email if action is not recognized
            $mail->Subject = 'Njoftim për Aktivitet';
            $body = "<p>I nderuar {$firstName} {$lastName},</p>
                     <p>Keni një njoftim të ri për aktivitetin tuaj.</p>
                     <p>Me respekt,<br>Departamenti HR</p>";
        }
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';
        $mail->send();

        return true;
    } catch (Exception $e) {
        // Log error if needed
        return false;
    }
}
/**
 * Sends approval notification emails
 */
function sendApprovalNotification($to_email, $firstName, $lastName, $status, $start_date, $end_date, $reason)
{
    $mail = getMailer();
    if (!$mail) {
        return false;
    }
    try {
        $mail->addAddress($to_email, "$firstName $lastName");
        $mail->Subject = 'Njoftim për Aprovimin e Aktivitetit';
        $body = "<p>I nderuar {$firstName} {$lastName},</p>
                 <p>Aktiviteti juaj është <strong>aprovuar</strong> me detajet e mëposhtme:</p>
                 <ul>
                     <li><strong>Lloji i Aktivitetit:</strong> " . ucwords(str_replace('_', ' ', $status)) . "</li>
                     <li><strong>Data e Fillimit:</strong> {$start_date}</li>
                     <li><strong>Data e Mbarimit:</strong> {$end_date}</li>";
        if (!empty($reason)) {
            $body .= "<li><strong>Arsyeja:</strong> {$reason}</li>";
        }
        $body .= "</ul>
                  <p>Mund të kontaktoni departamentin HR nëse keni ndonjë pyetje.</p>
                  <p>Me respekt,<br>Departamenti HR</p>";
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error
        return false;
    }
}
/**
 * Sends rejection notification emails
 */
function sendRejectionNotification($to_email, $firstName, $lastName, $status, $start_date, $end_date, $reason)
{
    $mail = getMailer();
    if (!$mail) {
        return false;
    }
    try {
        $mail->addAddress($to_email, "$firstName $lastName");
        $mail->Subject = 'Njoftim për Refuzimin e Aktivitetit';
        $body = "<p>I nderuar {$firstName} {$lastName},</p>
                 <p>Aktiviteti juaj është <strong>refuzuar</strong> me detajet e mëposhtme:</p>
                 <ul>
                     <li><strong>Lloji i Aktivitetit:</strong> " . ucwords(str_replace('_', ' ', $status)) . "</li>
                     <li><strong>Data e Fillimit:</strong> {$start_date}</li>
                     <li><strong>Data e Mbarimit:</strong> {$end_date}</li>";
        if (!empty($reason)) {
            $body .= "<li><strong>Arsyeja:</strong> {$reason}</li>";
        }
        $body .= "</ul>
                  <p>Nëse keni ndonjë pyetje, ju lutemi kontaktoni departamentin HR.</p>
                  <p>Me respekt,<br>Departamenti HR</p>";
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error
        return false;
    }
}
/**
 * Sends deletion notification emails
 */
function sendDeletionNotification($to_email, $firstName, $lastName, $status, $start_date, $end_date, $reason)
{
    $mail = getMailer();
    if (!$mail) {
        return false;
    }
    try {
        $mail->addAddress($to_email, "$firstName $lastName");
        $mail->Subject = 'Njoftim për Fshirjen e Aktivitetit';
        $body = "<p>I nderuar {$firstName} {$lastName},</p>
                 <p>Aktiviteti juaj është <strong>fshiua</strong> me detajet e mëposhtme:</p>
                 <ul>
                     <li><strong>Lloji i Aktivitetit:</strong> " . ucwords(str_replace('_', ' ', $status)) . "</li>
                     <li><strong>Data e Fillimit:</strong> {$start_date}</li>
                     <li><strong>Data e Mbarimit:</strong> {$end_date}</li>";
        if (!empty($reason)) {
            $body .= "<li><strong>Arsyeja:</strong> {$reason}</li>";
        }
        $body .= "</ul>
                  <p>Nëse keni ndonjë pyetje, ju lutemi kontaktoni departamentin HR.</p>
                  <p>Me respekt,<br>Departamenti HR</p>";
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error
        return false;
    }
}
// Retrieve Employees
$employees = [];
if ($is_admin) {
    $emp_sql = "SELECT id, email, firstName, last_name FROM googleauth ORDER BY firstName, last_name";
    $emp_stmt = $conn->prepare($emp_sql);
    if ($emp_stmt) {
        $emp_stmt->execute();
        $emp_result = $emp_stmt->get_result();
        if ($emp_result && $emp_result->num_rows > 0) {
            while ($row = $emp_result->fetch_assoc()) {
                $employees[] = $row;
            }
        } else {
            $mesazhi_error = "Nuk u gjetën punonjës.";
        }
        $emp_stmt->close();
    } else {
        $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
    }
} else {
    // For non-admin users, retrieve only themselves
    $emp_sql = "SELECT id, email, firstName, last_name FROM googleauth WHERE id = ? ORDER BY firstName, last_name";
    $emp_stmt = $conn->prepare($emp_sql);
    if ($emp_stmt) {
        $emp_stmt->bind_param("i", $user_employee_id);
        $emp_stmt->execute();
        $emp_result = $emp_stmt->get_result();
        if ($emp_result && $emp_result->num_rows > 0) {
            while ($row = $emp_result->fetch_assoc()) {
                $employees[] = $row;
            }
        } else {
            $mesazhi_error = "Punonjësi nuk u gjet.";
        }
        $emp_stmt->close();
    } else {
        $mesazhi_error = "Dështoi përgatitja e pyetjes për punonjësin.";
    }
}
// Retrieve Activities
$activities = [];
if ($is_admin) {
    $act_sql = "SELECT ea.id, ga.firstName, ga.last_name, ga.email, ea.status, ea.reason, ea.start_date, ea.end_date, ea.created_at, ea.approval_status, ea.employee_id 
               FROM employee_activity ea 
               JOIN googleauth ga ON ea.employee_id = ga.id 
               ORDER BY ea.created_at DESC";
    $act_result = $conn->query($act_sql);
} else {
    // For non-admin users, retrieve only their activities
    $act_sql = "SELECT ea.id, ga.firstName, ga.last_name, ga.email, ea.status, ea.reason, ea.start_date, ea.end_date, ea.created_at, ea.approval_status, ea.employee_id 
               FROM employee_activity ea 
               JOIN googleauth ga ON ea.employee_id = ga.id 
               WHERE ea.employee_id = ?
               ORDER BY ea.created_at DESC";
    $stmt = $conn->prepare($act_sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_employee_id);
        $stmt->execute();
        $act_result = $stmt->get_result();
        $stmt->close();
    } else {
        $mesazhi_error = "Dështoi përgatitja e pyetjes për aktivitetet.";
    }
}
if ($act_result && $act_result->num_rows > 0) {
    while ($row = $act_result->fetch_assoc()) {
        $activities[] = $row;
    }
} else {
    if ($is_admin) {
        $mesazhi_error = "Nuk u gjetën aktivitete.";
    } else {
        $mesazhi_error = "Ju nuk keni aktivitete të regjistruara.";
    }
}
$conn->close();
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Menaxhimi</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Aktiviteti</a></li>
                </ol>
            </nav>
            <!-- Toastify Notifications -->
            <script>
                <?php if (!empty($mesazhi_sukses)): ?>
                    Toastify({
                        text: "<?php echo htmlspecialchars($mesazhi_sukses); ?>",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                        close: true
                    }).showToast();
                <?php endif; ?>
                <?php if (!empty($mesazhi_error)): ?>
                    Toastify({
                        text: "<?php echo htmlspecialchars($mesazhi_error); ?>",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                        close: true
                    }).showToast();
                <?php endif; ?>
            </script>
            <!-- Assign New Activity Form -->
            <div class="card mb-4">
                <div class="card-header"><strong>Cakto Aktivitet të Re</strong></div>
                <div class="card-body">
                    <form method="POST" action="aktiviteti.php" id="assignForm">
                        <input type="hidden" name="assign_activity" value="1">
                        <div class="row mb-3">
                            <?php if ($is_admin): ?>
                                <div class="col-md-6">
                                    <label for="employee_id" class="form-label">Zgjidh Punonjësin<span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select input-custom-css px-3 py-2" required>
                                        <option value="">-- Zgjidh Punonjësin --</option>
                                        <?php foreach ($employees as $employee): ?>
                                            <option value="<?php echo htmlspecialchars($employee['id']); ?>">
                                                <?php echo htmlspecialchars($employee['firstName'] . ' ' . $employee['last_name'] . ' (' . $employee['email'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <!-- For non-admin users, set employee_id as hidden -->
                                <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($user_employee_id); ?>">
                            <?php endif; ?>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Zgjidh Llojin e Aktivitetit<span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select input-custom-css px-3 py-2" required>
                                    <option value="">-- Zgjidh Llojin e Aktivitetit --</option>
                                    <option value="leave">Pushim</option>
                                    <option value="rest">Pushim</option>
                                    <option value="sick_leave">Pushim Sëmundjeje</option>
                                    <option value="vacation">Pushim Veror</option>
                                    <option value="personal_leave">Pushim Personal</option>
                                    <!-- Add more options if needed -->
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Arsyeja (Opsionale)</label>
                            <textarea name="reason" id="reason" class="form-control input-custom-css px-3 py-2" rows="3" placeholder="Shkruani arsye për aktivitetin..."></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Data e Fillimit<span class="text-danger">*</span></label>
                                <input type="text" name="start_date" id="start_date" class="form-control flatpickr input-custom-css px-3 py-2" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Data e Mbarimit<span class="text-danger">*</span></label>
                                <input type="text" name="end_date" id="end_date" class="form-control flatpickr input-custom-css px-3 py-2" required>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary input-custom-css px-3 py-2">
                                <i class="fas fa-plus"></i> Cakto Aktivitetin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Existing Activities Table (Admin Only) -->
            <?php if ($is_admin): ?>
                <div class="card mb-4">
                    <div class="card-header"><strong>Aktivitetet Ekzistuese</strong></div>
                    <div class="card-body table-responsive">
                        <?php if (!empty($activities)): ?>
                            <table class="table table-striped table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Punonjësi</th>
                                        <th>Lloji i Aktivitetit</th>
                                        <th>Arsyeja</th>
                                        <th>Data e Fillimit</th>
                                        <th>Data e Mbarimit</th>
                                        <th>Data e Caktimit</th>
                                        <th>Statusi i Aprovimit</th>
                                        <th>Veprimet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activities as $activity): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($activity['id']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['firstName'] . ' ' . $activity['last_name'] . ' (' . $activity['email'] . ')'); ?></td>
                                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $activity['status']))); ?></td>
                                            <td><?php echo htmlspecialchars($activity['reason']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['end_date']); ?></td>
                                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($activity['created_at']))); ?></td>
                                            <td>
                                                <?php
                                                $status_label = '';
                                                switch ($activity['approval_status']) {
                                                    case 'approved':
                                                        $status_label = '<span class="badge bg-success rounded-pill input-custom-css px-3 py-2"><i class="fi fi-rr-check"></i> Aprovuar</span>';
                                                        break;
                                                    case 'rejected':
                                                        $status_label = '<span class="badge bg-danger rounded-pill input-custom-css px-3 py-2"><i class="fi fi-rr-cross"></i> Refuzuar</span>';
                                                        break;
                                                    case 'pending':
                                                        $status_label = '<span class="badge bg-warning text-dark rounded-pill input-custom-css px-3 py-2"><i class="fi fi-rr-hourglass"></i> Përshtatur</span>';
                                                        break;
                                                    case 'in_progress':
                                                        $status_label = '<span class="badge bg-info text-white rounded-pill input-custom-css px-3 py-2"><i class="fi fi-rr-hourglass"></i> Në Progres</span>';
                                                        break;
                                                    default:
                                                        $status_label = '<span class="badge bg-secondary rounded-pill input-custom-css px-3 py-2">Pa Status</span>';
                                                        break;
                                                }
                                                echo $status_label;
                                                ?>
                                            </td>
                                            <td>
                                                <!-- Edit Button -->
                                                <button class="btn btn-primary btn-sm edit-btn input-custom-css px-3 py-2"
                                                    data-id="<?php echo $activity['id']; ?>"
                                                    data-employee="<?php echo htmlspecialchars($activity['employee_id']); ?>"
                                                    data-status="<?php echo htmlspecialchars($activity['status']); ?>"
                                                    data-reason="<?php echo htmlspecialchars($activity['reason']); ?>"
                                                    data-start="<?php echo htmlspecialchars($activity['start_date']); ?>"
                                                    data-end="<?php echo htmlspecialchars($activity['end_date']); ?>">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                                <!-- Approve Button (Only if Pending) -->
                                                <?php if ($activity['approval_status'] === 'pending'): ?>
                                                    <button class="btn btn-success btn-sm approve-btn input-custom-css px-3 py-2" data-id="<?php echo $activity['id']; ?>" title="Aprovo">
                                                        <i class="fi fi-rr-check"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm reject-btn input-custom-css px-3 py-2" data-id="<?php echo $activity['id']; ?>" title="Refuzoj">
                                                        <i class="fi fi-rr-x"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <!-- Delete Button -->
                                                <button class="btn btn-danger btn-sm delete-btn input-custom-css px-3 py-2" data-id="<?php echo $activity['id']; ?>" title="Fshije">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center">Nuk u gjetën aktivitete.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Calendar -->
            <div class="card">
                <div class="card-header"><strong>Kalendar i Aktivitetit</strong></div>
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
        <!-- Edit Activity Modal (Admin Only) -->
        <?php if ($is_admin): ?>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="aktiviteti.php" id="editForm">
                        <input type="hidden" name="edit_activity" value="1">
                        <input type="hidden" name="activity_id" id="modal_activity_id" value="">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Redakto Aktivitetin</h5>
                                <button type="button" class="btn-close input-custom-css px-3 py-2" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="modal_employee_id" class="form-label">Zgjidh Punonjësin<span class="text-danger">*</span></label>
                                    <select name="employee_id" id="modal_employee_id" class="form-select input-custom-css px-3 py-2" required>
                                        <option value="">-- Zgjidh Punonjësin --</option>
                                        <?php foreach ($employees as $employee): ?>
                                            <option value="<?php echo htmlspecialchars($employee['id']); ?>">
                                                <?php echo htmlspecialchars($employee['firstName'] . ' ' . $employee['last_name'] . ' (' . $employee['email'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_status" class="form-label">Zgjidh Llojin e Aktivitetit<span class="text-danger">*</span></label>
                                    <select name="status" id="modal_status" class="form-select input-custom-css px-3 py-2" required>
                                        <option value="">-- Zgjidh Llojin e Aktivitetit --</option>
                                        <option value="leave">Pushim</option>
                                        <option value="rest">Pushim</option>
                                        <option value="sick_leave">Pushim Sëmundjeje</option>
                                        <option value="vacation">Pushim Veror</option>
                                        <option value="personal_leave">Pushim Personal</option>
                                        <!-- Add more options if needed -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="modal_reason" class="form-label">Arsyeja (Opsionale)</label>
                                    <textarea name="reason" id="modal_reason" class="form-control input-custom-css px-3 py-2" rows="3" placeholder="Shkruani arsye për aktivitetin..."></textarea>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="modal_start_date" class="form-label">Data e Fillimit<span class="text-danger">*</span></label>
                                        <input type="text" name="start_date" id="modal_start_date" class="form-control flatpickr input-custom-css px-3 py-2" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal_end_date" class="form-label">Data e Mbarimit<span class="text-danger">*</span></label>
                                        <input type="text" name="end_date" id="modal_end_date" class="form-control flatpickr input-custom-css px-3 py-2" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary input-custom-css px-3 py-2" data-bs-dismiss="modal">Anulo</button>
                                <button type="submit" class="btn btn-primary input-custom-css px-3 py-2">Përditëso Aktivitetin</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <!-- JavaScript Libraries -->
        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- FullCalendar JS -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js'></script>
        <!-- Tooltip.js (Optional) -->
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="https://unpkg.com/tooltip.js@1"></script>
        <!-- Font Awesome JS (Optional for Icons) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" integrity="sha512-yBca0CR4tuqebfwIHn3Y4SKEkC8vH/1zvMXJbA2LWkqz1GFQ9Z5vH4jFnE2VxgG6RbXalSdt6TjLtfO+cC8E2A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Toastify JS -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Flatpickr for all inputs with class 'flatpickr'
                flatpickr(".flatpickr", {
                    dateFormat: "Y-m-d",
                    locale: "sq", // Set language to Albanian
                });
                // Initialize FullCalendar
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'sq', // Set language to Albanian
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: [
                        <?php foreach ($activities as $activity) {
                            // Define colors based on approval status
                            switch ($activity['approval_status']) {
                                case 'approved':
                                    $event_color = '#28a745'; // Green for approved
                                    break;
                                case 'rejected':
                                    $event_color = '#dc3545'; // Red for rejected
                                    break;
                                case 'pending':
                                    $event_color = '#ffc107'; // Orange for pending
                                    break;
                                case 'in_progress':
                                    $event_color = '#17a2b8'; // Blue for in progress
                                    break;
                                default:
                                    $event_color = '#6c757d'; // Gray for no status
                                    break;
                            }
                            $title = addslashes(htmlspecialchars($activity['firstName'] . ' ' . $activity['last_name'] . ' - ' . $activity['email'] . ' - ' . ucwords(str_replace('_', ' ', $activity['status']))));
                            $reason = addslashes(htmlspecialchars($activity['reason']));
                            echo "{title:'{$title}',start:'{$activity['start_date']}',end:'" . date('Y-m-d', strtotime($activity['end_date'] . ' +1 day')) . "',color:'{$event_color}',description:'{$reason}'},";
                        } ?>
                    ],
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.description) {
                            new Tooltip(info.el, {
                                title: info.event.extendedProps.description,
                                placement: 'top',
                                trigger: 'hover',
                                container: 'body'
                            });
                        }
                    },
                    eventClick: function(info) {
                        Swal.fire({
                            title: info.event.title,
                            text: "Arsyeja: " + info.event.extendedProps.description,
                            icon: 'info',
                            confirmButtonText: 'Mbyll'
                        });
                    }
                });
                calendar.render();
                <?php if ($is_admin): ?>
                    // Initialize Edit Modal
                    var editModal = new bootstrap.Modal(document.getElementById('editModal'), {
                        keyboard: false
                    });
                    // Populate and Show Edit Modal on Edit Button Click
                    document.querySelectorAll('.edit-btn').forEach(function(button) {
                        button.addEventListener('click', function() {
                            var activity_id = this.getAttribute('data-id');
                            var employee_id = this.getAttribute('data-employee');
                            var status = this.getAttribute('data-status');
                            var reason = this.getAttribute('data-reason');
                            var start_date = this.getAttribute('data-start');
                            var end_date = this.getAttribute('data-end');
                            document.getElementById('modal_activity_id').value = activity_id;
                            document.getElementById('modal_employee_id').value = employee_id;
                            document.getElementById('modal_status').value = status;
                            document.getElementById('modal_reason').value = reason;
                            document.getElementById('modal_start_date').value = start_date;
                            document.getElementById('modal_end_date').value = end_date;
                            editModal.show();
                        });
                    });
                    // Handle Approve Button Click
                    document.querySelectorAll('.approve-btn').forEach(function(button) {
                        button.addEventListener('click', function() {
                            var activity_id = this.getAttribute('data-id');
                            Swal.fire({
                                title: 'Aprovoni Aktivitetin?',
                                text: "Jeni i sigurt që dëshironi të aprovojë këtë aktivitet?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#dc3545',
                                confirmButtonText: 'Po, aprovo!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "aktiviteti.php?approve_id=" + activity_id;
                                }
                            });
                        });
                    });
                    // Handle Reject Button Click
                    document.querySelectorAll('.reject-btn').forEach(function(button) {
                        button.addEventListener('click', function() {
                            var activity_id = this.getAttribute('data-id');
                            Swal.fire({
                                title: 'Refuzoni Aktivitetin?',
                                text: "Jeni i sigurt që dëshironi të refuzojë këtë aktivitet?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc3545',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Po, refuzoj!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "aktiviteti.php?reject_id=" + activity_id;
                                }
                            });
                        });
                    });
                    // Handle Delete Button Click
                    document.querySelectorAll('.delete-btn').forEach(function(button) {
                        button.addEventListener('click', function() {
                            var activity_id = this.getAttribute('data-id');
                            Swal.fire({
                                title: 'Fshij Aktivitetin?',
                                text: "Ky veprim është i pakthyeshëm!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc3545',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Po, fshij!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "aktiviteti.php?delete_id=" + activity_id;
                                }
                            });
                        });
                    });
                <?php endif; ?>
            });
        </script>
    </div>
</div>
<?php include 'partials/footer.php'; ?>