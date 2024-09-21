<?php
include 'conn-d.php';
// Function to safely retrieve GET parameters
function get_safe_param($conn, $param)
{
    return isset($_GET[$param]) ? $conn->real_escape_string($_GET[$param]) : '';
}
// Retrieve the Invoice ID securely
$invoiceId = get_safe_param($conn, 'id');
// Prepare and execute the SQL query for invoice details using prepared statements for security
$stmt = $conn->prepare("SELECT * FROM invoices WHERE invoice_number = ?");
$stmt->bind_param("s", $invoiceId);
$stmt->execute();
$result = $stmt->get_result();
// Check if the invoice exists
if ($result->num_rows > 0) {
    // Fetch invoice data
    $invoiceRow = $result->fetch_assoc();
    $customerID = $invoiceRow['customer_id'];
    $idOfInvoice = $invoiceRow['id'];
    // Get subaccount_name
    $subaccountName = $invoiceRow['subaccount_name'];
    // Get customer details securely
    $stmt2 = $conn->prepare("SELECT * FROM klientet WHERE id = ?");
    $stmt2->bind_param("i", $customerID);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    // Check if customer exists
    if ($result2->num_rows > 0) {
        $clientRow = $result2->fetch_assoc();
        $customerName = htmlspecialchars($clientRow['emri']);
        $customerEmail = htmlspecialchars($clientRow['emailadd']);
        $customerAddress = htmlspecialchars($clientRow['adresa']);
        $customerPhone = htmlspecialchars($clientRow['nrtel']);
        $customerPercentage = floatval($clientRow['perqindja']); // Assuming this is the base percentage
    } else {
        // Handle case where customer is not found
        $customerName = "N/A";
        $customerEmail = "N/A";
        $customerAddress = "N/A";
        $customerPhone = "N/A";
        $customerPercentage = 0;
    }
    // Get all payments for this invoice
    $stmtPayments = $conn->prepare("SELECT * FROM payments WHERE invoice_id = ? ORDER BY payment_date");
    $stmtPayments->bind_param("i", $idOfInvoice);
    $stmtPayments->execute();
    $resultForPayments = $stmtPayments->get_result();
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
        <!-- Fav Icon -->
        <link rel="icon" type="image/png" href="images/brand-icon.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <style>
            /* [CSS Styles remain unchanged] */
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

            /* Additional Styles for Payment Cards */
            .payment-card {
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 15px;
                margin-bottom: 10px;
                background-color: #fff;
            }

            .payment-title {
                font-size: 18px;
                font-weight: bold;
            }

            .payment-description {
                font-size: 16px;
            }
        </style>
        <title>Fatura - <?php echo htmlspecialchars($invoiceId); ?></title>
    </head>

    <body>
        <div class="btn-container fixed-top px-3 py-2 bg-light rounded-5 border ms-1" style="width: fit-content;">
            <a href="invoice.php" class="btn btn-sm btn-light border shadow-0 rounded-5" style="text-transform: none;">
                <i class="fa fa-angle-left me-2"></i>Kthehu
            </a>
            <a href="javascript:window.print()" style="text-transform: none;" class="btn btn-sm btn-success shadow-0 rounded-5">
                <i class="fa fa-print"></i> Printo
            </a>
            <button type="button" class="btn btn-sm btn-primary shadow-0 rounded-5" style="text-transform: none;" data-mdb-toggle="modal" data-mdb-target="#dergoFaturen">
                <i class="fi fi-rr-paper-plane"></i> Dërgo
            </button>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="dergoFaturen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title p-0 m-0" id="exampleModalLabel" style="font-size: 16px;">Dërgo faturën <?php echo htmlspecialchars($invoiceId); ?></h5>
                        </div>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="send_email.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="to" style="font-size: 14px;">Email i marrësit</label>
                                <p class="text-muted p-0 m-0" style="font-size: 12px;">
                                    Këtu është emaili i marrësit: <span id="recipient-email"></span>
                                </p>
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
                var recipientEmail = "<?php echo $customerEmail; ?>";
                var invoiceId = "<?php echo htmlspecialchars($invoiceId); ?>";
                // Update the paragraph and input field with the recipient's email
                document.getElementById('recipient-email').textContent = recipientEmail;
                document.getElementById('to').value = recipientEmail;
                document.getElementById('subject').value = 'Fatura juaj nga Baresha Network, #' + invoiceId;
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
                    <h4 class="text-muted text-left my-3">Numri i faturës </h4>
                    <div class="address text-end">
                        <p># <?php echo htmlspecialchars($invoiceRow['invoice_number']); ?></p>
                    </div>
                    <h4 class="text-muted text-left my-3">Lloji i faturës </h4>
                    <div>
                        <?php
                        if ($invoiceRow['type'] == 'grupor') {
                            // Display "Fatura e ndarë" badge
                            echo "<span class='badge bg-success text-white'>Fatura e ndarë</span> ";

                            // Display the subaccount name badge, ensuring it's properly escaped
                            $subaccountName = htmlspecialchars($invoiceRow['subaccount_name'], ENT_QUOTES, 'UTF-8');
                            echo "<span class='badge bg-primary text-white'>{$subaccountName}</span>";
                        } elseif ($invoiceRow['type'] == "individual") {
                            // Display "Fatura individuale" badge
                            echo "<span class='badge bg-primary text-white'>Fatura individuale</span>";
                        } else {
                            // Display a message if the invoice type is not defined
                            echo "Lloji i faturës nuk është përcaktuar.";
                        }
                        ?>
                    </div>


                </div>
            </div>
            <hr style="border: 1px dashed red;">
            <div class="row">
                <div class="col">
                    <div>
                        <?php if (!empty($customerName)) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Faturuar për :</p>
                            <h6 class="text-dark"><?php echo $customerName; ?></h6>
                        <?php endif; ?>
                        <?php if (!empty($customerAddress)) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Adresa :</p>
                            <h6 class="text-dark"><?php echo $customerAddress; ?></h6>
                        <?php endif; ?>
                        <?php if (!empty($customerEmail)) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Email-i :</p>
                            <h6 class="text-dark"><?php echo $customerEmail; ?></h6>
                        <?php endif; ?>
                        <?php if (!empty($customerPhone)) : ?>
                            <p class="text-muted m-0 p-0" style="font-size: 12px;">Numri i telefonit :</p>
                            <h6 class="text-dark"><?php echo $customerPhone; ?></h6>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col text-end">
                    <p class="text-muted m-0 p-0" style="font-size: 12px;">Numri i faturës :</p>
                    <h6 class="text-dark"><?php echo htmlspecialchars($invoiceRow['invoice_number']); ?></h6>
                    <p class="text-muted m-0 p-0" style="font-size: 12px;">Data e faturës :</p>
                    <h6 class="text-dark"><?php echo htmlspecialchars($invoiceRow['created_date']); ?></h6>
                    <p class="text-muted m-0 p-0" style="font-size: 12px;">Numri renditës :</p>
                    <h6 class="text-dark"><?php echo htmlspecialchars($invoiceRow['id']); ?></h6>
                </div>
            </div>
            <?php
            // Reset the data seek pointer to the beginning of the result set
            $result->data_seek(0);
            ?>
            <!-- Updated Sales Table with Rounded and Formatted Values -->
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Emërtimi</th>
                        <th>Çmimi (USD)</th>
                        <th>Totali pas konvertimit (EUR)</th>
                        <th>Shuma (USD)</th>
                        <th>Mbetja (EUR)</th>
                        <th>Totali (EUR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Initialize total amount for footer calculation
                    $totalAmountEUR = 0;
                    // Loop through the result set
                    while ($row = $result->fetch_assoc()) {
                        // Get percentage from klientet table
                        $percentage = $customerPercentage; // Retrieved earlier
                        // Apply rounding and formatting
                        // Round down the total_amount in USD
                        $total_amount_usd = floor($row['total_amount']);
                        $formatted_total_amount_usd = number_format($total_amount_usd, 2) . " $";
                        // Round down the total_amount_in_eur
                        $total_amount_eur = floor($row['total_amount_in_eur']);
                        $formatted_total_amount_eur = number_format($total_amount_eur, 2) . " €";
                        // Calculate remains (total_amount - total_amount_after_percentage) in USD
                        $remains_usd = floor($row['total_amount'] - $row['total_amount_after_percentage']);
                        $formatted_remains_usd = number_format($remains_usd, 2) . " $";
                        // Round down the total_amount_in_eur_after_percentage
                        $total_amount_eur_after_percentage = floor($row['total_amount_in_eur_after_percentage']);
                        $formatted_total_amount_eur_after_percentage = number_format($total_amount_eur_after_percentage, 2) . " €";
                        // Update total amount for footer
                        $totalAmountEUR += $total_amount_eur_after_percentage;
                        // Display data in table rows
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['item']) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_total_amount_usd) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_total_amount_eur) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_remains_usd) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_total_amount_eur_after_percentage) . "</td>";
                        echo "<td>" . htmlspecialchars($formatted_total_amount_eur_after_percentage) . "</td>"; // Assuming this is intentional
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <div class="total">
                <strong>Totali i Faturës: <?php echo number_format($totalAmountEUR, 2); ?> €</strong>
            </div>
        </div>
        <!-- MDB -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.js"></script>
    </body>

    </html>
<?php
} else {
    // Handle case where invoice is not found
    echo "Fatura nuk u gjet.";
}
$conn->close();
?>