<?php
session_start();
include('partials/header.php');
include('Invoice_2.php');
$invoice = new Invoice();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
  $deleteId = $_POST['delete_id'];
  $deleteResult = $invoice->deleteInvoice($deleteId);
  if ($deleteResult) {
    header("Location: invoice_list_2.php");
  } else {
    echo "Failed to delete invoice.";
  }
}

$invoiceList = $invoice->getInvoiceList();

if (!empty($_POST['companyName']) && $_POST['companyName']) {
  $invoice->saveInvoice($_POST);
}
?>
<script src="invoice.js"></script>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5 mb-2" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 py-1">
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset text-decoration-none">
              Faturë e shpejtë
            </a>
          </li>
        </ol>
      </nav>

      <ul class="nav nav-pills ms-1 bg-white my-2 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
        <li class="nav-item">
          <button class="nav-link rounded-5 shadow-none active px-2 py-1" style="text-transform: none;" id="pills-ls_faturat-tab" data-bs-toggle="pill" data-bs-target="#pills-ls_faturat" type="button" role="tab" aria-controls="pills-ls_faturat" aria-selected="true">
            Lista e faturave
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link rounded-5 shadow-none px-2 py-1" style="text-transform: none;" id="pills-krijo_fature-tab" data-bs-toggle="pill" data-bs-target="#pills-krijo_fature" type="button" role="tab" aria-controls="pills-krijo_fature" aria-selected="false">
            Krijo faturë
          </button>
        </li>
      </ul>

      <div class="tab-content" id="pills-tabContent">
        <!-- Invoice List Tab -->
        <div class="tab-pane fade show active" id="pills-ls_faturat" role="tabpanel" aria-labelledby="pills-ls_faturat-tab">
          <div class="card rounded-5 p-3 my-2">
            <table id="data-table" class="table table-bordered table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>Fatura Nr.</th>
                  <th>ID Fature</th>
                  <th>Data & Ora</th>
                  <th>Emri Klientit</th>
                  <th>Totali (€)</th>
                  <th>Vepro</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($invoiceList as $invoiceDetails) : ?>
                  <tr>
                    <td><?= htmlspecialchars($invoiceDetails["invoice_number"]) ?></td>
                    <td><?= htmlspecialchars($invoiceDetails["order_id"]) ?></td>
                    <td>
                      <?php
                      $months = ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nentor", "Dhjetor"];
                      $order_date = strtotime($invoiceDetails["order_date"]);
                      $formatted_date = date("d", $order_date) . " " . $months[date("n", $order_date) - 1] . " " . date("Y, H:i", $order_date);
                      echo htmlspecialchars($formatted_date);
                      ?>
                    </td>
                    <td><?= htmlspecialchars($invoiceDetails["order_receiver_name"]) ?></td>
                    <td><?= number_format($invoiceDetails["order_total_after_tax"], 2) ?></td>
                    <td class="text-nowrap">
                      <a style="text-decoration: none;" href="print_Invoice_2.php?invoice_id=<?= urlencode($invoiceDetails["order_id"]) ?>" title="Print Invoice" class="input-custom-css px-2 py-1">
                        <i class="fi fi-rr-print"></i>
                      </a>
                      <a style="text-decoration: none;" href="edit_Invoice_2.php?update_id=<?= urlencode($invoiceDetails["order_id"]) ?>" title="Edit Invoice" class="input-custom-css px-2 py-1">
                        <i class="fi fi-rr-edit"></i>
                      </a>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($invoiceDetails["order_id"]) ?>">
                        <button type="submit" class="input-custom-css px-2 py-1" title="Delete Invoice" onclick="return confirm('Are you sure you want to delete this invoice?');">
                          <i class="fi fi-rr-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Create Invoice Tab -->
        <div class="tab-pane fade" id="pills-krijo_fature" role="tabpanel" aria-labelledby="pills-krijo_fature-tab">
          <form action="api/post_methods/post_save_invoice.php" id="invoice-form" method="post" class="invoice-form card p-2" role="form" novalidate="">
            <div class="load-animate animated fadeInUp">
              <input id="currency" type="hidden" value="$">
              <div class="row g-2">
                <div class="col-lg-6 text-dark">
                  <h6 class="mb-1">Nga,</h6>
                  <small>
                    Baresha Music<br>
                    Shirokë, Suharekë, KS<br>
                    Kosov - 23000<br>
                    Tel: 00383 (0) 49 605 655<br>
                    Email: info@bareshamusic.com<br>
                    Tax ID: 811499228
                  </small>
                </div>
                <div class="col-lg-6">
                  <h6 class="text-dark mb-1">Për,</h6>
                  <label for="customer" class="form-label mb-1">Emri i klientit</label>
                  <select class="form-control form-control-sm rounded-5 border border-1 mb-1" name="customer" id="customer">
                    <option value="">Zgjidhni klientin</option>
                    <?php
                    include "conn-d.php";
                    $sql = "SELECT DISTINCT order_receiver_name, order_receiver_address, mobile, email, tax_id FROM invoice_order";
                    $result = $conn->query($sql);
                    if ($result === false) {
                      echo "<option value=''>Gabim në marrjen e klientëve</option>";
                    } else {
                      while ($row = $result->fetch_assoc()) {
                        $clientData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        echo '<option value="' . $clientData . '">' . htmlspecialchars($row['order_receiver_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                      }
                    }
                    ?>
                  </select>
                  <script>
                    new Selectr('#customer');
                  </script>
                  <input type="text" class="form-control form-control-sm rounded-5 border border-1 mb-1" name="companyName" id="companyName" placeholder="Emri i kompanisë" autocomplete="off">
                  <textarea class="form-control form-control-sm rounded-5 border border-1 mb-1" rows="2" name="address" id="address" placeholder="Adresa"></textarea>
                  <input type="text" class="form-control form-control-sm rounded-5 border border-1 mb-1" name="mobile" id="mobile" placeholder="Telefoni" autocomplete="off">
                  <input type="text" class="form-control form-control-sm rounded-5 border border-1 mb-1" name="email" id="email" placeholder="Email" autocomplete="off">
                  <input type="text" class="form-control form-control-sm rounded-5 border border-1" name="taxId" id="taxId" placeholder="Tax ID" autocomplete="off">
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-12">
                  <table class="table table-bordered table-hover table-sm" id="invoiceItem">
                    <tr class="table-light">
                      <th width="2%"><input id="checkAll" class="formcontrol" type="checkbox"></th>
                      <th width="15%">Artikulli Nr</th>
                      <th width="38%">Emri i artikullit</th>
                      <th width="15%">Sasia</th>
                      <th width="15%">Çmimi</th>
                      <th width="15%">Total</th>
                    </tr>
                    <tr>
                      <td><input class="itemRow" type="checkbox"></td>
                      <td><input type="text" name="productCode[]" id="productCode_1" class="form-control form-control-sm rounded-5 border border-1" autocomplete="off"></td>
                      <td><input type="text" name="productName[]" id="productName_1" class="form-control form-control-sm rounded-5 border border-1" autocomplete="off"></td>
                      <td><input type="number" name="quantity[]" id="quantity_1" class="form-control form-control-sm rounded-5 border border-1 quantity" autocomplete="off"></td>
                      <td><input type="number" name="price[]" id="price_1" class="form-control form-control-sm rounded-5 border border-1 price" autocomplete="off"></td>
                      <td><input type="number" name="total[]" id="total_1" class="form-control form-control-sm rounded-5 border border-1 total" autocomplete="off"></td>
                    </tr>
                  </table>
                  <div class="d-flex gap-2 mb-2">
                    <button class="input-custom-css px-2 py-1" id="addRows" type="button">+ Shto</button>
                    <button class="input-custom-css px-2 py-1 delete" id="removeRows" type="button">- Fshij</button>
                  </div>
                </div>
              </div>

              <div class="row g-2">
                <div class="col-lg-8">
                  <h6>Shënime:</h6>
                  <textarea class="form-control form-control-sm rounded-5 border border-1 mb-2" rows="2" name="notes" id="notes" placeholder="Shënimet e tua"></textarea>
                  <input data-loading-text="Ruajtja e faturës..." type="submit" name="invoice_btn" value="Ruaj faturën" class="input-custom-css px-3 py-1">
                </div>
                <div class="col-lg-4">
                  <div class="mb-1">
                    <label class="form-label mb-0">Totali i nëntotaleve:</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">€</span>
                      <input type="number" class="form-control form-control-sm rounded-5 border border-1" name="subTotal" id="subTotal">
                    </div>
                  </div>
                  <div class="mb-1">
                    <label class="form-label mb-0">Norma e tatimit:</label>
                    <div class="input-group input-group-sm">
                      <input type="number" class="form-control form-control-sm rounded-5 border border-1" name="taxRate" id="taxRate">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <div class="mb-1">
                    <label class="form-label mb-0">Shuma e tatimit:</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">€</span>
                      <input type="number" class="form-control form-control-sm rounded-5 border border-1" name="taxAmount" id="taxAmount">
                    </div>
                  </div>
                  <div class="mb-1">
                    <label class="form-label mb-0">Totali:</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">€</span>
                      <input type="number" class="form-control form-control-sm rounded-5 border border-1" name="totalAftertax" id="totalAftertax">
                    </div>
                  </div>
                  <div class="mb-1">
                    <label class="form-label mb-0">Shuma e paguar:</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">€</span>
                      <input type="number" class="form-control form-control-sm rounded-5 border border-1" name="amountPaid" id="amountPaid">
                    </div>
                  </div>
                  <div>
                    <label class="form-label mb-0">Shuma për të paguar:</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">€</span>
                      <input type="number" class="form-control form-control-sm rounded-5 border border-1" name="amountDue" id="amountDue">
                    </div>
                  </div>
                </div>
              </div>

              <div class="clearfix"></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#data-table').DataTable({
      ordering: false,
      fixedHeader: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
      },
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Te gjitha"]
      ],
      stripeClasses: ['stripe-color'],
      dom: "<'row'<'col-md-3'l><'col-md-6'BSR><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'i><'col-md-6'p>>",
      buttons: ['pdfHtml5', 'excelHtml5', 'copyHtml5', 'print'].map(type => ({
        extend: type,
        text: `<i class="fi fi-rr-file-${type.split('Html5')[0]}"></i> ${type.split('Html5')[0].toUpperCase()}`,
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      })),
      initComplete: () => {
        $(".dt-buttons").removeClass("dt-buttons btn-group");
        $("div.dataTables_length select").addClass("form-select form-select-sm").css({
          width: "auto"
        });
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    var customerSelect = document.getElementById('customer');
    var companyNameInput = document.getElementById('companyName');
    var addressInput = document.getElementById('address');
    var mobileInput = document.getElementById('mobile');
    var emailInput = document.getElementById('email');
    var taxIdInput = document.getElementById('taxId');

    customerSelect.addEventListener('change', function() {
      var selectedOption = this.options[this.selectedIndex];
      if (selectedOption.value) {
        var clientData = JSON.parse(selectedOption.value);
        companyNameInput.value = clientData.order_receiver_name || '';
        addressInput.value = clientData.order_receiver_address || '';
        mobileInput.value = clientData.mobile || '';
        emailInput.value = clientData.email || '';
        taxIdInput.value = clientData.tax_id || '';
      } else {
        companyNameInput.value = '';
        addressInput.value = '';
        mobileInput.value = '';
        emailInput.value = '';
        taxIdInput.value = '';
      }
    });
  });
</script>
<?php include 'partials/footer.php'; ?>