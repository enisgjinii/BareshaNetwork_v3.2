<?php
// access_denied.php

// Start output buffering and session (if needed)
session_start();
ob_start();

// Function to retrieve the client's real IP address
function getClientIP()
{
    $ipKeys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            // Handle multiple IP addresses (e.g., proxies)
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
    return 'UNKNOWN';
}

// Initialize variables
$deniedEmail = '';
$logStatus = '';
$errorOccurred = false;

// Process GET parameter
if (isset($_GET['email'])) {
    $deniedEmail = trim($_GET['email']);

    // Validate email format
    if (filter_var($deniedEmail, FILTER_VALIDATE_EMAIL)) {
        // Get user's IP address and user agent
        $ipAddress = getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        // Include your database connection code (conn-d.php)
        require_once('conn-d.php');

        try {
            // Prepare the SQL statement
            $sql = "INSERT INTO access_denial_logs (ip_address, email_attempted, user_agent, denied_at) 
                    VALUES (?, ?, ?, NOW())";
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters
                $stmt->bind_param('sss', $ipAddress, $deniedEmail, $userAgent);

                // Execute the statement
                if ($stmt->execute()) {
                    $logStatus = 'Your access denial attempt has been logged.';
                } else {
                    // Log the error internally
                    error_log("Database Execution Error: " . $stmt->error);
                    $logStatus = 'There was an issue logging your access attempt.';
                }

                // Close the statement
                $stmt->close();
            } else {
                // Log the error internally
                error_log("Database Preparation Error: " . $conn->error);
                $logStatus = 'There was an issue preparing the logging mechanism.';
            }

            // Close the database connection
            $conn->close();
        } catch (Exception $e) {
            // Log the exception internally
            error_log("Exception: " . $e->getMessage());
            $logStatus = 'An unexpected error occurred.';
            $errorOccurred = true;
        }
    } else {
        $logStatus = 'Invalid email format provided.';
        $errorOccurred = true;
    }
} else {
    $logStatus = 'No email parameter provided.';
    $errorOccurred = true;
}

// End output buffering and clean the buffer
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <!-- Include Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .access-denied-container {
            max-width: 500px;
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .access-denied-container h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }

        .access-denied-container p {
            font-size: 1rem;
            color: #6c757d;
        }

        .access-denied-container .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="access-denied-container">
        <h1>Access Denied</h1>
        <p>This web application is intended for internal use only.</p>
        <p>If you believe you should have access, please contact the administrator.</p>

        <?php if ($deniedEmail): ?>
            <p><strong>Your email:</strong> <?= htmlspecialchars($deniedEmail, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($logStatus): ?>
            <?php if ($errorOccurred): ?>
                <div class="alert alert-warning" role="alert">
                    <?= htmlspecialchars($logStatus, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php else: ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($logStatus, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <a href="kycu_1.php" class="btn btn-primary mt-3">Go Back</a>
    </div>

    <!-- Include Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optional: Add a fade-in animation using jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".access-denied-container").hide().fadeIn(1000);
        });
    </script>
</body>

</html>