<?php
// pagesat.php
// Include the database connection
include 'partials/header.php';
// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <style> 
        .compact-table th,
        .compact-table td {
            padding: 0.3rem;
            font-size: 0.9rem;
        }

        .compact-table th {
            white-space: nowrap;
        }

        .compact-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .compact-table a.btn,
        .compact-table button.btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
        }

        /* Remove horizontal scroll */
        .table-responsive {
            overflow-x: hidden;
        }

        /* Adjust table layout */
        .compact-table {
            table-layout: fixed;
            width: 100%;
        }

        /* Modal adjustments */
        .modal .form-control {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb Navigation -->
                <nav class="bg-white px-2 rounded-5" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><span class="text-reset">Financat</span></li>
                        <li class="breadcrumb-item active" aria-current="page"><span class="text-reset">Pagesat Youtube</span></li>
                    </ol>
                </nav>
                <!-- Card Container -->
                <div class="card shadow-none border rounded-5">
                    <div class="table-responsive p-3">
                        <!-- Date Range Filters -->
                        <div class="row mb-4">
                            <?php foreach (['min' => 'Prej', 'max' => 'Deri'] as $id => $label): ?>
                                <div class="col-md-6 mb-3">
                                    <label for="<?= $id ?>" class="form-label"><?= $label ?>:</label>
                                    <p class="text-muted small">Zgjidhni një diapazon <?= $id === 'min' ? 'fillues' : 'mbarues' ?> të datës për të filtruar rezultatet.</p>
                                    <div class="input-group rounded-5">
                                        <span class="input-group-text border-0 bg-white"><i class="fi fi-rr-calendar"></i></span>
                                        <input type="text" id="<?= $id ?>" name="<?= $id ?>" class="form-control rounded-5 flatpickr" placeholder="Zgjidhni datën">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Summary Dashboard -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-white bg-primary mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Shuma</h5>
                                        <p class="card-text" id="totalShuma">Loading...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-success mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Mesatare Shuma</h5>
                                        <p class="card-text" id="averageShuma">Loading...</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Fatura</h5>
                                        <p class="card-text" id="totalInvoices">Loading...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Payment DataTable -->
                        <table id="paymentTable" class="table w-100 text-dark compact-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Klienti</th>
                                    <th>Fatura</th>
                                    <th>Pershkrimi</th>
                                    <th>Shuma</th>
                                    <th>Menyra</th>
                                    <th>Data</th>
                                    <th>Kategorizimi</th>
                                    <th>Fatura PDF</th>
                                    <th>Veprime</th> <!-- Actions Column -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate data here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- CSRF Token -->
                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" id="fatura" name="fatura">
                        <div class="mb-3">
                            <label for="client_name" class="form-label">Klienti</label>
                            <input type="text" class="form-control" id="client_name" name="client_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="pershkrimi" class="form-label">Pershkrimi</label>
                            <input type="text" class="form-control" id="pershkrimi" name="pershkrimi" required>
                        </div>
                        <div class="mb-3">
                            <label for="shuma" class="form-label">Shuma</label>
                            <input type="number" step="0.01" class="form-control" id="shuma" name="shuma" required>
                        </div>
                        <div class="mb-3">
                            <label for="menyra" class="form-label">Menyra</label>
                            <select class="form-select" id="menyra" name="menyra" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="text" class="form-control flatpickr" id="data" name="data" placeholder="Zgjidhni datën" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategoria" class="form-label">Kategorizimi</label>
                            <input type="text" class="form-control" id="kategoria" name="kategoria" placeholder="Comma-separated categories">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbyll</button>
                        <button type="button" class="btn btn-primary" id="saveEdit">Ruaj Ndryshimet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Include jQuery and other JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" integrity="sha512-C7qz2SloUR4wnGaJkIKyv+Ad4hD9l9RBFaG+FK8LUY2i4QFRHHiEoO2r4u6v2fhXHz+FqzJ7dY6l6Z+4JzqKbw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- DataTables Date-EU Sorting Plugin -->
    <script src="https://cdn.datatables.net/plug-ins/1.13.1/sorting/date-eu.js"></script>
    <script>
        $(document).ready(() => {
            // Initialize Flatpickr for date filters
            flatpickr(".flatpickr", {
                locale: "sq",
                dateFormat: "d-m-Y",
                allowInput: true
            });
            // Initialize DataTable with Server-Side Processing
            const table = $('#paymentTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'fetch_pagesat.php',
                    type: 'POST',
                    data: function(d) {
                        d.action = 'fetch_payments';
                        d.min = $('#min').val();
                        d.max = $('#max').val();
                        d.csrf_token = '<?= $csrf_token ?>';
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', xhr, error, thrown);
                        alert('An error occurred while fetching data.');
                    }
                },
                columns: [{
                        data: 'client_name'
                    },
                    {
                        data: 'fatura'
                    },
                    {
                        data: 'pershkrimi'
                    },
                    {
                        data: 'total_shuma'
                    },
                    {
                        data: 'menyra'
                    },
                    {
                        data: 'data'
                    },
                    {
                        data: 'kategoria'
                    },
                    {
                        data: 'fatura_pdf',
                        render: function(data, type, row) {
                            return `<a href="fatura.php?invoice=${encodeURIComponent(data)}" target="_blank" class="btn btn-light py-1 px-2 border">
                                        <i class="fi fi-rr-print"></i>
                                    </a>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${row.fatura}"><i class="fi fi-rr-edit"></i></button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.fatura}"><i class="fi fi-rr-trash"></i></button>
                            `;
                        }
                    }
                ],
                dom: `
                    <'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>
                    <'row'<'col-12't>>
                    <'row'<'col-md-6'><'col-md-6'p>>
                `,
                buttons: [{
                        extend: "pdfHtml5",
                        text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
                        titleAttr: "Eksporto në PDF",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                    },
                    {
                        extend: "copyHtml5",
                        text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
                        titleAttr: "Kopjo në Clipboard",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                    },
                    {
                        extend: "excelHtml5",
                        text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
                        titleAttr: "Eksporto në Excel",
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
                        extend: "csvHtml5",
                        text: '<i class="fi fi-rr-file-csv fa-lg"></i> CSV',
                        titleAttr: "Eksporto në CSV",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                    },
                    {
                        extend: "print",
                        text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
                        titleAttr: "Printo tabelën",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                    }
                ],
                order: [
                    [5, 'desc']
                ],
                columnDefs: [{
                        width: '12%',
                        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    {
                        targets: 5,
                        type: 'date-eu'
                    }
                ],
                responsive: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
                },
                stripeClasses: ['stripe-color'],
                initComplete: function(settings, json) {
                    // Populate summary dashboard
                    if (json.summary) {
                        $('#totalShuma').text(parseFloat(json.summary.total_shuma).toLocaleString('de-DE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' EUR');
                        $('#averageShuma').text(parseFloat(json.summary.average_shuma).toLocaleString('de-DE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' EUR');
                        $('#totalInvoices').text(json.summary.total_invoices);
                    }
                    $('.dt-buttons').removeClass('dt-buttons btn-group');
                    $('div.dataTables_length select').addClass('form-select').css({
                        width: 'auto',
                        margin: '0 8px',
                        padding: '0.375rem 1.75rem 0.375rem 0.75rem',
                        lineHeight: '1.5',
                        border: '1px solid #ced4da',
                        borderRadius: '0.25rem'
                    });
                }
            });
            // Custom Filtering by Date Range using DataTables' search
            $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
                const min = $('#min').val();
                const max = $('#max').val();
                const date = moment(data[5], "DD-MM-YYYY");
                if (
                    (!min && !max) ||
                    (!min && date.isSameOrBefore(moment(max, "DD-MM-YYYY"))) ||
                    (moment(min, "DD-MM-YYYY").isSameOrBefore(date) && !max) ||
                    (moment(min, "DD-MM-YYYY").isSameOrBefore(date) && date.isSameOrBefore(moment(max, "DD-MM-YYYY")))
                ) {
                    return true;
                }
                return false;
            });
            // Redraw table on date filter change
            $('#min, #max').change(() => table.draw());
            // Inline Editing: Open Modal with Payment Details
            $('#paymentTable tbody').on('click', '.edit-btn', function() {
                const fatura = $(this).data('id');
                // Fetch current data via AJAX
                $.ajax({
                    url: 'fetch_pagesat.php',
                    type: 'POST',
                    data: {
                        action: 'get_payment',
                        fatura: fatura,
                        csrf_token: $('#csrf_token').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.error) {
                            // Populate modal with data
                            $('#editModal #fatura').val(response.fatura);
                            $('#editModal #client_name').val(response.client_name);
                            $('#editModal #pershkrimi').val(response.pershkrimi);
                            $('#editModal #shuma').val(response.shuma);
                            $('#editModal #menyra').val(response.menyra);
                            $('#editModal #data').val(response.data);
                            $('#editModal #kategoria').val(response.kategoria);
                            // Initialize Flatpickr in Modal
                            flatpickr("#editModal #data", {
                                locale: "sq",
                                dateFormat: "d-m-Y",
                                allowInput: true
                            });
                            $('#editModal').modal('show');
                        } else {
                            alert(response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Failed to fetch payment details.');
                    }
                });
            });
            // Save Edited Data
            $('#saveEdit').click(function() {
                const formData = $('#editForm').serialize();
                $.ajax({
                    url: 'fetch_pagesat.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#editModal').modal('hide');
                            table.ajax.reload(null, false);
                            alert('Payment updated successfully!');
                        } else {
                            alert(response.error || 'Failed to update payment.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('An error occurred while updating the payment.');
                    }
                });
            });
            // Delete Payment
            $('#paymentTable tbody').on('click', '.delete-btn', function() {
                const fatura = $(this).data('id');
                if (confirm('Are you sure you want to delete this payment?')) {
                    $.ajax({
                        url: 'fetch_pagesat.php',
                        type: 'POST',
                        data: {
                            action: 'delete_payment',
                            fatura: fatura,
                            csrf_token: $('#csrf_token').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload(null, false);
                                alert('Payment deleted successfully!');
                            } else {
                                alert(response.error || 'Failed to delete payment.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            alert('An error occurred while deleting the payment.');
                        }
                    });
                }
            });
        });
    </script>
    <?php include 'partials/footer.php'; ?>