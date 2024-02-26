<?php include 'partials/header.php'; ?>
<style>
    /* Selected checkboxes */
    .table td:first-child input[type="checkbox"].selected {
        background-color: red;
        border-color: #007bff;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Facebook</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                            Lista e faturave
                        </a>
                    </li>
                </ol>
            </nav>
            <div class="card shadow-sm rounded-5">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="example2" class="table w-100 border">
                                    <thead class="bg-light">
                                        <tr>
                                            <th></th>
                                            <th>Klienti</th>
                                            <th>Fatura</th>
                                            <th>Pershkrimi</th>
                                            <th>Shuma</th>
                                            <th>Menyra</th>
                                            <th>Data</th>
                                            <th>Kategorizimi</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $invoices = [];
                                        $payments = $conn->query("SELECT * FROM pagesatfacebook ORDER BY data DESC");
                                        while ($payment = mysqli_fetch_array($payments)) {
                                            $invoice_number = $payment['fatura'];
                                            $invoice_info = $conn->query("SELECT * FROM faturafacebook WHERE fatura='$invoice_number'");
                                            $invoice_data = mysqli_fetch_array($invoice_info);
                                            if (!empty($invoice_data)) {
                                                $client_id = $invoice_data['emri'];
                                                $client_info = $conn->query("SELECT * FROM facebook WHERE id='$client_id'");
                                                $client_data = mysqli_fetch_array($client_info);
                                                // Check if the invoice number already exists in the array
                                                if (array_key_exists($invoice_number, $invoices)) {
                                                    // If it exists, add the amount to the existing amount
                                                    $invoices[$invoice_number]['shuma'] += $payment['shuma'];
                                                } else {
                                                    // If it doesn't exist, add it to the array
                                                    $invoices[$invoice_number] = [
                                                        'client' => $client_data['emri_mbiemri'],
                                                        'pershkrimi' => $payment['pershkrimi'],
                                                        'shuma' => $payment['shuma'],
                                                        'menyra' => $payment['menyra'],
                                                        'data' => $payment['data'],
                                                        'kategoria' => $payment['kategoria']
                                                    ];
                                                }
                                            }
                                        }
                                        // Output the invoices
                                        foreach ($invoices as $invoice_number => $invoice) {
                                        ?>
                                            <tr>
                                                <td><input type="checkbox" name="selected_payments[]" value="<?= $invoice_number ?>"></td>
                                                <td><?= $invoice['client']; ?></td>
                                                <td><?= $invoice_number; ?></td>
                                                <td><?= $invoice['pershkrimi']; ?></td>
                                                <td><?= $invoice['shuma']; ?></td>
                                                <td><?= $invoice['menyra']; ?></td>
                                                <td><?= date("d-m-Y", strtotime($invoice['data'])); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($invoice['kategoria'])) {
                                                        $kategoria = @unserialize($invoice['kategoria']);
                                                        if ($kategoria !== false) {
                                                            $kategoria = array_map(function ($value) {
                                                                return ($value == 'null') ? 'Ska' : $value;
                                                            }, $kategoria);
                                                            echo implode(", ", $kategoria);
                                                        } else {
                                                            echo str_replace('null', 'Ska', $invoice['kategoria']);
                                                        }
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-light shadow-2 border border-1" target="_blank" href="fatura3Facebook.php?invoice=<?= $invoice_number; ?>">
                                                        <i class="fi fi-rr-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th></th>
                                            <th>Klienti</th>
                                            <th>Fatura</th>
                                            <th>Pershkrimi</th>
                                            <th>Shuma</th>
                                            <th>Menyra</th>
                                            <th>Data</th>
                                            <th>Kategorizimi</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    $(document).ready(function() {
        var dataTables = $('#example2').DataTable({
            responsive: false,
            search: {
                return: true,
            },
            order: [
                [7, "desc"]
            ],
            pageLength: 10, // limit to 10 entries per page
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:first-child').html('<input type="checkbox"/>');
            },
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select');
                lengthSelect.css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem',
                });
                // Add checkbox column header
                var th = $('<th><input type="checkbox" id="check-all"/></th>').prependTo('#example2 th tr');
                // Handle checkbox events
                $('#check-all').on('change', function() {
                    $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
                });
                $('#example2 tbody').on('click', 'input[type="checkbox"]', function(e) {
                    e.stopPropagation(); // Prevent row selection
                    $(this).closest('tr').toggleClass('selected', $(this).prop('checked'));
                });
            },
            dom: "<'row'<'col-md-3'l><'col-md-9'f>>" +
                "<'row'<'col-md-12'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",

            buttons: [{
                    extend: 'pdfHtml5',
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: 'Eksporto tabelen ne formatin PDF',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
                },
                {
                    extend: 'copyHtml5',
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                    titleAttr: 'Eksporto tabelen ne formatin Excel',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                    exportOptions: {
                        rows: function(idx, data, node) {
                            return $(node).find('input[type="checkbox"]').prop('checked');
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: 'Printo tabel&euml;n',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
                },
                {
                    text: '<i class="fi fi-rr-filter fa-lg"></i>&nbsp;&nbsp; Shfaq kategorine Biznes ',
                    titleAttr: 'Filtro bazuar n&euml; kategorin&euml; e biznesit',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                    action: function(e, dt, node, config) {
                        var value = 'Biznes';
                        dt.column(7).search(value).draw();
                    }
                },
                {
                    text: '<i class="fi fi-rr-filter fa-lg"></i>&nbsp;&nbsp; Shfaq kategorine Personal',
                    titleAttr: 'Filtro bazuar n&euml; kategorin&euml; e biznesit',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                    action: function(e, dt, node, config) {
                        var value = 'Personal';
                        dt.column(7).search(value).draw();
                    }
                },
                {
                    text: '<i class="fi fi-rr-filter fa-lg"></i>&nbsp;&nbsp; Shfaq kategorine Ska',
                    titleAttr: 'Filtro bazuar n&euml; kategorin&euml; e biznesit',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                    action: function(e, dt, node, config) {
                        var value = 'Ska';
                        dt.column(7).search(value).draw();
                    }
                },
                {
                    text: '<i class="fi fi-rr-close fa-lg"></i>&nbsp;&nbsp; Pastro',
                    titleAttr: 'Pastro filtrat',
                    className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                    action: function(e, dt, node, config) {
                        dt.column(7).search('').draw();
                    }
                }
            ],
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
        });
        // Initialize Bootstrap tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Prevent row selection on click (only allow checkbox selection)
        $('#example2 tbody').on('click', 'tr td:not(:first-child)', function(e) {
            e.stopPropagation();
        });
    });
</script>