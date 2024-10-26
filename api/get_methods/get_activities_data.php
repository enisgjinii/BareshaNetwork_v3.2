<?php
include '../../conn-d.php';
session_start();



// Check if the request is an Ajax call
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get parameters needed for DataTables
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $order_column_index = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $order_column = $_POST['columns'][$order_column_index]['data'];
    $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    // Allowed columns for ordering
    $orderable_columns = ['email', 'activity_date', 'pages', 'activity_count'];
    if (!in_array($order_column, $orderable_columns)) {
        $order_column = 'activity_date';
    }
    $order_dir = ($order_dir === 'desc') ? 'DESC' : 'ASC';

    // Build the base query with grouping and aggregation
    $baseQuery = "
        SELECT
            ga.email,
            DATE(ua.activity_time) as activity_date,
            GROUP_CONCAT(DISTINCT ua.page SEPARATOR ', ') as pages,
            COUNT(*) as activity_count
        FROM
            user_activity ua
        INNER JOIN
            googleauth ga ON ua.user_id = ga.id
    ";

    // Add search functionality
    $whereClauses = [];
    $params = [];

    if (!empty($search_value)) {
        $whereClauses[] = "(ga.email LIKE CONCAT('%', ?, '%') OR ua.page LIKE CONCAT('%', ?, '%'))";
        $params[] = $search_value;
        $params[] = $search_value;
    }

    // Combine where clauses
    if (count($whereClauses) > 0) {
        $baseQuery .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // Group by email and date
    $baseQuery .= " GROUP BY ga.email, DATE(ua.activity_time)";

    // Get total records
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM ($baseQuery) as sub";
    $stmt = $conn->prepare($totalRecordsQuery);
    if (count($params) > 0) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $stmt->bind_result($totalRecords);
    $stmt->fetch();
    $stmt->close();

    // Add order and limit
    $baseQuery .= " ORDER BY $order_column $order_dir LIMIT ?, ?";
    $params[] = $start;
    $params[] = $length;

    // Prepare final query
    $stmt = $conn->prepare($baseQuery);
    $types = '';
    foreach ($params as $param) {
        $types .= is_int($param) ? 'i' : 's';
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch data
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'email' => $row['email'],
            'activity_date' => $row['activity_date'],
            'pages' => $row['pages'],
            'activity_count' => $row['activity_count']
        ];
    }

    // Prepare the response
    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    ];

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
