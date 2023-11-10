
<?php
$category = $_GET['category'];
$payments = $conn->query("SELECT * FROM pagesat WHERE kategoria='$category' ORDER BY data DESC");
$table_html = "";
while ($payment = mysqli_fetch_array($payments)) {
    $invoice_number = $payment['fatura'];
    $invoice_info = $conn->query("SELECT * FROM fatura WHERE fatura='$invoice_number'");
    $invoice_data = mysqli_fetch_array($invoice_info);
    if (!empty($invoice_data)) {
        $client_id = $invoice_data['emri'];
        $client_info = $conn->query("SELECT * FROM klientet WHERE id='$client_id'");
        $client_data = mysqli_fetch_array($client_info);
        $table_html .= "<tr>";
        $table_html .= "<td><input type='checkbox'></td>";
        $table_html .= "<td>" . $client_data['emri'] . "</td>";
        $table_html .= "<td>" . $payment['fatura'] . "</td>";
        $table_html .= "<td>" . $payment['pershkrimi'] . "</td>";
        $table_html .= "<td>" . $payment['shuma'] . "</td>";
        $table_html .= "<td>" . $payment['menyra'] . "</td>";
        $table_html .= "<td>" . date("d-m-Y", strtotime($payment['data'])) . "</td>";
        $table_html .= "<td>" . $payment['kategoria'] . "</td>";
        $table_html .= "<td><a class='btn btn-light shadow-2 border border-1' target='_blank' href='fatura.php?invoice=" . $payment['fatura'] . "'><i class='fi fi-rr-print'></i></a></td>";
        $table_html .= "</tr>";
    }
}
echo $table_html;
?>