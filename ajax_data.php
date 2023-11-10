<?php
session_start();
include('conn-d.php');

$data_array = array();

$query = "SELECT f.*, y.*, 
            (SELECT SUM(totali) FROM shitje s WHERE s.fatura = f.fatura) AS shuma,
            (SELECT SUM(shuma) FROM pagesat p WHERE p.fatura = f.fatura) AS shuma_e_paguar
          FROM fatura f 
          LEFT JOIN yinc y ON f.emri = y.kanali 
          ORDER BY f.id DESC";
$result = $conn->query($query);

while ($data_row = mysqli_fetch_array($result)) {
    $id = $data_row['emri'];

    $borgjE = 0; // Variable to store the total

    // Fetch data for $nxerrjaEEmritKanalitResult
    $query1 = "SELECT * FROM yinc WHERE kanali=?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param('s', $id);
    $stmt1->execute();
    $nxerrjaEEmritKanalitResult = $stmt1->get_result();

    // Calculate $borgjE based on fetched data
    while ($row = $nxerrjaEEmritKanalitResult->fetch_array()) {
        if ($row['shuma'] > $row['pagoi']) {
            $amount = $row['shuma'] - $row['pagoi'];
            $borgjE += $amount;
        }
    }

    $emri_artikullit = $data_row['emrifull'];
    $fatura = $data_row['fatura'];
    $pagesa_e_mbetur = $data_row['klientit'];
    $totali = $data_row['mbetja'];
    $pagesa = $data_row['totali'];
    $data = $data_row['data'];
    $dda = $data_row['data'];
    $date = date_create($dda);
    $dats = date_format($date, 'Y-m-d');
    $sid = $data_row['fatura'];

    $gjendjaFatures = $data_row['gjendja_e_fatures'];
    $emertimi = $data_row['emertimi'];

    $dotClass = ($borgjE > 0) ? "dot-red" : "dot-green";
    $obligimeText = ($borgjE > 0) ? "Borgji total: " . $borgjE . " €" : "Ska obligime";

    $emrifull = "<p class='mx-5 dot " . $dotClass . "' data-toggle='tooltip' title='" . $obligimeText . "'></p><b>" . $emri_artikullit;
    if (!empty($data_row['linku_kenges'])) {
        $emrifull .= " | <i>" . $data_row['kengetari'] . "</i>";
    }
    $emrifull .= "</b>";

    $data_array[] = array(
        'emrifull' => $emrifull,
        'emriartikullit' => $emri_artikullit,
        'fatura' => $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
        'data' => $data_row['data'],
        'shuma' => $data_row['shuma'],
        'shuma_e_paguar' => $data_row['shuma_e_paguar'],
        'obli' => $borgjE,
        'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitje.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='fatura.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a>"
    );
}

// Process the DataTables Ajax request
$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$search = $_POST['search']['value'];

// Apply search filter (you may need to adjust this based on your column names)
if (!empty($search)) {
    $data_array = array_filter($data_array, function ($row) use ($search) {
        return strpos(strtolower($row['emrifull']), strtolower($search)) !== false;
    });
}

// Get the current page data
$current_page_data = array_slice($data_array, $start, $length);

// Prepare the response
$response = array(
    'draw' => $draw,
    'recordsTotal' => count($data_array),
    'recordsFiltered' => count($data_array),
    'data' => $current_page_data,
);

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
