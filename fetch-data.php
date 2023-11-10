<?php
include('conn-d.php');

$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

$sql = "SELECT * FROM revenue_data WHERE YEAR(data) = $selectedYear ORDER BY id";
$result = $conn->query($sql);

$chartData = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chartData[] = array($row['data'], (float) $row['estimatedRevenue']);
    }
}

$conn->close();

// Convert the PHP array to JSON format
echo json_encode($chartData);
?>