<?php include 'partials/header.php' ?>
<?php include 'modalPayment.php' ?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 shadow-sm rounded-5 mb-4 card">
          <div class="row mt-4">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" style="text-transform: none" id="pills-lista_e_faturave-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_faturave" type="button" role="tab" aria-controls="pills-lista_e_faturave" aria-selected="true">Lista e faturave</button>
              </li>
              <!-- <li class="nav-item" role="presentation">
                <button class="nav-link" style="text-transform: none" id="pills-krijo_fature-tab" data-bs-toggle="pill" data-bs-target="#pills-krijo_fature" type="button" role="tab" aria-controls="pills-krijo_fature" aria-selected="false">Krijo faturë</button>
              </li> -->
              <li class="nav-item" role="presentation">
                <button class="nav-link" style="text-transform: none" id="pills-lista_e_historise_se_faturave-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_historise_se_faturave" type="button" role="tab" aria-controls="pills-lista_e_historise_se_faturave" aria-selected="false">Lista e historise e faturave te paguara</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" style="text-transform: none" id="pills-lista_e_pagesave-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_pagesave" type="button" role="tab" aria-controls="pills-lista_e_pagesave" aria-selected="false">Lista e pagesave</button>
              </li>
            </ul>
            <hr>
            <div class="tab-content" id="pills-tabContent">
              <div class="tab-pane fade show active" id="pills-lista_e_faturave" role="tabpanel" aria-labelledby="pills-lista_e_faturave-tab">
                <button class="btn btn-primary btn-sm mb-3 text-white" style="text-transform: none" data-bs-toggle="modal" data-bs-target="#newInvoice">Krijos faturë</button>

                <!-- Modal Structure -->
                <div class="modal fade" id="newInvoice" tabindex="-1" aria-labelledby="newInvoiceLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="newInvoiceLabel">Krijoni një faturë të re</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <!-- Your form goes here -->
                        <form action="create_invoice.php" method="POST">
                          <div class="mb-3">
                            <label for="invoice_number" class="form-label">Numri i faturës:</label>
                            <?php
                            // Call the generateInvoiceNumber function to get the invoice number
                            $invoiceNumber = generateInvoiceNumber();
                            ?>
                            <input type="text" class="form-control rounded-5 shadow-sm py-3" id="invoice_number" name="invoice_number" value="<?php echo $invoiceNumber; ?>" required readonly>
                          </div>

                          <div class="mb-3">
                            <label for="customer_id" class="form-label">Emri i klientit:</label>
                            <select class="form-control rounded-5 shadow-sm py-3" id="customer_id" name="customer_id" required>
                              <option value="">Zgjidhni klientin</option>
                              <?php

                              require_once "conn-d.php";

                              $sql = "SELECT id,emri, perqindja FROM klientet ORDER BY id DESC";
                              $result = mysqli_query($conn, $sql);

                              if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                  echo "<option value='" . $row["id"] . "' data-percentage='" . $row["perqindja"] . "'>" . $row["emri"] . "</option>";
                                }
                              }

                              mysqli_close($conn);
                              ?>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label for="item" class="form-label">Përshkrimi:</label>
                            <textarea type="text" class="form-control rounded-5 shadow-sm py-3" id="item" name="item" required> </textarea>
                          </div>
                          <div class="mb-3">
                            <label for="percentage" class="form-label">Përqindja:</label>
                            <input type="text" class="form-control rounded-5 shadow-sm py-3" id="percentage" name="percentage" value="" required>
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

                          <div class="mb-3">
                            <label for="created_date" class="form-label">Data e krijimit të faturës:</label>
                            <input type="date" class="form-control rounded-5 shadow-sm py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                          </div>

                          <button type="submit" class="btn btn-primary btn-sm text-white rounded-5 shadow">Krijo faturë</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="table-responsive">
                  <table id="invoiceList" class="table table-bordered" data-source="get_invoice.php">
                    <thead class="table-light">
                      <tr>
                        <th style="font-size: 12px">ID</th>
                        <th style="font-size: 12px">Numri i faturës</th>
                        <th style="font-size: 12px">Emri i Klientit</th>
                        <th style="font-size: 12px">Shuma e përgjithshme</th>
                        <th style="font-size: 12px">Shuma e përgjithshme <br><br> pas perqindjes )</th>
                        <th style="font-size: 12px">Shuma e paguar</th>
                        <th style="font-size: 12px">Obligim</th>
                        <th style="font-size: 12px">Veprimi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
              <!-- <div class="tab-pane fade" id="pills-krijo_fature" role="tabpanel" aria-labelledby="pills-krijo_fature-tab"></div> -->
              <div class="tab-pane fade" id="pills-lista_e_historise_se_faturave" role="tabpanel" aria-labelledby="pills-lista_e_historise_se_faturave-tab">
                <?php include 'paymentHistory.php' ?>
              </div>
              <div class="tab-pane fade" id="pills-lista_e_pagesave" role="tabpanel" aria-labelledby="pills-lista_e_pagesave-tab">
                <div class="table-responsive">
                  <table id="completePayments2" class="table table-bordered w-100">
                    <thead>
                      <tr>
                        <th>Emri i klientit</th>
                        <th>Numri i faturës</th>
                        <th>Shuma totale e pagesës</th>
                        <th>Data</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>



            </div>
          </div>








        </div>
      </div>
    </div>
  </div>
  <?php function generateInvoiceNumber()
  {
    // Get the current date and time
    $currentDateTime = date("dmYHis");

    // Concatenate the prefix and date/time to create the invoice number
    $invoiceNumber = $currentDateTime;

    return $invoiceNumber;
  }
  ?>

