<?php include 'partials/header.php'; ?>
<?php
$is_pagesat_page = (basename($_SERVER['PHP_SELF']) === 'pagesat.php');
?>
<?php if ($is_pagesat_page) : ?>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="versionModal" tabindex="-1" aria-labelledby="versionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Zgjedh Versionin e Faqes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Pagesat janë krijuar në dy versione. Ju lutem zgjidhni njërin prej tyre.</p>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Pagesa ( Versioni Vjetër )</button>
                        <a href="invoice.php" class="btn btn-primary">Pagesa ( Versioni i Ri )</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Trigger Modal on Page Load -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var versionModal = new bootstrap.Modal(document.getElementById('versionModal'));
            versionModal.show();
        });
    </script>
<?php endif; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid py-4">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white px-3 py-2 rounded-5">
                    <li class="breadcrumb-item"><a href="#" class="text-reset text-decoration-none">Financat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pagesat e Kryera</li>
                </ol>
            </nav>

            <!-- Card Container -->
            <div class="card shadow-sm rounded-5 mt-3">
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="min" class="form-label">Prej:</label>
                            <input type="text" id="min" name="min" class="form-control flatpickr" placeholder="Zgjidhni datën">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max" class="form-label">Deri:</label>
                            <input type="text" id="max" name="max" class="form-control flatpickr" placeholder="Zgjidhni datën">
                        </div>
                    </div>

                    <!-- DataTable -->
                    <table id="paymentTable" class="table table-bordered w-100 text-dark">
                        <thead class="table-light">
                            <tr>
                                <th>Klienti</th>
                                <th>Fatura</th>
                                <th>Përshkrimi</th>
                                <th>Shuma</th>
                                <th>Mënyra</th>
                                <th>Data</th>
                                <th>Kategorizimi</th>
                                <th>Fatura PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated by DataTables via AJAX -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td id="totalShuma" class="text-start"><strong>0.00 €</strong></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<!-- Initialize DataTables and Flatpickr -->
<script>
    $(document).ready(function() {
        // Initialize Flatpickr
        flatpickr(".flatpickr", {
            locale: "sq",
            dateFormat: "d-m-Y",
            allowInput: true
        });

        // Initialize DataTable with AJAX
        var table = $('#paymentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'api/get_methods/get_payments.php',
                type: 'POST',
                data: function(d) {
                    d.min = $('#min').val();
                    d.max = $('#max').val();
                },
                dataSrc: function(json) {
                    $('#totalShuma').html('<strong>' + json.totalSum + '</strong>');
                    return json.data;
                }
            },
            // Arben Buzhala
            // 
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7, orderable: false, searchable: false }
            ],
            dom: "<'row mb-3'<'col-md-3'l><'col-md-6 text-center'B><'col-md-3'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [
                {
                    extend: "pdfHtml5",
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    titleAttr: "Eksporto në PDF",
                    className: "btn btn-danger btn-sm"
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fa fa-copy"></i> Kopjo',
                    titleAttr: "Kopjo në Clipboard",
                    className: "btn btn-secondary btn-sm"
                },
                {
                    extend: "excelHtml5",
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    titleAttr: "Eksporto në Excel",
                    className: "btn btn-success btn-sm",
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: "print",
                    text: '<i class="fa fa-print"></i> Printo',
                    titleAttr: "Printo tabelën",
                    className: "btn btn-info btn-sm"
                }
            ],
            order: [[5, 'desc']],
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var total = api.ajax.json() ? api.ajax.json().totalSum : '0.00 €';
                $('#totalShuma').html('<strong>' + total + '</strong>');
            }
        });

        // Event listeners for date filters
        $('#min, #max').change(function() {
            table.ajax.reload();
        });
    });
</script>

<style>
    .wrap-text {
        white-space: normal !important;
    }
    table tfoot {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-light i, .btn-danger i, .btn-secondary i, .btn-success i, .btn-info i {
        margin-right: 5px;
    }
</style>
