<?php
ob_start();
require_once 'vendor/autoload.php';
require_once 'conn-d.php';
require_once 'partials/header.php';
require_once 'modalPayment.php';
function generateInvoiceNumber()
{
  $chars = '0123456789';
  $length = 8;
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= $chars[rand(0, strlen($chars) - 1)];
  }
  return $string;
}
?>
<link rel="stylesheet" href="invoice_style.css">
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Financat</a></li>
          <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Pagesat Youtube</a></li>
        </ol>
      </nav>
      <div class="row">
        <div>
          <ul class="nav nav-pills bg-white my-2 mx-0 rounded-5" style="width: fit-content; border: 1px solid lightgrey;" id="pills-tab" role="tablist">
            <?php
            $tabs = [
              ['id' => 'pills-lista_e_faturave', 'text' => 'Lista e faturave ( Personale )', 'active' => true],
              ['id' => 'pills-lista_e_faturave_biznes', 'text' => 'Lista e faturave ( Biznes )', 'active' => true]
            ];
            if ($user_info['email'] !== 'lirie@bareshamusic.com') {
              $tabs = array_merge($tabs, [
                ['id' => 'pills-lista_e_faturave_te_kryera', 'text' => 'Pagesat e kryera ( Personal )', 'active' => false],
                ['id' => 'pills-lista_e_faturave_te_kryera_biznes', 'text' => 'Pagesa e kryera (Biznese)', 'active' => false]
              ]);
            }
            foreach ($tabs as $tab) {
              echo '<li class="nav-item" role="presentation"><button class="nav-link rounded-5 ' . ($tab['active'] ? 'active' : '') . '" style="text-transform: none" id="' . $tab['id'] . '-tab" data-bs-toggle="pill" data-bs-target="#' . $tab['id'] . '" type="button" role="tab" aria-controls="' . $tab['id'] . '" aria-selected="' . ($tab['active'] ? 'true' : 'false') . '">' . $tab['text'] . '</button></li>';
            }
            ?>
          </ul>
        </div>
      </div>
      <div>
        <?php
        $buttons = [
          ['icon' => 'fi fi-rr-add-document fa-lg', 'text' => 'Fatur&euml; e re', 'target' => '#newInvoice']
        ];
        foreach ($buttons as $button) {
          echo '<button style="text-transform: none;" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="' . $button['target'] . '"><i class="' . $button['icon'] . '"></i>&nbsp; ' . $button['text'] . '</button>';
        }
        ?>
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
                    <input type="hidden" id="invoice_number" name="invoice_number" value="<?php echo generateInvoiceNumber(); ?>" readonly required>
                    <div class="mb-3">
                      <label for="customer_id" class="form-label">Emri i klientit:</label>
                      <select class="form-control rounded-5 shadow-sm py-3" id="customer_id" name="customer_id" required>
                        <?php
                        $result = mysqli_query($conn, "SELECT id, emri, perqindja, emriart FROM klientet ORDER BY id DESC");
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<option value='{$row['id']}' data-percentage='{$row['perqindja']}'>{$row['emri']} ({$row['emriart']})</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <?php
                    function renderFormGroup($label, $input)
                    {
                      echo "<div class='mb-3'><label class='form-label'>{$label}</label>{$input}</div>";
                    }
                    function renderAmountInput($label, $prefix, $id, $name)
                    {
                      echo "<div class='col'><label for='{$id}' class='form-label'>{$label}</label><div class='input-group'><span class='input-group-text bg-white border-1 me-2 rounded-5'>{$prefix}</span><input type='number' step='0.01' class='form-control rounded-end shadow-none py-3' id='{$id}' name='{$name}' required></div></div>";
                    }
                    renderFormGroup('Përshkrimi:', '<textarea class="form-control rounded-5 shadow-sm py-3" id="item" name="item" required></textarea>');
                    renderFormGroup('Lloji:', '<select class="form-control rounded-5 shadow-sm py-3" name="type" id="type"><option value="individual">Individual</option><option value="grupor">Grupor</option></select>');
                    renderFormGroup('Përqindja:', '<input type="number" min="0" max="100" class="form-control rounded-5 shadow-none py-3" id="percentage" name="percentage" required>');
                    ?>
                    <div class="row mb-3">
                      <?php
                      renderAmountInput('Shuma e përgjithshme:', '$', 'total_amount', 'total_amount');
                      renderAmountInput('Shuma e përgjithshme pas përqindjes:', '$', 'total_amount_after_percentage', 'total_amount_after_percentage');
                      ?>
                    </div>
                    <div class="row mb-3">
                      <?php
                      renderAmountInput('Shuma e përgjithshme - EUR:', '€', 'total_amount_in_eur', 'total_amount_in_eur');
                      renderAmountInput('Shuma e përgjithshme pas përqindjes - EUR:', '€', 'total_amount_after_percentage_in_eur', 'total_amount_after_percentage_in_eur');
                      ?>
                    </div>
                    <div class="row mb-3">
                      <div class="col">
                        <div class="mb-3">
                          <label for="created_date" class="form-label">Data e krijimit të faturës:</label>
                          <input type="date" class="form-control rounded-5 border border-2 py-3" id="created_date" name="created_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                      </div>
                      <div class="col">
                        <div class="mb-3">
                          <label for="invoice_status" class="form-label">Gjendja e fatures:</label>
                          <select class="form-select rounded-5" id="invoice_status" name="invoice_status" required>
                            <option value="Rregullt">Rregullt</option>
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
              const convertToEUR = async (a, o) => {
                  if (isNaN(a) || a <= 0) {
                    document.getElementById(o).value = '';
                    return;
                  }
                  try {
                    const r = await fetch(`https://api.exconvert.com/convert?from=USD&to=EUR&amount=${a}&access_key=7ac9d0d8-2c2a1729-0a51382b-b85cd112`),
                      d = await r.json();
                    document.getElementById(o).value = d.result?.EUR ? d.result.EUR.toFixed(2) : 'N/A';
                  } catch {
                    document.getElementById(o).value = 'Error';
                  }
                },
                calculate = () => {
                  const t = parseFloat(total_amount.value) || 0,
                    p = parseFloat(percentage.value) || 0,
                    a = t - (p * t / 100);
                  total_amount_after_percentage.value = a.toFixed(2);
                  convertToEUR(t, 'total_amount_in_eur');
                  convertToEUR(a, 'total_amount_after_percentage_in_eur');
                },
                fetchClient = async (id) => {
                    if (!id) return;
                    try {
                      const r = await fetch('api/get_methods/get_check_client_type.php', {
                          method: 'POST',
                          headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                          },
                          body: new URLSearchParams({
                            customer_id: id
                          })
                        }),
                        d = await r.json();
                      type.value = d.status === 'success' ? d.type : 'individual';
                    } catch {
                      type.value = 'individual';
                    }
                  },
                  customer_id = document.getElementById('customer_id'),
                  percentage = document.getElementById('percentage'),
                  total_amount = document.getElementById('total_amount'),
                  total_amount_after_percentage = document.getElementById('total_amount_after_percentage'),
                  type = document.getElementById('type');
              total_amount.addEventListener('input', calculate);
              percentage.addEventListener('input', calculate);
              customer_id.addEventListener('change', e => fetchClient(e.target.value));
              new Selectr('#customer_id', {
                searchable: true,
                width: 300
              });
              new Selectr('#invoice_status', {
                searchable: true,
                width: 300
              });
              flatpickr("#created_date", {
                dateFormat: "Y-m-d",
                maxDate: "today"
              });
            });
          </script>
          <div class="tab-content text-dark" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-lista_e_faturave" role="tabpanel" aria-labelledby="pills-lista_e_faturave-tab">
              <div class="table-responsive">
                <div class="row">
                  <div class="mb-3 w-25">
                    <label for="monthFilter" class="form-label">Filtro sipas muajit</label>
                    <select id="monthFilter" class="form-select" aria-label="Month Filter">
                      <option value="">Select a month...</option>
                      <?php
                      $sql = "SELECT DISTINCT i.item AS month FROM invoices i JOIN klientet k ON i.customer_id=k.id WHERE k.lloji_klientit='Personal' ORDER BY i.id DESC";
                      $result = mysqli_query($conn, $sql);
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['month']}'>{$row['month']}</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <hr>
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
                <table id="invoiceListBiznes" class="table table-bordered w-100" data-source="get_invoices_biznes.php">
                  <thead class="table-light">
                    <tr>
                      <th></th>
                      <th class="text-dark" style="font-size:12px">Emri i klientit</th>
                      <th class="text-dark" style="font-size:12px">Pershkrimi</th>
                      <th class="text-dark" style="font-size:12px">Detajet</th>
                      <th class="text-dark" style="font-size:12px">Shuma e paguar</th>
                      <th class="text-dark" style="font-size:12px">F. EUR</th>
                      <th class="text-dark" style="font-size:12px">Obligim</th>
                      <th class="text-dark" style="font-size:12px">Veprim</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="tab-pane fade text-dark" id="pills-lista_e_faturave_te_kryera" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera-tab">
              <div class="row">
                <div class="col">
                  <label for="start_date1" class="form-label" style="font-size:14px;">Prej:</label>
                  <p class="text-muted" style="font-size:10px;">Zgjidhni një diapazon fillues të dates për të filtruar rezultatet</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0 text-dark" style="background-color:white;cursor:pointer;"><i class="fi fi-rr-calendar"></i></span>
                    <input type="date" id="start_date1" name="start_date1" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor:pointer;" readonly>
                  </div>
                </div>
                <div class="col text-dark">
                  <label for="end_date1" class="form-label" style="font-size:14px;">Deri:</label>
                  <p class="text-muted" style="font-size:10px;">Zgjidhni një diapazon mbarues të dates për të filtruar rezultatet.</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0" style="background-color:white;cursor:pointer;"><i class="fi fi-rr-calendar"></i></span>
                    <input type="text" id="end_date1" name="end_date1" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor:pointer;" readonly>
                  </div>
                </div>
              </div>
              <div class="col-2 my-4">
                <button id="clearFilters" class="input-custom-css px-3 py-2"><i class="fi fi-rr-clear-alt"></i>Pastro filtrat</button>
              </div>
              <hr>
              <div class="table-responsive">
                <table id="paymentsTable" class="table table-bordered w-100">
                  <thead class="table-light">
                    <tr>
                      <th class="text-dark" style="font-size:12px;width:10px;">Emri i klientit</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Data</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Banka</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Përshkrimi</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Shuma e paguar</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">F. EUR</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Veprim</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="tab-pane fade text-dark" id="pills-lista_e_faturave_te_kryera_biznes" role="tabpanel" aria-labelledby="pills-lista_e_faturave_te_kryera_biznes-tab">
              <div class="row">
                <div class="col">
                  <label for="startDateBiznes" class="form-label" style="font-size:14px;">Prej:</label>
                  <p class="text-muted" style="font-size:10px;">Zgjidhni një diapazon fillues të dates për të filtruar rezultatet</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0" style="background-color:white;cursor:pointer;"><i class="fi fi-rr-calendar"></i></span>
                    <input type="date" id="startDateBiznes" name="startDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor:pointer;" readonly>
                  </div>
                </div>
                <div class="col">
                  <label for="endDateBiznes" class="form-label" style="font-size:14px;">Deri:</label>
                  <p class="text-muted" style="font-size:10px;">Zgjidhni një diapazon mbarues të dates për të filtruar rezultatet.</p>
                  <div class="input-group rounded-5">
                    <span class="input-group-text border-0" style="background-color:white;cursor:pointer;"><i class="fi fi-rr-calendar"></i></span>
                    <input type="text" id="endDateBiznes" name="endDateBiznes" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor:pointer;" readonly>
                  </div>
                </div>
              </div>
              <div class="col-2 my-4">
                <button id="clearFiltersBtnBiznes" class="input-custom-css px-3 py-2"><i class="fi fi-rr-clear-alt"></i>Pastro filtrat</button>
              </div>
              <hr>
              <div class="table-responsive">
                <table id="paymentsTableBiznes" class="table table-bordered w-100">
                  <thead class="table-light">
                    <tr>
                      <th class="text-dark" style="font-size:12px;width:10px;">Emri i klientit</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Data</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Banka</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Përshkrimi</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Shuma e paguar</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">F. EUR</th>
                      <th class="text-dark" style="font-size:12px;width:10px;">Veprim</th>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    function formatRoundedNumber(data, currencySymbol = '€') {
      const number = parseFloat(data);
      if (isNaN(number)) return data;
      const rounded = Math.floor(number);
      return `${rounded.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ${currencySymbol}`;
    }
    function createActionButton(options) {
      const {
        href = '#', iconClass = '', classes = '', dataAttributes = '', disabled = false, tooltip = '', target = '_self'
      } = options;
      if (disabled) return `<button type="button" class="btn btn-outline-secondary ${classes}" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltip}"><i class="${iconClass}"></i></button>`;
      const tooltipAttributes = tooltip ? `data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltip}"` : '';
      return `<a href="${href}" class="btn input-custom-css px-2 py-2 ${classes}" ${dataAttributes} target="${target}" ${tooltipAttributes}><i class="${iconClass}"></i></a>`;
    }
    async function getCustomerNameAsync(customerId) {
      try {
        const response = await fetch('api/get_methods/get_customer_name.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            customer_id: customerId
          })
        });
        if (!response.ok) throw new Error(`Network response was not ok: ${response.statusText}`);
        return await response.text();
      } catch (error) {
        console.error('Error fetching customer name:', error);
        return 'Unknown Customer';
      }
    }
    function initializeDataTable(tableSelector, ajaxConfig) {
      return $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        searching: {
          regex: true
        },
        paging: true,
        pagingType: 'full_numbers',
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'><'col-md-6'p>>",
        ajax: ajaxConfig,
        order: [
          [0, 'desc']
        ],
        orderable: false,
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, 'Te gjitha']
        ],
        initComplete: function() {
          $(".dt-buttons").removeClass("dt-buttons btn-group");
          $("div.dataTables_length select").addClass("form-select").css({
            width: 'auto',
            margin: '0 8px',
            padding: '0.375rem 1.75rem 0.375rem 0.75rem',
            lineHeight: '1.5',
            border: '1px solid #ced4da',
            borderRadius: '0.25rem',
          });
        },
        language: {
          url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json'
        },
        buttons: [{
            extend: 'pdf',
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
            className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
          },
          {
            extend: 'excelHtml5',
            text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
            className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
            exportOptions: {
              modifier: {
                search: 'applied',
                order: 'applied',
                page: 'all'
              }
            },
          },
          {
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
            className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
          },
          {
            text: '<i class="fi fi-rr-trash fa-lg"></i> Fshij',
            className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
            action: function() {
              handleDeleteAction(tableSelector);
            },
          },
        ],
        stripeClasses: ['stripe-color'],
        columnDefs: [{
          targets: '_all',
          render: function(data, type, row) {
            return type === 'display' && data ? `<div class="compact-cell" style="white-space: normal;">${data}</div>` : data;
          },
        }],
        columns: getColumnsConfiguration(tableSelector),
        autoWidth: true,
        scrollX: false,
        createdRow: function(row) {
          $(row).addClass('compact-row');
        },
      });
    }
    function handleDeleteAction(tableSelector) {
      const tableInstance = $(tableSelector).DataTable();
      const selectedIds = $('.row-checkbox:checked').map(function() {
        return $(this).data('id');
      }).get();
      if (selectedIds.length === 0) {
        Swal.fire({
          icon: 'info',
          title: 'Nuk ke zgjedhur elemente',
          text: 'Ju lutem zgjedhni elemente për t\'i fshirë.'
        });
        return;
      }
      Swal.fire({
        icon: 'warning',
        title: 'Konfirmo Fshirjen',
        text: 'A jeni i sigurt që dëshironi të fshini elementet e zgjedhura?',
        showCancelButton: true,
        confirmButtonText: 'Po, Fshij',
        cancelButtonText: 'Anulo'
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
                text: response
              });
              tableInstance.ajax.reload(null, false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.error('Error deleting items:', textStatus, errorThrown);
              Swal.fire({
                icon: 'error',
                title: 'Gabim gjatë fshirjes',
                text: 'Dicka shkoi keq gjatë fshirjes. Ju lutem provoni përsëri.'
              });
            }
          });
        }
      });
    }
    function getColumnsConfiguration(tableSelector) {
      const commonColumns = [{
          data: 'id',
          render: function(data) {
            return `<input type="checkbox" class="row-checkbox" data-id="${data}">`;
          }
        },
        {
          data: 'customer_name',
          render: function(data, type, row) {
            // Consistent rendering logic for customer name
            const difference = row.customer_loan_amount - row.customer_loan_paid;
            const dotHTML = difference > 0 ? `<div class="custom-tooltip"><div class="custom-dot"></div><span class="custom-tooltiptext">${difference} €</span></div>` : '';
            const subaccountHTML = row.subaccount_name ? `<small class="subaccount-name">(${row.subaccount_name})</small>` : '';
            return `<div class="compact-cell"><p class="mb-1">${data}</p>${subaccountHTML}${dotHTML}</div>`;
          }
        },
        {
          data: 'item',
          render: function(data, type, row) {
            // Consistent rendering logic for item
            const stateClass = row.state_of_invoice === 'Parregullt' ? 'bg-danger' : row.state_of_invoice === 'Rregullt' ? 'bg-success' : '';
            return `<div class="item-column">${data || ''}</div><br><div class="badge-column">${row.state_of_invoice ? `<span class="badge ${stateClass} mx-1 rounded-5">${row.state_of_invoice}</span>` : ''}${row.type ? `<span class="badge bg-secondary mx-1 rounded-5">${row.type}</span>` : ''}</div>`;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            const amounts = [{
                amount: row.total_amount,
                currency: row.total_amount_currency || '$'
              },
              {
                amount: row.total_amount_after_percentage,
                currency: row.total_amount_after_percentage_currency || '$'
              },
              {
                amount: row.total_amount_in_eur,
                currency: '€',
                condition: row.total_amount_in_eur
              },
              {
                amount: row.total_amount_in_eur_after_percentage,
                currency: '€',
                condition: row.total_amount_in_eur_after_percentage
              },
            ];
            const details = amounts.filter(item => item.condition !== false).map(item => `<div class="col-12">${formatRoundedNumber(item.amount, item.currency)}</div>`).join('');
            return `<div class="row g-2">${details}</div>`;
          }
        },
        {
          data: 'paid_amount',
          render: function(data, type, row) {
            return formatRoundedNumber(data, row.paid_amount_currency || '€');
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            const profit = row.total_amount_in_eur - row.total_amount_in_eur_after_percentage;
            return formatRoundedNumber(profit, '€');
          }
        },
        {
          data: 'remaining_amount',
          render: function(data, type, row) {
            let remainingAmount, currency;
            if (row.total_amount_in_eur_after_percentage != null) {
              remainingAmount = row.total_amount_in_eur_after_percentage - row.paid_amount;
              currency = '€';
            } else {
              remainingAmount = row.total_amount_after_percentage - row.paid_amount;
              currency = '$';
            }
            remainingAmount = remainingAmount.toFixed(2);
            return formatRoundedNumber(remainingAmount, currency);
          }
        },
      ];
      const actionsColumn = {
        data: 'actions',
        render: function(data, type, row) {
          // Consistent rendering logic for actions
          const totalAmount = row.total_amount_in_eur_after_percentage ?? row.total_amount_after_percentage;
          const remainingAmount = totalAmount - row.paid_amount;
          const actionButtons = `
        <div class="btn-group" role="group" aria-label="Action Buttons">
          ${createActionButton({
            href: '#',
            iconClass: 'fi fi-rr-euro',
            classes: 'open-payment-modal',
            dataAttributes: `data-id="${row.id}" data-invoice-number="${row.invoice_number}" data-customer-id="${row.customer_id}" data-item="${row.item}" data-total-amount="${totalAmount}" data-paid-amount="${row.paid_amount}" data-remaining-amount="${remainingAmount}"`,
            tooltip: 'Open Payment Modal',
          })}
          ${createActionButton({
            href: `complete_invoice.php?id=${row.id}`,
            iconClass: 'fi fi-rr-edit',
            target: '_blank',
            tooltip: 'Edit Invoice',
          })}
          ${createActionButton({
            href: `print_invoice.php?id=${row.invoice_number}`,
            iconClass: 'fi fi-rr-print',
            target: '_blank',
            tooltip: 'Print Invoice',
          })}
          ${row.customer_email ? createActionButton({
            href: '#',
            iconClass: 'fi fi-rr-file-export',
            classes: 'send-invoice',
            dataAttributes: `data-id="${row.id}"`,
            tooltip: 'Send Invoice to Customer',
          }) : createActionButton({
            iconClass: 'fi fi-rr-file-export',
            disabled: true,
            tooltip: 'Nuk posedon email',
          })}
          ${row.email_of_contablist ? createActionButton({
            href: '#',
            iconClass: 'fi fi-rr-envelope',
            classes: 'send-invoices',
            dataAttributes: `data-id="${row.id}" data-invoice-number="${row.invoice_number}" data-email="${row.email_of_contablist}"`,
            tooltip: 'Send to Contablist',
          }) : createActionButton({
            iconClass: 'fi fi-rr-envelope',
            disabled: true,
            tooltip: 'Nuk posedon email të kontablistit',
          })}
        </div>`;
          const uploadOrDownloadButton = row.file_path ?
            `<a class="btn input-custom-css px-2 py-2" style="text-decoration:none;text-transform:none;" href="${row.file_path}" download data-bs-toggle="tooltip" data-bs-placement="top" title="Download File"><i class="fi fi-rr-download"></i></a>` :
            `<button type="button" class="btn input-custom-css px-2 py-2 upload-button" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#fileUploadModal-${row.id}" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload File"><i class="fi fi-rr-upload"></i></button>`;
          const fileUploadModal = `
        <div class="modal fade" id="fileUploadModal-${row.id}" tabindex="-1" aria-labelledby="fileUploadModalLabel-${row.id}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="fileUploadModalLabel-${row.id}">Ngarko faturën nga klienti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="fileUploadForm-${row.id}" enctype="multipart/form-data" action="api/post_methods/post_upload_invoice.php" method="POST">
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
        </div>`;
          return `${actionButtons}${uploadOrDownloadButton}${fileUploadModal}`;
        }
      };
      // Return the same columns for both tables
      return [...commonColumns, actionsColumn];
    }
    function handleCustomerChange() {
      const customerSelect = document.getElementById('customer_id'),
        percentageInput = document.getElementById('percentage'),
        totalAmountInput = document.getElementById('total_amount'),
        totalAfterPercentageInput = document.getElementById('total_amount_after_percentage');
      customerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex],
          percentage = parseFloat(selectedOption.getAttribute('data-percentage')) || 0,
          totalAmount = parseFloat(totalAmountInput.value) || 0,
          totalAfterPercentage = (totalAmount - totalAmount * (percentage / 100)).toFixed(2);
        percentageInput.value = percentage;
        totalAfterPercentageInput.value = totalAfterPercentage;
      });
    }
    function handleTotalAmountInput() {
      const totalAmountInput = document.getElementById('total_amount'),
        percentageInput = document.getElementById('percentage'),
        totalAfterPercentageInput = document.getElementById('total_amount_after_percentage');
      totalAmountInput.addEventListener('input', function() {
        const totalAmount = parseFloat(this.value) || 0,
          percentage = parseFloat(percentageInput.value) || 0,
          totalAfterPercentage = (totalAmount - totalAmount * percentage / 100).toFixed(2);
        totalAfterPercentageInput.value = totalAfterPercentage;
      });
    }
    function handlePaymentModal() {
      $(document).on('click', '.open-payment-modal', async function(e) {
        e.preventDefault();
        const button = $(this),
          id = button.data('id'),
          invoiceNumber = button.data('invoice-number'),
          customerId = button.data('customer-id'),
          item = button.data('item'),
          totalAmount = button.data('total-amount'),
          paidAmount = button.data('paid-amount'),
          remainingAmount = button.data('remaining-amount'),
          customerName = await getCustomerNameAsync(customerId),
          currency = button.data('currency') || '€';
        $('#invoiceId').val(id);
        $('#invoiceNumber').text(invoiceNumber);
        $('#customerName').text(customerName);
        $('#item').text(item);
        $('#totalAmount').text(formatRoundedNumber(totalAmount, currency));
        $('#paidAmount').text(formatRoundedNumber(paidAmount, currency));
        $('#remainingAmount').text(formatRoundedNumber(remainingAmount, currency));
        $('#customerId').text(customerId);
        $('#titleOfInvoice').text(`Fatura: ${invoiceNumber}`);
        $('#paymentAmount').val(Math.floor(remainingAmount).toFixed(2));
        $('.error-message').text('');
        $('#paymentModal').modal('show');
      });
    }
    function handlePaymentSubmission() {
      $('#submitPayment').on('click', function(e) {
        e.preventDefault();
        const invoiceId = $('#invoiceId').val(),
          paymentAmount = $('#paymentAmount').val(),
          bankInfo = $('#bankInfo').val(),
          typeOfPay = $('#type_of_pay').val(),
          description = $('#description').val();
        if (!invoiceId || !paymentAmount || !bankInfo || !typeOfPay) {
          Swal.fire({
            title: 'Plotësoni të gjitha fushat',
            text: 'Ju lutemi plotësoni të gjitha fushat për të kryer pagesën.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
          });
          return;
        }
        $.ajax({
          url: 'api/post_methods/post_payment.php',
          method: 'POST',
          data: {
            invoiceId,
            paymentAmount,
            bankInfo,
            type_of_pay: typeOfPay,
            description
          },
          success: function(response) {
            if (response.trim() === 'success') {
              $('#paymentModal').modal('hide');
              Swal.fire({
                title: 'Pagesa është kryer me sukses',
                icon: 'success',
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                timer: 5000,
                html: `<div style="text-align: left;"><p>Numri i Faturës: ${invoiceId}</p><p>Emri i Klientit: ${$('#customerName').text()}</p><p>Shuma e Paguar: ${paymentAmount} €</p></div>`,
                width: '500px',
              });
              const currentPage = table.page.info().page,
                currentPageSecondTable = tableSecond.page.info().page;
              table.ajax.reload(null, false).then(() => table.page(currentPage).draw(false));
              tableSecond.ajax.reload(null, false).then(() => tableSecond.page(currentPageSecondTable).draw(false));
            } else {
              Swal.fire({
                title: 'Pagesa dështoi',
                text: `Pagesa për ID-në e faturës: ${invoiceId} ka dështuar. Ju lutem provoni përsëri.`,
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
              });
              console.error(`Pagesa dështoi: ${response}`);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error('Gabim në kryerjen e pagesës:', textStatus, errorThrown);
            Swal.fire({
              title: 'Gabim gjatë kryerjes së pagesës',
              text: 'Dicka shkoi keq gjatë pagesës. Ju lutem provoni përsëri.',
              icon: 'error',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 5000,
            });
          }
        });
      });
    }
    function initializeFilterListeners(tableInstance) {
      $('#monthFilter, #amountFilter').on('change keyup', function() {
        tableInstance.draw();
      });
    }
    handleCustomerChange();
    handleTotalAmountInput();
    handlePaymentModal();
    handlePaymentSubmission();
    const table = initializeDataTable('#invoiceList', {
      url: $('#invoiceList').data('source'),
      data: function(d) {
        d.month = $('#monthFilter').val();
        d.amount = $('#amountFilter').val();
      },
    });
    const tableSecond = initializeDataTable('#invoiceListBiznes', {
      url: 'api/get_methods/get_invoices_biznes.php',
      type: 'POST',
    });
    initializeFilterListeners(table);
    initializeFilterListeners(tableSecond);
    function getCurrentDate() {
      const today = new Date(),
        dd = String(today.getDate()).padStart(2, '0'),
        mm = String(today.getMonth() + 1).padStart(2, '0'),
        yyyy = today.getFullYear();
      return `${yyyy}${mm}${dd}`;
    }
  });
</script>
<script src="pro_invoice.js"></script>
<script src="percentage_calculations.js"></script>
<!-- <script src="create_manual_invoice.js"></script> -->
<script src="paymentsTable.js"></script>
<script src="states.js"></script>
<?php include 'partials/footer.php' ?>
</body>
</html>