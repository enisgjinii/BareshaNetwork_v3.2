<?php
// ajax/get_payments.php

include '../../conn-d.php';

header('Content-Type: application/json');

// DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$order_dir = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';

// Columns mapping
$columns = ['k.emri', 'p.fatura', 'p.pershkrimi', 'p.shuma', 'p.menyra', 'p.data', 'p.kategoria'];

// Date filters
$min_date = isset($_POST['min']) ? $_POST['min'] : '';
$max_date = isset($_POST['max']) ? $_POST['max'] : '';

// Base query
$query = "SELECT p.fatura, p.kategoria, SUM(p.shuma) as total_shuma, p.menyra, p.data, p.pershkrimi, k.emri as client_name 
          FROM pagesat p 
          JOIN fatura f ON p.fatura = f.fatura 
          JOIN klientet k ON f.emri = k.id";

// Filtering
$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(k.emri LIKE ? OR p.fatura LIKE ? OR p.pershkrimi LIKE ? OR p.menyra LIKE ?)";
    $search_param = "%" . $search . "%";
    array_push($params, $search_param, $search_param, $search_param, $search_param);
    $types .= 'ssss';
}

if (!empty($min_date)) {
    $where[] = "p.data >= ?";
    $min_formatted = date("Y-m-d", strtotime($min_date));
    $params[] = $min_formatted;
    $types .= 's';
}

if (!empty($max_date)) {
    $where[] = "p.data <= ?";
    $max_formatted = date("Y-m-d", strtotime($max_date));
    $params[] = $max_formatted;
    $types .= 's';
}

if (count($where) > 0) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " GROUP BY p.fatura, p.kategoria";

// Total records
$total_query = "SELECT COUNT(DISTINCT p.fatura, p.kategoria) as total FROM pagesat p JOIN fatura f ON p.fatura = f.fatura JOIN klientet k ON f.emri = k.id";
$total_stmt = $conn->prepare($total_query);
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();
$total_records = $total_result['total'];
$total_stmt->close();

// Filtered records
if (count($where) > 0) {
    $filtered_query = "SELECT COUNT(DISTINCT p.fatura, p.kategoria) as total FROM pagesat p JOIN fatura f ON p.fatura = f.fatura JOIN klientet k ON f.emri = k.id WHERE " . implode(" AND ", $where);
    $filtered_stmt = $conn->prepare($filtered_query);
    if ($types) {
        $filtered_stmt->bind_param($types, ...$params);
    }
    $filtered_stmt->execute();
    $filtered_result = $filtered_stmt->get_result()->fetch_assoc();
    $filtered_records = $filtered_result['total'];
    $filtered_stmt->close();
} else {
    $filtered_records = $total_records;
}

// Ordering
$order_column_name = isset($columns[$order_column]) ? $columns[$order_column] : 'p.data';
$query .= " ORDER BY $order_column_name $order_dir";

// Pagination
$query .= " LIMIT ?, ?";
$params[] = $start;
$params[] = $length;
$types .= 'ii';

// Execute main query
$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$overall_total = 0;

while ($row = $result->fetch_assoc()) {
    $overall_total += $row['total_shuma'];
    $kategoria = !empty($row['kategoria']) ? @unserialize($row['kategoria']) : [];
    $kategoria_str = is_array($kategoria) ? implode(", ", array_map(fn ($v) => $v == 'null' ? 'Ska' : $v, $kategoria)) : str_replace('null', 'Ska', $row['kategoria']);
    $data[] = [
        htmlspecialchars($row['client_name']),
        htmlspecialchars($row['fatura']),
        htmlspecialchars($row['pershkrimi']),
        number_format($row['total_shuma'], 2) . " €",
        htmlspecialchars($row['menyra']),
        date("d-m-Y", strtotime($row['data'])),
        htmlspecialchars($kategoria_str),
        '<a class="btn btn-light py-1 px-2 border border-1" target="_blank" href="invoice.php?invoice=' . htmlspecialchars($row['fatura']) . '"><i class="fi fi-rr-print"></i></a>'
    ];
}

$stmt->close();

// Prepare response
$response = [
    "draw" => $draw,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $filtered_records,
    "data" => $data,
    "totalSum" => number_format($overall_total, 2) . " €"
];

echo json_encode($response);
?>
