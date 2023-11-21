<h2 class="mt-5">Historiku i shitjeve</h2>
<table id="paymentHistory" class="table table-bordered">
    <thead>
        <tr>
            <th>Numri i faturës</th>
            <th>Emri i Klientit</th>
            <th>Artikulli</th>
            <th>Shuma e përgjithshme</th>
            <th>Shuma e përgjithshme ( pas perqindjes )</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include 'conn-d.php';

        $paymentHistorySql = "SELECT * FROM invoices WHERE invoice_number = $invoice_number";
        $paymentHistoryResult = mysqli_query($conn, $paymentHistorySql);
        while ($payment = mysqli_fetch_assoc($paymentHistoryResult)) {

            $sql_user = "SELECT * FROM klientet WHERE id = $payment[customer_id]";
            $result_user = mysqli_query($conn, $sql_user);
            $row_user = mysqli_fetch_assoc($result_user);
            echo "<tr>";
            echo "<td>" . $payment["invoice_number"] . "</td>";
            echo "<td>" . $row_user["emri"] . "</td>";
            echo "<td>" . $payment["item"] . "</td>";
            echo "<td>" . $payment["total_amount"] . "</td>";
            echo "<td>" . $payment["total_amount_after_percentage"] . "</td>";
        }
        ?>
    </tbody>
</table>