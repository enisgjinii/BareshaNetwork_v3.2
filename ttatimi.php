<?php include 'partials/header.php'; ?>
<?php
// Include the database connection
include 'conn-d.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Tatimi</a></li>
                </ol>
            </nav>
            <!-- Nav Pills for Table and Form -->
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-5 text-uppercase" id="pills-TabelaTatimi-tab" data-bs-toggle="pill" data-bs-target="#pills-TabelaTatimi" type="button" role="tab" aria-controls="pills-TabelaTatimi" aria-selected="true">Tabela Tatimi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5 text-uppercase" id="pills-TatimiFormulari-tab" data-bs-toggle="pill" data-bs-target="#pills-TatimiFormulari" type="button" role="tab" aria-controls="pills-TatimiFormulari" aria-selected="false">Tatimi Formulari</button>
                </li>
            </ul>
            <!-- Tab Content -->
            <div class="tab-content" id="pills-tabContent">
                <!-- Tabela Tatimi Tab -->
                <div class="tab-pane fade show active" id="pills-TabelaTatimi" role="tabpanel" aria-labelledby="pills-TabelaTatimi-tab" tabindex="0">
                    <!-- Data Table Card -->
                    <div class="card shadow-sm rounded-5">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Të Dhënat e Tatimit</h5>
                            <!-- Sum Badge -->
                            <span class="badge bg-success rounded-5" id="totalVleraBadge">Total Vlera: 0.00 €</span>
                        </div>
                        <div class="card-body">
                            <!-- Alert for Actions -->
                            <div id="tableAlert"></div>
                            <!-- Filter Inputs -->
                            <div class="row mb-3">
                                <!-- Kategoria Filter -->
                                <div class="col-md-3">
                                    <label for="filterKategoria" class="form-label">Kategoria</label>
                                    <select id="filterKategoria" class="form-select">
                                        <option value="">Të gjitha</option>
                                        <option value="Kontribute">Kontribute</option>
                                        <option value="TVSH">TVSH</option>
                                        <option value="Tatim">Tatim</option>
                                    </select>
                                </div>
                                <!-- Periudha Filter -->
                                <div class="col-md-3">
                                    <label for="filterPeriudha" class="form-label">Periudha</label>
                                    <select id="filterPeriudha" class="form-select">
                                        <option value="">Të gjitha</option>
                                        <option value="TM1">TM1</option>
                                        <option value="TM2">TM2</option>
                                        <option value="TM3">TM3</option>
                                        <option value="TM4">TM4</option>
                                    </select>
                                </div>
                                <!-- Forma e Pagesës Filter -->
                                <div class="col-md-3">
                                    <label for="filterFormaPageses" class="form-label">Forma e Pagesës</label>
                                    <select id="filterFormaPageses" class="form-select">
                                        <option value="">Të gjitha</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                                <!-- Data e Pagesës Filter -->
                                <div class="col-md-3">
                                    <label for="filterDataPageses" class="form-label">Data e Pagesës</label>
                                    <input type="date" id="filterDataPageses" class="form-control" placeholder="Data">
                                </div>
                            </div>
                            <!-- Reset Filters Button -->
                            <div class="row mb-3">
                                <div class="col-md-1 d-flex align-items-end">
                                    <button id="resetFilters" class="input-custom-css px-3 py-2 w-100" title="Rivendos Filterat">
                                        <i class="fi fi-rr-rotate-right"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="tatimiTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Kategoria</th>
                                            <th>Data e Pagesës</th>
                                            <th>Përshkrimi</th>
                                            <th>Periudha</th>
                                            <th>Vlera (€)</th>
                                            <th>Forma e Pagesës</th>
                                            <th>Dokument</th>
                                            <th>Veprimet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tatimi Formulari Tab -->
                <div class="tab-pane fade" id="pills-TatimiFormulari" role="tabpanel" aria-labelledby="pills-TatimiFormulari-tab" tabindex="0">
                    <!-- Form Card -->
                    <div class="card shadow-sm rounded-5 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Tatimi Formulari</h5>
                        </div>
                        <div class="card-body">
                            <!-- Display Success and Error Messages -->
                            <div id="formAlert"></div>
                            <!-- Form -->
                            <form id="tatimiForm" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <!-- Zgjedh Kategorinë -->
                                    <div class="col-md-6">
                                        <label for="zgjedhKategorine" class="form-label">
                                            Zgjedh Kategorinë
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni kategorinë që i përket transaksionit tuaj.">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <select class="form-select" id="zgjedhKategorine" name="kategoria" required>
                                            <option value="" selected disabled>-- Zgjidhni një kategori --</option>
                                            <option value="Kontribute">Kontribute</option>
                                            <option value="TVSH">TVSH</option>
                                            <option value="Tatim">Tatim</option>
                                        </select>
                                    </div>
                                    <!-- Data e Pagesës -->
                                    <div class="col-md-6">
                                        <label for="dataPageses" class="form-label">
                                            Data e Pagesës
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën kur është kryer pagesa.">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <input type="date" class="form-control" id="dataPageses" name="data_pageses" required>
                                    </div>
                                    <!-- Përshkrimi -->
                                    <div class="col-12">
                                        <label for="pershkrimi" class="form-label">
                                            Përshkrimi
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani një përshkrim të shkurtër të transaksionit tuaj.">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <textarea class="form-control" id="pershkrimi" name="pershkrimi" rows="2" placeholder="Përshkruani transaksionin..." required></textarea>
                                    </div>
                                    <!-- Zgjedh Periodën -->
                                    <div class="col-md-6">
                                        <label for="zgjedhPerioden" class="form-label">
                                            Zgjedh Periodën
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni periudhën për të cilën aplikohet tatimi.">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <select class="form-select" id="zgjedhPerioden" name="periudha" required>
                                            <option value="" selected disabled>-- Zgjidhni një periudhë --</option>
                                            <option value="TM1">TM1</option>
                                            <option value="TM2">TM2</option>
                                            <option value="TM3">TM3</option>
                                            <option value="TM4">TM4</option>
                                        </select>
                                    </div>
                                    <!-- Shëno Vlerën -->
                                    <div class="col-md-6">
                                        <label for="shenoVleren" class="form-label">
                                            Shëno Vlerën
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni vlerën e transaksionit në €.">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" id="shenoVleren" name="vlera" required>
                                    </div>
                                    <!-- Forma e Pagesës -->
                                    <div class="col-md-6">
                                        <label for="formaPageses" class="form-label">
                                            Forma e Pagesës
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni mënyrën se si është kryer pagesa.">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <select class="form-select" id="formaPageses" name="forma_pageses" required>
                                            <option value="" selected disabled>-- Zgjidhni një formë pagesë --</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Cash">Cash</option>
                                        </select>
                                    </div>
                                    <!-- Ngarko Dokumentin -->
                                    <div class="col-md-6">
                                        <label for="ngarkoDokumentin" class="form-label">
                                            Ngarko Dokumentin
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Ngarkoni një dokument që mbështet transaksionin (max: 5MB).">
                                                <i class="bi bi-info-circle-fill text-primary"></i>
                                            </span>
                                        </label>
                                        <input type="file" class="form-control" id="ngarkoDokumentin" name="dokument" accept=".pdf,.doc,.docx,.jpg,.png" required>
                                    </div>
                                </div>
                                <!-- Form Buttons -->
                                <div class="mt-3 d-flex justify-content-end">
                                    <button type="reset" class="input-custom-css px-3 py-2 me-2">Anulo</button>
                                    <button type="submit" class="input-custom-css px-3 py-2">Dërgo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="editTatimiForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edito Tatimin</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Hidden ID Field -->
                                <input type="hidden" id="edit_id" name="id">
                                <!-- Zgjedh Kategorinë -->
                                <div class="mb-3">
                                    <label for="edit_kategoria" class="form-label">
                                        Zgjedh Kategorinë
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni kategorinë që i përket transaksionit tuaj.">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <select class="form-select" id="edit_kategoria" name="kategoria" required>
                                        <option value="" selected disabled>-- Zgjidhni një kategori --</option>
                                        <option value="Kontribute">Kontribute</option>
                                        <option value="TVSH">TVSH</option>
                                        <option value="Tatim">Tatim</option>
                                    </select>
                                </div>
                                <!-- Data e Pagesës -->
                                <div class="mb-3">
                                    <label for="edit_data_pageses" class="form-label">
                                        Data e Pagesës
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën kur është kryer pagesa.">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <input type="date" class="form-control" id="edit_data_pageses" name="data_pageses" required>
                                </div>
                                <!-- Përshkrimi -->
                                <div class="mb-3">
                                    <label for="edit_pershkrimi" class="form-label">
                                        Përshkrimi
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani një përshkrim të shkurtër të transaksionit tuaj.">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <textarea class="form-control" id="edit_pershkrimi" name="pershkrimi" rows="2" placeholder="Përshkruani transaksionin..." required></textarea>
                                </div>
                                <!-- Zgjedh Periodën -->
                                <div class="mb-3">
                                    <label for="edit_periudha" class="form-label">
                                        Zgjedh Periodën
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni periudhën për të cilën aplikohet tatimi.">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <select class="form-select" id="edit_periudha" name="periudha" required>
                                        <option value="" selected disabled>-- Zgjidhni një periudhë --</option>
                                        <option value="TM1">TM1</option>
                                        <option value="TM2">TM2</option>
                                        <option value="TM3">TM3</option>
                                        <option value="TM4">TM4</option>
                                    </select>
                                </div>
                                <!-- Shëno Vlerën -->
                                <div class="mb-3">
                                    <label for="edit_vlera" class="form-label">
                                        Shëno Vlerën
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni vlerën e transaksionit në €.">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <input type="number" class="form-control" id="edit_vlera" name="vlera" placeholder="0.00" min="0" step="0.01" required>
                                </div>
                                <!-- Forma e Pagesës -->
                                <div class="mb-3">
                                    <label for="edit_forma_pageses" class="form-label">
                                        Forma e Pagesës
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni mënyrën se si është kryer pagesa.">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <select class="form-select" id="edit_forma_pageses" name="forma_pageses" required>
                                        <option value="" selected disabled>-- Zgjidhni një formë pagesë --</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                                <!-- Ngarko Dokumentin -->
                                <div class="mb-3">
                                    <label for="edit_dokument" class="form-label">
                                        Ngarko Dokumentin
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Ngarkoni një dokument që mbështet transaksionin (max: 5MB).">
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                        </span>
                                    </label>
                                    <input type="file" class="form-control" id="edit_dokument" name="dokument" accept=".pdf,.doc,.docx,.jpg,.png">
                                    <small class="form-text text-muted">Lëreni bosh nëse nuk dëshironi të ndryshoni dokumentin.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbyll</button>
                                <button type="submit" class="btn btn-primary">Ruaj Ndryshimet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- View Document Modal -->
        <div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewDocumentModalLabel">Shiko Dokumentin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Document Display Area -->
                        <div id="documentContent" class="text-center">
                            <!-- Content will be injected via JavaScript -->
                            <p>Ngarko dokumentin...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Initialize Bootstrap Tooltips and DataTable -->
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            // Initialize DataTable
            var table = $('#tatimiTable').DataTable({
                ajax: {
                    url: 'get_tatimi.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'kategoria'
                    },
                    {
                        data: 'data_pageses'
                    },
                    {
                        data: 'pershkrimi'
                    },
                    {
                        data: 'periudha'
                    },
                    {
                        data: 'vlera',
                        render: function(data, type, row) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'forma_pageses'
                    },
                    {
                        data: 'dokument',
                        render: function(data, type, row) {
                            return '<button class="input-custom-css px-3 py-2 viewDocBtn" data-url="' + data + '"><i class="bi bi-file-earmark-text"></i> Shiko</button>';
                        },
                        orderable: false
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<button class="input-custom-css px-3 py-2 editBtn me-2" data-id="' + row.id + '"><i class="bi bi-pencil-square"></i> Edito</button>' +
                                '<button class="input-custom-css px-3 py-2 deleteBtn" data-id="' + row.id + '"><i class="bi bi-trash"></i> Fshij</button>';
                        },
                        orderable: false
                    }
                ],
                dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: [{
                        extend: "pdfHtml5",
                        text: '<i class="bi bi-file-earmark-pdf-fill"></i> PDF',
                        titleAttr: "Eksporto tabelën në formatin PDF",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    },
                    {
                        extend: "copyHtml5",
                        text: '<i class="bi bi-clipboard-fill"></i> Kopjo',
                        titleAttr: "Kopjo tabelën në Clipboard",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    },
                    {
                        extend: "excelHtml5",
                        text: '<i class="bi bi-file-earmark-excel-fill"></i> Excel',
                        titleAttr: "Eksporto tabelën në formatin Excel",
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
                        text: '<i class="bi bi-printer-fill"></i> Printo',
                        titleAttr: "Printo tabelën",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    },
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
                    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/sq.json",
                },
                stripeClasses: ['stripe-color'],
                paging: true,
                lengthChange: true,
                lengthMenu: [
                    [5, 14, 25, 50, -1],
                    [5, 14, 25, 50, "Të gjitha"]
                ],
                order: [
                    [0, "desc"]
                ],
                // Initialize the sum badge on table draw
                drawCallback: function(settings) {
                    updateTotalVlera();
                }
            });
            // Function to update the total Vlera badge
            function updateTotalVlera() {
                var total = 0;
                // Iterate over the visible rows and sum the Vlera column (index 5)
                table.rows({
                    filter: 'applied'
                }).every(function(rowIdx, tableLoop, rowLoop) {
                    var data = this.data();
                    var vlera = parseFloat(data.vlera);
                    if (!isNaN(vlera)) {
                        total += vlera;
                    }
                });
                // Update the badge with the formatted total
                $('#totalVleraBadge').text('Total Vlera: ' + total.toFixed(2) + ' €');
            }
            // Refresh DataTable on successful form submission
            function refreshTable() {
                table.ajax.reload(null, false);
            }
            // Handle Form Submission via AJAX
            $('#tatimiForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: 'process_form.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#tatimiForm button[type="submit"]').prop('disabled', true).text('Dërgo...');
                    },
                    success: function(response) {
                        $('#tatimiForm button[type="submit"]').prop('disabled', false).text('Dërgo');
                        if (response.status === 'success') {
                            $('#formAlert').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                            $('#tatimiForm')[0].reset();
                            refreshTable();
                        } else {
                            $('#formAlert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + response.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        }
                    },
                    error: function() {
                        $('#tatimiForm button[type="submit"]').prop('disabled', false).text('Dërgo');
                        $('#formAlert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Dështoi dërgimi i formularit. Ju lutem provoni përsëri.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    }
                });
            });
            // Handle Delete Button Click
            $('#tatimiTable tbody').on('click', '.deleteBtn', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Jeni të sigurt?',
                    text: "Ky veprim nuk mund të kthehet!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Po, fshij!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_tatimi.php',
                            type: 'POST',
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire(
                                        'U Fshi!',
                                        response.message,
                                        'success'
                                    );
                                    refreshTable();
                                } else {
                                    Swal.fire(
                                        'Gabim!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Gabim!',
                                    'Dështoi fshirja e rekordit.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
            // Handle Edit Button Click
            $('#tatimiTable tbody').on('click', '.editBtn', function() {
                var id = $(this).data('id');
                // Fetch the record data via AJAX
                $.ajax({
                    url: 'get_tatimi.php',
                    type: 'GET',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            var data = response.data;
                            // Populate the modal fields
                            $('#edit_id').val(data.id);
                            $('#edit_kategoria').val(data.kategoria);
                            $('#edit_data_pageses').val(data.data_pageses);
                            $('#edit_pershkrimi').val(data.pershkrimi);
                            $('#edit_periudha').val(data.periudha);
                            $('#edit_vlera').val(data.vlera);
                            $('#edit_forma_pageses').val(data.forma_pageses);
                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                            editModal.show();
                        } else {
                            Swal.fire(
                                'Gabim!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Gabim!',
                            'Dështoi marrja e të dhënave.',
                            'error'
                        );
                    }
                });
            });
            // Handle Edit Form Submission via AJAX
            $('#editTatimiForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: 'update_tatimi.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#editTatimiForm button[type="submit"]').prop('disabled', true).text('Ruaj...');
                    },
                    success: function(response) {
                        $('#editTatimiForm button[type="submit"]').prop('disabled', false).text('Ruaj Ndryshimet');
                        if (response.status === 'success') {
                            Swal.fire(
                                'Sukses!',
                                response.message,
                                'success'
                            );
                            var editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                            editModal.hide();
                            refreshTable();
                        } else {
                            Swal.fire(
                                'Gabim!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        $('#editTatimiForm button[type="submit"]').prop('disabled', false).text('Ruaj Ndryshimet');
                        Swal.fire(
                            'Gabim!',
                            'Dështoi përditësimi i të dhënave.',
                            'error'
                        );
                    }
                });
            });
            // Filter Functionality
            $('#filterKategoria, #filterPeriudha, #filterFormaPageses, #filterDataPageses').on('change keyup', function() {
                table.draw();
            });
            // Handle Reset Filters
            $('#resetFilters').on('click', function() {
                $('#filterKategoria, #filterPeriudha, #filterFormaPageses, #filterDataPageses').val('');
                table.draw();
            });
            // Custom Filtering Logic
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var kategoria = $('#filterKategoria').val().toLowerCase();
                    var periudha = $('#filterPeriudha').val().toLowerCase();
                    var formaPageses = $('#filterFormaPageses').val().toLowerCase();
                    var dataPageses = $('#filterDataPageses').val();
                    var rowKategoria = data[1].toLowerCase(); // Kategoria column
                    var rowPeriudha = data[4].toLowerCase(); // Periudha column
                    var rowFormaPageses = data[6].toLowerCase(); // Forma e Pagesës column
                    var rowDataPageses = data[2]; // Data e Pagesës column
                    // Kategoria Filter
                    if (kategoria && rowKategoria !== kategoria) {
                        return false;
                    }
                    // Periudha Filter
                    if (periudha && rowPeriudha !== periudha) {
                        return false;
                    }
                    // Forma e Pagesës Filter
                    if (formaPageses && rowFormaPageses !== formaPageses) {
                        return false;
                    }
                    // Data e Pagesës Filter
                    if (dataPageses) {
                        // Check if the row's date matches the filter date
                        if (rowDataPageses !== dataPageses) {
                            return false;
                        }
                    }
                    return true;
                }
            );
            // Handle View Document Button Click
            $('#tatimiTable tbody').on('click', '.viewDocBtn', function() {
                var docUrl = $(this).data('url');
                if (docUrl) {
                    var fileExt = docUrl.split('.').pop().toLowerCase();
                    var embedHtml = '';
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                        // Display image
                        embedHtml = '<img src="' + docUrl + '" alt="Dokumenti" class="img-fluid">';
                    } else if (['pdf'].includes(fileExt)) {
                        // Display PDF in iframe
                        embedHtml = '<iframe src="' + docUrl + '" width="100%" height="600px" style="border: none;"></iframe>';
                    } else if (['doc', 'docx'].includes(fileExt)) {
                        // Use Google Docs Viewer for Word documents
                        embedHtml = '<iframe src="https://docs.google.com/gview?url=' + encodeURIComponent(docUrl) + '&embedded=true" width="100%" height="600px" style="border: none;"></iframe>';
                    } else {
                        // Fallback for unsupported formats
                        embedHtml = '<p>Nuk është mbështetur për shikim në modal.</p>';
                    }
                    $('#documentContent').html(embedHtml);
                    var viewDocModal = new bootstrap.Modal(document.getElementById('viewDocumentModal'));
                    viewDocModal.show();
                } else {
                    Swal.fire(
                        'Gabim!',
                        'Dokumenti nuk u gjet.',
                        'error'
                    );
                }
            });
            // Initial sum calculation after data is loaded
            table.on('init.dt', function() {
                updateTotalVlera();
            });
        });
    </script>
</div>
<!-- View Document Modal -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shiko Dokumentin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="documentContent" class="text-center">
                    <p>Ngarko dokumentin...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>