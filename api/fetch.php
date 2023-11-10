<?php
// fetch_data.php

include '../conn-d.php';

$tableColumns = array('emri', 'emriart', 'fatura', 'data', 'shuma', 'shpag', 'obligim');

$query = "SELECT emri,emrifull,fatura,data FROM fatura ";

if (isset($_POST["search"]["value"])) {
    $searchValue = $_POST["search"]["value"];
    $query .= "WHERE emrifull LIKE '%$searchValue%' OR data LIKE '%$searchValue%' ";
}

if (isset($_POST["order"])) {
    $orderColumnIndex = $_POST['order']['0']['column'];
    $orderDirection = $_POST['order']['0']['dir'];
    $query .= "ORDER BY $tableColumns[$orderColumnIndex] $orderDirection ";
} else {
    $query .= 'ORDER BY id DESC ';
}

$queryLimit = '';

if ($_POST["length"] != -1) {
    $startIndex = $_POST['start'];
    $rowsPerPage = $_POST['length'];
    $queryLimit = "LIMIT $startIndex, $rowsPerPage";
}

$filteredRowCount = mysqli_num_rows(mysqli_query($conn, $query));

$result = mysqli_query($conn, $query . $queryLimit);

$data = array();


$shitje_query = "SELECT * FROM shitje";
$result2 = mysqli_query($conn, $shitje_query);

$pagesat_query = "SELECT * FROM pagesat";
$result3 = mysqli_query($conn, $pagesat_query);




while ($row = mysqli_fetch_array($result)) {
    $invoiceDate = date_create($row['data']);
    $formattedDate = date_format($invoiceDate, 'Y-m-d');

    $invoiceId = $row['fatura'];
    $totalSalesQuery = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$invoiceId'");
    $totalSales = mysqli_fetch_array($totalSalesQuery);

    $totalPaymentsQuery = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$invoiceId'");
    $totalPayments = mysqli_fetch_array($totalPaymentsQuery);

    $clientId = $row['emri'];
    $clientQuery = "SELECT * FROM klientet WHERE id=" . $clientId . " ";
    $clientResult = $conn->query($clientQuery);
    $client = mysqli_fetch_array($clientResult);

    $remainingAmount =  $totalSales['sum'] - $totalPayments['sum'];
    $rowArray = array();
    $rowArray[] = $row["emrifull"] . ' | ' . $row["emri"];
    $rowArray[] = $client["emriart"];
    $rowArray[] = $row["fatura"];
    $rowArray[] = $formattedDate;
    $rowArray[] = $totalSales["sum"];
    $rowArray[] = $totalPayments['sum'];
    $rowArray[] = $remainingAmount;
    $rowArray[] = '<a class="btn btn-primary btn-sm" href="sales.php?invoice=' . $invoiceId . '" target="_blank"><i class="ti-pencil"></i></a>
                     <a class="btn btn-success btn-sm" target="_blank" href="invoice.php?invoice=' . $invoiceId . '"><i class="ti-printer"></i></a> 
                     <a type="button" name="delete" class="btn btn-danger btn-xs delete" id="' . $invoiceId . '"><i class="ti-trash"></i> </a> ';

    $data[] = $rowArray;
}

function getTotalDataCount($conn)
{
    $countQuery = "SELECT COUNT(*) as total FROM fatura";
    $countResult = mysqli_query($conn, $countQuery);
    $countData = mysqli_fetch_assoc($countResult);
    return $countData['total'];
}

$output = array(
    "draw"           => intval($_POST["draw"]),
    "recordsTotal"   => getTotalDataCount($conn),
    "recordsFiltered" => $filteredRowCount,
    "data"           => $data
);

echo json_encode($output);
