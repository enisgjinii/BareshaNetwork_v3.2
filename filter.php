<?php

include 'conn-d.php';

// Get the SQL query parameter from the GET request
$sql = $_GET['sql'];

// Connect to the database
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Execute the modified query and get the results
$payments = $conn->query($sql);

// Loop through the results and generate the table rows
$tableRows = '';
while ($payment = mysqli_fetch_array($payments)) {
    $invoice_number = $payment['fatura'];
    $invoice_info = $conn->query("SELECT * FROM fatura WHERE fatura='$invoice_number'");
    $invoice_data = mysqli_fetch_array($invoice_info);

    if (!empty($invoice_data)) {
        $client_id = $invoice_data['emri'];
        $client_info = $conn->query("SELECT * FROM klientet WHERE id='$client_id'");
        $client_data = mysqli_fetch_array($client_info);

        $tableRows .= "<tr>";
        $tableRows .= "<td>" . $client_data['emri'] . "</td>";
        $tableRows .= "<td>" . $payment['fatura'] . "</td>";
        $tableRows .= "<td>" . $payment['pershkrimi'] . "</td>";
        $tableRows .= "<td>" . $payment['shuma'] . "</td>";
        $tableRows .= "<td>" . $payment['menyra'] . "</td>";
        $tableRows .= "<td>" . (!empty($payment['kategoria']) ? implode(", ", unserialize($payment['kategoria'])) : '') . "</td>";
        $tableRows .= "<td>" . date("d-m-Y", strtotime($payment['data'])) . "</td>";
        
        $tableRows .= "</tr>";
    }
}

// Close the database connection
$conn->close();

// Return the generated table rows as the response to the AJAX request
echo $tableRows;

?>
