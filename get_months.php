<?php
include 'conn-d.php';

$sql = "SELECT DISTINCT DATE_FORMAT(date_column, '%M - %Y') AS month_year FROM invoices ORDER BY date_column DESC";
$result = mysqli_query($conn, $sql);

$months = [];
while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month_year'];
}

echo json_encode($months);
