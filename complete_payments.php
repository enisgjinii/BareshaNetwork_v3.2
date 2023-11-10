<!DOCTYPE html>
<html lang="en">

<head>
    <title>Complete Payments</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.7/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.11.0/r-2.5.0/rg-1.4.1/rr-1.4.1/sc-2.3.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.7/af-2.6.0/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/cr-1.7.0/date-1.5.1/fc-4.3.0/fh-3.4.0/kt-2.11.0/r-2.5.0/rg-1.4.1/rr-1.4.1/sc-2.3.0/sb-1.6.0/sp-2.2.0/sl-1.7.0/sr-1.3.0/datatables.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <a href="invoice.php" class="btn btn-primary mt-3"> Back</a>
        <h1 class="mb-5 mt-1">Complete Payments</h1>
        <!-- Table for Complete Payments -->
        <table id="completePayments" class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Invoice Number</th>
                    <th>Total Payment Amount</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = mysqli_connect("localhost", "root", "", "bareshao_f");
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                $sql = "SELECT invoice_id, payment_date, SUM(payment_amount) AS total_payment_amount
                FROM payments
                GROUP BY invoice_id
                ORDER BY invoice_id DESC";

                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $invoice_id = $row["invoice_id"];
                        echo $invoice_id;

                        $sql2 = "SELECT customer_id FROM invoices WHERE id = $invoice_id";
                        $result2 = mysqli_query($conn, $sql2);

                        if ($result2 && mysqli_num_rows($result2) > 0) {
                            $row2 = mysqli_fetch_assoc($result2);
                            $customer_name = $row2["customer_id"];

                            $sql3 = "SELECT * FROM klientet WHERE id = $customer_name";
                            $result3 = mysqli_query($conn, $sql3);

                            if ($result3 && mysqli_num_rows($result3) > 0) {
                                $row3 = mysqli_fetch_assoc($result3);
                                echo "<tr>";
                                echo "<td>" . $row3["emri"] . "</td>";
                                echo "<td>" . $row["invoice_id"] . "</td>";
                                echo "<td>" . $row["total_payment_amount"] . "</td>";
                                echo "<td>" . $row["payment_date"] . "</td>";
                                echo "</tr>";
                            } else {
                               
                            }
                        } else {
                            echo "Error executing query for invoices: " . mysqli_error($conn);
                        }
                    }
                }


                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <!-- Include Bootstrap and DataTables JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#completePayments').DataTable(

                {
                    dom: 'Bflrtip',
                    ordering: false,
                }
            );
        });
    </script>
</body>

</html>