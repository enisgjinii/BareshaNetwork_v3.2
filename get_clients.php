<?php

header("Content-Type: application/json"); // Set the content type to return JSON
include 'conn-d.php'; // Change this to the correct path of your DB connection file

$clientData = $conn->query("SELECT * FROM klientet WHERE blocked='0'");

$output = [];
while ($row = mysqli_fetch_array($clientData)) {
    // Perform your calculations and conditions here

    $output[] = [
        "id" => $row['id'],
        "pagesaaa" => $pagesaaa,
        "emri_artikullit" => $emri_artikullit,
        "fatura" => $fatura,
        "pagesa_e_mbetur" => $pagesa_e_mbetur,
        "totali" => $totali,
        "pagesa" => $pagesa,
        "data" => $data,
        "shuma" => $shuma,
        "shuma_e_paguar" => $shuma_e_paguar,
        "obli" => $obli
        // Add any other calculations and data points needed
    ];
}

echo json_encode($output);