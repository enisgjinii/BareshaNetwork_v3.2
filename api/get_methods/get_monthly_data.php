<?php
// Include your database connection
include '../../conn-d.php'; // Ensure the path is correct

// Set header to JSON
header('Content-Type: application/json');

// Retrieve POST parameters
$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
$month = isset($_POST['month']) ? intval($_POST['month']) : date('m');

// Validate inputs
if ($year < 2000 || $year > 2100 || $month < 1 || $month > 12) {
    echo json_encode(['error' => 'Parametrat e pavlefshëm.']);
    exit;
}

// Determine the number of days in the selected month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Initialize categories with day numbers (01, 02, ..., 31)
$categories = [];
for ($i = 1; $i <= $daysInMonth; $i++) {
    $categories[] = str_pad($i, 2, '0', STR_PAD_LEFT);
}

// Initialize daily revenue and profit arrays with 0
$dailyRevenue = array_fill(0, $daysInMonth, 0);
$dailyProfit = array_fill(0, $daysInMonth, 0);

// Prepare SQL query to fetch daily data based on year and month
$sql = "
    SELECT 
        DAY(created_date) AS day, 
        SUM(total_amount) AS daily_revenue, 
        SUM(total_amount - total_amount_after_percentage) AS daily_profit
    FROM invoices
    WHERE YEAR(created_date) = ? AND MONTH(created_date) = ?
    GROUP BY DAY(created_date)
    ORDER BY DAY(created_date)
";

// Initialize the prepared statement
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    $yearTotalUsd = 0;
    $yearProfitUsd = 0;

    while ($row = $result->fetch_assoc()) {
        $day = intval($row['day']); // 1-31
        if ($day >= 1 && $day <= $daysInMonth) {
            $index = $day - 1; // 0-based index
            $dailyRevenue[$index] = $row['daily_revenue'] ? floatval($row['daily_revenue']) : 0;
            $dailyProfit[$index] = $row['daily_profit'] ? floatval($row['daily_profit']) : 0;

            $yearTotalUsd += $row['daily_revenue'] ?? 0;
            $yearProfitUsd += $row['daily_profit'] ?? 0;
        }
    }

    $stmt->close();

    // Prepare the response
    $response = [
        'totalUSD' => $dailyRevenue,
        'profitUSD' => $dailyProfit,
        'yearTotalUSD' => $yearTotalUsd,
        'yearProfitUsd' => $yearProfitUsd,
        'year' => $year,
        'month' => str_pad($month, 2, '0', STR_PAD_LEFT),
        'categories' => $categories
    ];

    echo json_encode($response);
} else {
    // Handle SQL preparation error
    echo json_encode(['error' => 'Dështoi kërkesa në bazën e të dhënave.']);
}
