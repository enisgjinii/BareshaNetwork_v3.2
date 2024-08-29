<?php
ob_start();
require_once 'vendor/autoload.php';
require_once 'conn-d.php';
require_once 'partials/header.php';
require_once 'modalPayment.php';
require_once 'loan_modal.php';
require_once 'invoices_trash_modal.php';
?>
<link rel="stylesheet" href="invoice_style.css">
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Financat</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Pagesat Youtube</a>
          </li>
        </ol>
      </nav>
      <div class="row mb-2">
        <div>
          <?php
          // Define an array for the buttons
          $buttons = [
            [
              'icon' => 'fi fi-rr-add-document fa-lg',
              'text' => 'Fatur&euml; e re',
              'target' => '#newInvoice',
            ],
            [
              'icon' => 'fi fi-rr-hand-holding-usd fa-lg',
              'text' => 'Borgjet',
              'target' => '#listOfLoansModal',
            ],
            [
              'icon' => 'fi fi-rr-delete-document fa-lg',
              'text' => 'Faturat e fshira',
              'target' => '#trashInvoices',
            ]
          ];
          // Iterate over the array to generate the buttons
          foreach ($buttons as $button) {
            echo '<button style="text-transform: none;" class="input-custom-css px-3 py-2 mx-1" data-bs-toggle="modal" data-bs-target="' . $button['target'] . '">';
            echo '<i class="' . $button['icon'] . '"></i>&nbsp; ' . $button['text'];
            echo '</button>';
          }
          ?>
          <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
            <?php
            // Define an array for the tabs
            $tabs = [
              [
                'id' => 'pills-lista_e_faturave',
                'text' => 'Lista e faturave ( Personale )',
                'active' => true,
              ],
              [
                'id' => 'pills-lista_e_faturave_biznes',
                'text' => 'Lista e faturave ( Biznes )',
                'active' => true,
              ],
              [
                'id' => 'pills-lista_e_splitinvoices',
                'text' => 'Lista e faturave të ndara',
                'active' => true,
              ]
            ];
            // Conditionally add additional tabs if the email doesn't match
            if ($user_info['email'] !== 'lirie@bareshamusic.com') {
              $tabs = array_merge($tabs, [
                [
                  'id' => 'pills-lista_e_faturave_te_kryera',
                  'text' => 'Pagesat e kryera ( Personal )',
                  'active' => false,
                ],
                [
                  'id' => 'pills-lista_e_faturave_te_kryera_biznes',
                  'text' => 'Pagesa e kryera (Biznese)',
                  'active' => false,
                ]
              ]);
            }
            // Iterate over the array to generate the tabs
            foreach ($tabs as $tab) {
              echo '<li class="nav-item" role="presentation">';
              echo '<button class="nav-link rounded-5 ' . ($tab['active'] ? 'active' : '') . '" style="text-transform: none" id="' . $tab['id'] . '-tab" data-bs-toggle="pill" data-bs-target="#' . $tab['id'] . '" type="button" role="tab" aria-controls="' . $tab['id'] . '" aria-selected="' . ($tab['active'] ? 'true' : 'false') . '">' . $tab['text'] . '</button>';
              echo '</li>';
            }
            ?>
          </ul>
        </div>
      </div>
      <div class="p-3 shadow-sm rounded-5 mb-4 card">
        <div class="row">
          <div class="modal fade" id="newInvoice" tabindex="-1" aria-labelledby="newInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="newInvoiceLabel">Krijoni një faturë të re</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark">
                  <form action="api/post_methods/post_create_invoice.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label for="invoice_number" class="form-label">Numri i faturës:</label>
                      <input type="text" class="form-control rounded-5 shadow-sm py-3" id="invoice_number" name="invoice_number" value="<?php echo generateInvoiceNumber(); ?>" required readonly>
                    </div>
                    <div class="mb-3">
                      <label for="customer_id" class="form-label">Emri i klientit:</label>
                      <select class="form-control rounded-5 shadow-sm py-3" id="customer_id" name="customer_id" required>
                        <?php
                        require_once "conn-d.php";
                        $result = mysqli_query($conn, "SELECT id,emri, perqindja FROM klientet ORDER BY id DESC");
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<option value='{$row['id']}' data-percentage='{$row['perqindja']}'>{$row['emri']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="item" class="form-label">Përshkrimi:</label>
                      <textarea class="form-control rounded-5 shadow-sm py-3" id="item" name="item" required></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="type" class="form-label">Lloji:</label>
                      <select class="form-control rounded-5 shadow-sm py-3" name="type" id="type">
                        <option value="individual">Individual</option>
                        <option value="grupor">Grupor</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="percentage" class="form-label">Përqindja:</label>
                      <input type="text" class="form-control rounded-5 shadow-none py-3" id="percentage" name="percentage" required>
                    </div>
                    <div class="row mb-3">
                      <div class="col">
                        <label for="total_amount" class="form-label">Shuma e përgjithshme:</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white border-1 me-2 rounded-start">$</span>
                          <input type="text" class="form-control rounded-end shadow-none py-3" id="total_amount" name="total_amount" required>
                        </div>
                      </div>
                      <div class="col">
                        <label for="total_amount_after_percentage" class="form-label">Shuma e përgjithshme pas përqindjes:</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white border-1 me-2 rounded-start">$</span>
                          <input type="text" class="form-control rounded-end shadow-none py-3" id="total_amount_after_percentage" name="total_amount_after_percentage" required>
                        </div>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col">
                        <label for="total_amount_in_eur" class="form-label">Shuma e përgjithshme - EUR:</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white border-1 me-2 rounded-start">€</span>
                          <input type="text" class="form-control rounded-end shadow-none py-3" id="total_amount_in_eur" name="total_amount_in_eur" required>
                        </div>
                      </div>
                      <div class="col">
                        <label for="total_amount_after_percentage_in_eur" class="form-label">Shuma e përgjithshme pas përqindjes - EUR:</label>
                        <div class="input-group">
                          <span class="input-group-text bg-white border-1 me-2 rounded-start">€</span>
                          <input type="text" class="form-control rounded-end shadow-none py-3" id="total_amount_after_percentage_in_eur" name="total_amount_after_percentage_in_eur" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label for="created_date" class="form-label">Data e krijimit të faturës:</label>
                          <input type="date" class="form-control rounded-5 border border-2 py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                      </div>
                      <div class="col">
                        <div class="mb-3">
                          <label for="invoice_status" class="form-label">Gjendja e fatures:</label>
                          <select class="form-control rounded-5 border border-2 py-3" id="invoice_status" name="invoice_status" required>
                            <option value="Rregullt" selected>Rregullt</option>
                            <option value="Parregullt">Parregullt</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm text-white rounded-5 shadow">Krijo faturë</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <script>
            document.addEventListener('DOMContentLoaded', () => {
              const convertToEUR = async (amount, outputId) => {
                try {
                  const response = await fetch(`https://api.exconvert.com/convert?from=USD&to=EUR&amount=${amount}&access_key=7ac9d0d8-2c2a1729-0a51382b-b85cd112`);
                  const data = await response.json();
                  if (data.result?.EUR) {
                    document.getElementById(outputId).value = data.result.EUR.toFixed(2);
                  } else {
                    console.error('Invalid API response:', data);
                  }
                } catch (error) {
                  console.error('Error:', error);
                }
              };
              const calculateAmountAfterPercentage = () => {
                const totalAmount = parseFloat(document.getElementById("total_amount").value);
                const percentage = parseFloat(document.getElementById("percentage").value);
                if (isNaN(totalAmount) || isNaN(percentage)) {
                  alert("Please enter valid total amount and percentage.");
                  return;
                }
                const amountAfterPercentage = totalAmount - (percentage * totalAmount / 100);
                document.getElementById("total_amount_after_percentage").value = amountAfterPercentage.toFixed(2);
                convertToEUR(totalAmount, "total_amount_in_eur");
                convertToEUR(amountAfterPercentage, "total_amount_after_percentage_in_eur");
              };
              document.getElementById("total_amount").addEventListener("input", calculateAmountAfterPercentage);
              document.getElementById("percentage").addEventListener("input", calculateAmountAfterPercentage);
              document.getElementById('customer_id').addEventListener('change', async function() {
                const customerId = this.value;
                if (customerId) {
                  try {
                    const response = await fetch('api/get_methods/get_check_client_type.php', {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                      },
                      body: new URLSearchParams({
                        customer_id: customerId
                      })
                    });
                    const data = await response.json();
                    document.getElementById('type').value = data.status === 'success' ? data.type : 'individual';
                  } catch (error) {
                    console.error('Error:', error);
                  }
                }
              });
              $("#created_date").flatpickr({
                dateFormat: "Y-m-d",
                maxDate: "today",
                locale: "sq"
              });
              new Selectr('#customer_id', {
                searchable: true,
                width: 300
              });
              new Selectr('#invoice_status', {
                searchable: true,
                width: 300
              });
            });
          </script>
          <div class="tab-content text-dark" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-lista_e_faturave" role="tabpanel" aria-labelledby="pills-lista_e_faturave-tab">
              <div class="table-responsive">
                <div class="row">
                  <!-- Month Filter -->
                  <div class="mb-3 w-25">
                    <label for="monthFilter" class="form-label">Filtro sipas muajit</label>
                    <select id="monthFilter" class="form-select" aria-label="Month Filter">
                      <option value="">Select a month...</option>
                      <?php
                      $sql = "
                SELECT DISTINCT i.item AS month 
                FROM invoices i
                JOIN klientet k ON i.customer_id = k.id
                WHERE k.lloji_klientit = 'Personal'
                ORDER BY i.id DESC
            ";
                      $result = mysqli_query($conn, $sql);
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['month'] . "'>" . $row['month'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <!-- Amount Filter -->
                  <div class="mb-3 w-25">
                    <label for="amountFilter" class="form-label">Filtro sipas shumës</label>
                    <div class="input-group mb-3 rounded-5">
                      <span class="input-group-text rounded-5 me-2">$/€</span>
                      <input type="number" id="amountFilter" class="form-control border border-2 rounded-5" placeholder="Shkruani shumën..." aria-label="Shkruani shumën...">
                    </div>
                  </div>
                </div>
                <hr>
                <!-- DataTable -->
                <table id="invoiceList" class="table table-bordered table-sm" data-source="api/get_methods/get_invoices.php">
                  <thead class="table-light">
                    <tr>
                      <th></th>
                      <th class="text-sm text-dark">Emri i klientit</th>
                      <th class="text-sm text-dark">Pershkrimi</th>
                      <th class="text-sm text-dark">Detajet</th>
                      <th class="text-sm text-dark">Shuma e paguar</th>
                      <th class="text-sm text-dark">F. EUR</th>
                      <th class="text-sm text-dark">Obligim</th>
                      <th class="text-sm text-dark">Veprimi</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="tab-pane fade text-dark" id="pills-lista_e_faturave_biznes" role="tabpanel" aria-labelledby="pills-lista_e_faturave_biznes-tab">
              <div class="table-responsive">
                <table id="invoiceListBiznes" class="table table-bordered w-100 " data-source="get_invoices_biznes.php">
                  <thead class="table-light">
                    <tr>
                      <th></th>
                      <th class="text-dark" style="font-size: 12px">ID</th>
                      <!-- <th style="font-size: 12px">Numri i faturës</th> -->
                      <th class="text-dark" style="font-size: 12px">Emri i klientit</th>
                      <th class="text-dark" style="font-size: 12px">Pershkrimi</th>
                      <th class="text-dark" style="font-size: 12px">Detajet</th>
                      <th class="text-dark" style="font-size: 12px">Shuma e paguar</th>
                      <th class="text-dark" style="font-size: 12px">F. EUR</th>
                      <th class="text-dark" style="font-size: 12px">Obligim</th>
                      <th class="text-dark" style="font-size: 12px">Veprimi</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="tab-pane fade text-dark" id="pills-lista_e_faturave_te_kryera" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera-tab">
              <div class="row">
                <div class="col">
                  <label for="max" class="form-label" style="font-size: 14px;">Prej:</label>
                  <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                    dates për të filtruar rezultatet</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0 text-dark" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="date" id="start_date1" name="start_date1" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;" readonly>
                  </div>
                </div>
                <div class="col text-dark">
                  <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
                  <p class="text-muted text-dark" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                    dates për të filtruar rezultatet.</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="end_date1" name="end_date1" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;" readonly>
                  </div>
                </div>
              </div>
              <div class="col-2 my-4">
                <button id="clearFilters" class="input-custom-css px-3 py-2">
                  <i class="fi fi-rr-clear-alt"></i>
                  Pastro filtrat
                </button>
              </div>
              <hr>
              <div class="table-responsive">
                <table id="paymentsTable" class="table table-bordered w-100">
                  <thead class="table-light">
                    <tr>
                      <th class="text-dark" style="font-size: 12px;width: 10px;">Emri i klientit</th>
                      <!-- <th style="white-space: normal;font-size: 12px;">ID e faturës</th> -->
                      <!-- <th style="white-space: normal;font-size: 12px;">Vlera</th> -->
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Data</th>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Banka</th>
                      <!-- <th style="white-space: normal;font-size: 12px;">Lloji</th> -->
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Përshkrimi</th>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Shuma e paguar</th>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Veprim</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="pills-lista_e_faturave_te_kryera_biznes" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera_biznes-tab">
              <div class="row">
                <div class="col">
                  <label for="max" class="form-label" style="font-size: 14px;">Prej:</label>
                  <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                    dates për të filtruar rezultatet</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="date" id="startDateBiznes" name="startDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;" readonly>
                  </div>
                </div>
                <div class="col">
                  <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
                  <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                    dates për të filtruar rezultatet.</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="endDateBiznes" name="endDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;" readonly>
                  </div>
                </div>
              </div>
              <div class="col-2 my-4">
                <button id="clearFiltersBtnBiznes" class="input-custom-css px-3 py-2">
                  <i class="fi fi-rr-clear-alt"></i>
                  Pastro filtrat
                </button>
              </div>
              <hr>
              <div class="table-responsive">
                <table id="paymentsTableBiznes" class="table table-bordered w-100">
                  <thead class="table-light">
                    <tr>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Emri i klientit</th>
                      <!-- <th style="white-space: normal;font-size: 12px;">ID e faturës</th> -->
                      <!-- <th style="white-space: normal;font-size: 12px;">Vlera</th> -->
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Data</th>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Banka</th>
                      <!-- <th style="white-space: normal;font-size: 12px;">Lloji</th> -->
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Përshkrimi</th>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Shuma e paguar</th>
                      <th class="text-dark" style="white-space: normal;font-size: 12px;width: 10px;">Veprim</th>
                    </tr>
                  </thead>
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
  $currentDateTime = date("dmYHis");
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
    var totalAmount = parseFloat(document.getElementById('total_amount').value);
    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
    document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
  });
  document.getElementById('total_amount').addEventListener('input', function() {
    var totalAmount = parseFloat(this.value);
    var percentage = parseFloat(document.getElementById('percentage').value);
    var totalAmountAfterPercentage = totalAmount - (totalAmount * (percentage / 100));
    document.getElementById('total_amount_after_percentage').value = totalAmountAfterPercentage.toFixed(2);
  });
  function getCustomerName(customerId) {
    var customerName = '';
    $.ajax({
      url: 'api/get_methods/get_customer_name.php',
      type: 'POST',
      data: {
        'customer_id': customerId
      },
      async: false,
      success: function(response) {
        customerName = response;
      }
    });
    return customerName;
  }
  $(document).ready(function() {
    var table = $('#invoiceList').DataTable({
      processing: true,
      serverSide: true,
      searching: {
        regex: true
      },
      paging: true,
      pagingType: "full_numbers",
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      "ajax": {
        "url": $('#invoiceList').data('source'),
        "data": function(d) {
          d.month = $('#monthFilter').val();
          d.amount = $('#amountFilter').val();
        }
      },
      order: [
        [0, "desc"]
      ],
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Te gjitha"]
      ],
      initComplete: function() {
        $(".dt-buttons").removeClass("dt-buttons btn-group");
        $("div.dataTables_length select").addClass("form-select").css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem"
        });
      },
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
      },
      buttons: [{
          extend: "pdf",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all"
            }
          }
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
        },
        {
          text: '<i class="fi fi-rr-trash fa-lg"></i> Fshij',
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          action: function() {
            var selectedIds = $('.row-checkbox:checked').map(function() {
              return $(this).data('id');
            }).get();
            if (selectedIds.length > 0) {
              Swal.fire({
                icon: 'warning',
                title: 'Konfirmo Fshirjen',
                text: 'A jeni i sigurt që dëshironi të fshini elementet e zgjedhura?',
                showCancelButton: true,
                confirmButtonText: 'Po, Fshij',
                cancelButtonText: 'Anulo'
              }).then((result) => {
                if (result.isConfirmed) {
                  $.post('api/delete_methods/delete_invoice.php', {
                    ids: selectedIds
                  }, function(response) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Fshirja u krye me sukses!',
                      text: response
                    });
                    var currentPage = table.page.info().page;
                    table.ajax.reload(() => table.page(currentPage).draw('page'));
                  }).fail(() => {
                    Swal.fire({
                      icon: 'error',
                      title: 'Gabim gjatë fshirjes',
                      text: 'Dicka shkoi keq gjatë fshirjes. Ju lutem provoni përsëri.'
                    });
                  });
                }
              });
            } else {
              Swal.fire({
                icon: 'info',
                title: 'Nuk ke zgjedhur elemente',
                text: 'Ju lutem zgjedhni elemente për t\'i fshirë.'
              });
            }
          }
        }
      ],
      stripeClasses: ["stripe-color"],
      columnDefs: [{
        targets: [0, 1, 2, 3, 4, 5, 6],
        render: function(data, type, row) {
          return type === 'display' && data ? `<div style="white-space: normal;">${data}</div>` : data;
        }
      }, ],
      columns: [{
          data: 'id',
          render: function(data) {
            return `<input type="checkbox" class="row-checkbox" data-id="${data}">`;
          }
        },
        {
          data: 'customer_name',
          render: function(data, type, row) {
            const difference = row.customer_loan_amount - row.customer_loan_paid;
            const dotHTML = difference > 0 ? `<div class="custom-tooltip"><div class="custom-dot"></div><span class="custom-tooltiptext">${difference} €</span></div>` : '';
            return `<p style="white-space: normal;">${data}</p>${dotHTML}`;
          }
        },
        {
          data: 'item',
          render: function(data, type, row) {
            const stateClass = row.state_of_invoice === 'Parregullt' ? 'bg-danger' : row.state_of_invoice === 'Rregullt' ? 'bg-success' : '';
            return `<div class="item-column">${data || ''}</div><br>
                    <div class="badge-column">
                        ${row.state_of_invoice ? `<span class="badge ${stateClass} mx-1 rounded-5">${row.state_of_invoice}</span>` : ''}
                        ${row.type ? `<span class="badge bg-secondary mx-1 rounded-5">${row.type}</span>` : ''}
                    </div>`;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            const details = [
              `<div class="d-flex justify-content-between"><p>Shuma e për.:</p><p>${row.total_amount} USD</p></div>`,
              `<div class="d-flex justify-content-between"><p>Shuma e për. % :</p><p>${row.total_amount_after_percentage} USD</p></div>`,
              row.total_amount_in_eur ? `<div class="d-flex justify-content-between"><p>EUR - Shuma e për. :</p><p>${row.total_amount_in_eur} EUR</p></div>` : '',
              row.total_amount_in_eur_after_percentage ? `<div class="d-flex justify-content-between"><p>EUR - Shuma e për. % :</p><p>${row.total_amount_in_eur_after_percentage} EUR</p></div>` : ''
            ].join('');
            return `<div class="amount-details" style="font-size:12px;">${details}</div>`;
          }
        },
        {
          data: 'paid_amount',
          render: function(data) {
            return `${parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €`;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            const profit = row.total_amount_in_eur - row.total_amount_in_eur_after_percentage;
            return `${profit.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €`;
          }
        },
        {
          data: 'remaining_amount',
          render: function(data, type, row) {
            const remaining = row.total_amount_in_eur_after_percentage !== null ?
              row.total_amount_in_eur_after_percentage - row.paid_amount :
              row.total_amount_after_percentage - row.paid_amount;
            return `${remaining.toFixed(2)} ${row.total_amount_in_eur_after_percentage !== null ? '€' : '$'}`;
          }
        },
        {
          data: 'actions',
          render: function(data, type, row) {
            const totalAmount = row.total_amount_in_eur_after_percentage !== null ? row.total_amount_in_eur_after_percentage : row.total_amount_after_percentage;
            const remainingAmount = totalAmount - row.paid_amount;
            return `
                <div>
                    <a href="#" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark open-payment-modal" 
                       data-id="${row.id}" data-invoice-number="${row.invoice_number}" data-customer-id="${row.customer_id}" 
                       data-item="${row.item}" data-total-amount="${totalAmount}" data-paid-amount="${row.paid_amount}" 
                       data-remaining-amount="${remainingAmount}"><i class="fi fi-rr-euro"></i></a>
                    <a href="complete_invoice.php?id=${row.id}" target="_blank" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">
                        <i class="fi fi-rr-edit"></i></a>
                    <a href="print_invoice.php?id=${row.invoice_number}" target="_blank" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">
                        <i class="fi fi-rr-print"></i></a>
                    ${row.customer_email ? 
                      `<a href="#" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark send-invoice" data-id="${row.id}">Dergo faktur tek kengtari</a>` : 
                      `<button class="bg-light border border-1 px-3 py-2 rounded-5 mx-1 text-dark" disabled><i class="fi fi-rr-file-export"></i></button>
                       <p style="white-space: normal;"><div class="custom-tooltip"><div class="custom-dot"></div><span class="custom-tooltiptext">Nuk posedon email</span></div></p>`}
                    ${row.email_of_contablist ? 
                      `<a href="#" class="bg-white border border-1 px-3 border-danger py-2 rounded-5 mx-1 text-dark send-to-contablist" 
                         data-invoice-number="${row.invoice_number}" data-email="${row.email_of_contablist}">Dergo tek kontablisti</a>` : 
                      `<button class="bg-light border border-1 px-3 py-2 rounded-5 mx-1 text-dark" disabled><i class="fi fi-rr-envelope"></i></button>
                       <p style="white-space: normal;"><div class="custom-tooltip"><div class="custom-dot"></div><span class="custom-tooltiptext">Nuk posedon email te kontablistit</span></div></p>`}
                </div>`;
          }
        }
      ]
    });
    // Trigger filter on change
    $('#monthFilter, #amountFilter').on('change keyup', function() {
      table.draw();
    });
    var tableSecond = $('#invoiceListBiznes').DataTable({
      processing: true,
      serverSide: true,
      "searching": {
        "regex": true
      },
      "paging": true,
      "pageLength": 10,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      ajax: {
        url: 'api/get_methods/get_invoices_biznes.php',
        type: 'POST',
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
            $('.row-checkbox:checked').each(function() {
              selectedIds.push($(this).data('id'));
            });
            if (selectedIds.length > 0) {
              Swal.fire({
                icon: 'warning',
                title: 'Konfirmo Fshirjen',
                text: 'A jeni i sigurt që dëshironi të fshini elementet e zgjedhura?',
                showCancelButton: true,
                confirmButtonText: 'Po, Fshij',
                cancelButtonText: 'Anulo',
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    url: 'api/delete_methods/delete_invoice.php',
                    type: 'POST',
                    data: {
                      ids: selectedIds
                    },
                    success: function(response) {
                      Swal.fire({
                        icon: 'success',
                        title: 'Fshirja u krye me sukses!',
                        text: response,
                      });
                      const currentPage = tableSecond.page.info().page;
                      // Reload table data
                      tableSecond.ajax.reload(function() {
                        // After reload, set the table to the saved current page
                        tableSecond.page(currentPage).draw('page');
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
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4, 5, 6, 7],
        "render": function(data, type, row) {
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      columns: [{
          data: 'id',
          render: function(data, type, row) {
            return '<input type="checkbox" class="row-checkbox" data-id="' + data + '">';
          }
        },
        {
          data: 'id'
        },
        {
          data: 'customer_name',
          render: function(data, type, row) {
            const loanAmount = row.customer_loan_amount || 0;
            const loanPaid = row.customer_loan_paid || 0;
            const difference = loanAmount - loanPaid;
            let fileDisplay = '';
            let uploadButton = '';
            if (row.file_path && row.file_path !== '') {
              fileDisplay = `
                <div class="file-info d-flex align-items-center">
                  <i class="fi fi-rr-check-circle text-success me-2"></i>
                  <a class="input-custom-css px-3 py-2" style="text-decoration: none; text-transform: none;" href="${row.file_path}" download>
                    <i class="fi fi-rr-download"></i>
                  </a>
                </div>
              `;
            } else {
              uploadButton = `
                <button type="button" class="input-custom-css px-3 py-2" style="text-decoration: none; text-transform: none;" data-bs-toggle="modal" data-bs-target="#fileUploadModal-${row.id}">
                  <i class="fi fi-rr-upload"></i>
                </button>
              `;
            }
            let tooltipHTML = '';
            if (difference > 0) {
              tooltipHTML = `
        <div class="custom-tooltip">
          <div class="custom-dot"></div>
          <span class="custom-tooltiptext">${difference} €</span>
        </div>
      `;
            }
            return `
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <span>${data}</span>
          ${tooltipHTML}
          ${uploadButton}
          ${fileDisplay}
        </div>
      </div>
      <!-- File Upload Modal -->
      <div class="modal fade" id="fileUploadModal-${row.id}" tabindex="-1" aria-labelledby="fileUploadModalLabel-${row.id}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="fileUploadModalLabel-${row.id}">Ngarko faturën nga klienti</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="fileUploadForm-${row.id}" enctype="multipart/form-data" action="upload_invoice.php" method="POST">
                <input type="hidden" name="invoice_id" value="${row.id}">
                <div class="mb-3">
                  <label for="fileInput-${row.id}" class="form-label">Zgjidhni skedarin (PDF ose DOC)</label>
                  <input type="file" name="file" class="form-control rounded-5 border border-2" id="fileInput-${row.id}" accept=".pdf,.doc,.docx" required>
                </div>
                <button type="submit" class="input-custom-css px-3 py-2">Dërgo</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      `;
          }
        },
        {
          data: 'item',
          render: function(data, type, row) {
            var stateOfInvoice = row.state_of_invoice;
            var badgeClass = '';
            if (stateOfInvoice === 'Parregullt') {
              badgeClass = 'bg-danger';
            } else if (stateOfInvoice === 'Rregullt') {
              badgeClass = 'bg-success';
            }
            var combinedData = '<div class="item-column">';
            combinedData += data;
            combinedData += '</div><br>';
            combinedData += '<div class="badge-column">';
            combinedData += '<span class="badge ' + badgeClass + ' mx-1 rounded-5">' + stateOfInvoice + '</span>';
            combinedData += '</div>';
            return combinedData;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            const conversionCellId = 'converted-amount-' + row.id;
            let compactHTML = `
      <div class="amount-details" style="font-size:12px;">
        <div class="d-flex justify-content-between">
          <p>Shuma e për.:</p>
          <p>${row.total_amount} USD</p>
        </div>
        <div class="d-flex justify-content-between">
          <p>Shuma e për. % :</p>
          <p>${row.total_amount_after_percentage} USD</p>
        </div>`;
            if (row.total_amount_in_eur) {
              compactHTML += `
        <div class="d-flex justify-content-between">
          <p>EUR - Shuma e për. :</p>
          <p>${row.total_amount_in_eur} EUR</p>
        </div>`;
            }
            if (row.total_amount_in_eur_after_percentage) {
              compactHTML += `
        <div class="d-flex justify-content-between">
          <p>EUR - Shuma e për. % :</p>
          <p>${row.total_amount_in_eur_after_percentage} EUR</p>
        </div>`;
            }
            compactHTML += `
      <div class="d-flex justify-content-between">
        <p>Converted Amount (to EUR):</p>
        <p id="${conversionCellId}">Loading...</p>
      </div>
    </div>`;
            // Return the compactHTML before initiating the fetch to ensure the cell is rendered
            setTimeout(() => {
              // Fetch the converted amount asynchronously
              const url = 'api/get_methods/get_currency.php?amount=' + row.total_amount_after_percentage;
              fetch(url)
                .then(response => response.json())
                .then(result => {
                  const conversionCell = document.getElementById(conversionCellId);
                  if (result.error) {
                    conversionCell.innerText = 'Error: ' + result.error;
                  } else if (result.result && result.result.EUR) {
                    conversionCell.innerText = result.result.EUR.toFixed(2) + ' EUR';
                  } else {
                    conversionCell.innerText = 'Error fetching rate';
                  }
                })
                .catch(error => {
                  document.getElementById(conversionCellId).innerText = 'Error fetching rate';
                });
            }, 0);
            return compactHTML;
          }
        },
        {
          data: 'paid_amount',
          render: function(data, type, row) {
            return data.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            }) + ' €';
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            var fitimiIBareshes = row.total_amount_in_eur - row.total_amount_in_eur_after_percentage;
            return fitimiIBareshes.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            }) + ' €';
          }
        },
        {
          data: 'remaining_amount',
          render: function(data, type, row) {
            var remainingAmount;
            if (row.total_amount_in_eur_after_percentage !== null && row.total_amount_in_eur_after_percentage !== undefined) {
              remainingAmount = row.total_amount_in_eur_after_percentage - row.paid_amount;
              remainingAmount = remainingAmount.toFixed(2) + ' €';
            } else {
              remainingAmount = row.total_amount_after_percentage - row.paid_amount;
              remainingAmount = remainingAmount.toFixed(2) + ' $';
            }
            return remainingAmount.toLocaleString(undefined, {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });
          }
        },
        {
          data: 'actions',
          render: function(data, type, row) {
            var totalAmount = row.total_amount_in_eur_after_percentage !== null && row.total_amount_in_eur_after_percentage !== undefined ?
              row.total_amount_in_eur_after_percentage : row.total_amount_after_percentage;
            var remainingAmount = totalAmount - row.paid_amount;
            var html = '<div>';
            // Payment modal link
            html += '<a href="#" style="text-decoration:none;" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark open-payment-modal" ' +
              'data-id="' + row.id + '" ' +
              'data-invoice-number="' + row.invoice_number + '" ' +
              'data-customer-id="' + row.customer_id + '" ' +
              'data-item="' + row.item + '" ' +
              'data-total-amount="' + totalAmount + '" ' +
              'data-paid-amount="' + row.paid_amount + '" ' +
              'data-remaining-amount="' + remainingAmount + '">' +
              '<i class="fi fi-rr-euro"></i></a>';
            // Complete invoice link
            html += '<a target="_blank" style="text-decoration:none;" href="complete_invoice.php?id=' + row.id + '" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">' +
              '<i class="fi fi-rr-edit"></i></a>';
            // Print invoice link
            html += '<a target="_blank" style="text-decoration:none;" href="print_invoice.php?id=' + row.invoice_number + '" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark">' +
              '<i class="fi fi-rr-print"></i></a>';
            // Send invoice to customer
            if (row.customer_email) {
              html += '<a href="#" style="text-decoration:none;" class="bg-white border border-1 px-3 py-2 rounded-5 mx-1 text-dark send-invoice" ' +
                'data-id="' + row.id + '">' +
                'Dergo faktur tek kengtari</a>';
            } else {
              html += '<button style="text-decoration:none;" class="bg-light border border-1 px-3 py-2 rounded-5 mx-1 text-dark" disabled>' +
                '<i class="fi fi-rr-file-export"></i></button>' +
                '<p style="white-space: normal;">' +
                '<div class="custom-tooltip">' +
                '<div class="custom-dot"></div>' +
                '<span class="custom-tooltiptext">Nuk posedon email</span>' +
                '</div>' +
                '</p>';
            }
            // Send invoice to contablist
            if (row.email_of_contablist) {
              html += '<a href="#" style="text-decoration:none;" class="bg-white border border-1 px-3 border-danger py-2 rounded-5 mx-1 text-dark send-invoices" ' +
                'data-id="' + row.id + '">' +
                'Dergo faktur tek kontabilisti</a>';
            } else {
              html += '<button style="text-decoration:none;" class="bg-light border border-1 px-3 py-2 rounded-5 mx-1 text-dark" disabled>' +
                '<i class="fi fi-rr-file-export"></i></button>' +
                '<p style="white-space: normal;">' +
                '<div class="custom-tooltip">' +
                '<div class="custom-dot"></div>' +
                '<span class="custom-tooltiptext">Nuk posedon email</span>' +
                '</div>' +
                '</p>';
            }
            html += '</div>';
            return html;
          }
        }
      ]
    });
    var currentPage = 0;
    $(document).on('click', '.open-payment-modal', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      var invoiceNumber = $(this).data('invoice-number');
      var customerId = $(this).data('customer-id');
      var item = $(this).data('item');
      var totalAmount = $(this).data('total-amount');
      var paidAmount = $(this).data('paid-amount');
      var remainingAmount = $(this).data('remaining-amount');
      var customerName = getCustomerName(customerId);
      var titleOfInvoice = $(this).data('invoice-number');
      // Clear any previous error messages
      $('.error-message').text('');
      $('#invoiceId').val(id);
      $('#invoiceNumber').text(invoiceNumber);
      $('#customerName').text(customerName);
      $('#item').text(item);
      $('#totalAmount').text(totalAmount);
      $('#paidAmount').text(paidAmount);
      $('#remainingAmount').text(remainingAmount.toFixed(2));
      $('#paymentModal').modal('show');
      $("#customerId").text(customerId);
      $('#titleOfInvoice').text('Fatura: ' + titleOfInvoice);
      $('#paymentAmount').val(remainingAmount); // Update paymentAmount with converted amoun
    });
    $('#paymentAmount').on('input', function() {
      var paymentAmount = parseFloat($(this).val());
      var remainingAmount = parseFloat($('#remainingAmount').text());
      if (paymentAmount > remainingAmount) {
        $('#paymentAmountError').text('Shuma e pagesës nuk mund të kalojë shumën e obligimit.');
        $('#submitPayment').prop('disabled', true);
      } else {
        $('#paymentAmountError').text('');
        $('#submitPayment').prop('disabled', false);
      }
    });
    $('#submitPayment').click(function(e) {
      e.preventDefault();
      var invoiceId = $('#invoiceId').val();
      var paymentAmount = $('#paymentAmount').val();
      var bankInfo = $('#bankInfo').val();
      var type_of_pay = $('#type_of_pay').val();
      var description = $('#description').val();
      // Check if any of the required fields are empty
      if (!invoiceId || !paymentAmount || !bankInfo || !type_of_pay) {
        // Display a message indicating that all fields are required
        Swal.fire({
          title: 'Plotësoni të gjitha fushat',
          text: 'Ju lutemi plotësoni të gjitha fushat për të kryer pagesën.',
          icon: 'error',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 5000
        });
        return; // Exit the function early
      }
      // All fields are filled, proceed with the AJAX request
      $.ajax({
        url: 'api/post_methods/post_payment.php',
        method: 'POST',
        data: {
          invoiceId: invoiceId,
          paymentAmount: paymentAmount,
          bankInfo: bankInfo,
          type_of_pay: type_of_pay,
          description: description
        },
        success: function(response) {
          if (response === 'success') {
            $('#paymentModal').modal('hide');
            var row = table.row('#' + invoiceId).node();
            $(row).addClass('success-row');
            setTimeout(function() {
              $(row).removeClass('success-row');
            }, 3000);
            Swal.fire({
              title: 'Pagesa është kryer me sukses',
              icon: 'success',
              showConfirmButton: false,
              position: 'top-end',
              toast: true,
              timer: 5000,
              html: '<div style="text-align: left;">' +
                '<p>Numri i Faturës: ' + invoiceId + '</p>' +
                '<p>Emri i Klientit: ' + customerName.textContent + '</p>' +
                '<p>Shuma e Paguar: ' + paymentAmount + ' €</p>' +
                '</div>',
              width: '500px'
            });
            var currentPage = table.page.info().page;
            var currentPageOfSecondTable = tableSecond.page.info().page;
            table.ajax.reload(function() {
              table.page(currentPage).draw(false);
            });
            tableSecond.ajax.reload(function() {
              tableSecond.page(currentPageOfSecondTable).draw(false);
            });
            completePayments.ajax.reload();
          } else {
            Swal.fire({
              title: 'Pagesa dështoi',
              text: 'Pagesa për ID-në e faturës: ' + invoiceId + ' ka dështuar. Ju lutemi provoni përsëri.',
              icon: 'error',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 5000
            });
            console.log('Pagesa dështoi: ' + response);
          }
        },
        error: function(error) {
          console.log('Gabim në kryerjen e pagesës: ' + error);
        }
      });
    });
    function createButtonConfig(extend, icon, text, titleAttr) {
      return {
        extend: extend,
        text: '<i class="' + icon + ' fa-lg"></i>&nbsp;&nbsp; ' + text,
        titleAttr: titleAttr,
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        filename: "faturat_e_fshira_" + getCurrentDate(),
        exportOptions: extend === 'excelHtml5' ? {
          modifier: {
            search: "applied",
            order: "applied",
            page: "all"
          }
        } : {}
      };
    }
    var buttonsConfig = [
      createButtonConfig("pdfHtml5", "fi fi-rr-file-pdf", "PDF", "Eksporto tabelen ne formatin PDF"),
      createButtonConfig("copyHtml5", "fi fi-rr-copy", "Kopjo", "Kopjo tabelen ne formatin Clipboard"),
      createButtonConfig("excelHtml5", "fi fi-rr-file-excel", "Excel", "Eksporto tabelen ne formatin Excel"),
      createButtonConfig("print", "fi fi-rr-print", "Printo", "Printo tabel&euml;n")
    ];
    var invoice_trash = $('#invoices_trash').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      buttons: buttonsConfig,
      ajax: {
        url: 'api/get_methods/get_invoices_trash_server.php',
        type: 'POST',
      },
      columns: [{
          data: 'invoice_number'
        },
        {
          data: 'client_name'
        },
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
        }
      ],
      order: [],
      initComplete: function() {
        var btns = $(".dt-buttons");
        btns.addClass("").removeClass("dt-buttons btn-group");
        var lengthSelect = $("div.dataTables_length select");
        lengthSelect.addClass("form-select").css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem"
        });
      },
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
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
  });
</script>
<script src="pro_invoice.js"></script>
<script src="percentage_calculations.js"></script>
<script src="create_manual_invoice.js"></script>
<script src="paymentsTable.js"></script>
<script src="invoice_trash.js"></script>
<script src="states.js"></script>
<?php include 'partials/footer.php' ?>
</body>
</html>