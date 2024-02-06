<?php include 'partials/header.php'; ?>

<style>
    .wrap-text {
        white-space: normal !important;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="pagesat.php" class="text-reset" style="text-decoration: none;">Pagesat e kryera</a></li>
                </nav>


                <div class="row">
                    <div class="col">
                        <div class="card shadow-sm rounded-5">
                            <div class="container table-responsive p-3">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="min" class="form-label" style="font-size: 14px;">Prej:</label>
                                        <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                                            dates për të filtruar rezultatet.</p>
                                        <div class="input-group rounded-5">

                                            <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span>
                                            <input type="text" id="min" name="min" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;">
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
                                        <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                                            dates për tëfiltruar rezultatet.</p>
                                        <div class="input-group rounded-5">

                                            <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="max" name="max" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;">
                                        </div>


                                    </div>
                                </div>


                                <table id="paymentTable" class="table table-bordered" style="width:100%">

                                    <thead class="bg-light">
                                        <!-- <td>#</td> -->
                                        <td>
                                            Klienti
                                        </td>
                                        <td>
                                            Fatura
                                        </td>
                                        <td>
                                            Pershkrimi
                                        </td>
                                        <td>
                                            Shuma
                                        </td>
                                        <td>
                                            Menyra
                                        </td>
                                        <td>
                                            Data
                                        </td>
                                        <td>
                                            Kategorizimi
                                        </td>
                                        <td>Fatura PDF</td>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $invoices_query = $conn->query("SELECT fatura, kategoria, SUM(shuma) as total_shuma, menyra, data, pershkrimi
                                        FROM pagesat 
                                        GROUP BY fatura, kategoria
                                        ORDER BY id DESC");


                                        while ($invoice_row = mysqli_fetch_assoc($invoices_query)) {
                                            $invoice_number = $invoice_row['fatura'];
                                            $total_shuma = $invoice_row['total_shuma'];
                                            $menyra = $invoice_row['menyra'];
                                            $data = $invoice_row['data'];
                                            $pershkrimi = $invoice_row['pershkrimi'];

                                            $invoice_info = $conn->query("SELECT * FROM fatura WHERE fatura='$invoice_number'");
                                            $invoice_data = mysqli_fetch_array($invoice_info);

                                            if (!empty($invoice_data)) {
                                                $client_id = $invoice_data['emri'];
                                                $client_info = $conn->query("SELECT * FROM klientet WHERE id='$client_id'");
                                                $client_data = mysqli_fetch_array($client_info);
                                        ?>
                                                <tr>
                                                    <td class="wrap-text">
                                                        <?= $client_data['emri']; ?>
                                                    </td>
                                                    <td class="wrap-text">
                                                        <?= $invoice_number; ?>
                                                    </td>
                                                    <td class="wrap-text">
                                                        <?= $pershkrimi; ?>
                                                    </td>
                                                    <td class="wrap-text">
                                                        <?= $total_shuma; ?>
                                                    </td>
                                                    <td class="wrap-text">
                                                        <?= $menyra; ?>
                                                    </td>
                                                    <td class="wrap-text">
                                                        <?= date("d-m-Y", strtotime($data)); ?>
                                                    </td>
                                                    <td class="wrap-text">
                                                        <?php
                                                        if (!empty($invoice_row['kategoria'])) {
                                                            $kategoria = @unserialize($invoice_row['kategoria']);
                                                            if ($kategoria !== false) {
                                                                $kategoria = array_map(function ($value) {
                                                                    return ($value == 'null') ? 'Ska' : $value;
                                                                }, $kategoria);
                                                                echo implode(", ", $kategoria);
                                                            } else {
                                                                echo str_replace('null', 'Ska', $invoice_row['kategoria']);
                                                            }
                                                        } else {
                                                            echo '';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-light py-1 px-2 border border-1" target="_blank" href="fatura.php?invoice=<?= $invoice_number; ?>">
                                                            <i class="fi fi-rr-print"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>

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
<script type="text/javascript">
    $(document).ready(function() {
        let minDate, maxDate;

        // Create date inputs
        minDate = new DateTime('#min', {
            format: 'MMMM Do YYYY'
        });
        maxDate = new DateTime('#max', {
            format: 'MMMM Do YYYY'
        });

        function getCurrentDate() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            return yyyy + mm + dd;
        }
        let table = $('#paymentTable').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",

            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    filename: "faturat_e_kryera_" + getCurrentDate() + "" // Set custom filename for PDF
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: "Kopjo tabelen ne formatin Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    filename: "faturat_e_kryera_" + getCurrentDate() + "" // Set custom filename for Copy
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
                    filename: "faturat_e_kryera_" + getCurrentDate() + "" // Set custom filename for Excel
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    filename: "faturat_e_kryera_" + getCurrentDate() + "" // Set custom filename for Print
                },
            ],

            order: [],
            stripeClasses: ['stripe-color'],
            columnDefs: [{
                width: '10%',
                targets: '_all'
            }, {
                targets: 5, // Assuming the "Data" column is at index 5
                type: 'date-range',
                // Customize the date format if needed
                render: function(data) {
                    return moment(data, 'DD-MM-YYYY').format('YYYY-MM-DD');
                }
            }],
            responsive: false,
            // dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' + 'Brtip',
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select'); // add Bootstrap form-select class
                lengthSelect.css({
                    'width': 'auto', // adjust width to fit content
                    'margin': '0 8px', // add some margin around the element
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem', // adjust padding to match Bootstrap's styles
                    'line-height': '1.5', // adjust line-height to match Bootstrap's styles
                    'border': '1px solid #ced4da', // add border to match Bootstrap's styles
                    'border-radius': '0.25rem', // add border radius to match Bootstrap's styles
                }); // adjust width to fit content
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
        });

        // Custom filtering function which will search data in column four between two values
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let min = minDate.val();
            let max = maxDate.val();
            let date = new Date(data[5]);

            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        });

        // Refilter the table
        $('#min, #max').on('change', function() {
            table.draw();
        });
    });
</script>