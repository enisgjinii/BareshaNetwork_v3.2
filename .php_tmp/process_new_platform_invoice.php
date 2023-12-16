<?php
include 'conn-d.php'; // Include your database connection code

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $client_id = $_POST["id_of_client"];
    $platform = $_POST["platform"];
    $platform_income = $_POST["platform_income"];
    $platform_income_after_percentage = $_POST["platform_income_after_percentage"];
    $date = $_POST["date"];
    $description = $_POST["description"];

    // Create a prepared statement
    $stmt = $conn->prepare("INSERT INTO platform_invoices (client_id, platform, platform_income, platform_income_after_percentage, date, description) VALUES (?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("isddss", $client_id, $platform, $platform_income, $platform_income_after_percentage, $date, $description);

        // Execute the statement
        if ($stmt->execute()) {
            // Success message using JavaScript alert
            echo "<script>
                        alert('Kërkesa u regjistrua me sukses');
                        window.location.href = 'quick_platform_invoice.php'; // Redirect to the form page
                      </script>";
        } else {
            // Error message using JavaScript alert
            echo "<script>
                        alert('Gabim gjate regjistrimit. Error: " . $stmt->error . "');
                        window.location.href = 'quick_platform_invoice.php'; // Redirect to the form page
                      </script>";
        }

        // Close statement
        $stmt->close();
    } else {
        // Error message if the statement was not prepared
        echo "<script>
                    alert('Gabim gjate regjistrimit. Error: Statement not prepared');
                    window.location.href = 'quick_platform_invoice.php'; // Redirect to the form page
                  </script>";
    }

    // Close connection
    $conn->close();
} else {
    // Redirect to the form page if accessed directly without submission
    header("Location: quick_platform_invoice.php");
    exit();
}
?>
