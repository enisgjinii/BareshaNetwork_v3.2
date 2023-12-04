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
                                    <a style="text-decoration: none;" href="print_invoice.php?id=<?php echo $invoice_number; ?>" class="input-custom-css px-3 py-2"><i class="fi fi-rr-print fa-lg"></i></a>
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
                            <div id="alert_message"></div>

                            <div class="row  mb-3">

                                <ul class="nav nav-pills mb-3 bg-white rounded-5 w-auto mx-2" style="border: 1px solid #dee2e6" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-5 active" id="pills-add_items-tab" data-bs-toggle="pill" data-bs-target="#pills-add_items" type="button" role="tab" aria-controls="pills-add_items" aria-selected="true"><i class="fi fi-rr-plus fa-lg"></i> &nbsp; Shto artikuj</button>
                                    </li>

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-5" id="pills-historiku_i_pagesave-tab" data-bs-toggle="pill" data-bs-target="#pills-historiku_i_pagesave" type="button" role="tab" aria-controls="pills-historiku_i_pagesave" aria-selected="false">
                                            <i class="fi fi-rr-document fa-lg"></i> &nbsp; Historiku i pagesave
                                        </button>
                                    </li>
                                </ul>
                                <div class="row mx-2 mb-3 rounded-5 shadow-sm bordered bg-white py-3">
                                    <div class="col">
                                        <h5 class="mb-4">Informacioni i faturës</h5>
                                        <ul class="list-group">
                                            <?php if (!empty($invoice_number)) { ?>
                                                <li class="list-group-item">
                                                    <p class="text-muted p-0 m-0" style="font-size: 10px;">Numri i faturës</p>
                                                    <p><?php echo $invoice_number ?></p>
                                                </li>
                                            <?php } ?>
                                            <?php if (!empty($invoice_number)) {
                                                $getInvoiceFirstCreateDate = "SELECT created_date FROM invoices WHERE invoice_number = '$invoice_number'";
                                                $getInvoiceFirstCreateDateResult = mysqli_query($conn, $getInvoiceFirstCreateDate);
                                                $k = mysqli_fetch_assoc($getInvoiceFirstCreateDateResult);
                                            ?>
                                                <li class="list-group-item">
                                                    <p class="text-muted p-0 m-0" style="font-size: 10px;">Data e krijimit te kesaj fature</p>
                                                    <p><?php echo $k['created_date'] ?></p>
                                                </li>
                                            <?php } ?>
                                            <?php if (!empty($k['artisti'])) { ?>
                                                <li class="list-group-item">
                                                    <p class="text-muted p-0 m-0" style="font-size: 10px;">Artisti</p>
                                                    <p>
                                                        <?php
                                                        $nameAndEmail = explode("|", $k['artisti']);
                                                        echo $nameAndEmail[0];
                                                        ?>
                                                    </p>
                                                </li>
                                            <?php } ?>
                                            <!-- Add similar checks for other fields -->
                                        </ul>
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-4">Customer Information</h5>
                                        <ul class="list-group">

                                            <?php
                                            // Check if $customer_id is set and not empty
                                            if (isset($customer_id) && !empty($customer_id)) {

                                                // Query to retrieve basic customer information from the "klientet" table
                                                $customerQuery = "SELECT id, emri, emriart,perqindja FROM klientet WHERE id = $customer_id";
                                                $customerResult = mysqli_query($conn, $customerQuery);

                                                // Check if the customer query was successful
                                                if ($customerResult) {
                                                    $customerRow = mysqli_fetch_assoc($customerResult);

                                                    // Query to calculate customer loan from the "yinc" table
                                                    $loanQuery = "SELECT SUM(y.shuma) AS total_shuma, SUM(y.pagoi) AS total_pagoi
                    FROM yinc y 
                    WHERE y.kanali = {$customerRow['id']}
                    GROUP BY y.kanali";

                                                    $loanResult = mysqli_query($conn, $loanQuery);

                                                    // Check if the loan query was successful
                                                    if ($loanResult) {
                                                        $loanRow = mysqli_fetch_assoc($loanResult);

                                                        // Calculate customer loan by subtracting total_pagoi from total_shuma
                                                        $customerLoan = $loanRow['total_shuma'] - $loanRow['total_pagoi'];

                                                        // Display Customer ID
                                                        echo '<li class="list-group-item">
                            <p class="text-muted p-0 m-0" style="font-size: 10px;">ID e klientit</p>
                            <p>' . $customer_id . '</p>
                        </li>';

                                                        // Display Full Name
                                                        echo '<li class="list-group-item">
                            <p class="text-muted p-0 m-0" style="font-size: 10px;">Emri i plotë</p>
                            <p>' . $customerRow["emri"] . '</p>
                        </li>';

                                                        // Display Artistic Name
                                                        echo '<li class="list-group-item">
                            <p class="text-muted p-0 m-0" style="font-size: 10px;">Emri artistik</p>
                            <p>' . $customerRow["emriart"] . '</p>
                        </li>';

                                                        // Display Debt
                                                        echo '<li class="list-group-item">
                            <p class="text-muted p-0 m-0" style="font-size: 10px;">Borgji</p>
                            <p>' . $customerLoan . '</p>
                        </li>';
                                                        // Display Precengate
                                                        echo '<li class="list-group-item">
                            <p class="text-muted p-0 m-0" style="font-size: 10px;">Përqindja</p>
                            <p id="percentage"  name="percentage">' . $customerRow['perqindja'] . '</p>
                        </li>';

                                                        // Add similar entries for other fields if needed
                                                    } else {
                                                        // Handle the loan query error
                                                        echo "Error: " . mysqli_error($conn);
                                                    }
                                                } else {
                                                    // Handle the basic information query error
                                                    echo "Error: " . mysqli_error($conn);
                                                }
                                            }
                                            ?>

                                        </ul>
                                    </div>




                                </div>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-add_items" role="tabpanel" aria-labelledby="pills-add_items-tab">
                                        <div class="card mb-4 shadow-0 border rounded-5 p-4">
                                            <div class="row">
                                                <div class="col-12">
                                                    <form action="add_item.php?id=<?php echo $invoice_id; ?>" method="POST">
                                                        <input type="text" hidden class="form-control rounded-5 shadow-sm py-3" name="invoice_number" value="<?php echo $invoice_number ?>">

                                                        <div class="row">


                                                            <div class="col mb-3">
                                                                <input type="hidden" readonly class="form-control rounded-5 shadow-sm py-3" name="customer_id" value="<?php echo $customer_id; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <!-- <div class="mb-3 row">
                                                                <div class="col mb-3">
                                                                    <input type="text" class="form-control rounded-5 shadow-sm py-3" id="percentage"  name="percentage" value="<?php echo $customer_precentage; ?>" required>
                                                                </div>
                                                            </div> -->
                                                            <div class="col mb-3">
                                                                <label for="item" class="form-label">Përshkrimi:</label>
                                                                <textarea type="text" class="form-control rounded-5 shadow-sm py-3" id="item" name="item" required></textarea>
                                                            </div>
                                                            <div class="col">
                                                                <label for="total_amount" class="form-label">Shuma e përgjithshme:</label>
                                                                <input type="text" class="form-control rounded-5 shadow-sm py-3" id="total_amount" name="total_amount" required>
                                                            </div>
                                                            <div class="col">
                                                                <label for="total_amount_after_percentage" class="form-label">Shuma e përgjithshme pas përqindjes:</label>
                                                                <input type="text" class="form-control rounded-5 shadow-sm py-3" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
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
                                                <div class="my-5">
                                                    <table id="salesHistory" class="table table-bordered" style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Emri i Klientit</th>
                                                                <th>Artikulli</th>
                                                                <th>Shuma e përgjithshme</th>
                                                                <th>Shuma e për. %</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            // Assuming $invoice_number is already defined
                                                            $salesHistorySql = "SELECT * FROM invoices WHERE invoice_number = ?";
                                                            $salesHistoryStmt = mysqli_prepare($conn, $salesHistorySql);

                                                            // Check if the prepare statement was successful
                                                            if ($salesHistoryStmt) {
                                                                mysqli_stmt_bind_param($salesHistoryStmt, "s", $invoice_number);
                                                                mysqli_stmt_execute($salesHistoryStmt);
                                                                $salesHistoryResult = mysqli_stmt_get_result($salesHistoryStmt);

                                                                $rowIndex = 0; // Variable to track the row index
                                                                $totalSum = 0; // Variable to store the sum of total amounts
                                                                $totalSumAfterPrecentage = 0; // Variable to store the sum of total amounts

                                                                while ($sales = mysqli_fetch_assoc($salesHistoryResult)) {
                                                                    $sql_user = "SELECT * FROM klientet WHERE id = ?";
                                                                    $result_user = mysqli_prepare($conn, $sql_user);
                                                                    mysqli_stmt_bind_param($result_user, "s", $sales["customer_id"]);
                                                                    mysqli_stmt_execute($result_user);
                                                                    $result_user = mysqli_stmt_get_result($result_user);
                                                                    $row_user = mysqli_fetch_assoc($result_user);
                                                                    $totalSum += $sales["total_amount"]; // Accumulate total amounts
                                                                    $totalSumAfterPrecentage += $sales["total_amount_after_percentage"]; // Accumulate total amounts
                                                                    echo "<tr>";
                                                                    echo "<td>" . $sales["id"] . "</td>";
                                                                    echo "<td>" . $row_user["emri"] . "</td>";
                                                                    echo "<td>" . $sales["item"] . "</td>";
                                                                    echo "<td>" . $sales["total_amount"] . "</td>";
                                                                    echo "<td>" . $sales["total_amount_after_percentage"] . "</td>";

                                                                    echo "<td>";

                                                                    // Display the delete button
                                                                    if ($rowIndex > 0) { ?>
                                                                        <form method='post' action='delete_item.php'>
                                                                            <button type='button' class='input-custom-css px-3 py-2 rounded-5 shadow-sm py-2' data-bs-toggle='offcanvas' data-bs-target='#editOffcanvas<?php echo $sales["id"]; ?>'>
                                                                                <i class='fi fi-rr-edit '></i> Redakto
                                                                            </button>

                                                                            <input type='hidden' name='invoice_number' value='<?php echo $sales["id"]; ?>'>
                                                                            <button type='submit' name='delete' class='input-custom-css px-3 py-2 rounded-5 shadow-sm py-2' style='text-transform: none;text-decoration: none'><i class='fi fi-rr-trash '></i> Fshij</button>

                                                                        </form>



                                                                        <div class='offcanvas offcanvas-end' tabindex='-1' id='editOffcanvas<?php echo $sales["id"]; ?>' aria-labelledby='editOffcanvasLabel<?php echo $sales["id"]; ?>'>
                                                                            <div class='offcanvas-header border-bottom'>
                                                                                <h5 class='offcanvas-title' id='editOffcanvasLabel$rowIndex'>Redakto shumën e përgjithshme</h5>

                                                                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                                                                            </div>

                                                                            <div class='offcanvas-body'>

                                                                                <form method='post' action='edit_item.php'>
                                                                                    <input type='text' hidden name='invoice_number' value='<?php echo $sales["id"]; ?>'>

                                                                                    <label for='editedTotalAmount<?php echo $rowIndex; ?>' class='form-label'>Shuma e re e përgjithshme:</label>
                                                                                    <input type='text' class='form-control rounded-5 shadow-sm border' id='editedTotalAmount<?php echo $rowIndex; ?>' name='editedTotalAmount' oninput='calculateTotalWithPercentage(<?php echo $rowIndex; ?>)' required>


                                                                                    <br>

                                                                                    <label for='percentage' class='form-label'>Perqindja:</label>
                                                                                    <input type="text" class="form-control rounded-5 shadow-sm py-3" id='percentage2_<?php echo $rowIndex; ?>' name='percentage2' value="<?php echo $customer_precentage; ?>" oninput='calculateTotalWithPercentage(<?php echo $rowIndex; ?>)' required>
                                                                                    <br>


                                                                                    <label for='totalAmountAfterPercentage<?php echo $rowIndex; ?>' class='form-label'>Shuma e re e përgjithshme me %:</label>
                                                                                    <input type='text' class='form-control rounded-5 shadow-sm border' id='totalAmountAfterPercentage<?php echo $rowIndex; ?>' name='totalAmountAfterPercentage' readonly required>


                                                                                    <button type='submit' name='edit' class='input-custom-css px-3 py-2 mt-3 rounded-5 shadow'> <i class="fi fi-rr-edit"></i> Ruaj ndryshimet</button>
                                                                                </form>
                                                                            </div>


                                                                        </div>
                                                                    <?php                                } ?>

                                                                    </td>
                                                                    </tr>

                                                                <?php
                                                                    $rowIndex++;
                                                                } ?>
                                                                <div class="row gap-2 text-center">
                                                                    <div class="card col border rounded-5 shadow-sm my-3 ">
                                                                        <p class="pt-2">Totali i shumës së përgjithshme :</p>
                                                                        <h5 class="pb-1 fw-bold"> <?php echo $totalSum ?> €</p>
                                                                    </div>
                                                                    <div class="card col border rounded-5 shadow-sm my-3">
                                                                        <p class="pt-2">Totali i shumës së përgjithshme pas përqindjes:</p>
                                                                        <h5 class="pb-1 fw-bold"> <?php echo $totalSumAfterPrecentage ?> €</p>
                                                                    </div>
                                                                </div>

                                                            <?php } else {
                                                                // Handle the prepare statement error
                                                                echo "Error: " . mysqli_error($conn);
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                    <script>
                                                        function calculateTotalWithPercentage(row) {
                                                            var totalAmountInput = document.getElementById('editedTotalAmount' + row);
                                                            var percentageInput = document.getElementById('percentage2_' + row);
                                                            var totalAmountAfterPercentageInput = document.getElementById('totalAmountAfterPercentage' + row);

                                                            var totalAmount = parseFloat(totalAmountInput.value);
                                                            var percentage = parseFloat(percentageInput.value);

                                                            console.log('Total Amount:', totalAmount);
                                                            console.log('Percentage:', percentage);

                                                            var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
                                                            totalAmountAfterPercentageInput.value = totalAmountAfterPercentage.toFixed(2);

                                                            console.log('Total Amount After Percentage:', totalAmountAfterPercentage);
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
                var percentage = parseFloat(document.getElementById('percentage').textContent);
                var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
                document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
            });
        </script>