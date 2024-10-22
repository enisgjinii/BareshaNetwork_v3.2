<?php
// api/get_methods/get_profit_data.php

include '../../conn-d.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Retrieve filter parameters
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;
$category = isset($_POST['category']) ? $_POST['category'] : null;
$company = isset($_POST['company']) ? $_POST['company'] : null;
$registrant = isset($_POST['registrant']) ? $_POST['registrant'] : null;

// Validate and sanitize input parameters
$filters = [];
$types = '';
$params = [];

// Function to log errors internally
function log_error($message)
{
    error_log($message);
}

// Function to build WHERE clause
function build_where_clause($filters)
{
    if (empty($filters)) {
        return '';
    }
    return ' WHERE ' . implode(' AND ', $filters);
}

if ($startDate) {
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $startDate);
    if ($date && $date->format('Y-m-d') === $startDate) {
        // Set time to 00:00:00 for start of the day
        $date->setTime(0, 0, 0);
        $filters[] = 'invoice_date >= ?';
        $params[] = $date->format('Y-m-d H:i:s');
        $types .= 's';
    } else {
        echo json_encode(['success' => false, 'message' => 'Data e Fillimit nuk është e vlefshme.']);
        exit;
    }
}

if ($endDate) {
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $endDate);
    if ($date && $date->format('Y-m-d') === $endDate) {
        // Set time to 23:59:59 for end of the day
        $date->setTime(23, 59, 59);
        $filters[] = 'invoice_date <= ?';
        $params[] = $date->format('Y-m-d H:i:s');
        $types .= 's';
    } else {
        echo json_encode(['success' => false, 'message' => 'Data e Mbarimit nuk është e vlefshme.']);
        exit;
    }
}

if ($category && strtolower($category) !== 'all') {
    // Validate category against allowed enum values
    $allowedCategories = ['Shpenzimet', 'Investimet', 'Obligime', 'Tjetër'];
    if (in_array($category, $allowedCategories)) {
        $filters[] = 'category = ?';
        $params[] = $category;
        $types .= 's';
    } else {
        echo json_encode(['success' => false, 'message' => 'Kategoria e dhënë nuk është e vlefshme.']);
        exit;
    }
}

if ($company && strtolower($company) !== 'all') {
    // Sanitize and validate company name
    $company = trim($company);
    if (mb_strlen($company, 'UTF-8') <= 255) {
        $filters[] = 'company_name = ?';
        $params[] = $company;
        $types .= 's';
    } else {
        echo json_encode(['success' => false, 'message' => 'Emri i kompanisë është shumë i gjatë.']);
        exit;
    }
}

if ($registrant && strtolower($registrant) !== 'all') {
    // Sanitize and validate registrant
    $registrant = trim($registrant);
    if (mb_strlen($registrant, 'UTF-8') <= 255) {
        $filters[] = 'registrant = ?';
        $params[] = $registrant;
        $types .= 's';
    } else {
        echo json_encode(['success' => false, 'message' => 'Regjistruesi është shumë i gjatë.']);
        exit;
    }
}

// Queries for totals by category and company

// Query for category totals
$queryCategory = 'SELECT 
                    category, 
                    vlera_faktura AS total_shuma 
                  FROM invoices_kont' . build_where_clause($filters) .
    ' GROUP BY category ORDER BY category ASC';

// Query for company totals
$queryCompany = 'SELECT 
                    company_name, 
                    vlera_faktura AS total_shuma 
                  FROM invoices_kont' . build_where_clause($filters) .
    ' GROUP BY company_name ORDER BY company_name ASC';

// Function to execute query and fetch results
function execute_query($conn, $query, $types, $params)
{
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        log_error('SQL prepare failed: ' . $conn->error);
        return false;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        log_error('SQL execute failed: ' . $stmt->error);
        return false;
    }

    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

// Execute the queries
$categoryTotals = execute_query($conn, $queryCategory, $types, $params);
if ($categoryTotals === false) {
    echo json_encode(['success' => false, 'message' => 'Ndodhi një gabim i brendshëm gjatë marrjes së të dhënave sipas Kategorisë.']);
    exit;
}

$companyTotals = execute_query($conn, $queryCompany, $types, $params);
if ($companyTotals === false) {
    echo json_encode(['success' => false, 'message' => 'Ndodhi një gabim i brendshëm gjatë marrjes së të dhënave sipas Kompanisë.']);
    exit;
}

// Prepare the response
$response = [
    'success' => true,
    'data' => [
        'category_totals' => $categoryTotals,
        'company_totals' => $companyTotals
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Close connections
$conn->close();
