<?php
// Include your database connection file here
include('conn-d.php');

// Define columns
$columns = array(
    0 => 'Emri',
    1 => 'Artist',
    2 => 'ReportingPeriod',
    3 => 'AccountingPeriod',
    4 => 'Release',
    5 => 'Track',
    6 => 'Country',
    7 => 'RevenueUSD',
    8 => 'RevenueShare',
    9 => 'SplitPayShare',
    10 => 'Partner',
);

try {
    // Get the value for filtering (assuming $artistii is already defined)
    $artistii = isset($_POST['artistii']) ? $_POST['artistii'] : '';

    // Get data for pagination
    $start = isset($_POST['start']) ? $_POST['start'] : 0;
    $length = isset($_POST['length']) ? $_POST['length'] : 10;
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

    // Fetch records with pagination and search
    $sql = "SELECT *, CONCAT(ReportingPeriod, ' - ', AccountingPeriod) AS Period FROM platformat_2 WHERE Emri = '$artistii'";
    if (!empty($search)) {
        $sql .= " AND (";
        foreach ($columns as $col) {
            $sql .= "$col LIKE '%$search%' OR ";
        }
        $sql = rtrim($sql, "OR ");
        $sql .= ")";
    }

    // Count total filtered records for pagination
    $totalFilteredQuery = $conn->query($sql);
    if ($totalFilteredQuery === false) {
        throw new Exception("Error fetching total filtered records.");
    }
    $totalFiltered = mysqli_num_rows($totalFilteredQuery);

    // Add pagination and ordering to SQL query
    $order_column_index = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
    $order_column = $columns[$order_column_index];

    $sql .= " ORDER BY CASE WHEN Partner = 'Spotify' THEN 0 ELSE 1 END, Partner $order_dir, $order_column $order_dir LIMIT $start, $length";

    $query = $conn->query($sql);
    if ($query === false) {
        throw new Exception("Error fetching records.");
    }

    // Prepare response data
    $data = array();
    while ($row = $query->fetch_assoc()) {
        $nestedData = array();
        foreach ($columns as $col) {
            if ($col === 'Country') {
                // Custom rendering for country column
                $country_code = $row['Country'];
                $country_name = ""; // Provide country name if needed
                $nestedData[] = '<img class="img-thumbnail rounded-0" src="https://flagcdn.com/w2560/' . strtolower($country_code) . '.jpg"
                    width="2560" height="2560"
                    alt="' . $country_name . '"><br><br><p>' . $row['Country'] . '</p>';
            } else if ($col === 'ReportingPeriod' || $col === 'AccountingPeriod') {
                // Combine ReportingPeriod and AccountingPeriod into one column
                $nestedData[] = $row['Period'];
            } else {
                $nestedData[] = $row[$col];
            }
        }
        $data[] = $nestedData;
    }

    // Prepare and send JSON response
    $json_data = array(
        "draw"            => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        "recordsTotal"    => intval(mysqli_num_rows($totalFilteredQuery)),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    );

    echo json_encode($json_data);
} catch (Exception $e) {
    // Handle exceptions
    $error_message = $e->getMessage();
    $json_error = array(
        "error" => $error_message
    );
    echo json_encode($json_error);
}
?>
