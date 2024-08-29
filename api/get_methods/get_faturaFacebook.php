<?php
include('../../conn-d.php');

// Get the date range from GET parameters
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

$data_array = array();

// Modify the query to include date filtering
$query = "SELECT * FROM faturafacebook";
$params = array();
$types = "";

if ($start_date && $end_date) {
    $query .= " WHERE data BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= "ss"; // 's' for string, add two as we have two date parameters
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($data_row = mysqli_fetch_array($result)) {
    $id = $data_row['emri'];

    // Retrieve the 'emri' value from the "facebook" table using a subquery
    $subquery = "SELECT emri_mbiemri FROM facebook WHERE id = ?";
    $stmt2 = $conn->prepare($subquery);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $subresult = $stmt2->get_result();
    $subdata_row = mysqli_fetch_array($subresult);
    $emri_artikullit = $subdata_row['emri_mbiemri'];

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

    $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitjefacebook` WHERE fatura='$sid'");
    $qq4 = mysqli_fetch_array($q4);
    $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesatfacebook` WHERE fatura='$sid'");
    $merrep = mysqli_fetch_array($merrpagesen);
    $shuma = $qq4["sum"];
    $shuma_e_paguar = $merrep['sum'];
    $obli = $qq4['sum'] - $merrep['sum'];

    if ($obli !== 0 && $obli > 0) {
        $data_array[] = array(
            'emrifull' => $emri_artikullit,
            'fatura' => $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
            'data' => $data_row['data'],
            'shuma' => $shuma,
            'shuma_e_paguar' => $shuma_e_paguar,
            'obli' => $obli,
            'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitjeFacebook.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='faturaDetajeFacebook.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a>"
        );
    }
}

header('Content-Type: application/json');
echo json_encode(array('data' => $data_array));