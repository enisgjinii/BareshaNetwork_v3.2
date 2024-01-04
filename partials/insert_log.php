<?php
// Include your database connection code here (e.g., connect to $conn)
include 'conn-d.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the log data from the AJAX request
    $userInformations = $_POST["user_informations"];
    $logDescription = $_POST["log_description"];
    $dateInformation = $_POST["date_information"];

    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param("sss", $userInformations, $logDescription, $dateInformation);

        if ($stmt->execute()) {
            // Log data inserted successfully
            echo "Log data inserted successfully!";
        } else {
            // Handle the case where the INSERT operation fails
            echo "Error inserting log data: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Handle the case where the prepared statement cannot be created
        echo "Error preparing the INSERT statement: " . $conn->error;
    }

    // Close the database connection (if necessary)
    // $conn->close();
} else {
    // Handle cases where the request method is not POST
    echo "Invalid request method.";
}
