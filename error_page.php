<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <!-- Include your CSS stylesheets or Bootstrap CDN links here -->
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-danger">Error Occurred</h1>
        <p>
            <?php
            // Display the error message if available in the URL parameter
            if (isset($_GET['message'])) {
                echo htmlspecialchars($_GET['message']);
            } else {
                echo "An unexpected error occurred.";
            }
            ?>
        </p>
        <!-- You can add additional content or links here as needed -->
        <a href="index.php" class="btn btn-primary">Go Back</a>
    </div>

    <!-- Include your JavaScript files or Bootstrap CDN links here -->
</body>

</html>
