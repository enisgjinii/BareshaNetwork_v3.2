<?php

include 'conn-d.php';
// Retrieve the search query from the AJAX request
$searchQuery = $_GET["query"];

// Modify the existing query to filter the data based on the search query
$invoice_query = "SELECT * FROM fatura WHERE emri LIKE '%$searchQuery%' ORDER BY id DESC";
$invoice_result = mysqli_query($conn, $invoice_query);

// Generate the HTML for the filtered data
$html = "";
while ($invoice = mysqli_fetch_assoc($invoice_result)) {
    // Retrieve the name of the client
    $invoiceId = $invoice['emri'];
    $get_name_of_client = "SELECT emri, emriart FROM klientet WHERE id='$invoiceId'";
    $get_name_of_client_result = mysqli_query($conn, $get_name_of_client);
    $get_name_of_client_row = mysqli_fetch_assoc($get_name_of_client_result);

    // Calculate the total amount of loan
    $total_amount_of_loan = 0;
    $loan_query = "SELECT shuma,pagoi FROM yinc WHERE kanali='$invoiceId'";
    $loan_result = mysqli_query($conn, $loan_query);
    while ($loan_row = mysqli_fetch_assoc($loan_result)) {
        $total_amount_of_loan_calculated = $loan_row['shuma'] - $loan_row['pagoi'];
        if ($total_amount_of_loan_calculated > 0) {
            $total_amount_of_loan += $total_amount_of_loan_calculated;
        }
    }

    $html .= "<tr>";
    $html .= "<td>";
    $html .= $invoice['emri'] . " | " . $get_name_of_client_row['emri'] . " | " . $get_name_of_client_row['emriart'];
    $html .= "</td>";
    $html .= "<td>";
    $html .= "<table class='table table-bordered'>";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>ID (tabela fatura)</th>";
    $html .= "<th>ID (tabela shitje)</th>";
    $html .= "<th>ID (tabela pagesat)</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    $html .= "<tr>";
    $html .= "<td>" . $invoice['fatura'] . "</td>";
    $html .= "<td>";
    if (isset($sales_data[$invoice['fatura']])) {
        foreach ($sales_data[$invoice['fatura']] as $sales_row) {
            $html .= $sales_row['fatura'] . "<br>";
        }
    }
    $html .= "</td>";
    $html .= "<td>";
    if (isset($payed_data[$invoice['fatura']])) {
        foreach ($payed_data[$invoice['fatura']] as $payed_row) {
            $html .= $payed_row['fatura'] . "<br>";
        }
    }
    $html .= "</td>";
    $html .= "</tr>";
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</td>";
    $html .= "<td>" . $invoice['data'] . "</td>";
    $html .= "<td>" . $total_amount_of_loan . "</td>";
    $html .= "<td>";
    $sum = 0;
    if (isset($sales_data[$invoice['fatura']])) {
        foreach ($sales_data[$invoice['fatura']] as $sales_row) {
            $html .= $sales_row['totali'] . "<br>";
            $sum += $sales_row['totali'];
        }
    }
    $html .= "Sum: " . $sum;
    $html .= "</td>";
    $html .= "<td>";
    $payed = 0;
    if (isset($payed_data[$invoice['fatura']])) {
        foreach ($payed_data[$invoice['fatura']] as $payed_row) {
            $html .= $payed_row['shuma'] . "<br>";
            $payed += $payed_row['shuma'];
        }
    }
    $html .= "Payed: " . $payed;
    $html .= "</td>";
    $html .= "</tr>";
}

// Return the HTML to the AJAX request
echo $html;
