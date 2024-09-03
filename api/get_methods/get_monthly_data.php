<?php
// Include your database connection
include '../../conn-d.php';

$year = $_POST['year'];

// Fetch data from the database for the selected year
$sql = "SELECT 
          MONTH(created_date) as month,
          SUM(total_amount) as total_usd,
          SUM(total_amount - total_amount_after_percentage) as profit_usd
        FROM invoices
        WHERE YEAR(created_date) = ?
        GROUP BY MONTH(created_date)
        ORDER BY MONTH(created_date)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$totalUsd = array_fill(0, 12, null);
$profitUsd = array_fill(0, 12, null);

$yearTotalUsd = 0;
$yearProfitUsd = 0;

while ($row = $result->fetch_assoc()) {
    $month = $row['month'] - 1; // Adjust for 0-based array index
    $totalUsd[$month] = $row['total_usd'] ? floatval($row['total_usd']) : null;
    $profitUsd[$month] = $row['profit_usd'] ? floatval($row['profit_usd']) : null;

    $yearTotalUsd += $row['total_usd'] ?? 0;
    $yearProfitUsd += $row['profit_usd'] ?? 0;
}

$response = [
    'totalUSD' => $totalUsd,
    'profitUSD' => $profitUsd,
    'yearTotalUSD' => $yearTotalUsd,
    'yearProfitUSD' => $yearProfitUsd,
    'year' => $year
];

echo json_encode($response);
