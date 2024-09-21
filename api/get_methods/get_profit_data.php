<?php
// api/get_methods/get_profit_data.php

include '../../conn-d.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Implement authentication (if required)
// Example: Check if the user is authenticated
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
//     exit;
// }

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

// Build the SQL query based on filters
$query = 'SELECT 
            DATE(invoice_date) AS date, 
            category, 
            company_name,
            registrant,
            ROUND(SUM(CAST(vlera_faktura AS DECIMAL(10,2))), 2) AS total_shuma 
          FROM invoices_kont';

// Append filters to the query
if (!empty($filters)) {
    $query .= ' WHERE ' . implode(' AND ', $filters);
}

$query .= ' GROUP BY DATE(invoice_date), category, company_name, registrant ORDER BY DATE(invoice_date) ASC';

// Prepare and execute the statement
$stmt = $conn->prepare($query);

if ($stmt === false) {
    log_error('SQL prepare failed: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Ndodhi një gabim i brendshëm.']);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    log_error('SQL execute failed: ' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Ndodhi një gabim i brendshëm.']);
    exit;
}

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output the data with JSON_UNESCAPED_UNICODE to preserve special characters
echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);

// Close connections
$stmt->close();
$conn->close();
