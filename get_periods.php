<?php
// get_periods.php

// Assuming you have a database connection already established ($conn)
include('conn-d.php');
if (isset($_GET['client'])) {
    $selectedClient = $_GET['client'];

    // Use a prepared statement to prevent SQL injection
    $getPeriodsQuery = "SELECT DISTINCT AccountingPeriod FROM platformat_2 WHERE Emri = ?";
    $stmt = $conn->prepare($getPeriodsQuery);
    $stmt->bind_param('s', $selectedClient);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['AccountingPeriod'] . "'>" . $row['AccountingPeriod'] . "</option>";
        }
    } else {
        echo "<option value=''>No periods found</option>";
    }

    $stmt->close();
}
?>
