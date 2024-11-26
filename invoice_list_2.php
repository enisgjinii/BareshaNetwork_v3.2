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
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Faturë e shpejtë
            </a>
          </li>
      </nav>
      <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 shadow-none active" style="text-transform: none" id="pills-ls_faturat-tab" data-bs-toggle="pill" data-bs-target="#pills-ls_faturat" type="button" role="tab" aria-controls="pills-ls_faturat" aria-selected="true">Lista e faturave</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-5 shadow-none" style="text-transform: none" id="pills-krijo_fature-tab" data-bs-toggle="pill" data-bs-target="#pills-krijo_fature" type="button" role="tab" aria-controls="pills-krijo_fature" aria-selected="false">Krijo faturë</button>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-ls_faturat" role="tabpanel" aria-labelledby="pills-ls_faturat-tab">
          <div class="card rounded-5 p-5 my-3">
            <!-- <p class="text-muted">Lista e faturave te reja</p> -->
            <table id="data-table" class="table table-bordered table-sm">
              <!-- <caption>Lista e faturave te shpejta</caption> -->
              <thead>
                <tr>
                  <th class="text-dark">Fatura Nr.</th>
                  <th class="text-dark">ID Fature</th>
                  <th class="text-dark">Data & Ora</th>
                  <th class="text-dark">Emri Klientit</th>
                  <th class="text-dark">Totali (€)</th>
                  <th class="text-dark">Vepro</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($invoiceList as $invoiceDetails) : ?>
                  <tr>
                    <td class="text-dark"><?= htmlspecialchars($invoiceDetails["invoice_number"]) ?></td>
                    <td class="text-dark"><?= htmlspecialchars($invoiceDetails["order_id"]) ?></td>
                    <td class="text-dark">
                      <?php
                      // Array of months in Albanian
                      $months = ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nentor", "Dhjetor"];
                      // Convert and format the order date
                      $order_date = strtotime($invoiceDetails["order_date"]);
                      $formatted_date = date("d", $order_date) . " " . $months[date("n", $order_date) - 1] . " " . date("Y, H:i", $order_date);
                      echo htmlspecialchars($formatted_date);
                      ?>
                    </td>
                    <td class="text-dark"><?= htmlspecialchars($invoiceDetails["order_receiver_name"]) ?></td>
                    <td class="text-dark"><?= number_format($invoiceDetails["order_total_after_tax"], 2) ?></td>
                    <td class="text-dark">
                      <a href="print_Invoice_2.php?invoice_id=<?= urlencode($invoiceDetails["order_id"]) ?>" title="Print Invoice" class="input-custom-css px-3 py-2">
                        <i class="fi fi-rr-print"></i>
                      </a>
                      <a href="edit_Invoice_2.php?update_id=<?= urlencode($invoiceDetails["order_id"]) ?>" title="Edit Invoice" class="action-btn input-custom-css px-3 py-2">
                        <i class="fi fi-rr-edit"></i>
                      </a>
                      <form method="post" class="action-form d-inline">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($invoiceDetails["order_id"]) ?>">
                        <button type="submit" class="action-btn input-custom-css px-3 py-2" title="Delete Invoice" onclick="return confirm('Are you sure you want to delete this invoice?');">
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
        <div class="tab-pane fade" id="pills-krijo_fature" role="tabpanel" aria-labelledby="pills-krijo_fature-tab">
          <form action="save_invoice.php" id="invoice-form" method="post" class="invoice-form card p-3" role="form" novalidate="">
            <div class="load-animate animated fadeInUp">
              <input id="currency" type="hidden" value="$">
              <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 text-dark">
                  <h3>Nga,</h3>
                  Baresha Music
                  <br>
                  Shirokë <br>
                  Suharekë,KS<br>
                  Kosov - 23000 <br>
                  Telefoni : 00383 (0) 49 605 655<br>
                  Email: info@bareshamusic.com<br>
                  Tax ID : 811499228
                </div>
                <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 pull-right">
                  <h3 class="text-dark">Për,</h3>
                  <label for="customer" class="form-label">Emri i klientit</label>
                  <select class="form-control rounded-5 border border-1" name="customer" id="customer">
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
                  <br>
                  <div class="form-group">
                    <input type="text" class="form-control rounded-5 border border-1" name="companyName" id="companyName" placeholder="Emri i kompanisë ( klientit )" autocomplete="off">
                  </div>
                  <div class="form-group">
                    <textarea class="form-control rounded-5 border border-1" rows="3" name="address" id="address" placeholder="Adresa"></textarea>
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control rounded-5 border border-1" name="mobile" id="mobile" placeholder="Telefoni" autocomplete="off">
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control rounded-5 border border-1" name="email" id="email" placeholder="Email" autocomplete="off">
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control rounded-5 border border-1" name="taxId" id="taxId" placeholder="Tax ID" autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <table class="table table-bordered table-hover" id="invoiceItem">
                    <thead></thead>
                    <tr>
                      <th width="2%"><input id="checkAll" class="formcontrol" type="checkbox"></th>
                      <th width="15%">Artikulli Nr</th>
                      <th width="38%">Emri i artikullit</th>
                      <th width="15%">Sasia</th>
                      <th width="15%">Çmimi</th>
                      <th width="15%">Total</th>
                    </tr>
                    <tr>
                      <td><input class="itemRow" type="checkbox"></td>
                      <td><input type="text" name="productCode[]" id="productCode_1" class="form-control rounded-5 border border-1" autocomplete="off"></td>
                      <td><input type="text" name="productName[]" id="productName_1" class="form-control rounded-5 border border-1" autocomplete="off"></td>
                      <td><input type="number" name="quantity[]" id="quantity_1" class="form-control rounded-5 border border-1 quantity" autocomplete="off"></td>
                      <td><input type="number" name="price[]" id="price_1" class="form-control rounded-5 border border-1 price" autocomplete="off"></td>
                      <td><input type="number" name="total[]" id="total_1" class="form-control rounded-5 border border-1 total" autocomplete="off"></td>
                    </tr>
                  </table>
                </div>
              </div>
              <div class="row my-3">
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                  <button class="input-custom-css px-3 py-2" id="addRows" type="button">+ Shtoni më shumë</button>
                  <button class="input-custom-css px-3 py-2 delete" id="removeRows" type="button">- Fshije</button>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                  <h5>Shënime: </h5>
                  <div class="form-group">
                    <textarea class="form-control rounded-5 border border-1 txt" rows="5" name="notes" id="notes" placeholder="Shënimet e tua"></textarea>
                  </div>
                  <br>
                  <div class="form-group">
                    <!-- <input type="hidden" value="<?php echo $_SESSION['userid']; ?>" class="form-control rounded-5 border border-1" name="userId"> -->
                    <input data-loading-text="Ruajtja e faturës..." type="submit" name="invoice_btn" value="Ruaj faturën" class="input-custom-css px-3 py-2 submit_btn invoice-save-btm">
                  </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                  <span class="form-inline">
                    <div class="form-group">
                      <label>Totali i nëntotaleve: &nbsp;</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text currency">€</span>
                        </div>
                        <input value="" type="number" class="form-control rounded-5 border border-1" name="subTotal" id="subTotal" placeholder="Totali i nëntotaleve">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Norma e tatimit: &nbsp;</label>
                      <div class="input-group">
                        <input value="" type="number" class="form-control rounded-5 border border-1" name="taxRate" id="taxRate" placeholder="Norma e tatimit">
                        <div class="input-group-append">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Shuma e tatimit: &nbsp;</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text currency">€</span>
                        </div>
                        <input value="" type="number" class="form-control rounded-5 border border-1" name="taxAmount" id="taxAmount" placeholder="Shuma e tatimit">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Totali: &nbsp;</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text currency">€</span>
                        </div>
                        <input value="" type="number" class="form-control rounded-5 border border-1" name="totalAftertax" id="totalAftertax" placeholder="Totali">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Shuma e paguar: &nbsp;</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text currency">€</span>
                        </div>
                        <input value="" type="number" class="form-control rounded-5 border border-1" name="amountPaid" id="amountPaid" placeholder="Shuma e paguar">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Shuma për të paguar: &nbsp;</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text currency">€</span>
                        </div>
                        <input value="" type="number" class="form-control rounded-5 border border-1" name="amountDue" id="amountDue" placeholder="Shuma për të paguar">
                      </div>
                    </div>
                  </span>
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
  // Add datatable
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
      dom: "<'row'<'col-md-3'l><'col-md-6'BSR><'col-md-3'f>><'row'<'col-md-12'QPtr>><'row'<'col-md-6'i><'col-md-6'p>>",
      buttons: ['pdfHtml5', 'excelHtml5', 'copyHtml5', 'print'].map(type => ({
        extend: type,
        text: `<i class="fi fi-rr-file-${type.split('Html5')[0]} fa-lg"></i> ${type.split('Html5')[0].toUpperCase()}`,
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      })),
      initComplete: () => {
        $(".dt-buttons").removeClass("dt-buttons btn-group");
        $("div.dataTables_length select").addClass("form-select").css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem"
        });
      }
    });
  })
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
        // Clear all fields if no client is selected
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