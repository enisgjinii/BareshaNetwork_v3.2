<?php
ob_start();
include 'partials/header.php';
include 'modalPayment.php';
include 'loan_modal.php';
include 'invoices_trash_modal.php';
require_once 'vendor/autoload.php';


$config = require_once 'second_config.php';

$client = initializeGoogleClient($config);

if (isset($_GET['code'])) {
  handleAuthentication($client);
}

function initializeGoogleClient($config)
{
  $client = new Google_Client();
  $client->setClientId($config['client_id']);
  $client->setClientSecret($config['client_secret']);
  $client->setRedirectUri($config['redirect_uri']);
  $client->setAccessType('offline');
  $client->setApprovalPrompt('force');

  $client->addScope([
    'https://www.googleapis.com/auth/youtube',
    'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/youtubepartner',
    'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
    'https://www.googleapis.com/auth/yt-analytics.readonly'
  ]);

  return $client;
}

function handleAuthentication($client)
{
  try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    $youtube = new Google\Service\YouTube($client);
    $channels = $youtube->channels->listChannels('snippet', ['mine' => true]);
    $channel = $channels->items[0];
    $channelId = $channel->id;
    $channelName = $channel->snippet->title;

    if (isset($token['refresh_token'])) {
      $refreshToken = $token['refresh_token'];
      storeRefreshTokenInDatabase($refreshToken, $channelId, $channelName);
    }

    $_SESSION['refresh_token'] = $refreshToken;

    echo "<script>console.log('Refresh Token: " . json_encode($refreshToken) . "');</script>";
    echo "<script>console.log('Channel ID: $channelId');</script>";
    echo "<script>console.log('Channel Name: $channelName');</script>";

    // Redirect to a different page after authentication
    header('Location: authenticated_channels.php');
    exit;
  } catch (Google\Service\Exception $e) {
    echo '<pre>';
    print_r(json_decode($e->getMessage()));
    echo '</pre>';
  }
}
?>




<?php if (!isset($_SESSION['oauth_uid'])) {
  echo "
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({
              icon: 'error',
              title: 'Qasja u refuzua',
              text: 'Ju nuk keni qasje në këtë faqe. Ju lutemi kontaktoni administratorin.',
              showConfirmButton: false,
              timer: 3000  // Auto close after 3 seconds
          }).then(() => {
              window.location.href = 'index.php'; // Redirect to index.php
          });
      });
  </script>
