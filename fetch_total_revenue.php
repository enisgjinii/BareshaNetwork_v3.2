<?php
include('conn-d.php');

// Grab the value of client and reporting period from POST parameters
$selectedClient = $_POST['selectedClient'] ?? null;
$reportingPeriod = $_POST['reportingPeriod'] ?? null;

// Query to get total revenue
$sql = "SELECT SUM(RevenueUSD) as total FROM platformat_2 WHERE Emri = '$selectedClient' AND ReportingPeriod = '$reportingPeriod'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalRevenue = $row['total'];
    echo $totalRevenue;
} else {
    echo '0';
}
