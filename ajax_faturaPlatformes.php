<?php
include('conn-d.php');

$data_array = array(); // Change the variable name to $data_array
$query = "SELECT * FROM faturaplatformes ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_array($result)) {
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
    $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitjeplatforma` WHERE fatura='$sid'");
    $qq4 = mysqli_fetch_array($q4);
    $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesatplatformat` WHERE fatura='$sid'");
    $merrep = mysqli_fetch_array($merrpagesen);
    $shuma = $qq4["sum"];
    $shuma_e_paguar = $merrep['sum'];
    $obli = $qq4['sum'] - $merrep['sum'];


    if ($obli !== 0 && $obli > 0) {
        if ($_POST['acc'] == "3" && ($gjendjaFatures == "Rregullt" || $gjendjaFatures == "")) {

            $data_array[] = array( // Use $data_array instead of $data
                'emrifull' => "<span class='badge rounded-pill text-bg-danger text-white w-100'>" . $emri_artikullit . "</span>",
                'emriartikullit' => $emri_artikullit,
                'fatura' => $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
                'data' => $row['data'],
                'shuma' => $shuma,
                'shuma_e_paguar' => $shuma_e_paguar,
                'obli' => $obli,
                'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitjePlatforma.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='faturaPlatforma.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a>"
            );
        } else if ($_POST['acc'] == "1") {
            $data_array[] = array(

                'emrifull' => ($gjendjaFatures == "Rregullt" || $gjendjaFatures == ""
                    ? "<p class='badge rounded-2 text-bg-light shadow-sm border p-2 ms-2'>" . $emri_artikullit . "</p><span class='badge rounded-pill text-bg-success text-white ms-2'>" . $gjendjaFatures . "</span>"
                    : "<p class='badge rounded-2 text-bg-light shadow-sm border p-2 ms-2'>" . $emri_artikullit . "</p><span class='badge rounded-pill text-bg-danger text-white ms-2'>" . $gjendjaFatures . "</span>"
                ),


                'emriartikullit' => $emri_artikullit,
                'fatura' => $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
                'data' => $row['data'],
                'shuma' => $shuma,
                'shuma_e_paguar' => $shuma_e_paguar,
                'obli' => $obli,
                'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitjePlatforma.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='faturaPlatforma.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a>"
            );
        }
    }
}

echo json_encode(array('data' => $data_array)); // Use $data_array instead of $data