</div>

<script>
  document.getElementById('customer_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var percentage = selectedOption.getAttribute('data-percentage');
    document.getElementById('percentage').value = percentage;

    // Calculate Total Amount after Percentage
    var totalAmount = parseFloat(document.getElementById('total_amount').value);
    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
    document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
  });

  document.getElementById('total_amount').addEventListener('input', function() {
    // Calculate Total Amount after Percentage when Total Amount changes
    var totalAmount = parseFloat(this.value);
    var percentage = parseFloat(document.getElementById('percentage').value);
    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
    document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
  });
</script>

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

    var completePayments2 = $('#completePayments2').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url": "your_server_side_script.php", // Replace with the actual server-side script URL
        "type": "POST"
      },
      "columns": [{
          "data": "customer_name"
        },
        {
          "data": "invoice_id"
        },
        {
          "data": "total_payment_amount"
        },
        {
          "data": "payment_date"
        } // Assuming 'latest_payment_date' is now 'payment_date' from the server-side API
      ],
      "order": [
        [1, "desc"] // Default ordering by invoice_id in descending order
      ],
      "columnDefs": [
        // Format the date column as you need
        {
          "targets": 3, // Assuming 'payment_date' is at index 3 in the columns array
          "render": function(data, type, row) {
            return moment(data).format('YYYY-MM-DD'); // Format the date using Moment.js or any other library you prefer
          }
        }
      ]
    });


    var table = $('#invoiceList').DataTable({
      processing: true,
      serverSide: true,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      ajax: {
        url: 'get_invoices.php', // Change to the correct server-side script URL
        type: 'POST'
      },
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
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      buttons: [{
          extend: "pdfHtml5",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "copyHtml5",
          text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
          titleAttr: "Kopjo tabelen ne formatin Clipboard",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
          titleAttr: "Eksporto tabelen ne formatin Excel",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all",
            },
          },
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
          titleAttr: "Printo tabel&euml;n",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
      ],
      stripeClasses: ["stripe-color"],
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
              '<a href="#" class="btn btn-outline-success mx-1 py-2 open-payment-modal" ' +
              'data-id="' + row.id + '" ' +
              'data-invoice-number="' + row.invoice_number + '" ' +
              'data-customer-id="' + row.customer_id + '" ' + // Use data-customer-id
              'data-item="' + row.item + '" ' +
              'data-total-amount="' + row.total_amount_after_percentage + '" ' +
              'data-paid-amount="' + row.paid_amount + '" ' +
              'data-remaining-amount="' + (row.total_amount_after_percentage - row.paid_amount) + '"> Paguaj</a><br><br> ' +
              '<a href="delete_invoice.php?id=' + row.invoice_number + '" class="btn btn-outline-danger mx-1 py-2">Fshije</a><br><br>' + '<a href="complete_invoice.php?id=' + row.id + '" class="btn btn-outline-primary  py-2 mx-1">Edito</a></div>';
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

<!-- Include Bootstrap JS if needed -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>



<?php include 'partials/footer.php' ?>
</body>

</html>