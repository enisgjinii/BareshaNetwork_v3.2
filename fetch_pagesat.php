<?php
// fetch_pagesat.php

session_start();

// Include the database connection
include 'conn-d.php';

// Function to sanitize input
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate CSRF token
function validate_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Ensure request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Check if 'action' parameter is set
if (!isset($_POST['action'])) {
    echo json_encode(['error' => 'No action specified']);
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'fetch_payments':
        fetch_payments($conn);
        break;
    case 'get_payment':
        get_payment($conn);
        break;
    case 'update_payment':
        update_payment($conn);
        break;
    case 'delete_payment':
        delete_payment($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Function to handle fetching payments for DataTables
function fetch_payments($conn)
{
    // Retrieve DataTables parameters
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $order_column_index = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtoupper($_POST['order'][0]['dir']), ['ASC', 'DESC']) ? strtoupper($_POST['order'][0]['dir']) : 'ASC';

    // Define columns mapping
    $columns = [
        0 => 'k.emri',
        1 => 'p.fatura',
        2 => 'p.pershkrimi',
        3 => 'total_shuma',
        4 => 'p.menyra',
        5 => 'p.data',
        6 => 'p.kategoria',
        7 => 'p.fatura'
    ];

    $order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'p.data';

    // Base query
    $base_query = "
        FROM pagesat p
        JOIN fatura f ON p.fatura = f.fatura
        JOIN klientet k ON f.emri = k.id
    ";

    // Filtering
    $filter_query = "";
    $params = [];
    $types = "";

    // Date Range Filtering
    if (isset($_POST['min']) && !empty($_POST['min']) && isset($_POST['max']) && !empty($_POST['max'])) {
        $min_date = date("Y-m-d", strtotime($_POST['min']));
        $max_date = date("Y-m-d", strtotime($_POST['max']));
        $filter_query .= " WHERE p.data BETWEEN ? AND ?";
        $params[] = $min_date;
        $params[] = $max_date;
        $types .= "ss";
    } elseif (isset($_POST['min']) && !empty($_POST['min'])) {
        $min_date = date("Y-m-d", strtotime($_POST['min']));
        $filter_query .= " WHERE p.data >= ?";
        $params[] = $min_date;
        $types .= "s";
    } elseif (isset($_POST['max']) && !empty($_POST['max'])) {
        $max_date = date("Y-m-d", strtotime($_POST['max']));
        $filter_query .= " WHERE p.data <= ?";
        $params[] = $max_date;
        $types .= "s";
    }

    // Search Filtering
    if (!empty($search)) {
        if (strpos($filter_query, 'WHERE') !== false) {
            $filter_query .= " AND (k.emri LIKE ? OR p.fatura LIKE ? OR p.pershkrimi LIKE ?)";
        } else {
            $filter_query .= " WHERE (k.emri LIKE ? OR p.fatura LIKE ? OR p.pershkrimi LIKE ?)";
        }
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }

    try {
        // Total records without filtering
        $stmt_total = $conn->prepare("SELECT COUNT(*) " . $base_query);
        $stmt_total->execute();
        $stmt_total->bind_result($total_records);
        $stmt_total->fetch();
        $stmt_total->close();

        // Total records with filtering
        $stmt_filtered = $conn->prepare("SELECT COUNT(*) " . $base_query . $filter_query);
        if ($types) {
            $stmt_filtered->bind_param($types, ...$params);
        }
        $stmt_filtered->execute();
        $stmt_filtered->bind_result($total_filtered);
        $stmt_filtered->fetch();
        $stmt_filtered->close();

        // Data query with ordering and limit
        $data_query = "
            SELECT 
                p.fatura, 
                p.kategoria, 
                SUM(p.shuma) AS total_shuma, 
                p.menyra, 
                p.data, 
                p.pershkrimi, 
                k.emri AS client_name 
            " . $base_query . $filter_query . "
            GROUP BY p.fatura, p.kategoria 
            ORDER BY " . $order_column . " " . $order_dir . "
            LIMIT ?, ?
        ";

        // Add limit parameters
        $params_limit = [$start, $length];
        $types_limit = "ii";
        $params_final = array_merge($params, $params_limit);
        $types_final = $types . $types_limit;

        $stmt_data = $conn->prepare($data_query);
        if ($types_final) {
            $stmt_data->bind_param($types_final, ...$params_final);
        }
        $stmt_data->execute();
        $result = $stmt_data->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $kategoria = !empty($row['kategoria']) ? unserialize($row['kategoria']) : [];
            $kategoria_str = is_array($kategoria)
                ? implode(", ", array_map(function ($v) {
                    return ($v === 'null') ? 'Ska' : htmlspecialchars($v);
                }, $kategoria))
                : str_replace('null', 'Ska', htmlspecialchars($row['kategoria']));
            $formatted_date = date("d-m-Y", strtotime($row['data']));
            $data[] = [
                'client_name' => htmlspecialchars($row['client_name']),
                'fatura'      => htmlspecialchars($row['fatura']),
                'pershkrimi'  => htmlspecialchars($row['pershkrimi']),
                'total_shuma' => number_format($row['total_shuma'], 2, '.', ','),
                'menyra'      => htmlspecialchars($row['menyra']),
                'data'        => $formatted_date,
                'kategoria'   => $kategoria_str,
                'fatura_pdf'  => htmlspecialchars($row['fatura'])
            ];
        }
        $stmt_data->close();

        // Fetch summary data
        $summary_query = "
            SELECT 
                SUM(p.shuma) AS total_shuma,
                AVG(p.shuma) AS average_shuma,
                COUNT(DISTINCT p.fatura) AS total_invoices
            " . $base_query . $filter_query;
        $stmt_summary = $conn->prepare($summary_query);
        if ($types) {
            $stmt_summary->bind_param($types, ...$params);
        }
        $stmt_summary->execute();
        $stmt_summary->bind_result($total_shuma, $average_shuma, $total_invoices);
        $stmt_summary->fetch();
        $stmt_summary->close();

        // Prepare response
        $response = [
            "draw" => $draw,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_filtered,
            "data" => $data,
            "summary" => [
                "total_shuma" => $total_shuma !== null ? number_format($total_shuma, 2, '.', ',') : '0.00',
                "average_shuma" => $average_shuma !== null ? number_format($average_shuma, 2, '.', ',') : '0.00',
                "total_invoices" => $total_invoices !== null ? $total_invoices : '0'
            ]
        ];

        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        error_log("Fetch Payments Error: " . $e->getMessage());
        echo json_encode(['error' => 'An error occurred while fetching payments']);
        exit;
    }
}

