<?php

// Include the database connection
include 'conn-d.php';

// Get the ID from the URL or any other source
$invoiceId = $_GET['id']; // Assuming you pass the ID through the URL, e.g., print_invoice.php?id=1

// Prepare and execute the SQL query
$sql = "SELECT * FROM invoices WHERE id = $invoiceId";
$result = $conn->query($sql);

// Check if there is a result
if ($result->num_rows > 0) {
    // Fetch data from the result set
    $row = $result->fetch_assoc();

    // Get the customer name from table klientet column emri where in invoice customer id is going to be same with that 
    $customerName = $row['customer_id'];
    $sql2 = "SELECT * FROM klientet WHERE id = $customerName";
    $result2 = $conn->query($sql2);
    $row2 = $result2->fetch_assoc();
    $customerName = $row2['emri'];


    // Output HTML with invoice details
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- Google Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
        <!-- MDB -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css">
        <!-- UIcons -->
        <link rel="stylesheet" href="assets/uicons-regular-rounded/css/uicons-regular-rounded.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <style>
            * {
                font-family: 'Inter', sans-serif;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: 'Roboto', sans-serif;
                background-color: #f0f0f0;
            }

            .container {
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
                background-color: #fff;
                border-radius: 5px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .header {
                text-align: left;
            }

            .header h1 {
                margin-bottom: 10px;
            }

            .logo {
                text-align: left;
                margin-top: 20px;
            }

            .logo img {
                width: 100px;
            }

            .address {
                text-align: left;

            }

            .address p {
                margin: 5px 0;
            }

            .customer-info {
                margin-top: 20px;
            }

            .invoice-details {
                margin-top: 20px;
            }

            .invoice-details .row {
                justify-content: space-between;
            }

            .sales-table {
                margin-top: 20px;
                width: 100%;
                border-collapse: collapse;
            }

            .sales-table th,
            .sales-table td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }

            .sales-table th {
                background-color: #f2f2f2;
            }

            .total {
                margin-top: 10px;
                text-align: right;
                font-weight: bold;
            }

            .btn-container {
                text-align: right;
                margin-top: 20px;
            }

            @media print {
                .container {
                    max-width: 1900px;
                    margin: 0px auto;
                    padding: 0px;
                    background-color: #fff;
                    border-radius: 0px;
                    box-shadow: 0 0px 0px rgba(0, 0, 0, 0.2);
                }

                .btn-container {
                    display: none;
                }
            }
        </style>
        <title>Fatura - <?php echo $_GET['id']; ?></title>
    </head>

    <body>

        <div class="btn-container fixed-top px-3 py-2 bg-light rounded-5 border ms-1" style="width: fit-content;">
            <a href="invoice.php" class="btn btn-sm btn-light border shadow-0 rounded-5" style="text-transform: none;"><i class="fa fa-angle-left me-2"></i>Kthehu</a>
            <a href="javascript:window.print()" style="text-transform: none;" class="btn btn-sm btn-success shadow-0 rounded-5"><i class="fa fa-print"></i> Printo</a>

            <?php if (!empty($row['emailadd'])) { ?>
                <button type="button" class="btn btn-sm btn-primary shadow-0 rounded-5" style="text-transform: none;" data-mdb-toggle="modal" data-mdb-target="#dergoFaturen">
                    <i class="fi fi-rr-paper-plane"></i>
                    D&euml;rgo</button>
            <?php } else {  ?>

                <a href="#" class="btn btn-sm btn-primary disabled shadow-0 rounded-5" style="text-transform: none;">
                    <i class="fi fi-rr-paper-plane"></i>
                    D&euml;rgo</a>
            <?php } ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="dergoFaturen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title p-0 m-0" id="exampleModalLabel" style="font-size: 16px;">Dërgo faturën <?php echo $_GET['invoice']; ?></h5>
                            <p class="text-muted p-0 m-0" style="font-size: 12px;">Ju po dërgoni një faturë te klienti - <?php echo $row['klient_emri']; ?></p>
                        </div>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="send_email.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="to" style="font-size: 14px;">Email i marrësit</label>
                                <p class="text-muted p-0 m-0" style="font-size: 12px;">Këtu është emaili i marrësit: <span id="recipient-email"></span></p>
                                <input type="email" class="form-control" name="to_email" id="to" required>
                            </div>
                            <div class="form-group my-2">
                                <label for="subject" style="font-size: 14px;">Subjekt</label>
                                <input type="text" class="form-control" name="subject" id="subject" required>
                            </div>
                            <div class="form-group">
                                <label for="message" style="font-size: 14px;">Mesazh</label>
                                <textarea class="form-control" name="message" id="message" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="file" style="font-size: 14px;">Bashkangjit faturën PDF:</label>
                                <input type="file" class="form-control" name="pdf_attachment" id="file" accept=".pdf" required>
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="send" style="text-transform: none;">Dërgo faturën</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // JavaScript to copy the email into the input field and paragraph
            document.addEventListener('DOMContentLoaded', function() {
                // Replace 'row.emailadd' with the actual PHP variable that holds the email address
                var recipientEmail = "<?php echo $row['emailadd']; ?>";
                var invoiceId = "<?php echo $_GET['invoice']; ?>";

                // Update the paragraph and input field with the recipient's email
                document.getElementById('recipient-email').textContent = recipientEmail;
                document.getElementById('to').value = recipientEmail;

                document.getElementById('subject').textContent = invoiceId;
                document.getElementById('subject').value = 'Fatura juaj nga Baresha Network , #' + invoiceId;
            });
        </script>
        <div class="container">
            <div class="row">
                <div class="logo text-left">
                    <img src="images/brand-icon.png" alt="Company Logo">
                </div>
                <div class="col text-start">
                    <h4 class="text-muted text-left my-3">Baresha Network</h4>
                    <div class="address ">
                        <p><i class="fi fi-rr-marker pe-2"></i> 8RVC+762, R118, Shiroke, Suhareke</p>
                        <p><i class="fi fi-rr-envelope pe-2"></i> info@bareshamusic.com</p>
                        <p><i class="fi fi-rr-phone-call pe-2"></i> +383 (049) 605 655</p>
                    </div>
                </div>
                <div class="col text-end">
                    <h4 class="text-muted text-left my-3">Numri i fatur&euml;s </h4>
                    <div class="address text-end">
                        <p># <?php echo $row['invoice_number']; ?></p>
                    </div>
                </div>
            </div>
            <hr style="border: 1px dashed red;">
            <div class="row">
                <div class="col">
                    <div>

                        <!-- <h1>Invoice Details</h1>
                        <p>Invoice ID: <?php echo $row['id']; ?></p>
                        <p>Customer Name: <?php echo $row['customer_id']; ?></p> -->
                        <?php if (!empty($row['customer_id'])) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Faturuar p&euml;r :</p>
                            <h6 class="text-dark"><?php echo $customerName ?></h6>
                        <?php endif; ?>
                        <?php if (!empty($row['adresa'])) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Adresa :</p>
                            <h6 class="text-dark"><?php echo $row['adresa']; ?></h6>
                        <?php endif; ?>
                        <?php if (!empty($row['emailadd'])) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Email-i :</p>
                            <h6 class="text-dark"><?php echo $row['emailadd']; ?></h6>
                        <?php endif; ?>
                        <?php if (!empty($row['nrtel'])) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Numri i telefonit :</p>
                            <h6 class="text-dark"><?php echo $row['nrtel']; ?></h6>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col text-end">


                    <p class="text-muted m-0 p-0" style="font-size: 12px;">Numri i fatur&euml;s :</p>
                    <h6 class="text-dark"><?php echo $row['invoice_number']; ?></h6>


                    <p class="text-muted m-0 p-0" style="font-size: 12px;">Data e fatur&euml;s :</p>
                    <h6 class="text-dark"><?php echo $row['created_date']; ?></h6>


                    <p class="text-muted m-0 p-0" style="font-size: 12px;">Numri rendit&euml;s :</p>
                    <h6 class="text-dark"><?php echo $row['id']; ?></h6>



                </div>
            </div>
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Em&euml;rtimi</th>
                        <th>Numri i fatures</th>
                        <th>Shuma e pergjitshme</th>
                        <th>Perqindja</th>
                        <th>Shuma e pergjitshme ( pas perqindjes )</th>
                        <th>Pagesat e kryera</th>
                        <!-- 
                        <th>Shuma e pergjitshme</th>
                        <th>Perqindja</th>
                        <th>Shuma e pergjitshme ( pas perqindjes )</th>
                        <th>Shuma e paguar</th>
                        <th>Mbetja</th>
                        <th class="text-right">Totali</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rendori = 1;

                    // Fetch invoice details along with payment details, item, total_amount, and percentage using JOINs
                    $query = "SELECT i.id, i.item, SUM(i.total_amount) as total_amount, SUM(i.total_amount_after_percentage) as total_amount_after_percentage, k.perqindja, p.invoice_id, SUM(p.payment_amount) AS total_payment_amount
              FROM invoices i
              JOIN klientet k ON i.customer_id = k.id
              LEFT JOIN payments p ON i.id = p.invoice_id 
              WHERE i.id = $invoiceId
              GROUP BY i.id";

                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td><?php echo $rendori; ?></td>
                            <td><?php echo $row['item']; ?></td>
                            <td><?php echo $row['invoice_id']; ?></td>
                            <td><?php echo $row['total_amount']; ?></td>
                            <td><?php echo $row['perqindja']; ?></td>
                            <td><?php echo $row['total_amount_after_percentage']; ?></td>
                            <td><?php echo $row['total_payment_amount']; ?></td>
                        </tr>

                    <?php
                        $rendori++;
                    }
                    ?>
                </tbody>



            </table>
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
                        return "<p>$bankInfo</p><hr/><img src='" . $bankLogosDirectory . "$bankInfo.png' alt='$bankInfo Logo' class='rounded-0 img-thumbnail' style='width: 200px;' >";
                }
            }
            ?>

            <table id="paymentHistory" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Emertimi
                        <th>Shuma e pagesës</th>
                        <th>Data e pagesës</th>
                        <th>Informata e bankës</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $increment = 1;
                    $paymentHistorySql = "SELECT * FROM payments WHERE invoice_id = $invoiceId";
                    $paymentHistoryResult = mysqli_query($conn, $paymentHistorySql);

                    while ($payment = mysqli_fetch_assoc($paymentHistoryResult)) {

                        $invoice_query = "SELECT * FROM invoices WHERE id = $payment[invoice_id]";
                        $invoice_result = mysqli_query($conn, $invoice_query);
                        $invoice_row = mysqli_fetch_assoc($invoice_result);
                        
                        echo "<tr>";
                        echo "<td>" . $increment . "</td>";
                        echo "<td>" . $invoice_row["item"] . "</td>";
                        echo "<td>" . $payment["payment_amount"] . "</td>";
                        echo "<td>" . $payment["payment_date"] . "</td>";
                        echo "<td>" . getBankInfoWithLogo($payment["bank_info"]) . "</td>";
                        echo "</tr>";
                        $increment++;
                    }

                    ?>
                </tbody>
            </table>
            <hr style="border: 1px dashed red;">
            <!-- <div class="total">
                <h4>Totali : <?php echo $totalSales['total_amount_after_percentage']; ?> €</h4>
            </div> -->


        </div>
        <!-- MDB -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.js"></script>



    </body>

    </html>
<?php

} else {
    echo "Invoice not found";
}

// Close the database connection
$conn->close();
?>