";
} else {
?>


  <div class="main-panel">
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="container">
          <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page"><a href="invoice.php" class="text-reset" style="text-decoration: none;">
                  Pagesat Youtube ( Version i ri )
                  <span class="badge bg-success rounded-5">v3.3 Punon</span>

                </a></li>
          </nav>
          <div id="alert_message"></div>
          <div class="row mb-4">
            <div>
              <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#newInvoice"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e
                re</button>
              <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#listOfLoansModal"><i class="fi fi-rr-hand-holding-usd fa-lg"></i>&nbsp; Borgjet</button>
              <button style="text-transform: none;" class="input-custom-css px-3 py-2 " data-bs-toggle="modal" data-bs-target="#trashInvoices"><i class="fi fi-rr-delete-document fa-lg"></i>&nbsp; Faturat e fshira</button>
              <a style="text-transform: none;text-decoration: none;" href="<?php echo $client->createAuthUrl(); ?>" class="input-custom-css px-3 py-2">
                <i class="fi fi-brands-youtube fa-lg"></i>&nbsp; Lidh kanal
              </a>



            </div>
          </div>
          <div class="p-5 shadow-sm rounded-5 mb-4 card">

            <!-- <div class="row gap-2 mb-5">
              <div class="col border shadow-3 rounded-5 p-3">
                <?php
                $sql = "SELECT SUM(total_amount) FROM invoices";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);

                $totalAmount = $row['SUM(total_amount)'];
                ?>
                <h3 class="text-center"><?php echo $totalAmount ?> €</h3>
                <h6 class="text-center text-muted mt-3">Totali i nxerrur nga te gjitha faturat e krijuara</h6>
              </div>

              <div class="col border shadow-sm rounded-5 p-3">
                <?php
                $sql = "SELECT SUM(payment_amount) FROM payments";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);

                $totalAmount = $row['SUM(payment_amount)'];
                ?>
                <h3 class="text-center"><?php echo $totalAmount ?> €</h3>
                <h6 class="text-center text-muted mt-3">Totali i nxerrur nga te gjitha pagesat</h6>
              </div>

            </div> -->

            <div class="row">
              <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" style="text-transform: none" id="pills-lista_e_faturave-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_faturave" type="button" role="tab" aria-controls="pills-lista_e_faturave" aria-selected="true">Lista e faturave</button>
                </li>
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
                          <th></th>
                          <th style="font-size: 12px">ID</th>
                          <th style="font-size: 12px">Numri i faturës</th>
                          <th style="font-size: 12px">Emri i Klientit</th>
                          <th style="font-size: 12px">Pershkrimi</th>
                          <!-- <th style="font-size: 12px">Shuma e përgjithshme</th> -->
                          <th style="font-size: 12px">Shuma e përgjithshme <br><br> pas perqindjes</th>
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
  <?php } ?>
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
        // responsive:true,
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
            extend: "pdf",
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
          }, {
            text: '<i class="fi fi-rr-trash fa-lg"></i>&nbsp;&nbsp; Fshij',
            className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
            action: function() {
              const selectedIds = [];

              // Iterate over the checkboxes and get the selected IDs
              $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).data('id'));
              });

              // Check if any checkboxes are selected
              if (selectedIds.length > 0) {
                // Show SweetAlert2 confirmation dialog
                Swal.fire({
                  icon: 'warning',
                  title: 'Konfirmo Fshirjen',
                  text: 'A jeni i sigurt që dëshironi të fshini elementet e zgjedhura?',
                  showCancelButton: true,
                  confirmButtonText: 'Po, Fshij',
                  cancelButtonText: 'Anulo',
                }).then((result) => {
                  if (result.isConfirmed) {
                    // User confirmed, send an AJAX request to delete the selected items
                    $.ajax({
                      url: 'delete_invoice.php',
                      type: 'POST',
                      data: {
                        ids: selectedIds
                      },
                      success: function(response) {
                        // Handle the response from the server (if needed)
                        Swal.fire({
                          icon: 'success',
                          title: 'Fshirja u krye me sukses!',
                          text: response,
                        });

                        // Store the current page number
                        const currentPage = table.page.info().page;
                        const currentInvoiceTrash = invoice_trash.page.info().page;

                        // Reload the DataTable and restore the current page
                        table.ajax.reload(function() {
                          table.page(currentPage).draw(false);
                          invoice_trash.page(currentInvoiceTrash).draw(false);
                        });
                      },
                      error: function(error) {
                        console.error('Error deleting items:', error);
                        Swal.fire({
                          icon: 'error',
                          title: 'Gabim gjatë fshirjes',
                          text: 'Dicka shkoi keq gjatë fshirjes. Ju lutem provoni përsëri.',
                        });
                      }
                    });
                  }
                });
              } else {
                // Alert if no checkboxes are selected
                Swal.fire({
                  icon: 'info',
                  title: 'Nuk ke zgjedhur elemente',
                  text: 'Ju lutem zgjedhni elemente për t\'i fshirë.',
                });
              }
            },
          },
        ],
        stripeClasses: ["stripe-color"],

        columns: [{
            data: 'id',
            render: function(data, type, row) {
              return '<input type="checkbox" class="row-checkbox" data-id="' + data + '">';
            }
          },
          {
            data: 'id',
          },
          {
            data: 'invoice_number',
            render: function(data, type, row) {
              return '<a class="input-custom-css px-3 py-2" style="text-decoration: none;" href="complete_invoice.php?id=' + row.id + '">' + data + '</a>';
            }
          },
          {
            data: 'customer_name',
            render: function(data, type, row) {
              // Assuming 'customer_loan' is the field you want to display
              var dotValue = parseFloat(row.customer_loan);

              // Conditionally render the dot based on the value
              if (!isNaN(dotValue) && dotValue !== 0) {
                // Use a span element to represent the dot and add a title attribute for the tooltip
                var dotColor = dotValue > 0 ? 'red' : 'green';
                var dotHtml = '<span class="dot" style="background-color: ' + dotColor + '" title="' + dotValue.toFixed(2) + ' €' + '"></span>';
                return '<div style="position: relative;">' + dotHtml + '<br><br>' + data + '</div>';
              } else {
                // Return the customer name without the dot for NaN or zero values
                return data;
              }
            }
          },
          {
            data: 'item'
          },

          // {
          //   data: 'total_amount'
          // },
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
                '<a href="complete_invoice.php?id=' + row.id + '" class="btn btn-outline-primary  py-2 mx-1">Edito</a></div>';
            }
          }
        ],
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
          // Payment amount exceeds remaining amount
          // Display an error message or disable the "Make Payment" button
          // You can customize this part to your preference.

          // Display an error message:
          $('#paymentAmountError').text('Shuma e pagesës nuk mund të kalojë shumën e obligimit.');

          // Disable the "Make Payment" button
          $('#submitPayment').prop('disabled', true);
        } else {
          // Payment amount is within the acceptable range
          // Clear the error message and enable the "Make Payment" button

          // Clear the error message
          $('#paymentAmountError').text('');

          // Enable the "Make Payment" button
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
                title: 'Pagesa dështoi',
                text: 'Pagesa për ID-në e faturës: ' + invoiceId + ' ka dështuar. Ju lutemi provoni përsëri.',
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000 // Adjust the timer for how long the toast message will be displayed
              });

              console.log('Pagesa dështoi: ' + response);
            }
          },
          error: function(error) {
            console.log('Gabim në kryerjen e pagesës: ' + error);
          }
        });
      });


      var invoice_trash = $('#invoices_trash').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
          "<'row'<'col-md-12'tr>>" +
          "<'row'<'col-md-6'><'col-md-6'p>>",
        buttons: [{
            extend: "pdfHtml5",
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            titleAttr: "Eksporto tabelen ne formatin PDF",
            className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
            filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for PDF
          },
          {
            extend: "copyHtml5",
            text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            titleAttr: "Kopjo tabelen ne formatin Clipboard",
            className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
            filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for Copy
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
            filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for Excel
          },
          {
            extend: "print",
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: "Printo tabel&euml;n",
            className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
            filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for Print
          },
        ],

        ajax: {
          url: 'invoices_trash_server.php',
          type: 'POST',
        },
        columns: [{
            data: 'invoice_number'
          },
          {
            data: 'client_name'
          }, // Update to use the client_name field

          {
            data: 'item'
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
            data: 'created_date'
          },
          // {
          //   data: null,
          //   render: function(data, type, row) {
          //     return '<button class="btn btn-outline-success rounded-5 shadow-sm py-2 btn-sm restore-btn" data-id="' + row.id + '"><i class="fi fi-rr-undo fa-lg"></i>&nbsp;&nbsp;Rikthe</button>';
          //   }
          // }
        ],
        order: [],
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
        fixedHeader: true,
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color']
      });

      function getCurrentDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        return yyyy + mm + dd;
      }

      $('#invoices_trash tbody').on('click', '.restore-btn', function() {
        var invoiceId = $(this).data('id');

        // Send an AJAX request to restore the invoice
        $.ajax({
          url: 'restore_invoice.php', // Create a PHP file for restoring invoices
          type: 'POST',
          data: {
            id: invoiceId
          },
          success: function(response) {
            // Parse the JSON response
            var result = JSON.parse(response);

            if (result.success) {
              // Show success message with SweetAlert2
              Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: result.message,
                timer: 3000,
                showConfirmButton: false
              }).then(function() {


                // Store the current page number
                currentPage = invoice_trash.page.info().page;
                currentTablePage = table.page.info().page;



                // Reload the DataTable and restore the current page
                invoice_trash.ajax.reload(function() {
                  invoice_trash.page(currentPage).draw(false);
                  table.page(currentTablePage).draw(false);
                });


              });
            } else {
              // Show error message with SweetAlert2
              Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: result.message
              });
            }

          },
          error: function(error) {
            console.error('Error restoring invoice:', error);
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