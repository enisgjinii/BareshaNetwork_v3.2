<?php
include('../../conn-d.php');

// Get the date range from POST parameters
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
    $types .= "ss";
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($data_row = mysqli_fetch_array($result)) {
    $id = $data_row['emri']; // This is the client id from facebook table
    $primary_id = $data_row['id']; // This is the primary key of faturafacebook

    // Retrieve client details from facebook table
    $subquery = "SELECT emri_mbiemri, emaili_klientit FROM facebook WHERE id = ?";
    $stmt2 = $conn->prepare($subquery);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $subresult = $stmt2->get_result();
    $subdata_row = mysqli_fetch_array($subresult);
    $emri_artikullit = $subdata_row['emri_mbiemri'];
    $emaili_klientit = $subdata_row['emaili_klientit'];

    $fatura = $data_row['fatura'];
    $shuma_klientit = $data_row['klientit'];
    $mbetja = $data_row['mbetja'];
    $totali = $data_row['totali'];
    $data = $data_row['data'];
    $sid = $data_row['fatura'];

    $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitjefacebook` WHERE fatura='$sid'");
    $qq4 = mysqli_fetch_array($q4);

    $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesatfacebook` WHERE fatura='$sid'");
    $merrep = mysqli_fetch_array($merrpagesen);

    $shuma = $qq4["sum"];
    $shuma_e_paguar = $merrep['sum'];
    $obli = $qq4['sum'] - $merrep['sum'];

    $email_button = "";
    if (!empty($emaili_klientit)) {
        $email_button = "<button class='btn btn-info btn-sm py-2 rounded-5 text-white send-invoice' data-email='$emaili_klientit' data-id='$primary_id'><i class='fi fi-rr-envelope'></i></button>";
    }

    if ($obli !== 0 && $obli > 0) {
        $data_array[] = array(
            'emrifull' => $emri_artikullit,
            'fatura' => $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
            'data' => $data_row['data'],
            'shuma' => $shuma,
            'shuma_e_paguar' => $shuma_e_paguar,
            'obli' => $obli,
            'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitjeFacebook.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='faturaDetajeFacebook.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a> $email_button"
        );
    }
}

header('Content-Type: application/json');
echo json_encode(array('data' => $data_array));