// Function to handle fetching a single payment's details
function get_payment($conn)
{
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    // Validate and sanitize input
    if (!isset($_POST['fatura']) || empty(trim($_POST['fatura']))) {
        echo json_encode(['error' => 'No fatura specified']);
        exit;
    }

    $fatura = sanitize($_POST['fatura']);

    try {
        $query = "
            SELECT 
                p.fatura, 
                k.emri AS client_name, 
                p.pershkrimi, 
                p.shuma, 
                p.menyra, 
                p.data, 
                p.kategoria
            FROM pagesat p
            JOIN fatura f ON p.fatura = f.fatura
            JOIN klientet k ON f.emri = k.id
            WHERE p.fatura = ?
            LIMIT 1
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $fatura);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $kategoria = !empty($row['kategoria']) ? unserialize($row['kategoria']) : [];
            $kategoria_str = is_array($kategoria)
                ? implode(", ", array_map(function ($v) {
                    return ($v === 'null') ? 'Ska' : htmlspecialchars($v);
                }, $kategoria))
                : str_replace('null', 'Ska', htmlspecialchars($row['kategoria']));

            $formatted_date = date("d-m-Y", strtotime($row['data']));

            echo json_encode([
                'fatura' => htmlspecialchars($row['fatura']),
                'client_name' => htmlspecialchars($row['client_name']),
                'pershkrimi' => htmlspecialchars($row['pershkrimi']),
                'shuma' => number_format($row['shuma'], 2, '.', ','),
                'menyra' => htmlspecialchars($row['menyra']),
                'data' => $formatted_date,
                'kategoria' => $kategoria_str
            ]);
        } else {
            echo json_encode(['error' => 'No data found for the specified fatura']);
        }
    } catch (Exception $e) {
        error_log("Get Payment Error: " . $e->getMessage());
        echo json_encode(['error' => 'An error occurred while fetching payment details']);
    }

    exit;
}

// Function to handle updating a payment
function update_payment($conn)
{
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }

    // Validate required fields
    $required_fields = ['fatura', 'pershkrimi', 'shuma', 'menyra', 'data'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            echo json_encode(['success' => false, 'error' => "Field '$field' is required"]);
            exit;
        }
    }

    // Sanitize inputs
    $fatura = sanitize($_POST['fatura']);
    $pershkrimi = sanitize($_POST['pershkrimi']);
    $shuma = floatval($_POST['shuma']);
    $menyra = sanitize($_POST['menyra']);
    $data = date("Y-m-d", strtotime($_POST['data']));
    $kategoria_input = isset($_POST['kategoria']) ? $_POST['kategoria'] : '';
    $kategoria_array = array_filter(array_map('trim', explode(',', $kategoria_input)));
    $kategoria = serialize($kategoria_array);

    try {
        $query = "
            UPDATE pagesat p
            JOIN fatura f ON p.fatura = f.fatura
            SET p.pershkrimi = ?, p.shuma = ?, p.menyra = ?, p.data = ?, p.kategoria = ?
            WHERE p.fatura = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdsssi", $pershkrimi, $shuma, $menyra, $data, $kategoria, $fatura);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update payment']);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Update Payment Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'An error occurred while updating the payment']);
    }

    exit;
}

// Function to handle deleting a payment
function delete_payment($conn)
{
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }

    // Validate and sanitize input
    if (!isset($_POST['fatura']) || empty(trim($_POST['fatura']))) {
        echo json_encode(['success' => false, 'error' => 'No fatura specified for deletion']);
        exit;
    }

    $fatura = sanitize($_POST['fatura']);

    try {
        $query = "DELETE FROM pagesat WHERE fatura = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $fatura);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No record found to delete']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete payment']);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Delete Payment Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'An error occurred while deleting the payment']);
    }

    exit;
}
