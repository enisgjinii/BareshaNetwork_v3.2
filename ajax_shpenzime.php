<?php
// ajax_shpenzime.php

include 'conn-d.php';

// Define the columns for sorting
$columns = array(
    'kanali',
    'shuma',
    'pagoi',
    'shuma - pagoi', // This will calculate 'Obligim'
    '', // This will be an empty column for the 'Paguaj' button
    'lloji',
    'pershkrimi',
    'data',
    '', // This will be an empty column for the 'Delete' button
);

// Process and validate DataTables request
$requestData = $_POST;

// Paging
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;
$length = isset($requestData['length']) ? intval($requestData['length']) : 10;
// Ordering
$orderColumn = isset($requestData['order'][0]['column']) ? intval($requestData['order'][0]['column']) : 0;
$orderDir = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'asc';

// Convert the DataTables column index to the corresponding SQL column name
$orderBy = $columns[$orderColumn];

// Modify the SQL query to handle custom ordering when 'id' column is selected
if ($orderBy === 'kanali') {
    $orderBy = 'id DESC, kanali'; // Order by id in descending order and then by 'kanali'
}

// Searching
$search = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

// Fetch data from the database with filtering and ordering
$whereClause = '';
if (!empty($search)) {
    $whereClause = "WHERE (kanali LIKE '%$search%' OR lloji LIKE '%$search%' OR pershkrimi LIKE '%$search%' OR data LIKE '%$search%')
                    AND EXISTS (SELECT * FROM klientet WHERE yinc.kanali = klientet.id)";
}
$query = "SELECT yinc.*, klientet.emri AS klienti_emri
          FROM yinc
          LEFT JOIN klientet ON yinc.kanali = klientet.id
          $whereClause
          ORDER BY $orderBy
          LIMIT $start, $length";


$result = $conn->query($query);






$data = array();
while ($k = mysqli_fetch_array($result)) {

    $sid = $k['kanali'];
    $gstaf = $conn->query("SELECT * FROM klientet WHERE id='$sid'");
    $gstafi = mysqli_fetch_array($gstaf);
    //My number is 928.
    $myNumber = $k['shuma'];

    //I want to get 25% of 928.
    $percentToGet = $gstafi['perqindja'];

    //Convert our percentage value into a decimal.
    $percentInDecimal = $percentToGet / 100;

    //Get the result.
    $percent = $percentInDecimal * $myNumber;
    $rr = $gstafi['emri'];

    $data[] = array(


        $rr,
        $k['shuma'],
        $k['pagoi'],
        $k['shuma'] - $k['pagoi'],
        '<a data-bs-toggle="modal" data-bs-target="#pages' . $k['id'] . '" class="btn btn-primary text-white shadow-sm rounded-5 py-2"><i class="fi fi-rr-money-bill-wave"></i></a>',
        $k['lloji'],
        $k['pershkrimi'],
        $k['data'],
        '<form method="POST" action="">
          <input type="hidden" name="delete_id" value="' . $k['id'] . '">
          <button type="submit" class="btn btn-danger text-white shadow-sm rounded-5 py-2" name="delete" onclick="return confirm(\'Jeni i sigurt q&euml; d&euml;shironi ta fshini k&euml;t&euml; regjistrim?\');"><i class="fi fi-rr-trash"></i></button>
        </form>'
    );
}

// Get the total records count for pagination
$totalRecords = $conn->query("SELECT COUNT(*) as count FROM yinc")->fetch_array()['count'];

// Return data in JSON format
header('Content-Type: application/json');
echo json_encode(array(
    'draw' => intval($requestData['draw']),
    'recordsTotal' => intval($totalRecords),
    'recordsFiltered' => intval($totalRecords),
    'data' => $data,
));
