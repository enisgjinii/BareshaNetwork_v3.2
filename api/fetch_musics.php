<?php
header('Content-Type: application/json');
require_once '../conn-d.php';

// Configuration
$table = "ngarkimi";
$columns = ['kengetari', 'emri', 'teksti', 'muzika', 'orkestra', 'co', 'facebook', 'instagram', 'veper', 'klienti', 'platformat', 'linku', 'linkuplat', 'data', 'gjuha', 'infosh', 'nga'];
$client_id = isset($_GET['id']) ? $_GET['id'] : '';

// Paging
$limit = '';
if (isset($_GET['start']) && $_GET['length'] != -1) {
    $start = intval($_GET['start']);
    $length = intval($_GET['length']);
    $limit = "LIMIT $start, $length";
}

// Ordering
$order = '';
if (isset($_GET['order']) && count($_GET['order'])) {
    $orderBy = [];
    for ($i = 0, $ien = count($_GET['order']); $i < $ien; $i++) {
        $columnIdx = intval($_GET['order'][$i]['column']);
        $requestColumn = $columns[$columnIdx];
        $orderBy[] = "$requestColumn {$_GET['order'][$i]['dir']}";
    }

    $order = 'ORDER BY ' . implode(', ', $orderBy);
}

// Searching
$search = '';
if (isset($_GET['search']) && $_GET['search']['value'] != '') {
    $searchValue = $_GET['search']['value'];
    $searchArray = array();
    foreach ($columns as $column) {
        $searchArray[] = "$column LIKE '%$searchValue%'";
    }

    $search = 'WHERE ' . implode(' OR ', $searchArray);
}

// Filtering by client id
if ($client_id) {
    $search .= ($search ? ' AND ' : 'WHERE ') . "klienti = '$client_id'";
}

// Querying data
$query_base = "SELECT * FROM $table $search $order $limit";
$result_base = mysqli_query($conn, $query_base) or die(mysqli_error($conn));

$query_total = "SELECT COUNT(id) as total FROM $table";
$result_total = mysqli_query($conn, $query_total) or die(mysqli_error($conn));
$total_rows = mysqli_fetch_assoc($result_total)['total'];

$query_filtered = "SELECT COUNT(id) as filtered FROM $table $search";
$result_filtered = mysqli_query($conn, $query_filtered) or die(mysqli_error($conn));
$filtered_rows = mysqli_fetch_assoc($result_filtered)['filtered'];

// Fetching related data
$result_data = [];
while ($row = mysqli_fetch_assoc($result_base)) {
    $klientid = $row['klienti'];
    $postuarng = $row['nga'];

    $kueri2 = mysqli_query($conn, "SELECT * FROM klientet WHERE id='$klientid'");
    $k2 = mysqli_fetch_array($kueri2);
    $kueri3 = mysqli_query($conn, "SELECT * FROM users WHERE id='$postuarng'");
    $k3 = mysqli_fetch_array($kueri3);

    $row['klienti'] = $k2['emri'];
    $row['nga'] = $k3['name'];

    $formatted_row = [];
    foreach ($columns as $column) {
        if ($column === 'kengetari') {
            $formatted_row[$column] = $row[$column] . '<br><br><a class="btn btn-danger text-white shadow-sm rounded-5" href="?del='.$row['id'].'"><i class="fi fi-rr-trash"></i></a>';

        } else {
            $formatted_row[$column] = $row[$column];
        }
    }


    $result_data[] = $formatted_row;
}

// Output data
$output = array(
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
    "recordsTotal" => intval($total_rows),
    "recordsFiltered" => intval($filtered_rows),
    "data" => $result_data,
);

echo json_encode($output);
