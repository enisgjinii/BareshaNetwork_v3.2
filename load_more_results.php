
<?php

if (isset($_POST['offset'])) {
    $offset = intval($_POST['offset']);
    $limit = 500; // The same initial limit

    $fatura_query = "SELECT f.*, k.emri AS klient_emri FROM fatura AS f 
                     LEFT JOIN klientet AS k ON f.emri = k.id 
                     ORDER BY f.id DESC LIMIT $limit OFFSET $offset";
    $fatura_result = $conn->query($fatura_query);

    while ($row = mysqli_fetch_assoc($fatura_result)) {
        $invoice_id = $row['id'];
        $invoice_date = $row['data'];
        $invoice_fatura = $row['fatura'];
        $invoice_klient_emri = $row['klient_emri'];

        $shitjet_query = "SELECT * FROM shitje WHERE fatura = '$invoice_fatura'";
        $shitjet_result = $conn->query($shitjet_query);
        $shitjet_data = $shitjet_result->fetch_assoc();

        $pagesat_query = "SELECT * FROM pagesat WHERE fatura = '$invoice_fatura'";
        $pagesat_result = $conn->query($pagesat_query);
        $pagesat_data = $pagesat_result->fetch_assoc();
?>
        <div class="col-12 mb-4">
            <div class="p-3 card rounded-5 shadow-sm">
                <div class="row">
                    <div class="col">
                        <?php echo $invoice_klient_emri; ?>
                    </div>
                    <div class="col">
                        <?php echo $invoice_date;  ?>
                    </div>
                    <div class="col">
                        <?php echo $invoice_fatura; ?>
                    </div>
                    <div class="col">
                        <a class="btn btn-primary btn-sm rounded-5 text-white" style="text-transform: none;" href="fatura_details.php?invoice_fatura=<?php echo $invoice_fatura; ?>">Detaje</a>
                    </div>
                    <div class="col">
                        <?php echo $shitjet_data['totali']; ?>
                    </div>
                    <div class="col">
                        <?php echo $pagesat_data['shuma']; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
?>