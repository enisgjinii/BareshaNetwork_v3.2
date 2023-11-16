<!DOCTYPE html>
<html>

<head>
    <title>Complete Invoice</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.7/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.11.0/r-2.5.0/rg-1.4.1/rr-1.4.1/sc-2.3.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.7/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.11.0/r-2.5.0/rg-1.4.1/rr-1.4.1/sc-2.3.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.js"></script>
</head>

<body>

    <div class="container">
        <h1 class="mt-5">Detajet e faturës</h1>
        <a href="invoice.php" class="btn btn-primary">Kthehu</a>

        <?php
        if (isset($_GET["id"])) {
            $invoice_id = $_GET["id"];

            // Database connection setup
            $conn = mysqli_connect("localhost", "root", "", "bareshao_f");

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

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
                <table class="table table-bordered mt-4">
                    <tr>
                        <th>Numri i faturës:</th>
                        <td><?php echo $row["invoice_number"]; ?></td>
                    </tr>
                    <tr>
                        <th>Emri i Klientit:</th>
                        <td><?php echo $customer_id; ?></td>
                    </tr>

                    </tr>

                    <tr>
                        <th>Statusi:</th>

                        <?php if ($row["status"] == "I paguar") { ?>
                            <td class="text-success"><?php echo $row["status"]; ?></td>
                        <?php } else if ($row["status"] == "I pjesshëm") { ?>
                            <td class="text-warning"><?php echo $row["status"]; ?></td>
                        <?php } else if ($row["status"] == "I  papaguar") { ?>
                            <td class="text-danger"><?php echo $row["status"]; ?></td>
                        <?php } else { ?>
                            <td class="text-danger"><?php echo $row["status"]; ?></td>
                        <?php } ?>

                    </tr>
                </table>

                <div class="row gap-3">
                    <div class="col-3 card">
                        <h2 class="mt-4">Bëj pagesën</h2>
                        <form action="complete_invoice.php?id=<?php echo $invoice_id; ?>" method="POST">
                            <div class="mb-3">
                                <label for="payment_amount" class="form-label">Shuma e pagesës:</label>
                                <input type="text" class="form-control" id="payment_amount" name="payment_amount" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Bëj pagesën</button>
                        </form>
                    </div>

                    <div class="col card">
                        <h2 class="mt-4">Shtoni më shumë artikuj në faturë</h2>
                        <form action="add_item.php?id=<?php echo $invoice_id; ?>" method="POST">
                            <!-- Your existing form fields for invoice information -->
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="invoice_number" class="form-label">Numri i faturës</label>
                                    <input type="text" readonly class="form-control" name="invoice_number" value="<?php echo $row["invoice_number"]; ?>">
                                </div>
                                <div class="col mb-3">
                                    <label for="customer_id" class="form-label">Emri i Klientit</label>
                                    <input type="text" readonly class="form-control" name="customer_id" value="<?php echo $customer_id; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3">
                                    <label for="item" class="form-label">Item:</label>
                                    <textarea type="text" class="form-control" id="item" name="item" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="percentage" class="form-label">Percentage:</label>
                                    <input type="text" class="form-control" id="percentage" name="percentage" value="<?php echo $customer_precentage; ?>" required>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="total_amount" class="form-label">Total Amount:</label>
                                        <input type="text" class="form-control" id="total_amount" name="total_amount" required>
                                    </div>
                                    <div class="col">
                                        <label for="total_amount_after_percentage" class="form-label">Total Amount after percentage:</label>
                                        <input type="text" class="form-control" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="created_date" class="form-label">Created invoice date:</label>
                                    <input type="date" class="form-control" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Shto</button>
                        </form>
                        <br>
                    </div>


                </div>
                <br>
                <div class="row gap-3">

                    <div class="col card">
                        <!-- Payment History Table -->
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
                        <br>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col card">
                        <!-- Payment History Table -->
                        <h2 class="mt-5">Historiku i pagesave</h2>
                        <table id="paymentHistory" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Shuma e pagesës</th>
                                    <th>Data e pagesës</th>
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
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>

                <br>

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

                // Insert a new payment record into the payments table
                $insert_sql = "INSERT INTO payments (invoice_id, payment_amount, payment_date) VALUES ($invoice_id, $payment_amount, '$payment_date')";

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