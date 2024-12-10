<?php
// get_music.php

header('Content-Type: application/json');

// Include database connection
include '../../conn-d.php';

// Function to sanitize input data
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

// Initialize response array
$response = [
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    "recordsTotal" => 0,
    "recordsFiltered" => 0,
    "data" => [],
    "error" => ""
];

// Validate and sanitize DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$searchValue = isset($_POST['search']['value']) ? sanitize($_POST['search']['value']) : '';

// Note: We are ignoring $start and $length intentionally to return all records
// $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
// $length = isset($_POST['length']) ? intval($_POST['length']) : 10;

$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDirection = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? strtolower($_POST['order'][0]['dir']) : 'desc';

$columns = [
    0 => 'n.id',
    1 => 'n.kengetari',
    2 => 'n.emri',
    3 => 'n.teksti',
    4 => 'n.muzika',
    5 => 'n.orkestra',
    6 => 'n.co',
    7 => 'n.facebook',
    8 => 'n.instagram',
    9 => 'n.veper',
    10 => 'k.emri',
    11 => 'n.platformat',
    12 => 'n.linku',
    13 => 'n.linkuplat',
    14 => 'n.data',
    15 => 'n.gjuha',
    16 => 'n.infosh',
    17 => 'u.name'
];

$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'n.id';

// Additional filters
$startDate = isset($_POST['startDate']) ? sanitize($_POST['startDate']) : null;
$endDate = isset($_POST['endDate']) ? sanitize($_POST['endDate']) : null;
$artistFilter = isset($_POST['artist']) ? sanitize($_POST['artist']) : null;
$clientFilter = isset($_POST['client']) ? sanitize($_POST['client']) : null;

$baseQuery = "FROM ngarkimi n
    LEFT JOIN klientet k ON n.klienti = k.id
    LEFT JOIN users u ON n.nga = u.id";

$conditions = [];
$params = [];
$paramTypes = "";

// Add date filters if provided
if ($startDate && $endDate) {
    $conditions[] = "n.data BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $paramTypes .= "ss";
} elseif ($startDate) {
    $conditions[] = "n.data >= ?";
    $params[] = $startDate;
    $paramTypes .= "s";
} elseif ($endDate) {
    $conditions[] = "n.data <= ?";
    $params[] = $endDate;
    $paramTypes .= "s";
}

// Add artist filter if provided
if ($artistFilter) {
    $conditions[] = "n.kengetari LIKE ?";
    $params[] = "%" . $artistFilter . "%";
    $paramTypes .= "s";
}

// Add client filter if provided
if ($clientFilter) {
    $conditions[] = "k.emri LIKE ?";
    $params[] = "%" . $clientFilter . "%";
    $paramTypes .= "s";
}

// Add global search filter if provided
if ($searchValue) {
    $searchConditions = [];
    foreach ($columns as $col) {
        // Avoid searching on 'n.linku' and 'n.linkuplat' as they are formatted as HTML
        if (!in_array($col, ['n.linku', 'n.linkuplat'])) {
            $searchConditions[] = "$col LIKE ?";
            $params[] = "%" . $searchValue . "%";
            $paramTypes .= "s";
        }
    }
    if ($searchConditions) {
        $conditions[] = "(" . implode(" OR ", $searchConditions) . ")";
    }
}

// Combine conditions
$whereClause = "";
if ($conditions) {
    $whereClause = " WHERE " . implode(" AND ", $conditions);
}

// Total records without filtering
$totalRecordsQuery = "SELECT COUNT(*) AS count FROM ngarkimi";
$totalStmt = $conn->prepare($totalRecordsQuery);
if ($totalStmt) {
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $response['recordsTotal'] = isset($totalRow['count']) ? intval($totalRow['count']) : 0;
    $totalStmt->close();
} else {
    $response['error'] = "Gabim në përgatitjen e pyetjes së përgjithshme.";
    echo json_encode($response);
    exit;
}

// Total records with filtering
$filteredRecordsQuery = "SELECT COUNT(*) AS count " . $baseQuery . $whereClause;
$filteredStmt = $conn->prepare($filteredRecordsQuery);
if ($filteredStmt) {
    if ($params) {
        $filteredStmt->bind_param($paramTypes, ...$params);
    }
    $filteredStmt->execute();
    $filteredResult = $filteredStmt->get_result();
    $filteredRow = $filteredResult->fetch_assoc();
    $response['recordsFiltered'] = isset($filteredRow['count']) ? intval($filteredRow['count']) : 0;
    $filteredStmt->close();
} else {
    $response['error'] = "Gabim në përgatitjen e pyetjes së filtruar.";
    echo json_encode($response);
    exit;
}

// Data query without pagination
$dataQuery = "SELECT n.*, k.emri AS klienti_emri, u.name AS postuar_nga " . $baseQuery . $whereClause . " ORDER BY $orderColumn $orderDirection";

$dataStmt = $conn->prepare($dataQuery);
if ($dataStmt) {
    // Bind parameters if any
    if ($params) {
        $dataStmt->bind_param($paramTypes, ...$params);
    }

    $dataStmt->execute();
    $dataResult = $dataStmt->get_result();

    $data = [];
    while ($row = $dataResult->fetch_assoc()) {
        // Format links with proper validation
        $linku = filter_var($row['linku'], FILTER_VALIDATE_URL) ? '<a class="input-custom-css px-3 py-2" href="' . $row['linku'] . '" target="_blank">Hap Linkun</a>' : 'N/A';
        $linkuplat = filter_var($row['linkuplat'], FILTER_VALIDATE_URL) ? '<a class="input-custom-css px-3 py-2" href="' . $row['linkuplat'] . '" target="_blank">Hap Linkun</a>' : 'N/A';

        $data[] = [
            'id' => $row['id'],
            'kengetari' => $row['kengetari'],
            'emri' => $row['emri'],
            'teksti' => $row['teksti'] ?? 'N/A',
            'muzika' => $row['muzika'] ?? 'N/A',
            'orkestra' => $row['orkestra'] ?? 'N/A',
            'co' => $row['co'] ?? 'N/A',
            'facebook' => $row['facebook'] ?? 'N/A',
            'instagram' => $row['instagram'] ?? 'N/A',
            'veper' => $row['veper'] ?? 'N/A',
            'klienti_emri' => $row['klienti_emri'] ?? 'N/A',
            'platformat' => $row['platformat'] ?? 'N/A',
            'linku' => $linku,
            'linkuplat' => $linkuplat,
            'data' => $row['data'] ?? 'N/A',
            'gjuha' => $row['gjuha'] ?? 'N/A',
            'infosh' => $row['infosh'] ?? 'N/A',
            'postuar_nga' => $row['postuar_nga'] ?? 'N/A'
        ];
    }

    $response['data'] = $data;
    $dataStmt->close();
} else {
    $response['error'] = "Gabim në përgatitjen e pyetjes së të dhënave.";
}

$conn->close();
echo json_encode($response);
