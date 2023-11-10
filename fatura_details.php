<?php
include 'partials/header.php';

// Assuming you have a database connection established
include 'conn-d.php';

$invoice_fatura = isset($_GET['invoice_fatura']) ? $_GET['invoice_fatura'] : null;

// Retrieve invoice details based on $invoice_fatura
$invoice_query = "SELECT f.*, k.emri AS klient_emri,k.id AS klient_id FROM fatura AS f 
                 LEFT JOIN klientet AS k ON f.emri = k.id 
                 WHERE f.fatura = '$invoice_fatura'";
$invoice_result = $conn->query($invoice_query);


if ($invoice_result && $invoice_result->num_rows > 0) {
    $invoice_data = $invoice_result->fetch_assoc();
    $invoice_date = $invoice_data['data'];
    $invoice_fatura = $invoice_data['fatura'];
    $invoice_klient_emri = $invoice_data['klient_emri'];
    $invoice_klient_id = $invoice_data['klient_id'];

    $shitje_query = "SELECT SUM(totali) AS totali FROM shitje WHERE fatura = '$invoice_fatura'";
    $shitje_result = $conn->query($shitje_query);
    $shitje_totali = $shitje_result->fetch_assoc()['totali'];

    $pagesa_query = "SELECT SUM(shuma) AS shuma FROM pagesat WHERE fatura = '$invoice_fatura'";
    $pagesa_result = $conn->query($pagesa_query);
    $pagesa_shuma = $pagesa_result->fetch_assoc()['shuma'];


    $loan_query = "SELECT * FROM yinc WHERE kanali = '$invoice_klient_id'";
    $loan_result = $conn->query($loan_query);

    $total_loan = 0;

    while ($loan = mysqli_fetch_assoc($loan_result)) {
        $amount = $loan['shuma'] - $loan['pagoi'];
        if ($amount > 0) {
            $total_loan += $amount;
        }
    }
} else {
    // Handle no results or invalid invoice ID
}

?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 mb-4 card rounded-5 shadow-sm">
                    <h3 class="card-title">Detajet e Fatur&euml;s</h3>
                    <table class="table table-bordered">
                        <tr>
                            <th>Klienti</th>
                            <td><?php echo $invoice_klient_emri; ?></td>
                        </tr>
                        <tr>
                            <th>Data</th>
                            <td><?php echo $invoice_date; ?></td>
                        </tr>
                        <tr>
                            <th>Fatura</th>
                            <td><?php echo $invoice_fatura; ?></td>
                        </tr>
                        <tr>
                            <th>Shuma</th>
                            <td><?php echo $shitje_totali; ?></td>
                        </tr>
                        <tr>
                            <th>Pagesa</th>
                            <td><?php echo $pagesa_shuma; ?></td>
                        </tr>
                        <tr>
                            <th>Borgji</th>
                            <td><?php echo $total_loan; ?></td>
                        </tr>
                        status
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'partials/footer.php';
?>