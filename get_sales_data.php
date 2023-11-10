<?php
// get_sales_data.php

// Assuming you have a database connection established in this file

include 'conn-d.php';

$invoiceId = $_GET['invoice_id'];

$sales_query = "SELECT fatura,totali FROM shitje WHERE fatura='$invoiceId'";
$sales_result = mysqli_query($conn, $sales_query);
$sales_data = array();

while ($sales_row = mysqli_fetch_assoc($sales_result)) {
    $sales_data[] = $sales_row;
}

// Output the sales data as HTML
foreach ($sales_data as $sales_row) {
    echo $sales_row['fatura'] . "<br>";
}
?>
