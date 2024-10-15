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
            <ul class="nav nav-pills bg-white my-2 mx-0 rounded-5" id="pills-tab" role="tablist" style="width: fit-content; border: 1px solid lightgrey;">
                <li class="nav-item" role="presentation">
                    <button style="text-transform: none" class="nav-link active rounded-5" id="pills-TabelaTatimi-tab" data-bs-toggle="pill" data-bs-target="#pills-TabelaTatimi" type="button" role="tab" aria-controls="pills-TabelaTatimi" aria-selected="true">Tabela Tatimi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button style="text-transform: none" class="nav-link rounded-5" id="pills-TatimiFormulari-tab" data-bs-toggle="pill" data-bs-target="#pills-TatimiFormulari" type="button" role="tab" aria-controls="pills-TatimiFormulari" aria-selected="false">Tatimi Formulari</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button style="text-transform: none" class="nav-link rounded-5" id="pills-Guida-tab" data-bs-toggle="pill" data-bs-target="#pills-Guida" type="button" role="tab" aria-controls="pills-Guida" aria-selected="false">Guida vizuale</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button style="text-transform: none" class="nav-link rounded-5" id="pills-GuidaVideo-tab" data-bs-toggle="pill" data-bs-target="#pills-GuidaVideo" type="button" role="tab" aria-controls="pills-GuidaVideo" aria-selected="false">Guida Video</button>
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
                            <div class="row mb-2 g-2 align-items-end">
                                <!-- Kategoria Filter -->
                                <div class="col-md-3">
                                    <label for="filterKategoria" class="form-label">Kategoria</label>
                                    <select id="filterKategoria" class="form-select rounded-5">
                                        <option value="">Të gjitha</option>
                                        <option value="Kontribute">Kontribute</option>
                                        <option value="TVSH">TVSH</option>
                                        <option value="TV">Tatimi i vlerës së shtuar</option>
                                        <option value="Tatim">Tatim</option>
                                    </select>
                                </div>
                                <!-- Periudha Filter Section -->
                                <div class="col-md-3 periudha-container">
                                    <!-- Periudha Select for TVSH -->
                                    <div id="filterPeriudhaSelectContainer">
                                        <label for="filterPeriudhaSelect" class="form-label">Periudha</label>
                                        <select id="filterPeriudhaSelect" class="form-select rounded-5">
                                            <option value="">Të gjitha</option>
                                            <option value="TM1">TM1</option>
                                            <option value="TM2">TM2</option>
                                            <option value="TM3">TM3</option>
                                            <option value="TM4">TM4</option>
                                        </select>
                                    </div>
                                    <!-- Periudha Date Picker for Kontribute and Tatim -->
                                    <div id="filterPeriudhaDateContainer" style="display: none;">
                                        <label for="filterPeriudhaDate" class="form-label">Periudha</label>
                                        <input type="text" id="filterPeriudhaDate" class="form-control rounded-5" placeholder="Select Month and Year">
                                    </div>
                                    <script>
                                        // Initialize Flatpickr with Month Select Plugin for month/year selection (no range)
                                        flatpickr("#filterPeriudhaDate", {
                                            plugins: [
                                                new monthSelectPlugin({
                                                    shorthand: true, // Short month names like Jan, Feb
                                                    dateFormat: "m-Y", // Output format for the selected date
                                                    altFormat: "F Y", // Alternative display format (e.g., April 2024)
                                                    theme: "light" // Choose the theme; 'light' or 'dark'
                                                })
                                            ],
                                            locale: "sq",
                                            minDate: "2015-01", // Earliest selectable month
                                        });
                                    </script>
                                </div>
                                <!-- Forma e Pagesës Filter -->
                                <div class="col-md-3">
                                    <label for="filterFormaPageses" class="form-label">Forma e Pagesës</label>
                                    <select id="filterFormaPageses" class="form-select rounded-5">
                                        <option value="">Të gjitha</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                                
                                <!-- Shtetsia Filter -->
                                <div class="col-md-2">
                                    <label for="filterShteti" class="form-label">Shteti</label>
                                    <select id="filterShteti" name="filterShteti" class="form-select rounded-5">
                                        <option value="">Të gjitha</option>
                                        <option value="Kosovë">Kosovë</option>
                                        <option value="Shqipëri">Shqipëri</option>
                                    </select>
                                </div>
                                <!-- Reset Filters Button -->
                                <div class="col-md-1 d-flex align-items-center">
                                    <button id="resetFilters" class="input-custom-css px-3 py-2 w-100 rounded-5" title="Rivendos Filterat">
                                        <i class="fi fi-rr-rotate-right"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- dottet separator -->
                            <hr style="height: 3px;">
                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover rounded-5" id="tatimiTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Kategoria</th>
                                            <th>Data e Pagesës</th>
                                            <th>Përshkrimi</th>
                                            <th>Shtetsia</th>
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
                                                <i class="fi fi-rr-info text-primary"></i>
                                            </span>
                                        </label>
                                        <select class="form-select" id="zgjedhKategorine" name="kategoria" required>
                                            <option value="" selected disabled>-- Zgjidhni një kategori --</option>
                                            <option value="Kontribute">Kontribute (CM)</option>
                                            <option value="TVSH">TVSH (QL , QS)</option>
                                            <option value="TV">Tatimi i vlerës së shtuar (TV)</option>
                                            <option value="Tatim">Tatim (WM)</option>
                                        </select>
                                    </div>
                                    <!-- Data e Pagesës -->
                                    <div class="col-md-6">
                                        <label for="dataPageses" class="form-label">
                                            Data e Pagesës
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni datën kur është kryer pagesa.">
                                                <i class="fi fi-rr-info text-primary"></i>
                                            </span>
                                        </label>
                                        <input type="date" class="form-control" id="dataPageses" name="data_pageses" required>
                                    </div>
                                    <!-- Përshkrimi -->
                                    <div class="col-md-6">
                                        <label for="pershkrimi" class="form-label">
                                            Përshkrimi
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani një përshkrim të shkurtër të transaksionit tuaj.">
                                                <i class="fi fi-rr-info text-primary"></i>
                                            </span>
                                        </label>
                                        <textarea class="form-control" id="pershkrimi" name="pershkrimi" rows="2" placeholder="Përshkruani transaksionin..." required></textarea>
                                    </div>
                                    <!-- I need a input for invoice id but can be text and number -->
                                    <div class="col-md-6">
                                        <label for="invoice_id" class="form-label">
                                            ID e Faturës
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni ID e faturës që i kryer pagesa.">
                                                <i class="fi fi-rr-info text-primary"></i>
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" id="invoice_id" name="invoice_id" required>
                                    </div>
                                    <!-- Periudha Select for TVSH -->
                                    <div class="col-md-6 periudha-container">
                                        <!-- Periudha Select for TVSH -->
                                        <div id="zgjedhPeriodenSelectContainer">
                                            <label for="zgjedhPeriodenSelect" class="form-label">Zgjedh Periodën</label>
                                            <select class="form-select" id="zgjedhPeriodenSelect" name="periudha" required>
                                                <option value="" selected disabled>-- Zgjidhni një periudhë --</option>
                                                <option value="TM1">TM1</option>
                                                <option value="TM2">TM2</option>
                                                <option value="TM3">TM3</option>
                                                <option value="TM4">TM4</option>
                                            </select>
                                        </div>
                                        <!-- Periudha Date Picker for Kontribute and Tatim -->
                                        <div id="zgjedhPeriodenDateContainer" style="display: none;">
                                            <label for="zgjedhPeriodenDate" class="form-label">Zgjedh Periodën</label>
                                            <input type="month" class="form-control" id="zgjedhPeriodenDate" name="periudha" required disabled>
                                        </div>
                                        <script>
                                            // Initialize Flatpickr with Month Select Plugin for month/year selection (no range)
                                            flatpickr("#zgjedhPeriodenDate", {
                                                plugins: [
                                                    new monthSelectPlugin({
                                                        shorthand: true, // Short month names like Jan, Feb
                                                        dateFormat: "m-Y", // Output format for the selected date
                                                        altFormat: "F Y", // Alternative display format (e.g., April 2024)
                                                        theme: "light" // Choose the theme; 'light' or 'dark'
                                                    })
                                                ],
                                                locale: "sq",
                                                minDate: "2015-01", // Earliest selectable month
                                            });
                                        </script>
                                    </div>
                                    <!-- Shëno Vlerën -->
                                    <div class="col-md-6">
                                        <label for="shenoVleren" class="form-label">
                                            Shëno Vlerën
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni vlerën e transaksionit në €.">
                                                <i class="fi fi-rr-info text-primary"></i>
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" id="shenoVleren" name="vlera" required>
                                    </div>
                                    <!-- Shtetsia -->
                                    <div class="col-md-6">
                                        <label for="shenoVleren" class="form-label">
                                            Shteti
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni shtetin ku eshte bere tatimi">
                                                <i class="fi fi-rr-info text-primary"></i>
                                            </span>
                                        </label>
                                        <select class="form-select" id="shteti" name="shteti" required>
                                            <option value="" selected disabled>-- Zgjidhni një shtet --</option>
                                            <option value="Kosovë">Kosovë</option>
                                            <option value="Shqipëri">Shqipëri</option>
                                        </select>
                                    </div>
                                    <!-- Forma e Pagesës -->
                                    <div class="col-md-6">
                                        <label for="formaPageses" class="form-label">
                                            Forma e Pagesës
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni mënyrën se si është kryer pagesa.">
                                                <i class="fi fi-rr-info text-primary"></i>
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
                                                <i class="fi fi-rr-info text-primary"></i>
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
                <div class="tab-pane fade" id="pills-Guida" role="tabpanel" aria-labelledby="pills-Guida-tab" tabindex="0">
                    <div class="mb-3 rounded-5 bordered bg-white">
                        <img src="guidaPerTatime.png" class="img-fluid" alt="">
                    </div>
                </div>
                <!-- Plyr CSS -->
                <link rel="stylesheet" href="https://cdn.plyr.io/3.7.2/plyr.css" />
                <!-- Plyr JS -->
                <script src="https://cdn.plyr.io/3.7.2/plyr.js"></script>
                <div class="tab-pane fade" id="pills-GuidaVideo" role="tabpanel" aria-labelledby="pills-GuidaVideo-tab" tabindex="0">
                    <div class="mb-3 rounded-5 bordered bg-white">
                        <video id="player" class="img-fluid" playsinline controls>
                            <source src="assets/guidaPerTatime.mp4" type="video/mp4" />
                        </video>
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
                                            <i class="fi fi-rr-info text-primary"></i>
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
                                            <i class="fi fi-rr-info text-primary"></i>
                                        </span>
                                    </label>
                                    <input type="date" class="form-control" id="edit_data_pageses" name="data_pageses" required>
                                </div>
                                <!-- Përshkrimi -->
                                <div class="mb-3">
                                    <label for="edit_pershkrimi" class="form-label">
                                        Përshkrimi
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Shkruani një përshkrim të shkurtër të transaksionit tuaj.">
                                            <i class="fi fi-rr-info text-primary"></i>
                                        </span>
                                    </label>
                                    <textarea class="form-control" id="edit_pershkrimi" name="pershkrimi" rows="2" placeholder="Përshkruani transaksionin..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <!-- ID e Faturës -->
                                    <label for="edit_invoice_id" class="form-label">
                                        ID e Faturës
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni ID e faturës që mbështet transaksionin.">
                                            <i class="fi fi-rr-info text-primary"></i>
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_invoice_id" name="edit_invoice_id">
                                </div>
                                <!-- Periudha Select for TVSH -->
                                <div id="editPeriudhaSelectContainer" class="mb-3">
                                    <label for="edit_periudhaSelect" class="form-label">Zgjedh Periodën</label>
                                    <select class="form-select" id="edit_periudhaSelect" name="periudha" required>
                                        <option value="" selected disabled>-- Zgjidhni një periudhë --</option>
                                        <option value="TM1">TM1</option>
                                        <option value="TM2">TM2</option>
                                        <option value="TM3">TM3</option>
                                        <option value="TM4">TM4</option>
                                    </select>
                                </div>
                                <!-- Periudha Date Picker for Kontribute and Tatim -->
                                <div id="editPeriudhaDateContainer" class="mb-3" style="display: none;">
                                    <label for="edit_periudhaDate" class="form-label">Zgjedh Periodën</label>
                                    <input type="month" class="form-control" id="edit_periudhaDate" name="periudha" required disabled>
                                </div>
                                <script>
                                    flatpickr("#edit_periudhaDate", {
                                        plugins: [
                                            new monthSelectPlugin({
                                                shorthand: true, // Short month names like Jan, Feb
                                                dateFormat: "m-Y", // Output format for the selected date
                                                altFormat: "F Y", // Alternative display format (e.g., April 2024)
                                                theme: "light" // Choose the theme; 'light' or 'dark'
                                            })
                                        ],
                                        locale: "sq",
                                        minDate: "2015-01", // Earliest selectable month
                                    });
                                </script>
                                <!-- Shëno Vlerën -->
                                <div class="mb-3">
                                    <label for="edit_vlera" class="form-label">
                                        Shëno Vlerën
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni vlerën e transaksionit në €.">
                                            <i class="fi fi-rr-info text-primary"></i>
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="edit_vlera" name="vlera" required>
                                </div>
                                <!-- Shtetsia -->
                                <div class="mb-3">
                                    <label for="edit_shteti" class="form-label">
                                        Shteti
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni shtetin që kryer pagesa.">
                                            <i class="fi fi-rr-info text-primary"></i>
                                        </span>
                                    </label>
                                    <select class="form-select" id="edit_shteti" name="edit_shteti" required>
                                        <option value="" selected disabled>-- Zgjidhni një shtet --</option>
                                        <option value="Kosovë">Kosovë</option>
                                        <option value="Shqipëri">Shqipëri</option>
                                    </select>
                                </div>
                                <!-- Forma e Pagesës -->
                                <div class="mb-3">
                                    <label for="edit_forma_pageses" class="form-label">
                                        Forma e Pagesës
                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni mënyrën se si është kryer pagesa.">
                                            <i class="fi fi-rr-info text-primary"></i>
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
                                            <i class="fi fi-rr-info text-primary"></i>
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
                                <p>Ngarko dokumentin...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(() => {
                // Function to update the total Vlera badge
                const updateTotalVlera = () => {
                    const total = table.column(6, {
                        filter: 'applied'
                    }).data().reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                    $('#totalVleraBadge').text(`Total Vlera: ${total.toFixed(2)} €`);
                };
                // Initialize Bootstrap tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();
                // Initialize Flatpickr for filterPeriudhaDate
                flatpickr("#filterPeriudhaDate", {
                    plugins: [
                        new monthSelectPlugin({
                            shorthand: true,
                            dateFormat: "m-Y",
                            altFormat: "F Y",
                            theme: "light"
                        })
                    ],
                    locale: "sq",
                    minDate: "2015-01",
                });
                // Initialize DataTable
                const table = $('#tatimiTable').DataTable({
                    ajax: {
                        url: 'get_tatimi.php',
                        dataSrc: ''
                    },
                    columns: [{
                            data: 'id'
                        },
                        {
                            data: 'invoice_id'
                        },
                        {
                            data: 'kategoria'
                        },
                        {
                            data: 'data_pageses',
                            render: data => {
                                const date = new Date(data);
                                return new Intl.DateTimeFormat('sq', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }).format(date);
                            }
                        },
                        {
                            data: 'pershkrimi'
                        },
                        {
                            data: 'shteti'
                        },
                        {
                            data: 'periudha'
                        },
                        {
                            data: 'vlera',
                            render: $.fn.dataTable.render.number(',', '.', 2, '')
                        },
                        {
                            data: 'forma_pageses'
                        },
                        {
                            data: 'dokument',
                            render: data => `
                        <button class="input-custom-css px-3 py-2 viewDocBtn" data-url="${data}">
                            <i class="fi fi-rr-file"></i>
                        </button>
                    `,
                            orderable: false
                        },
                        {
                            data: null,
                            render: data => `
                        <button class="input-custom-css px-3 py-2 editBtn me-2" data-id="${data.id}">
                            <i class="fi fi-rr-pencil"></i>
                        </button>
                        <button class="input-custom-css px-3 py-2 deleteBtn" data-id="${data.id}">
                            <i class="fi fi-rr-trash"></i>
                        </button>
                    `,
                            orderable: false
                        }
                    ],
                    dom: `
                <'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>
                <'row'<'col-md-12'tr>>
                <'row'<'col-md-6'i><'col-md-6'p>>
            `,
                    buttons: [{
                            extend: "pdfHtml5",
                            text: '<i class="bi bi-file-earmark-pdf-fill"></i> PDF',
                            titleAttr: "Eksporto tabelën në formatin PDF",
                            className: "btn btn-light btn-sm me-2 rounded-5"
                        },
                        {
                            extend: "copyHtml5",
                            text: '<i class="bi bi-clipboard-fill"></i> Kopjo',
                            titleAttr: "Kopjo tabelën në Clipboard",
                            className: "btn btn-light btn-sm me-2 rounded-5"
                        },
                        {
                            extend: "excelHtml5",
                            text: '<i class="bi bi-file-earmark-excel-fill"></i> Excel',
                            titleAttr: "Eksporto tabelën në formatin Excel",
                            className: "btn btn-light btn-sm me-2 rounded-5",
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
                            text: '<i class="bi bi-printer-fill"></i> Printo',
                            titleAttr: "Printo tabelën",
                            className: "btn btn-light btn-sm me-2 rounded-5"
                        }
                    ],
                    initComplete: () => {
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
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/sq.json"
                    },
                    stripeClasses: ['stripe-color'],
                    paging: true,
                    lengthChange: true,
                    lengthMenu: [5, 14, 25, 50, -1],
                    pageLength: 5,
                    order: [
                        [0, "desc"]
                    ],
                    drawCallback: updateTotalVlera
                });
                // Function to toggle Periudha fields based on Kategoria
                const togglePeriudhaFields = ($kategoriaSelect, $periudhaSelectContainer, $periudhaDateContainer) => {
                    const selectedKategoria = $kategoriaSelect.val();
                    if (selectedKategoria === 'TVSH') {
                        $periudhaSelectContainer.show().find('select').attr('required', true).prop('disabled', false);
                        $periudhaDateContainer.hide().find('input').removeAttr('required').prop('disabled', true);
                    } else if (['Kontribute', 'Tatim', 'TV'].includes(selectedKategoria)) {
                        $periudhaSelectContainer.hide().find('select').removeAttr('required').prop('disabled', true);
                        $periudhaDateContainer.show().find('input').attr('required', true).prop('disabled', false);
                    } else {
                        $periudhaSelectContainer.hide().find('select').removeAttr('required').prop('disabled', true);
                        $periudhaDateContainer.hide().find('input').removeAttr('required').prop('disabled', true);
                    }
                };
                // Initialize Periudha fields on page load
                const initializePeriudhaFields = () => {
                    // Filter Section
                    togglePeriudhaFields(
                        $('#filterKategoria'),
                        $('#filterPeriudhaSelectContainer'),
                        $('#filterPeriudhaDateContainer')
                    );
                    // Add Form
                    togglePeriudhaFields(
                        $('#zgjedhKategorine'),
                        $('#zgjedhPeriodenSelectContainer'),
                        $('#zgjedhPeriodenDateContainer')
                    );
                    // Edit Modal
                    togglePeriudhaFields(
                        $('#edit_kategoria'),
                        $('#editPeriudhaSelectContainer'),
                        $('#editPeriudhaDateContainer')
                    );
                };
                initializePeriudhaFields();
                // Event listeners for Kategoria selects
                $('#filterKategoria, #zgjedhKategorine, #edit_kategoria').on('change', function() {
                    const isFilter = $(this).attr('id') === 'filterKategoria';
                    togglePeriudhaFields(
                        $(this),
                        isFilter ? $('#filterPeriudhaSelectContainer') : ($(this).attr('id') === 'zgjedhKategorine' ? $('#zgjedhPeriodenSelectContainer') : $('#editPeriudhaSelectContainer')),
                        isFilter ? $('#filterPeriudhaDateContainer') : ($(this).attr('id') === 'zgjedhKategorine' ? $('#zgjedhPeriodenDateContainer') : $('#editPeriudhaDateContainer'))
                    );
                    if (isFilter) table.draw();
                });
                // Event listeners for all filter inputs, including #filterShteti
                $('#filterPeriudhaSelect, #filterPeriudhaDate, #filterFormaPageses, #filterDataPageses, #filterShteti').on('change', () => table.draw());
                // ** Updated Custom Filtering Logic **
                $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
                    const table = $('#tatimiTable').DataTable();
                    const rowData = table.row(dataIndex).data();
                    const kategoria = $('#filterKategoria').val() ? $('#filterKategoria').val().toLowerCase() : '';
                    const periudhaSelect = $('#filterPeriudhaSelect').val() ? $('#filterPeriudhaSelect').val().toLowerCase() : '';
                    const periudhaDate = $('#filterPeriudhaDate').val() ? $('#filterPeriudhaDate').val() : '';
                    const formaPageses = $('#filterFormaPageses').val() ? $('#filterFormaPageses').val().toLowerCase() : '';
                    const dataPageses = $('#filterDataPageses').val() ? $('#filterDataPageses').val() : '';
                    const shtetsia = $('#filterShteti').val() ? $('#filterShteti').val().toLowerCase() : '';
                    const rowKategoria = rowData.kategoria.toLowerCase();
                    const rowPeriudha = rowData.periudha.toLowerCase();
                    const rowFormaPageses = rowData.forma_pageses.toLowerCase();
                    const rowDataPageses = rowData.data_pageses;
                    const rowShteti = rowData.shteti.toLowerCase();
                    // Kategoria Filter
                    if (kategoria && rowKategoria !== kategoria) return false;
                    // Periudha Filter
                    if (kategoria === 'tvsh') {
                        if (periudhaSelect && rowPeriudha !== periudhaSelect) return false;
                    } else if (['kontribute', 'tatim', 'tv'].includes(kategoria)) {
                        if (periudhaDate && rowPeriudha !== periudhaDate) return false;
                    }
                    // Forma e Pagesës Filter
                    if (formaPageses && rowFormaPageses !== formaPageses) return false;
                    // Data e Pagesës Filter
                    if (dataPageses && rowDataPageses !== dataPageses) return false;
                    // Shtetsia Filter
                    if (shtetsia && rowShteti !== shtetsia) return false;
                    return true;
                });
                // Handle Reset Filters
                $('#resetFilters').on('click', e => {
                    e.preventDefault();
                    $('#filterKategoria, #filterPeriudhaSelect, #filterPeriudhaDate, #filterFormaPageses, #filterDataPageses, #filterShteti').val('');
                    togglePeriudhaFields(
                        $('#filterKategoria'),
                        $('#filterPeriudhaSelectContainer'),
                        $('#filterPeriudhaDateContainer')
                    );
                    table.draw();
                });
                // Handle Form Submission via AJAX (Add Form)
                $('#tatimiForm').on('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    $.ajax({
                        url: 'process_form.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        beforeSend: () => $('#tatimiForm button[type="submit"]').prop('disabled', true).text('Dërgo...'),
                        success: response => {
                            $('#tatimiForm button[type="submit"]').prop('disabled', false).text('Dërgo');
                            const alertType = response.status === 'success' ? 'success' : 'danger';
                            const alertMessage = response.message;
                            $('#formAlert').html(`
                        <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
                            ${alertMessage}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                            if (response.status === 'success') {
                                $('#tatimiForm')[0].reset();
                                togglePeriudhaFields(
                                    $('#zgjedhKategorine'),
                                    $('#zgjedhPeriodenSelectContainer'),
                                    $('#zgjedhPeriodenDateContainer')
                                );
                                table.ajax.reload(null, false);
                            }
                        },
                        error: () => {
                            $('#tatimiForm button[type="submit"]').prop('disabled', false).text('Dërgo');
                            $('#formAlert').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Dështoi dërgimi i formularit. Ju lutem provoni përsëri.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);
                        }
                    });
                });
                // Handle Delete Button Click
                $('#tatimiTable tbody').on('click', '.deleteBtn', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Jeni të sigurt?',
                        text: "Ky veprim nuk mund të kthehet!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Po, fshij!'
                    }).then(result => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'delete_tatimi.php',
                                type: 'POST',
                                data: {
                                    id
                                },
                                dataType: 'json',
                                success: response => {
                                    const icon = response.status === 'success' ? 'success' : 'error';
                                    Swal.fire(response.status === 'success' ? 'U Fshi!' : 'Gabim!', response.message, icon);
                                    if (response.status === 'success') table.ajax.reload(null, false);
                                },
                                error: () => Swal.fire('Gabim!', 'Dështoi fshirja e rekordit.', 'error')
                            });
                        }
                    });
                });
                // Handle Edit Button Click
                $('#tatimiTable tbody').on('click', '.editBtn', function() {
                    const id = $(this).data('id');
                    $.ajax({
                        url: 'get_tatimi.php',
                        type: 'GET',
                        data: {
                            id
                        },
                        dataType: 'json',
                        success: response => {
                            if (response.status === 'success') {
                                const data = response.data;
                                // Populate Edit Modal Fields
                                const editForm = $('#editTatimiForm');
                                editForm.find('#edit_id').val(data.id);
                                editForm.find('#edit_kategoria').val(data.kategoria);
                                editForm.find('#edit_data_pageses').val(data.data_pageses);
                                editForm.find('#edit_pershkrimi').val(data.pershkrimi);
                                editForm.find('#edit_vlera').val(data.vlera);
                                editForm.find('#edit_shteti').val(data.shteti);
                                editForm.find('#edit_forma_pageses').val(data.forma_pageses);
                                editForm.find('#edit_invoice_id').val(data.invoice_id);
                                // Toggle Periudha Fields
                                togglePeriudhaFields(
                                    editForm.find('#edit_kategoria'),
                                    $('#editPeriudhaSelectContainer'),
                                    $('#editPeriudhaDateContainer')
                                );
                                // Set Periudha Value
                                if (data.kategoria === 'TVSH') {
                                    editForm.find('#edit_periudhaSelect').val(data.periudha);
                                } else {
                                    editForm.find('#edit_periudhaDate').val(data.periudha);
                                }
                                // Show Edit Modal
                                $('#editModal').modal('show');
                            } else {
                                Swal.fire('Gabim!', response.message, 'error');
                            }
                        },
                        error: () => Swal.fire('Gabim!', 'Dështoi marrja e të dhënave.', 'error')
                    });
                });
                // Handle Edit Form Submission via AJAX
                $('#editTatimiForm').on('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    $.ajax({
                        url: 'update_tatimi.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        beforeSend: () => $('#editTatimiForm button[type="submit"]').prop('disabled', true).text('Ruaj...'),
                        success: response => {
                            $('#editTatimiForm button[type="submit"]').prop('disabled', false).text('Ruaj Ndryshimet');
                            const icon = response.status === 'success' ? 'success' : 'error';
                            Swal.fire(response.status === 'success' ? 'Sukses!' : 'Gabim!', response.message, icon);
                            if (response.status === 'success') {
                                $('#editModal').modal('hide');
                                table.ajax.reload(null, false);
                            }
                        },
                        error: () => {
                            $('#editTatimiForm button[type="submit"]').prop('disabled', false).text('Ruaj Ndryshimet');
                            Swal.fire('Gabim!', 'Dështoi përditësimi i të dhënave.', 'error');
                        }
                    });
                });
                // Handle View Document Button Click
                $('#tatimiTable tbody').on('click', '.viewDocBtn', function() {
                    const docUrl = $(this).data('url');
                    if (docUrl) {
                        const fileExt = docUrl.split('.').pop().toLowerCase();
                        let embedHtml = '';
                        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                            embedHtml = `<img src="${docUrl}" alt="Dokumenti" class="img-fluid">`;
                        } else if (fileExt === 'pdf') {
                            embedHtml = `<iframe src="${docUrl}" width="100%" height="600px" style="border: none;"></iframe>`;
                        } else if (['doc', 'docx'].includes(fileExt)) {
                            embedHtml = `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(docUrl)}&embedded=true" width="100%" height="600px" style="border: none;"></iframe>`;
                        } else {
                            embedHtml = `<p>Nuk është mbështetur për shikim në modal.</p>`;
                        }
                        $('#documentContent').html(embedHtml);
                        $('#viewDocumentModal').modal('show');
                    } else {
                        Swal.fire('Gabim!', 'Dokumenti nuk u gjet.', 'error');
                    }
                });
            });
        </script>
    </div>
</div>
<?php include 'partials/footer.php'; ?>