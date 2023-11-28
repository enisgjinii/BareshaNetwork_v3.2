<?php

// Include the database connection
include 'conn-d.php';

// Get the ID from the URL or any other source
$invoiceId = $_GET['id']; // Assuming you pass the ID through the URL, e.g., print_invoice.php?id=1

// Prepare and execute the SQL query
$sql = "SELECT * FROM invoices WHERE invoice_number = '$invoiceId'";
$result = $conn->query($sql);

// Check if there is a result
if ($result->num_rows > -1) {
    // Fetch data from the result set
    $row = $result->fetch_assoc();
    $customerID = $row['customer_id'];
    $sql2 = "SELECT * FROM klientet WHERE id = '$customerID'";

    $result2 = $conn->query($sql2);
    $clientRow = $result2->fetch_assoc();
    $customerName = $clientRow['emri'];
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
        <link rel="icon" type="image/png" href="images/brand-icon.png">

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
                /* background-color: #fff; */
                /* border-radius: 5px; */
                /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); */
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
                    max-width: 1000px;
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
            <div class="card">
                <div class="card-header bg-black"></div>
                <div class="card-body">

                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <img src="images/brand-icon.png" alt="Company Logo" class="logo" style="width: 100px;">
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-xl-12">

                                <ul class="list-unstyled float-end">
                                    <li style="font-size: 30px; color: red;">Baresha Network</li>
                                    <li>8RVC+762, R118, Shiroke, Suhareke</li>
                                    <li>+383 (049) 605 655</li>
                                    <li>info@baresha_network.com</li>
                                </ul>
                            </div>
                        </div>

                        <div class="row text-center">
                            <h3 class="text-uppercase text-center mt-3" style="font-size: 40px;">Fatura</h3>
                            <p><?php echo $row['invoice_number']; ?></p>
                        </div>

                        <div class="row mx-3">
                            <?php
                            // Reset the data seek pointer to the beginning of the result set
                            $result->data_seek(0);
                            ?>

                            <table class="sales-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Emërtimi</th>
                                        <th>Çmimi</th>
                                        <th>Perqindja</th>
                                        <th>Shuma</th>
                                        <th>Mbetja</th>
                                        <th>Totali</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Loop through the result set
                                    $totalAmount = 0;
                                    while ($row = $result->fetch_assoc()) {
                                        $percentageQuery = "SELECT * FROM klientet WHERE id = " . $row['customer_id'];
                                        $percentageResult = $conn->query($percentageQuery);
                                        $percentageRow = $percentageResult->fetch_assoc();
                                        $percentage = $percentageRow['perqindja'];

                                        // Calculate the total amount after percentage
                                        $remains = $row['total_amount']  - $row['total_amount_after_percentage'];
                                        // Display data in table rows
                                        echo "<tr>";
                                        echo "<td>{$row['id']}</td>";
                                        echo "<td>{$row['item']}</td>";
                                        echo "<td>{$row['total_amount']}</td>";
                                        echo "<td>{$percentage}%</td>"; // Corrected variable name and added %
                                        echo "<td>{$remains}</td>";
                                        echo "<td>{$row['total_amount_after_percentage']}</td>";
                                        echo "<td>{$row['total_amount']}</td>";
                                        echo "</tr>";

                                        $totalAmount += $row['total_amount_after_percentage'];
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <p class="text-end mt-3" style="font-size: 30px;font-weight: 400;">
                                Total:
                                <span><?php echo $totalAmount; ?> €</span>
                            </p>
                        </div>

                        <div class="row">
                            <div class="" style="margin-left:60px">

                            </div>

                        </div>

                        <div class="row mt-2 mb-5">
                            <p class="fw-bold">Faturuar per: <span class="text-muted"><?php // Get actual date and time
                                                                                        echo $percentageRow['emri']; ?></span></p>
                            <p class="fw-bold">Data e nxerrjes se faturës: <span class="text-muted"><?php // Get actual date and time
                                                                                echo date('d/m/Y'); ?></span></p>
                            <!-- <p class="fw-bold">Nënshkrimi:</p> -->
                        </div>

                    </div>



                </div>
                <div class="card-footer bg-black"></div>
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