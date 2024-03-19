<?php
if (isset($_GET['email'])) {
    $deniedEmail = $_GET['email'];

    // Get user's IP address and user agent
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    // Include your database connection code (conn-d.php)
    require_once('conn-d.php');

    // Insert the denial record into the database
    $sql = "INSERT INTO access_denial_logs (ip_address, email_attempted, user_agent) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $ipAddress, $deniedEmail, $userAgent);

    if ($stmt->execute()) {
        // Record inserted successfully
        // echo "Access denied. Your email: " . htmlspecialchars($deniedEmail);
    } else {
        // Error occurred while inserting the record
        echo "Error: Unable to log access denial.";
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // echo "Access denied.";
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <!-- Include Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            text-align: center;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #d9534f;
        }

        p {
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Access Denied</h1>
        <p>This web app is for internal use only.</p>
        <p>If you believe you should have access, please contact the administrator.</p>

        <?php
        if (isset($_GET['email'])) {
            $deniedEmail = $_GET['email'];
            echo '<p>Your email: ' . htmlspecialchars($deniedEmail) . '</p>';
        }
        ?>

        <a href="kycu_1.php" class="btn btn-primary">Go Back</a>
    </div>

    <!-- Optional: Add a fade-in animation using jQuery -->
    <script>
        $(document).ready(function() {
            $(".container").fadeIn(1000);
        });
    </script>
</body>


