<?php include 'partials/header.php'; ?>
<?php
if (isset($_GET["id"])) {
    $invoice_id = $_GET["id"];
    require_once "conn-d.php";
    $sql = "SELECT * FROM invoices WHERE id = $invoice_id";
    $result = mysqli_query($conn, $sql);
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
        if (isset($customer_id) && !empty($customer_id)) {
            $customerQuery = "SELECT id, emri, emriart,perqindja FROM klientet WHERE id = $customer_id";
            $customerResult = mysqli_query($conn, $customerQuery);
            if ($customerResult) {
                $customerRow = mysqli_fetch_assoc($customerResult);
                $loanQuery = "SELECT SUM(y.shuma) AS total_shuma, SUM(y.pagoi) AS total_pagoi
FROM yinc y 
WHERE y.kanali = {$customerRow['id']}
GROUP BY y.kanali";
                $loanResult = mysqli_query($conn, $loanQuery);
                if ($loanResult) {
                    $loanRow = mysqli_fetch_assoc($loanResult);
                }
                // $customerLoan = $loanRow['total_shuma'] - $loanRow['total_pagoi'];
            }
        }
    }
?>

    <body>
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="container-fluid">
                    <a href="invoice.php" class="input-custom-css px-3 py-2 d-flex align-items-center " style="text-decoration: none;width: fit-content;">
                        <i class="fi fi-rr-arrow-small-left fa-lg"></i>
                        <span class="ml-2">Kthehu</span>
                    </a>
                    <div class="row mt-3 mb-3">
                        <div class="col">
                            <a style="text-decoration: none;" href="print_invoice.php?id=<?php echo $invoice_number; ?>" class="input-custom-css px-3 py-2"><i class="fi fi-rr-print fa-lg"></i></a>
                            <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#newPayment"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Kryej pagesë</button>
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
                                                    <input type="text" pattern="\d+(\.\d+)?" class="form-control rounded-5 border border-2 py-3" id="payment_amount" name="payment_amount" required>
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
                                                        <option value="PayPal">PayPal</option>
                                                        <option value="Ria">Ria</option>
                                                        <option value="Money Gram"> Money Gram</option>
                                                        <option value="Western Union">Western Union</option>
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
                    <div class="row mb-3">
                        <ul class="nav nav-tabs mb-3 bg-white w-auto mx-2 px-2 pb-2 rounded-5" style="border: 1px solid #dee2e6" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-add_items-tab" data-bs-toggle="pill" data-bs-target="#pills-add_items" type="button" role="tab" aria-controls="pills-add_items" aria-selected="true"><i class="fi fi-rr-plus fa-lg"></i> &nbsp; Shto artikuj</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-historiku_i_pagesave-tab" data-bs-toggle="pill" data-bs-target="#pills-historiku_i_pagesave" type="button" role="tab" aria-controls="pills-historiku_i_pagesave" aria-selected="false">
                                    <i class="fi fi-rr-document fa-lg"></i> &nbsp; Historiku i pagesave
                            </button>
                            </li>
                        </ul>
                        <p id="percentage" name="percentage" hidden>
                            <?php echo $customer_precentage; ?>
                        </p>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-add_items" role="tabpanel" aria-labelledby="pills-add_items-tab">
                                <div class="card mb-4 shadow-0 border rounded-5 p-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <form action="api/post_methods/post_add_item.php?id=<?php echo $invoice_id; ?>" method="POST">
                                                <input type="text" hidden class="form-control rounded-5 border border-2 py-3" name="invoice_number" value="<?php echo $invoice_number ?>">
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <input type="hidden" readonly class="form-control rounded-5 border border-2 py-3" name="customer_id" value="<?php echo $customer_id; ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <label for="item" class="form-label">Përshkrimi:</label>
                                                        <textarea type="text" class="form-control rounded-5 border border-2 py-3" id="item" name="item" required></textarea>
                                                    </div>
                                                    <div class="col">
                                                        <label for="total_amount" class="form-label">Shuma e përgjithshme:</label>
                                                        <input type="text" class="form-control rounded-5 border border-2 py-3" id="total_amount" name="total_amount" required>
                                                    </div>
                                                    <div class="col">
                                                        <label for="total_amount_after_percentage" class="form-label">Shuma e përgjithshme pas përqindjes:</label>
                                                        <input type="text" class="form-control rounded-5 border border-2 py-3" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="mb-3 col" hidden>
                                                            <label for="created_date" class="form-label">Data e krijimit te fatures:</label>
                                                            <input type="date" class="form-control rounded-5 border border-2 py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                        </div>
                                                        <div class="mb-3 col">
                                                            <label for="invoice_status" class="form-label">Gjendja e fatures:</label>
                                                            <select class="form-control rounded-5 border border-2 py-3 shadow-sm py-3" id="invoice_status" name="invoice_status" required>
                                                                <option value="Rregullt" selected>Rregullt</option>
                                                                <option value="Parregullt">Parregullt</option>
                                                            </select>
                                                            <script>
                                                                new Selectr('#invoice_status', {
                                                                    searchable: true,
                                                                })
                                                            </script>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="input-custom-css px-3 py-2">Shto</button>
                                            </form>
                                        </div>
                                        <div class="my-5">
                                            <table id="salesHistory" class="table table-striped table-bordered" style="width: 100%;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Emri i Klientit</th>
                                                        <th>Artikulli</th>
                                                        <th>Shuma e përgjithshme USD</th>
                                                        <th>Shuma e për. % USD</th>
                                                        <th>Shuma e përgjithshme EUR</th>
                                                        <th>Shuma e për. % EUR</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Assuming $invoice_number is already defined and sanitized
                                                    $salesHistorySql = "SELECT * FROM invoices WHERE invoice_number = ?";
                                                    $salesHistoryStmt = mysqli_prepare($conn, $salesHistorySql);

                                                    if ($salesHistoryStmt) {
                                                        mysqli_stmt_bind_param($salesHistoryStmt, "s", $invoice_number);
                                                        mysqli_stmt_execute($salesHistoryStmt);
                                                        $salesHistoryResult = mysqli_stmt_get_result($salesHistoryStmt);

                                                        $rowIndex = 0; 
                                                        $totalSumUSD = 0; 
                                                        $totalSumAfterPercentageUSD = 0;
                                                        $totalSumEUR = 0;
                                                        $totalSumAfterPercentageEUR = 0; 

                                                        while ($sales = mysqli_fetch_assoc($salesHistoryResult)) {
                                                            
                                                            $sql_user = "SELECT * FROM klientet WHERE id = ?";
                                                            $result_user_stmt = mysqli_prepare($conn, $sql_user);
                                                            mysqli_stmt_bind_param($result_user_stmt, "i", $sales["customer_id"]);
                                                            mysqli_stmt_execute($result_user_stmt);
                                                            $result_user = mysqli_stmt_get_result($result_user_stmt);
                                                            $row_user = mysqli_fetch_assoc($result_user);

                                                            $totalSumUSD += $sales["total_amount"];
                                                            $totalSumAfterPercentageUSD += $sales["total_amount_after_percentage"];
                                                            $totalSumEUR += $sales["total_amount_in_eur"];
                                                            $totalSumAfterPercentageEUR += $sales["total_amount_in_eur_after_percentage"];

                                                            
                                                            $formattedTotalUSD = number_format($sales["total_amount"], 2) . " USD";
                                                            $formattedTotalAfterPercentageUSD = number_format($sales["total_amount_after_percentage"], 2) . " USD";
                                                            $formattedTotalEUR = number_format($sales["total_amount_in_eur"], 2) . " EUR";
                                                            $formattedTotalAfterPercentageEUR = number_format($sales["total_amount_in_eur_after_percentage"], 2) . " EUR";

                                                            echo "<tr>";
                                                            echo "<td>" . htmlspecialchars($sales["id"]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row_user["emri"]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($sales["item"]) . "</td>";
                                                            echo "<td>" . $formattedTotalUSD . "</td>";
                                                            echo "<td>" . $formattedTotalAfterPercentageUSD . "</td>";
                                                            echo "<td>" . $formattedTotalEUR . "</td>";
                                                            echo "<td>" . $formattedTotalAfterPercentageEUR . "</td>";
                                                            echo "<td>";
                                                    ?>
                                                            <div class="d-flex">
                                                                <!-- Edit Button -->
                                                                <button type='button' class='input-custom-css px-3 py-2 me-1' data-bs-toggle='offcanvas' data-bs-target='#editOffcanvas<?php echo $sales["id"]; ?>' title="Redakto">
                                                                    <i class='fi fi-rr-edit'></i>
                                                                </button>
                                                                <!-- Delete Form -->
                                                                <form method='post' action='delete_item.php' onsubmit="return confirm('A jeni të sigurtë që dëshironi të fshini këtë artikull?');">
                                                                    <input type='hidden' name='invoice_number' value='<?php echo htmlspecialchars($sales["id"]); ?>'>
                                                                    <button type='submit' name='delete' class='input-custom-css px-3 py-2' title="Fshij">
                                                                        <i class='fi fi-rr-trash'></i>
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            <!-- Edit Offcanvas -->
                                                            <div class='offcanvas offcanvas-end' tabindex='-1' id='editOffcanvas<?php echo $sales["id"]; ?>' aria-labelledby='editOffcanvasLabel<?php echo $sales["id"]; ?>'>
                                                                <div class='offcanvas-header'>
                                                                    <h5 class='offcanvas-title' id='editOffcanvasLabel<?php echo $sales["id"]; ?>'>Redakto Shumën</h5>
                                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                                </div>
                                                                <div class='offcanvas-body'>
                                                                    <form method='post' action='edit_item.php'>
                                                                        <input type='hidden' name='invoice_id' value='<?php echo htmlspecialchars($sales["id"]); ?>'>

                                                                        <!-- USD Amounts -->
                                                                        <h6 class="mt-3">USD Shuma</h6>
                                                                        <div class="mb-3">
                                                                            <label for='editedTotalAmountUSD<?php echo $rowIndex; ?>' class='form-label'>Shuma e Re e Përgjithshme USD:</label>
                                                                            <input type='number' step='0.01' class='form-control' id='editedTotalAmountUSD<?php echo $rowIndex; ?>' name='editedTotalAmountUSD' value='<?php echo htmlspecialchars($sales["total_amount"]); ?>' oninput='calculateTotalWithPercentageUSD(<?php echo $rowIndex; ?>)' required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for='percentageUSD<?php echo $rowIndex; ?>' class='form-label'>Perqindja (%) USD:</label>
                                                                            <input type="number" step="0.01" class="form-control" id='percentageUSD<?php echo $rowIndex; ?>' name='percentageUSD' value="<?php echo htmlspecialchars($customer_percentage); ?>" oninput='calculateTotalWithPercentageUSD(<?php echo $rowIndex; ?>)' required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for='totalAmountAfterPercentageUSD<?php echo $rowIndex; ?>' class='form-label'>Shuma Pas Përqindjes USD:</label>
                                                                            <input type='number' step='0.01' class='form-control' id='totalAmountAfterPercentageUSD<?php echo $rowIndex; ?>' name='totalAmountAfterPercentageUSD' value='<?php echo htmlspecialchars($sales["total_amount_after_percentage"]); ?>' readonly required>
                                                                        </div>

                                                                        <!-- EUR Amounts -->
                                                                        <h6 class="mt-4">EUR Shuma</h6>
                                                                        <div class="mb-3">
                                                                            <label for='editedTotalAmountEUR<?php echo $rowIndex; ?>' class='form-label'>Shuma e Re e Përgjithshme EUR:</label>
                                                                            <input type='number' step='0.01' class='form-control' id='editedTotalAmountEUR<?php echo $rowIndex; ?>' name='editedTotalAmountEUR' value='<?php echo htmlspecialchars($sales["total_amount_in_eur"]); ?>' oninput='calculateTotalWithPercentageEUR(<?php echo $rowIndex; ?>)' required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for='percentageEUR<?php echo $rowIndex; ?>' class='form-label'>Perqindja (%) EUR:</label>
                                                                            <input type="number" step="0.01" class="form-control" id='percentageEUR<?php echo $rowIndex; ?>' name='percentageEUR' value="<?php echo htmlspecialchars($customer_percentage); ?>" oninput='calculateTotalWithPercentageEUR(<?php echo $rowIndex; ?>)' required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for='totalAmountAfterPercentageEUR<?php echo $rowIndex; ?>' class='form-label'>Shuma Pas Përqindjes EUR:</label>
                                                                            <input type='number' step='0.01' class='form-control' id='totalAmountAfterPercentageEUR<?php echo $rowIndex; ?>' name='totalAmountAfterPercentageEUR' value='<?php echo htmlspecialchars($sales["total_amount_in_eur_after_percentage"]); ?>' readonly required>
                                                                        </div>

                                                                        <button type='submit' name='edit' class='input-custom-css px-3 py-2 mt-3'> <i class="fi fi-rr-edit"></i> Ruaj Ndryshimet</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                    <?php
                                                            echo "</td>";
                                                            echo "</tr>";
                                                            $rowIndex++;
                                                        }
                                                    } else {
                                                        // Handle the prepare statement error
                                                        echo "<tr><td colspan='8' class='text-center text-danger'>Error: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <th colspan="3" class="text-end">Totali:</th>
                                                        <th><?php echo number_format($totalSumUSD, 2); ?> USD</th>
                                                        <th><?php echo number_format($totalSumAfterPercentageUSD, 2); ?> USD</th>
                                                        <th><?php echo number_format($totalSumEUR, 2); ?> EUR</th>
                                                        <th><?php echo number_format($totalSumAfterPercentageEUR, 2); ?> EUR</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                            <!-- JavaScript Functions for Calculations -->
                                            <script>
                                                function calculateTotalWithPercentageUSD(row) {
                                                    var totalAmountInput = document.getElementById('editedTotalAmountUSD' + row);
                                                    var percentageInput = document.getElementById('percentageUSD' + row);
                                                    var totalAmountAfterPercentageInput = document.getElementById('totalAmountAfterPercentageUSD' + row);

                                                    var totalAmount = parseFloat(totalAmountInput.value) || 0;
                                                    var percentage = parseFloat(percentageInput.value) || 0;

                                                    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
                                                    totalAmountAfterPercentageInput.value = totalAmountAfterPercentage.toFixed(2);
                                                }

                                                function calculateTotalWithPercentageEUR(row) {
                                                    var totalAmountInput = document.getElementById('editedTotalAmountEUR' + row);
                                                    var percentageInput = document.getElementById('percentageEUR' + row);
                                                    var totalAmountAfterPercentageInput = document.getElementById('totalAmountAfterPercentageEUR' + row);

                                                    var totalAmount = parseFloat(totalAmountInput.value) || 0;
                                                    var percentage = parseFloat(percentageInput.value) || 0;

                                                    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
                                                    totalAmountAfterPercentageInput.value = totalAmountAfterPercentage.toFixed(2);
                                                }
                                            </script>

                                        </div>
                                    </div>
                                </div>
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
                                            <th>Përshkrimi</th>
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
                                            echo "<td>" . html_entity_decode($payment["description"]) . "</td>";
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
                    $('#paymentHistory').DataTable({
                        initComplete: function() {
                            var btns = $(".dt-buttons");
                            btns.addClass("").removeClass("dt-buttons btn-group");
                            var lengthSelect = $("div.dataTables_length select");
                            lengthSelect.addClass("form-select");
                            lengthSelect.css({
                                width: "auto",
                                margin: "0 8px",
                                padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                                lineHeight: "1.5",
                                border: "1px solid #ced4da",
                                borderRadius: "0.25rem",
                            });
                        },
                    });
                    $('#salesHistory').DataTable({
                        responsive:true,
                        initComplete: function() {
                            var btns = $(".dt-buttons");
                            btns.addClass("").removeClass("dt-buttons btn-group");
                            var lengthSelect = $("div.dataTables_length select");
                            lengthSelect.addClass("form-select");
                            lengthSelect.css({
                                width: "auto",
                                margin: "0 8px",
                                padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                                lineHeight: "1.5",
                                border: "1px solid #ced4da",
                                borderRadius: "0.25rem",
                            });
                        },
                    });
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
        ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>
    </body>

    </html>
    <script>
        document.getElementById('total_amount').addEventListener('input', function() {
            // Calculate Total Amount after Percentage when Total Amount changes
            var totalAmount = parseFloat(this.value);
            var percentage = parseFloat(document.getElementById('percentage').textContent);
            var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
            document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
        });
    </script>