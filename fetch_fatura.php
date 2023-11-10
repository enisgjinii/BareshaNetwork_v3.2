<?php
include('conn-d.php');

$query = "SELECT * FROM fatura";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$data_array = array(); // Change the variable name to $data_array

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $emri_artikullit = $row['emrifull'];
    $fatura = $row['fatura'];
    $pagesa_e_mbetur = $row['klientit'];
    $totali = $row['mbetja'];
    $pagesa = $row['totali'];
    $data = $row['data'];
    $dda = $row['data'];
    $date = date_create($dda);
    $dats = date_format($date, 'Y-m-d');
    $sid = $row['fatura'];

    $gjendjaFatures = $row['gjendja_e_fatures'];

    // Calculate the outstanding balance
    $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$sid'");
    $qq4 = $q4->fetch_assoc();
    $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$sid'");
    $merrep = $merrpagesen->fetch_assoc();
    $shuma = $qq4["sum"];
    $shuma_e_paguar = $merrep['sum'];
    $obli = $qq4['sum'] - $merrep['sum'];

    if ($obli !== 0 && $obli > 0) {
        $data_array[] = [
            'emrifull' => "<span class='badge rounded-pill text-bg-danger text-white w-100'>$emri_artikullit</span>",
            'emriartikullit' => $emri_artikullit,
            'fatura' => "$fatura<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
            'data' => $row['data'],
            'shuma' => $shuma,
            'shuma_e_paguar' => $shuma_e_paguar,
            'obli' => $obli,
            'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitje.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='fatura.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a>",
        ];
    }
}

echo json_encode(array('data' => $data_array));
