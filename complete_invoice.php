<?php include 'partials/header.php'; ?>
<?php
if (isset($_GET["id"])) {
    $invoice_id = $_GET["id"];

    require_once "conn-d.php";

    // Retrieve the current invoice details
    $sql = "SELECT * FROM invoices WHERE id = $invoice_id";
    $result = mysqli_query($conn, $sql);

    // Retrieve the customer details
    $sql2 = "SELECT * FROM klientet WHERE id = (SELECT customer_id FROM invoices WHERE id = $invoice_id)";
    $result2 = mysqli_query($conn, $sql2);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $row2 = mysqli_fetch_assoc($result2);

        $invoice_number = $row["invoice_number"];
        $invouce_status = $row["status"];
        $customer_id = $row2["id"];
        $customer_name = $row2["emri"];
        $customer_precentage = $row2["perqindja"];
?>

        <body>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="container-fluid">

                        <div class="container">
                            <a href="invoice.php" class="input-custom-css px-3 py-2 d-flex align-items-center " style="text-decoration: none;width: fit-content;">
                                <i class="fi fi-rr-arrow-small-left fa-lg"></i>
                                <span class="ml-2">Kthehu</span>
                            </a>

                            <!-- <br>
                    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);margin-top: 15px;" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Detajet e fatures</a>
                            </li>


                    </nav> -->
                            <div class="row mt-3 mb-3">
                                <div class="col">
                                    <a style="text-decoration: none;" href="print_invoice.php?id=<?php echo $invoice_id; ?>" class="input-custom-css px-3 py-2"><i class="fi fi-rr-print fa-lg"></i></a>
                                    <!-- Make a modal to make a payment -->
                                    <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#newPayment"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Kryej pagesë</button>

                                    <!-- Modal -->
                                    <form method="post">
                                        <div class="modal fade" id="newPayment" tabindex="-1" aria-labelledby="newPaymentLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="newPaymentLabel">Kryej pagesë të re</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="mb-3">
                                                            <label for="payment_amount" class="form-label">Shuma e pagesës:</label>
                                                            <input type="text" pattern="\d+(\.\d+)?" class="form-control rounded-5 shadow-sm py-3" id="payment_amount" name="payment_amount" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="bank_info" class="form-label">Mënyra e pagesës:</label>
                                                            <select id="bank_info" name="bank_info" class="form-select rounded-5 shadow-sm py-3">
                                                                <option value="cash">Para në duar</option>
                                                                <option value="BankaEkonomike">Banka Ekonomike (Kosovo)</option>
                                                                <option value="BankaKombetareTregtare">Banka Kombëtare Tregtare (Albania)</option>
                                                                <option value="BankaPerBiznes">Banka për Biznes (Kosovo)</option>
                                                                <option value="NLBKomercijalnaBanka">NLB Komercijalna banka (Slovenia)</option>
                                                                <option value="NLBBanka">NLB Banka (Slovenia)</option>
                                                                <option value="ProCreditBank">ProCredit Bank (Germany)</option>
                                                                <option value="RaiffeisenBankKosovo">Raiffeisen Bank Kosovo (Austria)</option>
                                                                <option value="TEBSHA">TEB SH.A. (Turkey)</option>
                                                                <option value="ZiraatBank">Ziraat Bank (Turkey)</option>
                                                                <option value="TurkiyeIsBank">Turkiye Is Bank (Turkey)</option>
                                                            </select>
                                                        </div>

                                                        <select id="type_of_pay" name="type_of_pay" class="form-select rounded-5 shadow-sm py-3">
                                                            <option value="Biznes">Biznes</option>
                                                            <option value="Personal">Personal</option>
                                                        </select>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbyll</button>
                                                        <button type="submit" class="input-custom-css px-3 py-2">Kryej pagesë</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <div id="alert_message"></div>
                            <div class="row  mb-3">

                                <ul class="nav nav-pills mb-3 bg-white rounded-5 w-auto mx-2" style="border: 1px solid #dee2e6" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-5 active" id="pills-add_items-tab" data-bs-toggle="pill" data-bs-target="#pills-add_items" type="button" role="tab" aria-controls="pills-add_items" aria-selected="true"><i class="fi fi-rr-plus fa-lg"></i> &nbsp; Shto artikuj</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-5" id="pills-historiku_i_shitjeve-tab" data-bs-toggle="pill" data-bs-target="#pills-historiku_i_shitjeve" type="button" role="tab" aria-controls="pills-historiku_i_shitjeve" aria-selected="false">
                                            <i class="fi fi-rr-document fa-lg"></i> &nbsp; Historiku i shitjeve
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-5" id="pills-historiku_i_pagesave-tab" data-bs-toggle="pill" data-bs-target="#pills-historiku_i_pagesave" type="button" role="tab" aria-controls="pills-historiku_i_pagesave" aria-selected="false">
                                            <i class="fi fi-rr-document fa-lg"></i> &nbsp; Historiku i pagesave
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-add_items" role="tabpanel" aria-labelledby="pills-add_items-tab">
                                        <div class="card mb-4 shadow-0 border rounded-5 p-4">
                                            <h4 class="mt-4">Shtoni më shumë artikuj në faturë</h4>
                                            <p class="text-muted">Ketu shtoni artikuj ne faturë me klikoni buton </p>
                                            <hr>
                                            <form action="add_item.php?id=<?php echo $invoice_id; ?>" method="POST">
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <label for="invoice_number" class="form-label">Numri i faturës</label>
                                                        <input type="text" readonly class="form-control rounded-5 shadow-sm py-3" name="invoice_number" value="<?php echo $row["invoice_number"]; ?>">
                                                    </div>
                                                    <div class="col mb-3">
                                                        <label for="customer_id" class="form-label">Emri i Klientit</label>
                                                        <input type="text" readonly class="form-control rounded-5 shadow-sm py-3" name="customer_id" value="<?php echo $customer_id; ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <label for="item" class="form-label">Përshkrimi:</label>
                                                        <textarea type="text" class="form-control rounded-5 shadow-sm py-3" id="item" name="item" required></textarea>
                                                    </div>
                                                    <div class="col mb-3">
                                                        <label for="percentage" class="form-label">Përqindja:</label>
                                                        <input type="text" class="form-control rounded-5 shadow-sm py-3" id="percentage" name="percentage" value="<?php echo $customer_precentage; ?>" required>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <div class="col">
                                                            <label for="total_amount" class="form-label">Shuma e përgjithshme:</label>
                                                            <input type="text" class="form-control rounded-5 shadow-sm py-3" id="total_amount" name="total_amount" required>
                                                        </div>
                                                        <div class="col">
                                                            <label for="total_amount_after_percentage" class="form-label">Shuma e përgjithshme pas përqindjes:</label>
                                                            <input type="text" class="form-control rounded-5 shadow-sm py-3" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="mb-3 col">
                                                            <label for="created_date" class="form-label">Data e krijimit te fatures:</label>
                                                            <input type="date" class="form-control rounded-5 shadow-sm py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                        </div>
                                                        <div class="mb-3 col">
                                                            <label for="invoice_status" class="form-label">Gjendja e fatures:</label>
                                                            <select class="form-control rounded-5 shadow-sm py-3 shadow-sm py-3" id="invoice_status" name="invoice_status" required>
                                                                <option value="Rregullt" selected>Rregullt</option>
                                                                <option value="Parregullt">Parregullt</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="input-custom-css px-3 py-2">Shto</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-historiku_i_shitjeve" role="tabpanel" aria-labelledby="pills-historiku_i_shitjeve-tab">
                                        <div class="card mb-4 shadow-0 border rounded-5 p-4">
                                            <h4 class="mt-4">Historiku i shitjeve</h4>
                                            <hr>
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
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-historiku_i_pagesave" role="tabpanel" aria-labelledby="pills-historiku_i_pagesave-tab">
                                        <div class="card mb-4 shadow-0 border rounded-5 p-4">
                                            <h4 class="mt-4">Historiku i pagesave</h4>
                                            <hr>
                                            <?php
                                            // Function to generate bank info with logos
                                            function getBankInfoWithLogo($bankInfo)
                                            {
                                                $bankLogosDirectory = "bank_logos/";

                                                switch ($bankInfo) {
                                                    case "cash":
                                                        return "Para në duar";
                                                    case "":
                                                        return "Ky rekord nuk permban nje metode pagese";
                                                    default:
                                                        return "<p>$bankInfo</p><hr/><img src='" . $bankLogosDirectory . "$bankInfo.png' alt='$bankInfo Logo' class='rounded-0' style='width: max-content'>";
                                                }
                                            }
                                            ?>

                                            <table id="paymentHistory" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Shuma e pagesës</th>
                                                        <th>Data e pagesës</th>
                                                        <th>Informata e bankës</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $paymentHistorySql = "SELECT * FROM payments WHERE invoice_id = $invoice_id";
                                                    $paymentHistoryResult = mysqli_query($conn, $paymentHistorySql);

                                                    while ($payment = mysqli_fetch_assoc($paymentHistoryResult)) {
                                                        echo "<tr>";
                                                        echo "<td>" . $payment["payment_amount"] . "</td>";
                                                        echo "<td>" . $payment["payment_date"] . "</td>";
                                                        echo "<td>" . getBankInfoWithLogo($payment["bank_info"]) . "</td>";
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>



                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <script>
                            $(document).ready(function() {
                                $('#paymentHistory').DataTable();
                            });
                        </script>
                <?php
            } else {
                echo "Invoice not found.";
            }

            if (isset($_POST["payment_amount"])) {
                $payment_amount = $_POST["payment_amount"];
                $payment_date = date("Y-m-d"); // You can customize the date format
                $bank_info = $_POST["bank_info"];
                $type_of_pay = $_POST["type_of_pay"];

                // Insert a new payment record into the payments table
                $insert_sql = "INSERT INTO payments (invoice_id, payment_amount, payment_date, bank_info, type_of_pay) VALUES ($invoice_id, $payment_amount, '$payment_date', '$bank_info', '$type_of_pay')";

                if (mysqli_query($conn, $insert_sql)) {
                    // Update the paid amount and status in the invoices table
                    $new_paid_amount = $row["paid_amount"] + $payment_amount;
                    $total_amount = $row["total_amount_after_percentage"];
                    $status = ($new_paid_amount == $total_amount) ? 'I paguar' : 'I pjesshëm';

                    $update_sql = "UPDATE invoices SET paid_amount = $new_paid_amount, status = '$status' WHERE id = $invoice_id";

                    if (mysqli_query($conn, $update_sql)) {
                        // header("Location: complete_invoice.php?id=" . $invoice_id);
                        exit;
                    } else {
                        echo "Error updating invoice: " . mysqli_error($conn);
                    }
                } else {
                    echo "Error inserting payment: " . mysqli_error($conn);
                }
            }

            mysqli_close($conn);
        }
                ?>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

        </body>

        </html>


        </body>

        </html>
        <script>
            document.getElementById('total_amount').addEventListener('input', function() {
                // Calculate Total Amount after Percentage when Total Amount changes
                var totalAmount = parseFloat(this.value);
                var percentage = parseFloat(document.getElementById('percentage').value);
                var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
                document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
            });
        </script>