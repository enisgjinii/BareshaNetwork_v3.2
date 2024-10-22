<?php
// edit_activity.php

session_start();


// **Include PHPMailer**
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer using Composer
require 'vendor/autoload.php';

// **Database Configuration**

include 'conn-d.php';

$success_msg = "";
$error_msg = "";

// Get activity ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $activity_id = intval($_GET['id']);

    // Fetch activity details
    $stmt = $conn->prepare("SELECT * FROM employee_activity WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $activity = $result->fetch_assoc();
        } else {
            $error_msg = "Activity not found.";
        }
        $stmt->close();
    } else {
        $error_msg = "Failed to prepare statement.";
    }
} else {
    $error_msg = "Invalid activity ID.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_activity'])) {
    // Retrieve and sanitize form inputs
    $employee_id = intval($_POST['employee_id']);
    $status = $_POST['status'];
    $reason = trim($_POST['reason']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate inputs
    $allowed_statuses = ['leave', 'rest', 'sick_leave', 'vacation', 'personal_leave'];
    if (empty($employee_id) || empty($status) || empty($start_date) || empty($end_date)) {
        $error_msg = "All fields except reason are required.";
    } elseif (!in_array($status, $allowed_statuses)) {
        $error_msg = "Invalid status selected.";
    } elseif ($start_date > $end_date) {
        $error_msg = "Start date cannot be after end date.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("UPDATE employee_activity SET employee_id = ?, status = ?, reason = ?, start_date = ?, end_date = ? WHERE id = ?");
        if ($stmt === false) {
            $error_msg = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        } else {
            $stmt->bind_param("issssi", $employee_id, $status, $reason, $start_date, $end_date, $activity_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Fetch employee email and name
                $emp_stmt = $conn->prepare("SELECT firstName, last_name, email FROM googleauth WHERE id = ?");
                if ($emp_stmt) {
                    $emp_stmt->bind_param("i", $employee_id);
                    $emp_stmt->execute();
                    $emp_result = $emp_stmt->get_result();
                    if ($emp_result->num_rows > 0) {
                        $employee = $emp_result->fetch_assoc();
                        // Send email
                        $mail_sent = sendEmailNotification($employee['email'], $employee['firstName'], $employee['last_name'], $status, $start_date, $end_date, $reason);
                        if ($mail_sent) {
                            $success_msg = "Activity updated and email notification sent successfully.";
                        } else {
                            $success_msg = "Activity updated successfully, but failed to send email notification.";
                        }
                        // Refresh activity details
                        $activity['employee_id'] = $employee_id;
                        $activity['status'] = $status;
                        $activity['reason'] = $reason;
                        $activity['start_date'] = $start_date;
                        $activity['end_date'] = $end_date;
                    } else {
                        $error_msg = "Employee not found.";
                    }
                    $emp_stmt->close();
                } else {
                    $error_msg = "Failed to prepare employee query.";
                }
            } else {
                $error_msg = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

// Function to send email notification using PHPMailer
function sendEmailNotification($to_email, $firstName, $lastName, $status, $start_date, $end_date, $reason)
{
    $mail = new PHPMailer(true);
    try {
        // Server settings
        // $mail->SMTPDebug = 2;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.example.com';                     // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'your_email@example.com';               // SMTP username
        $mail->Password   = 'your_email_password';                  // SMTP password
        $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('no-reply@example.com', 'HR Department');
        $mail->addAddress($to_email, $firstName . ' ' . $lastName); // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Activity Update Notification';

        // Prepare status in human-readable form
        $status_readable = ucwords(str_replace('_', ' ', $status));

        // Email Body
        $body = "<p>Dear {$firstName} {$lastName},</p>
                 <p>Your activity has been updated as follows:</p>
                 <ul>
                     <li><strong>Activity Type:</strong> {$status_readable}</li>
                     <li><strong>Start Date:</strong> {$start_date}</li>
                     <li><strong>End Date:</strong> {$end_date}</li>";
        if (!empty($reason)) {
            $body .= "<li><strong>Reason:</strong> {$reason}</li>";
        }
        $body .= "</ul>
                 <p>Please contact the HR department if you have any questions.</p>
                 <p>Best regards,<br>HR Department</p>";

        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error in production
        // error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Fetch employees for the dropdown
$employees = [];
$emp_sql = "SELECT id, email, firstName, last_name FROM googleauth ORDER BY firstName, last_name";
$emp_result = $conn->query($emp_sql);
if ($emp_result && $emp_result->num_rows > 0) {
    while ($row = $emp_result->fetch_assoc()) {
        $employees[] = $row;
    }
} else {
    $error_msg = "No employees found.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Activity</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Activity</h2>
            <a href="aktiviteti.php" class="btn btn-secondary">Back to Activities</a>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($activity)): ?>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="edit_activity.php?id=<?php echo $activity_id; ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Select Employee<span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">-- Select Employee --</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?php echo htmlspecialchars($employee['id']); ?>" <?php if ($employee['id'] == $activity['employee_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($employee['firstName'] . ' ' . $employee['last_name'] . ' (' . $employee['email'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Select Activity Type<span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">-- Select Activity Type --</option>
                                    <option value="leave" <?php if ($activity['status'] == 'leave') echo 'selected'; ?>>Leave</option>
                                    <option value="rest" <?php if ($activity['status'] == 'rest') echo 'selected'; ?>>Rest</option>
                                    <option value="sick_leave" <?php if ($activity['status'] == 'sick_leave') echo 'selected'; ?>>Sick Leave</option>
                                    <option value="vacation" <?php if ($activity['status'] == 'vacation') echo 'selected'; ?>>Vacation</option>
                                    <option value="personal_leave" <?php if ($activity['status'] == 'personal_leave') echo 'selected'; ?>>Personal Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason (Optional)</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Enter reason for the activity..."><?php echo htmlspecialchars($activity['reason']); ?></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date<span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($activity['start_date']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date<span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($activity['end_date']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" name="update_activity" class="btn btn-primary w-100">Update Activity</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>