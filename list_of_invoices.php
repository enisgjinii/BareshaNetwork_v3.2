<?php include 'partials/header.php' ?>



<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Invoice List</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Invoice history</button>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                <table id="invoiceList" class="table table-bordered" data-source="get_invoice.php">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Numri i faturës</th>
                            <th>Emri i Klientit</th>
                            <th>Shuma e përgjithshme</th>
                            <th>Shuma e përgjithshme ( pas perqindjes )</th>
                            <th>Shuma e paguar</th>
                            <th>Obligim</th>
                            <th>Veprimi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                <div class="table-responsive">
                <table id="completePayments" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>Emri i Klientit</th>
                            <th>ID e faturës</th>
                            <th>Numri i faturës</th>
                            <th>Shuma totale e pagesës</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        
                        include 'conn-d.php';

                        $sql = "SELECT *, (SELECT SUM(payment_amount) FROM payments p2 WHERE p2.invoice_id = payments.invoice_id) AS total_payment_amount
        FROM payments
        ORDER BY payment_id DESC";


                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $invoice_id = $row["invoice_id"];

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
                                    // echo "Error executing query for invoices: " . mysqli_error($conn);
                                }
                            }
                        }


                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table></div>
            </div>
        </div>
        </div></div></div>
    </div>

    <!-- Modal for displaying invoice details and making payment -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Invoice Details and Payment <br> <span class="text-muted" style="font-size: 12px;">Klikoni në butonin "Bëj pagesën" për të kryer pagesën </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Numri i faturës:</th>
                            <td><span id="invoiceNumber"></span></td>
                        </tr>
                        <tr>
                            <th>Emri i klientit:</th>
                            <td><span id="customerName"></span></td>
                        </tr>
                        <tr>
                            <th>Përshkrimi:</th>
                            <td><span id="item"></span></td>
                        </tr>
                        <tr>
                            <th>Shuma e përgjithshme:</th>
                            <td><span id="totalAmount"></span></td>
                        </tr>
                        <tr>
                            <th>Shuma e paguar:</th>
                            <td><span id="paidAmount"></span></td>
                        </tr>
                        <tr>
                            <th>Obligim:</th>
                            <td><span id="remainingAmount"></span></td>
                        </tr>
                    </table>
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Shkruani shumën e pagesës:</label>
                        <input type="text" class="form-control" id="paymentAmount" required>
                    </div>
                    <input type="hidden" id="invoiceId" name="invoiceId">
                    <button type="button" class="btn btn-primary" id="submitPayment">Bëj pagesën</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS if needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Initialize DataTables with server-side processing -->
    <script>
        function getCustomerName(customerId) {
            var customerName = '';

            $.ajax({
                url: 'get_customer_name.php',
                type: 'POST',
                data: {
                    'customer_id': customerId
                },
                async: false, // Wait for the request to complete
                success: function(response) {
                    customerName = response;
                }
            });

            return customerName;
        }



        $(document).ready(function() {

            var completePayments = $('#completePayments').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "ajax": {
                    "url": "get_history_of_payments.php", // Replace with the actual URL of your server-side PHP script
                    "type": "POST"
                },
                "columns": [{
                        "data": 0
                    },
                    {
                        "data": 1
                    },
                    {
                        "data": 2
                    },
                    {
                        "data": 3
                    },
                    {
                        "data": 4
                    }
                ]
            });
            var table = $('#invoiceList').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'get_invoices.php', // Change to the correct server-side script URL
                    type: 'POST'
                },
                searching: true,
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'invoice_number'
                    },
                    {
                        data: 'customer_id',
                        render: function(data) {
                            return getCustomerName(data);
                        }
                    },
                    
                    {
                        data: 'total_amount'
                    },
                    {
                        data: 'total_amount_after_percentage'
                    },
                    {
                        data: 'paid_amount'
                    },
                    {
                        data: 'remaining_amount',
                        render: function(data, type, row) {
                            const remainingAmount = row.total_amount_after_percentage - row.paid_amount;
                            return remainingAmount.toFixed(2);
                        }
                    },
                    {
                        data: 'actions',
                        render: function(data, type, row) {
                            return '<div> ' +
                                '<a href="#" class="btn btn-success open-payment-modal" ' +
                                'data-id="' + row.id + '" ' +
                                'data-invoice-number="' + row.invoice_number + '" ' +
                                'data-customer-id="' + row.customer_id + '" ' + // Use data-customer-id
                                'data-item="' + row.item + '" ' +
                                'data-total-amount="' + row.total_amount_after_percentage + '" ' +
                                'data-paid-amount="' + row.paid_amount + '" ' +
                                'data-remaining-amount="' + (row.total_amount_after_percentage - row.paid_amount) + '"><i class="fi fi-rr-money-bill-wave"></i> Paguaj</a> ' +
                                '<a href="delete_invoice.php?id=' + row.invoice_number + '" class="btn btn-danger mx-1"><i class="fi fi-rr-trash"></i> Fshije</a>' + '<a href="complete_invoice.php?id=' + row.id + '" class="btn btn-primary mx-1"><i class="fi fi-rr-edit"></i> Edito</a></div>';
                        }
                    }
                ]
            });

            var currentPage = 0; // Initialize with page 1 (zero-based index)

            // Handle click on "Paguaj" button
            $(document).on('click', '.open-payment-modal', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var invoiceNumber = $(this).data('invoice-number');
                var customerId = $(this).data('customer-id'); // Use data-customer-id
                var item = $(this).data('item');
                var totalAmount = $(this).data('total-amount');
                var paidAmount = $(this).data('paid-amount');
                var remainingAmount = $(this).data('remaining-amount');

                var customerName = getCustomerName(customerId); // Fetch customer name

                // Populate modal content
                $('#invoiceId').val(id);
                $('#invoiceNumber').text(invoiceNumber);
                $('#customerName').text(customerName);
                $('#item').text(item);
                $('#totalAmount').text(totalAmount);
                $('#paidAmount').text(paidAmount);
                $('#remainingAmount').text(remainingAmount.toFixed(2));

                // Show the modal
                $('#paymentModal').modal('show');
            });

            $('#paymentAmount').on('input', function() {
                var paymentAmount = parseFloat($(this).val());
                var remainingAmount = parseFloat($('#remainingAmount').text());

                if (paymentAmount > remainingAmount) {
                    // Display an error message or disable the "Make Payment" button
                    // You can customize this part to your preference.
                    // For example, displaying an error message:
                    $('#paymentAmountError').text('Payment amount cannot exceed the remaining amount.');
                    $('#submitPayment').prop('disabled', true);
                } else {
                    // Clear the error message and enable the "Make Payment" button
                    $('#paymentAmountError').text('');
                    $('#submitPayment').prop('disabled', false);
                }
            }); // Handle click on "Make Payment" button within the modal
            $('#submitPayment').click(function(e) {
                e.preventDefault();
                var invoiceId = $('#invoiceId').val();
                var paymentAmount = $('#paymentAmount').val();

                // Use AJAX to submit payment and update the DataTable
                $.ajax({
                    url: 'make_payment.php',
                    method: 'POST',
                    data: {
                        invoiceId: invoiceId,
                        paymentAmount: paymentAmount
                    },
                    success: function(response) {
                        if (response === 'success') {
                            // Payment was successful, close the modal
                            $('#paymentModal').modal('hide');

                            // Apply a CSS class to the table row
                            var row = table.row('#' + invoiceId).node();
                            $(row).addClass('success-row');

                            // Set a timeout to remove the CSS class after 3 seconds
                            setTimeout(function() {
                                $(row).removeClass('success-row');
                            }, 3000);

                            // Show SweetAlert2 success toast message
                            Swal.fire({
                                title: 'Pagesa është kryer me sukses',
                                icon: 'success',
                                showConfirmButton: false,
                                position: 'top-end',
                                toast: true,
                                timer: 5000, // Display the toast for 5 seconds
                                html: '<div style="text-align: left;">' +
                                    '<p>Numri i Faturës: ' + invoiceId + '</p>' +
                                    '<p>Emri i Klientit: ' + customerName.textContent + '</p>' +
                                    '<p>Shuma e Paguar: ' + paymentAmount + ' €</p>' +
                                    '</div>',
                                width: '500px'
                            });

                            // Store the current page number
                            currentPage = table.page.info().page;

                            // Reload the DataTable and restore the current page
                            table.ajax.reload(function() {
                                table.page(currentPage).draw(false);
                            });

                            // Refresh the DataTable
                            completePayments.ajax.reload();



                        } else {
                            // Handle any payment failure and display an error message

                            // Show SweetAlert2 error toast message
                            Swal.fire({
                                title: 'Payment Failed',
                                text: 'Payment for invoice ID: ' + invoiceId + ' has failed. Please try again.',
                                icon: 'error',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000 // Adjust the timer for how long the toast message will be displayed
                            });

                            console.log('Payment failed: ' + response);
                        }
                    },
                    error: function(error) {
                        console.log('Error making payment: ' + error);
                    }
                });
            });


        });
    </script>

</body>

</html>