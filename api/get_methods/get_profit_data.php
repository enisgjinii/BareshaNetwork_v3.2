<?php
include '../../conn-d.php'; // Adjust the path as needed

header('Content-Type: application/json');

// Retrieve and sanitize POST parameters
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';
$company = isset($_POST['company']) ? $_POST['company'] : '';
$registrant = isset($_POST['registrant']) ? $_POST['registrant'] : '';

// Build WHERE clause based on filters
$whereClauses = [];
$params = [];
$types = '';

if (!empty($startDate)) {
    $whereClauses[] = "invoice_date >= ?";
    $params[] = $startDate;
    $types .= 's';
}
if (!empty($endDate)) {
    $whereClauses[] = "invoice_date <= ?";
    $params[] = $endDate;
    $types .= 's';
}
if (!empty($category)) {
    $whereClauses[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}
if (!empty($company)) {
    $whereClauses[] = "company_name = ?";
    $params[] = $company;
    $types .= 's';
}
if (!empty($registrant)) {
    $whereClauses[] = "registrant = ?";
    $params[] = $registrant;
    $types .= 's';
}

$where = '';
if (count($whereClauses) > 0) {
    $where = "WHERE " . implode(" AND ", $whereClauses);
}

// Fetch Monthly Totals for Line Chart
$monthlySql = "
    SELECT DATE_FORMAT(invoice_date, '%b') AS month, SUM(vlera_faktura) AS total
    FROM invoices_kont
    $where
    GROUP BY DATE_FORMAT(invoice_date, '%m')
    ORDER BY DATE_FORMAT(invoice_date, '%m')
";
$monthlyStmt = $conn->prepare($monthlySql);
if ($where !== '') {
    $monthlyStmt->bind_param($types, ...$params);
}
$monthlyStmt->execute();
$monthlyResult = $monthlyStmt->get_result();
$monthlyTotals = [];
while ($row = $monthlyResult->fetch_assoc()) {
    $monthlyTotals[] = [
        'month' => $row['month'],
        'total' => $row['total']
    ];
}
$monthlyStmt->close();

// Fetch Category Totals for Doughnut Chart
$categorySql = "
    SELECT category, SUM(vlera_faktura) AS total_shuma
    FROM invoices_kont
    $where
    GROUP BY category
";
$categoryStmt = $conn->prepare($categorySql);
if ($where !== '') {
    $categoryStmt->bind_param($types, ...$params);
}
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
$categoryTotals = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categoryTotals[] = [
        'category' => $row['category'],
        'total_shuma' => $row['total_shuma']
    ];
}
$categoryStmt->close();

// Fetch Company Totals for Bar Chart
$companySql = "
    SELECT company_name, SUM(vlera_faktura) AS total_shuma
    FROM invoices_kont
    $where
    GROUP BY company_name
";
$companyStmt = $conn->prepare($companySql);
if ($where !== '') {
    $companyStmt->bind_param($types, ...$params);
}
$companyStmt->execute();
$companyResult = $companyStmt->get_result();
$companyTotals = [];
while ($row = $companyResult->fetch_assoc()) {
    $companyTotals[] = [
        'company_name' => $row['company_name'],
        'total_shuma' => $row['total_shuma']
    ];
}
$companyStmt->close();

// Return JSON response
echo json_encode([
    'success' => true,
    'data' => [
        'monthly_totals' => $monthlyTotals,
        'category_totals' => $categoryTotals,
        'company_totals' => $companyTotals
    ]
]);

$conn->close();
