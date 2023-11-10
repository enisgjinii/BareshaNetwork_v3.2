<?php
session_start();
include('conn-d.php');

$data_array = [];

$query = "SELECT * FROM fatura ORDER BY id DESC";
$result = $conn->query($query);

while ($data_row = mysqli_fetch_array($result)) {
    $id = $data_row['emri'];

    $query1 = "SELECT * FROM yinc WHERE kanali=?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param('s', $id);
    $stmt1->execute();
    $nxerrjaEEmritKanalitResult = $stmt1->get_result();

    $borgjeTotal = 0; // Variable to store the total borje
    while ($row = $nxerrjaEEmritKanalitResult->fetch_array()) {
        $amount = $row['shuma'] - $row['pagoi'];
        if ($amount > 0) {
            $borgjeTotal += $amount;
        }
    }

    $fatura = $data_row['fatura'];
    $shuma = $shuma_e_paguar = $obligim = 0;

    $shumaQuery = "SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura=?";
    $shumaStmt = $conn->prepare($shumaQuery);
    $shumaStmt->bind_param('s', $fatura);
    $shumaStmt->execute();
    $shumaResult = $shumaStmt->get_result();

    if ($shumaResult->num_rows > 0) {
        $shumaRow = $shumaResult->fetch_array();
        $shuma = $shumaRow["sum"];
    }

    $shumaPaguarQuery = "SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura=?";
    $shumaPaguarStmt = $conn->prepare($shumaPaguarQuery);
    $shumaPaguarStmt->bind_param('s', $fatura);
    $shumaPaguarStmt->execute();
    $shumaPaguarResult = $shumaPaguarStmt->get_result();

    if ($shumaPaguarResult->num_rows > 0) {
        $shumaPaguarRow = $shumaPaguarResult->fetch_array();
        $shuma_e_paguar = $shumaPaguarRow['sum'];
    }

    $obligim = $shuma - $shuma_e_paguar;

    $stmt3 = $conn->prepare("SELECT linku_kenges,kengetari FROM `shitje` WHERE fatura = ?");
    $stmt3->bind_param('s', $fatura);
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    $emriKengetarit = "";
    $linkuIKenges = "";

    if ($result3->num_rows > 0) {
        $row = $result3->fetch_assoc();
        $linkuIKenges = $row['linku_kenges'];
        $emriKengetarit = $row['kengetari'];
    }

    if ($_SESSION['acc'] == '3' && $data_row['gjendja_e_fatures'] == 'Rregullt' && $obligim > 0) {
        $data_array[] = buildDataArray(
            $data_row['emrifull'],
            $fatura,
            $data_row['data'],
            $shuma,
            $shuma_e_paguar,
            $obligim,
            "<a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='fatura.php?invoice=$fatura'><i class='fi fi-rr-print'></i></a>"
        );
    } elseif ($_SESSION['acc'] == '1' && $obligim > 0) {
        $statusClass = $borgjeTotal ? "dot-red" : "dot-green";
        $statusTooltip = $borgjeTotal ? "Borgji total: $borgjeTotal €" : "Ska obligime";

        $data_array[] = buildDataArray(
            $data_row['emrifull'],
            $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
            $data_row['data'],
            $shuma,
            $shuma_e_paguar,
            $obligim,
            "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitje.php?fatura=$fatura' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='fatura.php?invoice=$fatura'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$fatura'><i class='fi fi-rr-trash'></i></a>",
            $statusClass,
            $statusTooltip,
            $emriKengetarit,
            $linkuIKenges
        );
    }
}

header('Content-Type: application/json');
echo json_encode(array('data' => $data_array));

// Helper function to build the data array for each row
function buildDataArray($emrifull, $fatura, $data, $shuma, $shuma_e_paguar, $obligim, $aksion, $statusClass = "", $statusTooltip = "", $emriKengetarit = "", $linkuIKenges = "")
{   
    $emrifull = "<p class='mx-5 dot $statusClass' data-toggle='tooltip' title='$statusTooltip'></p><b>$emrifull";
    if (!empty($linkuIKenges)) {
        $emrifull .= " | <i>$emriKengetarit</i>";
    }
    $emrifull .= "</b>";

    return array(
        'emrifull' => $emrifull,
        'emriartikullit' => $emrifull,
        'fatura' => $fatura,
        'data' => $data,
        'shuma' => $shuma,
        'shuma_e_paguar' => $shuma_e_paguar,
        'obli' => $obligim,
        'aksion' => $aksion
    );
}
