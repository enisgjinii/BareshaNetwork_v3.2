<?php
// Include necessary partials and database connection
include 'partials/header.php';
include 'conn-d.php';
?>
<!DOCTYPE html>
<html lang="sq">

<head>
    <!-- Existing CSS and Libraries -->
    <!-- Include Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Include SweetAlert2 CSS (if not already included in header.php) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include Selectr CSS (if not already included) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.css">
    <!-- Include any other necessary CSS here -->
    <style>
        /* Existing Styles */
        /* ... (Your existing CSS) ... */

        /* Custom styles for the modal */
        #documentImage {
            max-height: 80vh;
            object-fit: contain;
        }

        #documentMessage {
            text-align: center;
        }

        /* Custom styles for DataTables */
        .table-responsive {
            overflow-x: auto;
        }

        /* Optional: Adjust badge positioning */
        .badge {
            font-size: 0.75rem;
        }

        /* Adjust nav-pills for better responsiveness */
        .nav-pills {
            flex-wrap: nowrap;
        }

        .nav-pills .nav-link {
            white-space: nowrap;
        }

        /* Customize Swal alerts to use Bootstrap buttons */
        .swal2-popup.custom-swal-popup {
            width: 100% !important;
            max-width: 400px;
            /* Reduced max-width for compactness */
            padding: 1.5rem;
            /* Adjust padding as needed */
        }

        .swal2-title {
            font-size: 1.25rem;
            /* Adjust title font size */
            margin-bottom: 1rem;
        }

        .swal2-input,
        .swal2-select {
            width: 100% !important;
            padding: 0.5rem;
            box-sizing: border-box;
        }

        .swal2-confirm,
        .swal2-cancel {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .form-label {
            font-weight: 600;
        }

        .form-text {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Additional Flatpickr styling (optional) */
        .flatpickr-calendar {
            z-index: 9999;
            /* Ensure the calendar appears above other elements */
        }

        /* Chart Container Styling */
        #lineChart,
        #doughnutChart,
        #barChart {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Card Body Padding Adjustment for Charts */
        .card-body {
            padding: 1rem;
        }

        /* START BULK EDIT ENHANCEMENTS */

        /* Style for the Bulk Edit Button */
        #bulkEditButton {
            margin-bottom: 15px;
        }

        /* Adjust table for checkbox column */
        th.select-checkbox {
            width: 40px;
            text-align: center;
        }

        td.select-checkbox {
            text-align: center;
        }

        /* END BULK EDIT ENHANCEMENTS */
    </style>
</head>

<body>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb Navigation -->
                <nav class="bg-white px-3 py-2 rounded-5 mb-4" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-reset text-decoration-none">Kontabiliteti</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php echo __FILE__; ?>" class="text-reset text-decoration-none">Shpenzimet e objektit</a>
                        </li>
                    </ol>
                </nav>
                <!-- Add Expense Button -->
                <div class="row mb-4 position-relative">
                    <div class="col-12">
                        <a href="post_newKont.php" class="input-custom-css px-3 py-2 text-decoration-none">
                            Shto shpenzim
                        </a>
                    </div>
                </div>
                <!-- Main Tabs -->
                <ul class="nav nav-pills mb-3 flex-nowrap overflow-auto" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabelat-tab" data-bs-toggle="tab" data-bs-target="#tabelat-tab-pane" type="button" role="tab" aria-controls="tabelat-tab-pane" aria-selected="true">Tabelat</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="raportet-tab" data-bs-toggle="tab" data-bs-target="#raportet-tab-pane" type="button" role="tab" aria-controls="raportet-tab-pane" aria-selected="false">Raportet</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <!-- Tabelat Tab Pane -->
                    <div class="tab-pane fade show active" id="tabelat-tab-pane" role="tabpanel" aria-labelledby="tabelat-tab" tabindex="0">
                        <div class="card shadow-sm mb-4 p-3">
                            <!-- Bulk Edit Button -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button id="bulkEditButton" class="btn btn-warning" disabled>
                                        <i class="fi fi-rr-edit me-1"></i> Edito të zgjedhurat
                                    </button>
                                </div>
                            </div>
                            <!-- Sub Tabs for Tables -->
                            <ul class="nav nav-pills mb-3 flex-nowrap overflow-auto" id="pills-tab" role="tablist">
                                <?php
                                $tabs = [
                                    'all' => 'Të gjitha',
                                    'investimet' => 'Investimet',
                                    'obligime' => 'Obligime',
                                    'shpenzimet' => 'Shpenzimet',
                                    'Pagesa_KS' => 'Pagesa KS',
                                    'Pagesa_AL' => 'Pagesa AL',
                                    'tjeter' => 'Tjetër'
                                ];
                                foreach ($tabs as $key => $label) {
                                    $active = ($key === 'all') ? 'active' : '';
                                    echo "
                        <li class='nav-item' role='presentation'>
                            <button class='nav-link rounded-5 {$active}' 
                                    id='pills-{$key}-tab' 
                                    data-bs-toggle='pill' 
                                    data-bs-target='#pills-{$key}' 
                                    type='button' role='tab' 
                                    aria-controls='pills-{$key}' 
                                    aria-selected='" . ($key === 'all' ? 'true' : 'false') . "'>
                                {$label}
                            </button>
                        </li>
                    ";
                                }
                                ?>
                            </ul>
                            <!-- Sub Tab Content -->
                            <div class="tab-content" id="pills-tabContent">
                                <?php
                                // Define column labels including a new checkbox column
                                $columns = [
                                    'select' => '<input type="checkbox" id="select-all" />', // Checkbox for selecting all rows
                                    'invoice_date' => 'Data e fat.',
                                    'invoice_number' => 'Nr. fat.',
                                    'description' => 'Përshkrimi',
                                    'category' => 'Kategoria',
                                    'company_name' => 'Kompani',
                                    'document_path' => '<i class="fi fi-rr-file"></i>',
                                    'vlera_faktura' => 'Vlera',
                                    'action' => 'Veprim'
                                ];
                                // Loop through tabs to render content
                                foreach ($tabs as $key => $label) {
                                    $activeClass = ($key === 'all') ? 'show active' : '';
                                    echo "<div class='tab-pane fade {$activeClass}' id='pills-{$key}' role='tabpanel' aria-labelledby='pills-{$key}-tab' tabindex='0'>";
                                    echo "<div class='table-responsive'>";
                                    echo "<table class='table table-bordered table-striped table-hover w-100' id='table-{$key}'>";
                                    echo "<thead class='table-light'><tr>";
                                    foreach ($columns as $columnName) {
                                        echo "<th class='select-checkbox'>{$columnName}</th>";
                                    }
                                    echo "</tr></thead><tbody></tbody></table></div></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Raportet Tab Pane -->
                    <div class="tab-pane fade" id="raportet-tab-pane" role="tabpanel" aria-labelledby="raportet-tab" tabindex="0">
                        <div class="card shadow-sm mb-4 p-3">
                            <h3 class="mb-4">Raportet Financiare</h3>
                            <!-- Filters Section -->
                            <div class="row g-3 align-items-center justify-content-between">
                                <div class="col-12 col-md-2">
                                    <label for="filterStartDate" class="form-label">Data e Fillimit</label>
                                    <input type="text" id="filterStartDate" class="form-control flatpickr-date" placeholder="Data e Fillimit">
                                </div>
                                <div class="col-12 col-md-2">
                                    <label for="filterEndDate" class="form-label">Data e Mbarimit</label>
                                    <input type="text" id="filterEndDate" class="form-control flatpickr-date" placeholder="Data e Mbarimit">
                                </div>
                                <div class="col-12 col-md-2">
                                    <label for="filterCategory" class="form-label">Kategoria</label>
                                    <select id="filterCategory" class="form-select">
                                        <option value="">Të gjitha</option>
                                        <option value="Shpenzimet">Shpenzimet</option>
                                        <option value="Investimet">Investimet</option>
                                        <option value="Obligime">Obligime</option>
                                        <option value="Pagesa KS">Pagesa KS</option>
                                        <option value="Pagesa AL">Pagesa AL</option>
                                        <option value="Tjetër">Tjetër</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label for="filterCompany" class="form-label">Kompania</label>
                                    <select id="filterCompany" class="form-select">
                                        <option value="">Të gjitha</option>
                                        <?php
                                        // Fetch unique company names from the database
                                        $companySql = "SELECT DISTINCT company_name FROM invoices_kont ORDER BY company_name ASC";
                                        $companyStmt = $conn->prepare($companySql);
                                        $companyStmt->execute();
                                        $companyResult = $companyStmt->get_result();
                                        if ($companyResult->num_rows > 0) {
                                            while ($row = $companyResult->fetch_assoc()) {
                                                $company = htmlspecialchars($row['company_name'], ENT_QUOTES, 'UTF-8');
                                                echo "<option value=\"{$company}\">{$company}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label for="filterRegistrant" class="form-label">Regjistruesi</label>
                                    <select id="filterRegistrant" class="form-select">
                                        <option value="">Të gjitha</option>
                                        <?php
                                        // Fetch unique registrants from the database
                                        $registrantSql = "SELECT DISTINCT registrant FROM invoices_kont ORDER BY registrant ASC";
                                        $registrantStmt = $conn->prepare($registrantSql);
                                        $registrantStmt->execute();
                                        $registrantResult = $registrantStmt->get_result();
                                        if ($registrantResult->num_rows > 0) {
                                            while ($row = $registrantResult->fetch_assoc()) {
                                                $registrant = htmlspecialchars($row['registrant'], ENT_QUOTES, 'UTF-8');
                                                echo "<option value=\"{$registrant}\">{$registrant}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Filter Buttons -->
                            <div class="row my-3">
                                <div class="col-12 col-md-3 d-flex">
                                    <button id="applyFilters" class="input-custom-css px-3 py-2 me-2">
                                        <i class="fi fi-rr-filter me-1"></i> Apliko
                                    </button>
                                    <button id="resetFilters" class="input-custom-css px-3 py-2">
                                        <i class="fi fi-rr-rotate-right me-1"></i> Rivendos
                                    </button>
                                </div>
                            </div>
                            <!-- Initialize Selectr for Select Elements -->
                            <script>
                                new Selectr('#filterCategory', {
                                    searchable: true,
                                    width: '100%'
                                });
                                new Selectr('#filterCompany', {
                                    searchable: true,
                                    width: '100%'
                                });
                                new Selectr('#filterRegistrant', {
                                    searchable: true,
                                    width: '100%'
                                });
                            </script>
                            <!-- Detailed Totals Section -->
                            <div class="row mb-4 g-3">
                                <!-- Total by Category -->
                                <div class="col-12 col-md-6">
                                    <div class="card rounded-5">
                                        <div class="card-header">
                                            <h5 class="mb-0">Total sipas Kategorisë</h5>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Kategoria</th>
                                                            <th>Vlera</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="detailedTotalByCategory">
                                                        <!-- Dynamically populated -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Total by Company -->
                                <div class="col-12 col-md-6">
                                    <div class="card rounded-5">
                                        <div class="card-header">
                                            <h5 class="mb-0">Total sipas Kompanisë</h5>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Kompania</th>
                                                            <th>Vlera</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="detailedTotalByCompany">
                                                        <!-- Dynamically populated -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Charts Section -->
                            <div class="row mb-4 g-3">
                                <!-- Line Chart -->
                                <div class="col-12">
                                    <div class="card rounded-5">
                                        <div class="card-header">
                                            <h5 class="mb-0">Trend i Shpenzimeve</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="lineChart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 g-3">
                                <!-- Doughnut Chart -->
                                <div class="col-12">
                                    <div class="card rounded-5">
                                        <div class="card-header">
                                            <h5 class="mb-0">Përqendrimi sipas Kategorisë</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="doughnutChart"></div> <!-- Changed ID to 'doughnutChart' -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4 g-3">
                                <!-- Bar Chart -->
                                <div class="col-12">
                                    <div class="card rounded-5">
                                        <div class="card-header">
                                            <h5 class="mb-0">Shpenzimet sipas Kompanisë</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="barChart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Charts Section -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>

    <!-- Document Preview Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span id="documentName">Document</span> Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="documentContent" class="text-center">
                        <img id="documentImage" src="" alt="Document Image" class="img-fluid mb-3" style="display: none; max-height: 80vh;">
                        <iframe id="documentPDF" src="" width="100%" height="600px" style="display: none;"></iframe>
                        <p id="documentMessage" class="mt-3" style="display: none;">
                            Preview not available.
                            <a id="downloadLinkBody" href="#" download>Download</a>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="downloadLinkFooter" href="#" class="btn btn-primary" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbyll</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Replace Document Modal -->
    <div class="modal fade" id="replaceDocumentModal" tabindex="-1" aria-labelledby="replaceDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="replaceDocumentForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="replaceDocumentModalLabel">Zëvendëso Dokumentin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="replaceDocumentId" name="id" value="">
                        <div class="mb-3">
                            <label for="newDocument" class="form-label">Zgjidh Dokumentin e Ri</label>
                            <input class="form-control" type="file" id="newDocument" name="newDocument" accept="image/*,application/pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Zëvendëso</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Modal -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="bulkEditForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edito të zgjedhurat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bulkEditColumn" class="form-label">Zgjidh Kolonën</label>
                            <select id="bulkEditColumn" class="form-select" required>
                                <option value="" disabled selected>Zgjidh një kolonë</option>
                                <option value="invoice_date">Data e fat.</option>
                                <option value="invoice_number">Nr. fat.</option>
                                <option value="description">Përshkrimi</option>
                                <option value="category">Kategoria</option>
                                <option value="company_name">Kompani</option>
                                <option value="vlera_faktura">Vlera</option>
                                <!-- Add more columns if needed -->
                            </select>
                        </div>
                        <div class="mb-3" id="bulkEditValueContainer">
                            <label for="bulkEditValue" class="form-label">Vlera e Re</label>
                            <input type="text" id="bulkEditValue" class="form-control" required>
                            <!-- Additional inputs can be dynamically inserted based on column type -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Ruaj</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Include SweetAlert2 JS (if not already included in footer.php) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Include Selectr JS (if not already included) -->
    <script src="https://cdn.jsdelivr.net/npm/selectr/dist/selectr.min.js"></script>
    <!-- Include DataTables JS (if not already included) -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <!-- Include ApexCharts JS (if not already included) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Include Bootstrap JS (if not already included in footer.php) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your existing JavaScript code -->
    <script>
        // JavaScript code for handling tables and modals
        // Global variable to store DataTables instances
        var dataTables = {};
        // Variables to store ApexCharts instances
        var lineChart, doughnutChart, barChart;

        function confirmDelete(id) {
            Swal.fire({
                title: 'A jeni i sigurt?',
                text: "Ju nuk do të jeni në gjendje ta ktheni këtë!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Po, fshijeni!',
                cancelButtonText: 'Anulo',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with deletion via POST request
                    fetch(`api/delete_methods/delete_newKont.php?id=${id}&action=delete`, {
                            method: 'POST',
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                Swal.fire(
                                    'U fshi!',
                                    'Rekordi është fshirë me sukses.',
                                    'success'
                                ).then(() => {
                                    // Refresh only the active table
                                    refreshTable();
                                    // Refresh charts
                                    fetchAndRenderData();
                                });
                            } else {
                                Swal.fire(
                                    'Gabim!',
                                    'Pati një problem me fshirjen e rekordit.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Gabim!',
                                'Kishte një problem me kërkesën.',
                                'error'
                            );
                        });
                }
            });
        }

        function refreshTable() {
            var activeTab = $('#pills-tab .nav-link.active').attr('id');
            var tableId = 'table-' + activeTab.replace('pills-', '').replace('-tab', '');
            var dataTable = dataTables[tableId];
            if (dataTable) {
                dataTable.ajax.reload(null, false);
            } else {
                console.error('Tabela e të dhënave nuk është gjetur për', tableId);
            }
        }

        // Function to show the Replace Document Modal
        function showReplaceModal(id) {
            $('#replaceDocumentId').val(id);
            $('#replaceDocumentModal').modal('show');
        }

        // Handle the form submission for replacing the document
        $(document).ready(function() {
            $('#replaceDocumentForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var id = $('#replaceDocumentId').val();
                $.ajax({
                    url: 'api/edit_methods/replace_document.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // Ensure the response is parsed as JSON
                    success: function(response) {
                        console.log('AJAX response:', response);
                        $('#replaceDocumentModal').modal('hide');
                        if (response.success) {
                            Swal.fire({
                                title: 'Sukses',
                                text: 'Dokumenti u zëvendësua me sukses.',
                                icon: 'success'
                            }).then(() => {
                                // Refresh only the active table and charts
                                refreshTable();
                                fetchAndRenderData();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gabim',
                                text: response.message || 'Ndodhi një gabim.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#replaceDocumentModal').modal('hide');
                        console.error('AJAX error:', textStatus, errorThrown);
                        Swal.fire({
                            title: 'Gabim',
                            text: 'Ndodhi një gabim gjatë zëvendësimit të dokumentit: ' + textStatus,
                            icon: 'error'
                        });
                    }
                });
            });

            // Define your initCSS function for reusability
            function initCSS(selector, classes, styles) {
                $(selector).addClass(classes).css(styles);
            }

            var tableIds = [
                'table-all',
                'table-investimet',
                'table-obligime',
                'table-shpenzimet',
                'table-Pagesa_KS',
                'table-Pagesa_AL',
                'table-tjeter'
            ];

            tableIds.forEach(function(tableId) {
                dataTables[tableId] = $('#' + tableId).DataTable({
                    responsive: true, // Enable Responsive Extension
                    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'QPBtr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    stripeClasses: ["stripe-color"],
                    ajax: {
                        url: 'api/get_methods/get_table_data.php',
                        type: 'POST', // Use POST as per your PHP script
                        data: function(d) {
                            let categoryKey = tableId.replace('table-', '');
                            let category = categoryKey.replace('_', ' '); // Replace underscores with spaces
                            d.category = category;
                            console.log('Loading table:', tableId, 'Category:', d.category);
                        },
                        dataSrc: function(json) {
                            if (Array.isArray(json.data)) {
                                return json.data;
                            } else if (json.success === false) {
                                Swal.fire({
                                    title: 'Error',
                                    text: json.message || 'Failed to load data.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                return [];
                            } else {
                                // Handle unexpected response format
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Unexpected response format.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                return [];
                            }
                        },
                        error: function(xhr, error, thrown) {
                            console.error('Error fetching data for ' + tableId + ': ', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to load data for ' + tableId.replace('table-', '') + '.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    columnDefs: [{
                        targets: '_all',
                        render: function(data, type, row) {
                            return type === 'display' && data !== null ? '<div class="text-wrap">' + data + '</div>' : data;
                        }
                    }],
                    columns: [{
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `<input type="checkbox" class="row-select" data-id="${data}" />`;
                            }
                        },
                        {
                            data: 'invoice_date',
                            render: function(data, type, row) {
                                return type === 'display' ? `<span class="editable" data-column="invoice_date" data-id="${row.id}">${data}</span>` : data;
                            }
                        },
                        {
                            data: 'invoice_number',
                            render: function(data, type, row) {
                                return type === 'display' ? `<span class="editable" data-column="invoice_number" data-id="${row.id}">${data}</span>` : data;
                            }
                        },
                        {
                            data: 'description',
                            render: function(data, type, row) {
                                return type === 'display' ? `<span class="editable" data-column="description" data-id="${row.id}">${data}</span>` : data;
                            }
                        },
                        {
                            data: 'category',
                            render: function(data, type, row) {
                                return type === 'display' ? `<span class="editable" data-column="category" data-id="${row.id}">${data}</span>` : data;
                            }
                        },
                        {
                            data: 'company_name',
                            render: function(data, type, row) {
                                return type === 'display' ? `<span class="editable" data-column="company_name" data-id="${row.id}">${data}</span>` : data;
                            }
                        },
                        {
                            data: 'document_path',
                            render: function(data, type, row) {
                                var fileName = data.split('/').pop(); // Extract basename
                                // Use encodeURIComponent to safely include the file path and name
                                return `
                        <a href="#" class="view-document input-custom-css text-decoration-none px-3 py-2 me-2" data-bs-toggle="modal" data-bs-target="#documentModal" data-file="${encodeURIComponent(data)}" data-name="${encodeURIComponent(fileName)}">
                            <i class="fi fi-rr-file"></i>
                        </a>
                        <button onclick="showReplaceModal(${row.id})" class="input-custom-css px-3 py-2">
                            <i class="fi fi-rr-pencil"></i>
                        </button>`;
                            }
                        },
                        {
                            data: 'vlera_faktura',
                            render: function(data, type, row) {
                                return type === 'display' ? `<span class="editable" data-column="vlera_faktura" data-id="${row.id}">${data}</span>` : data;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                        <button onclick="confirmDelete(${row.id})" class="input-custom-css px-3 py-2">
                            <i class="fi fi-rr-trash"></i>
                        </button>
                    `;
                            }
                        }
                    ],
                    initComplete: function() {
                        // Apply CSS to the length select dropdown
                        initCSS("div.dataTables_length select", "form-select", {
                            width: "auto",
                            margin: "0 8px",
                            padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                            lineHeight: "1.5",
                            border: "1px solid #ced4da",
                            borderRadius: "0.25rem",
                        });

                        // Apply CSS to Q (Assuming Q corresponds to a specific element)
                        initCSS("div.dataTables_Q", "custom-Q-class", {
                            /* Your desired CSS properties for Q */
                            margin: "0 8px",
                            padding: "0.5rem",
                            backgroundColor: "#f8f9fa",
                            borderRadius: "0.25rem",
                        });

                        // Apply CSS to B (Buttons)
                        initCSS("div.dataTables_B", "btn-group", {
                            /* Your desired CSS properties for B */
                            margin: "0 8px",
                        });

                        // Apply CSS to P (Pagination)
                        initCSS("div.dataTables_p", "pagination-custom", {
                            /* Your desired CSS properties for Pagination */
                            margin: "0 8px",
                        });

                        // Apply CSS to other elements as needed
                        // For example, to the filter input
                        initCSS("div.dataTables_filter input", "form-control", {
                            width: "250px",
                            padding: "0.375rem 0.75rem",
                            border: "1px solid #ced4da",
                            borderRadius: "0.25rem",
                        });

                        // You can continue adding CSS for other elements represented by different letters
                        // For instance, 't' for the table, 'r' for processing, etc.
                    },
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
                    },
                    responsive: {
                        details: {
                            type: 'column',
                            target: 'tr'
                        }
                    },
                });
            });


            // Editable fields handling with Flatpickr integration for date fields
            $('body').on('click', '.editable', function() {
                var $this = $(this);
                var currentValue = $this.text().trim();
                var column = $this.data('column');
                var id = $this.data('id');
                var columnHeader = getColumnHeader(column);

                // Define predefined categories
                var categories = ["Shpenzimet", "Investimet", "Obligime", "Pagesa KS", "Pagesa AL", "Tjetër"];

                // Determine if the current column is a date field
                var isDateField = (column === 'invoice_date'); // Add more date columns if necessary

                if (column === 'category') {
                    // Render a SweetAlert2 modal with a dropdown for category selection
                    let options = '<option value="">Zgjidh Kategorinë</option>';
                    categories.forEach(function(cat) {
                        let selected = cat === currentValue ? 'selected' : '';
                        options += `<option value="${cat}" ${selected}>${cat}</option>`;
                    });
                    Swal.fire({
                        title: `Ndrysho ${columnHeader}`,
                        html: `
                            <div class="form-group">
                                <label for="swal-select" class="form-label">${columnHeader}</label>
                                <select id="swal-select" class="swal2-select form-select">
                                    ${options}
                                </select>
                                <small class="form-text text-muted">Zgjidhni një kategori dhe klikoni ruaj.</small>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ruaj',
                        cancelButtonText: 'Anulo',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return new Promise((resolve, reject) => {
                                const newValue = document.getElementById('swal-select').value;
                                if (newValue) {
                                    updateValue(id, column, newValue, $this, resolve);
                                } else {
                                    reject(new Error('Ju lutem zgjidhni një kategori.'));
                                }
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading(),
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'btn btn-primary me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Success message already handled in updateValue
                        }
                    }).catch(error => {
                        Swal.fire({
                            title: 'Gabim!',
                            text: error.message,
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    });
                } else if (isDateField) {
                    // Render a SweetAlert2 modal with Flatpickr for date selection
                    Swal.fire({
                        title: `Ndrysho ${columnHeader}`,
                        html: `
                            <div class="form-group">
                                <label for="swal-input" class="form-label">${columnHeader}</label>
                                <input id="swal-input" class="swal2-input form-control flatpickr-date" placeholder="Zgjidhni datën" value="${currentValue}">
                                <small class="form-text text-muted">Zgjidhni një datë dhe klikoni ruaj.</small>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ruaj',
                        cancelButtonText: 'Anulo',
                        showLoaderOnConfirm: true,
                        didOpen: () => {
                            // Initialize Flatpickr on the input after the modal is rendered
                            flatpickr("#swal-input", {
                                dateFormat: "Y-m-d",
                                allowInput: true,
                                locale: "sq", // Set locale to Albanian if needed
                                // Optional: Add additional Flatpickr options here
                            });
                        },
                        preConfirm: () => {
                            return new Promise((resolve, reject) => {
                                const newValue = document.getElementById('swal-input').value.trim();
                                if (newValue) {
                                    updateValue(id, column, newValue, $this, resolve);
                                } else {
                                    reject(new Error('Ju lutem zgjidhni një datë.'));
                                }
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading(),
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'btn btn-primary me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Success message already handled in updateValue
                        }
                    }).catch(error => {
                        Swal.fire({
                            title: 'Gabim!',
                            text: error.message,
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    });
                } else {
                    // Handle other editable fields (e.g., text inputs)
                    Swal.fire({
                        title: `Ndrysho ${columnHeader}`,
                        html: `
                            <div class="form-group">
                                <label for="swal-input" class="form-label">${columnHeader}</label>
                                <input id="swal-input" class="swal2-input form-control" placeholder="Shkruani ${columnHeader}" value="${currentValue}">
                                <small class="form-text text-muted">Ndryshoni vlerën dhe klikoni ruaj.</small>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ruaj',
                        cancelButtonText: 'Anulo',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return new Promise((resolve, reject) => {
                                const newValue = document.getElementById('swal-input').value.trim();
                                if (newValue) {
                                    updateValue(id, column, newValue, $this, resolve);
                                } else {
                                    reject(new Error('Vlera nuk mund të jetë bosh.'));
                                }
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading(),
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'btn btn-primary me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Success message already handled in updateValue
                        }
                    }).catch(error => {
                        Swal.fire({
                            title: 'Gabim!',
                            text: error.message,
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    });
                }
            });

            // Function to get the correct column header
            function getColumnHeader(column) {
                var headers = {
                    'id': 'ID',
                    'invoice_date': 'Data e faturës',
                    'invoice_number': 'Numri i faturës',
                    'description': 'Përshkrimi',
                    'category': 'Kategoria',
                    'company_name': 'Emri i kompanisë',
                    'document_path': 'Dokumenti',
                    'vlera_faktura': 'Vlera e faturës'
                };
                return headers[column] || column;
            }

            // Handle tab shown event to adjust DataTables
            $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                var targetTab = $(e.target).attr("href");
                var key = targetTab.replace("#pills-", "");
                var tableId = 'table-' + key;
                if (dataTables[tableId]) {
                    dataTables[tableId].columns.adjust().responsive.recalc();
                    dataTables[tableId].ajax.reload(null, false);
                }
            });

            function updateValue(id, column, value, $element, resolve) {
                $.ajax({
                    url: 'api/edit_methods/update_newKont.php',
                    method: 'POST',
                    data: {
                        id: id,
                        column: column,
                        value: value
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Successfully updated
                            $element.text(value); // Update the UI
                            resolve(); // Resolve the Swal promise
                            // Display success alert
                            Swal.fire({
                                title: 'Sukses!',
                                text: 'Vlera është përditësuar me sukses.',
                                icon: 'success',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            });
                            // Refresh charts
                            fetchAndRenderData();
                        } else {
                            // If update fails, handle error
                            Swal.showValidationMessage('Dështoi përditësimi: ' + (response.message || 'Ndodhi një gabim i panjohur.'));
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.showValidationMessage('Ndodhi një gabim gjatë përditësimit: ' + textStatus);
                    }
                });
            }

            // Modal Handling for Document Preview
            $('#documentModal').on('show.bs.modal', function(event) {
                const triggerLink = $(event.relatedTarget); // The element that triggered the modal
                const filePathEncoded = triggerLink.data('file');
                const fileNameEncoded = triggerLink.data('name');
                // Decode URI components
                const filePath = decodeURIComponent(filePathEncoded);
                const fileName = decodeURIComponent(fileNameEncoded);
                // Update modal title
                $('#documentName').text(fileName);
                // Reset modal content
                $('#documentImage').hide();
                $('#documentPDF').hide();
                $('#documentMessage').hide();
                // Set download links
                $('#downloadLinkFooter').attr('href', filePath);
                $('#downloadLinkBody').attr('href', filePath);
                // Determine file type using a regex to extract extension
                const extensionMatch = filePath.match(/\.([^.?#]+)(?:[\?#]|$)/);
                const fileExtension = extensionMatch ? extensionMatch[1].toLowerCase() : '';
                if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(fileExtension)) {
                    // It's an image
                    $('#documentImage').attr('src', filePath).show();
                } else if (fileExtension === 'pdf') {
                    // It's a PDF
                    $('#documentPDF').attr('src', filePath).show();
                } else {
                    // Other file types
                    $('#documentMessage').show();
                }
            });

            // Function to fetch and render data based on filters
            function fetchAndRenderData() {
                var startDate = $('#filterStartDate').val();
                var endDate = $('#filterEndDate').val();
                var category = $('#filterCategory').val();
                var company = $('#filterCompany').val();
                var registrant = $('#filterRegistrant').val();
                // Show loading spinner
                Swal.fire({
                    title: 'Po ngarkohet...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: 'api/get_methods/get_profit_data.php',
                    type: 'POST',
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        category: category,
                        company: company,
                        registrant: registrant
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Process and display totals
                            displayTotals(response.data);
                            // Render charts
                            renderCharts(response.data);
                            Swal.close(); // Close the loading spinner
                        } else {
                            Swal.fire({
                                title: 'Gabim',
                                text: response.message || 'Ndodhi një gabim gjatë marrjes së të dhënave të raportit.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching profit data:', error);
                        Swal.fire({
                            title: 'Gabim',
                            text: 'Ndodhi një gabim gjatë marrjes së të dhënave të raportit.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            // Function to display totals
            function displayTotals(data) {
                var categoryTotals = data.category_totals;
                var companyTotals = data.company_totals;
                // Display detailed totals by category
                var detailedCategoryHTML = '';
                categoryTotals.forEach(function(record) {
                    var category = record.category;
                    var total = parseFloat(record.total_shuma).toFixed(2);
                    detailedCategoryHTML += `<tr>
                            <td>${capitalizeFirstLetter(category)}</td>
                            <td>€${total}</td>
                        </tr>`;
                });
                $('#detailedTotalByCategory').html(detailedCategoryHTML);
                // Display detailed totals by company
                var detailedCompanyHTML = '';
                companyTotals.forEach(function(record) {
                    var company = record.company_name;
                    var total = parseFloat(record.total_shuma).toFixed(2);
                    detailedCompanyHTML += `<tr>
                            <td>${capitalizeFirstLetter(company)}</td>
                            <td>€${total}</td>
                        </tr>`;
                });
                $('#detailedTotalByCompany').html(detailedCompanyHTML);
            }

            // Helper function to capitalize first letter
            function capitalizeFirstLetter(string) {
                if (typeof string !== 'string' || string.length === 0) return string;
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // Function to render ApexCharts
            function renderCharts(data) {
                var categoryTotals = data.category_totals;
                var companyTotals = data.company_totals;
                var monthlyTotals = data.monthly_totals; // Assuming your API provides monthly totals
                // Prepare data for Line Chart (Trend of Expenses Over Time)
                var lineCategories = [];
                var lineSeriesData = [];
                if (monthlyTotals && Array.isArray(monthlyTotals)) {
                    monthlyTotals.forEach(function(record) {
                        lineCategories.push(record.month); // e.g., 'Jan', 'Feb', etc.
                        lineSeriesData.push(parseFloat(record.total).toFixed(2));
                    });
                }
                // Prepare data for Doughnut Chart (Distribution by Category)
                var doughnutLabels = [];
                var doughnutSeries = [];
                categoryTotals.forEach(function(record) {
                    doughnutLabels.push(record.category);
                    doughnutSeries.push(parseFloat(record.total_shuma).toFixed(2));
                });
                // Prepare data for Bar Chart (Expenses by Company)
                var barLabels = [];
                var barSeriesData = [];
                companyTotals.forEach(function(record) {
                    barLabels.push(record.company_name);
                    barSeriesData.push(parseFloat(record.total_shuma).toFixed(2));
                });
                // Initialize or Update Line Chart
                if (!lineChart) {
                    var lineOptions = {
                        chart: {
                            type: 'line',
                            height: 350
                        },
                        series: [{
                            name: 'Shpenzimet',
                            data: lineSeriesData
                        }],
                        xaxis: {
                            categories: lineCategories,
                            title: {
                                text: 'Muaji'
                            }
                        },
                        yaxis: {
                            title: {
                                text: '€'
                            }
                        },
                        title: {
                            text: 'Trend i Shpenzimeve',
                            align: 'center'
                        }
                    };
                    lineChart = new ApexCharts(document.querySelector("#lineChart"), lineOptions);
                    lineChart.render();
                } else {
                    lineChart.updateOptions({
                        xaxis: {
                            categories: lineCategories
                        },
                        series: [{
                            name: 'Shpenzimet',
                            data: lineSeriesData
                        }]
                    });
                }
                // Initialize or Update Doughnut Chart
                if (!doughnutChart) {
                    var doughnutOptions = {
                        chart: {
                            type: 'donut', // Changed from 'pie' to 'donut'
                            height: 350
                        },
                        series: doughnutSeries,
                        labels: doughnutLabels,
                        title: {
                            text: 'Përqendrimi sipas Kategorisë',
                            align: 'center'
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 300
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };
                    doughnutChart = new ApexCharts(document.querySelector("#doughnutChart"), doughnutOptions);
                    doughnutChart.render();
                } else {
                    doughnutChart.updateOptions({
                        labels: doughnutLabels,
                        series: doughnutSeries
                    });
                }
                // Initialize or Update Bar Chart
                if (!barChart) {
                    var barOptions = {
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        series: [{
                            name: 'Shpenzimet',
                            data: barSeriesData
                        }],
                        xaxis: {
                            categories: barLabels,
                            title: {
                                text: 'Kompania'
                            }
                        },
                        yaxis: {
                            title: {
                                text: '€'
                            }
                        },
                        title: {
                            text: 'Shpenzimet sipas Kompanisë',
                            align: 'center'
                        }
                    };
                    barChart = new ApexCharts(document.querySelector("#barChart"), barOptions);
                    barChart.render();
                } else {
                    barChart.updateOptions({
                        xaxis: {
                            categories: barLabels
                        },
                        series: [{
                            name: 'Shpenzimet',
                            data: barSeriesData
                        }]
                    });
                }
            }

            // Event listeners for filter buttons
            $('#applyFilters').on('click', function() {
                fetchAndRenderData();
            });
            $('#resetFilters').on('click', function() {
                $('#filterStartDate').val('');
                $('#filterEndDate').val('');
                $('#filterCategory').val('');
                $('#filterCompany').val('');
                $('#filterRegistrant').val('');
                fetchAndRenderData();
            });

            // Initialize Flatpickr on date inputs
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "sq" // Set locale to Albanian if needed
            });

            // Initial data load
            fetchAndRenderData();
        });

        // Function to handle updating values via AJAX
        function updateValue(id, column, value, $element, resolve) {
            $.ajax({
                url: 'api/edit_methods/update_newKont.php',
                method: 'POST',
                data: {
                    id: id,
                    column: column,
                    value: value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Successfully updated
                        $element.text(value); // Update the UI
                        resolve(); // Resolve the Swal promise
                        // Display success alert
                        Swal.fire({
                            title: 'Sukses!',
                            text: 'Vlera është përditësuar me sukses.',
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                        // Refresh charts
                        fetchAndRenderData();
                    } else {
                        // If update fails, handle error
                        Swal.showValidationMessage('Dështoi përditësimi: ' + (response.message || 'Ndodhi një gabim i panjohur.'));
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.showValidationMessage('Ndodhi një gabim gjatë përditësimit: ' + textStatus);
                }
            });
        }
    </script>
    <script>
        // Additional JavaScript functions can be placed here if needed

        // Bulk Edit Functionality
        $(document).ready(function() {
            // Function to toggle the Bulk Edit button
            function toggleBulkEditButton() {
                var selectedCount = $('.row-select:checked').length;
                if (selectedCount > 0 && selectedCount <= 10) {
                    $('#bulkEditButton').prop('disabled', false);
                } else {
                    $('#bulkEditButton').prop('disabled', true);
                    if (selectedCount > 10) {
                        Swal.fire({
                            title: 'Shumë të zgjedhura!',
                            text: 'Ju mund të zgjidhni deri në 10 rreshta për një herë.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                }
            }

            // Monitor checkbox changes
            $('body').on('change', '.row-select', function() {
                var selectedCount = $('.row-select:checked').length;
                if (selectedCount > 10) {
                    $(this).prop('checked', false);
                    toggleBulkEditButton();
                } else {
                    toggleBulkEditButton();
                }
            });

            // Handle "Select All" checkbox functionality
            $('body').on('change', '#select-all', function() {
                var isChecked = $(this).is(':checked');
                $('.row-select').prop('checked', isChecked);
                toggleBulkEditButton();
            });

            // Handle Bulk Edit Button Click
            $('#bulkEditButton').on('click', function() {
                var selectedCount = $('.row-select:checked').length;
                if (selectedCount === 0) {
                    Swal.fire({
                        title: 'Asnjë zgjedhje!',
                        text: 'Ju duhet të zgjidhni të paktën një rresht për të edituar.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
                $('#bulkEditModal').modal('show');
            });

            // Handle Column Selection to Adjust Input Type
            $('#bulkEditColumn').on('change', function() {
                var selectedColumn = $(this).val();
                var container = $('#bulkEditValueContainer');
                container.empty(); // Clear previous input

                if (selectedColumn === 'invoice_date') {
                    container.append(`
                        <label for="bulkEditValue" class="form-label">Data e Re</label>
                        <input type="text" id="bulkEditValue" class="form-control flatpickr-date" placeholder="Zgjidhni datën" required>
                    `);
                    flatpickr("#bulkEditValue", {
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        locale: "sq"
                    });
                } else if (selectedColumn === 'category') {
                    container.append(`
                        <label for="bulkEditValue" class="form-label">Kategoria e Re</label>
                        <select id="bulkEditValue" class="form-select" required>
                            <option value="" disabled selected>Zgjidhni një kategori</option>
                            <option value="Shpenzimet">Shpenzimet</option>
                            <option value="Investimet">Investimet</option>
                            <option value="Obligime">Obligime</option>
                            <option value="Pagesa KS">Pagesa KS</option>
                            <option value="Pagesa AL">Pagesa AL</option>
                            <option value="Tjetër">Tjetër</option>
                        </select>
                    `);
                } else {
                    // Default input for text fields
                    container.append(`
                        <label for="bulkEditValue" class="form-label">Vlera e Re</label>
                        <input type="text" id="bulkEditValue" class="form-control" placeholder="Shkruani vlerën e re" required>
                    `);
                }
            });

            // Handle Bulk Edit Form Submission
            $('#bulkEditForm').on('submit', function(e) {
                e.preventDefault();

                var selectedIds = [];
                $('.row-select:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });

                var selectedColumn = $('#bulkEditColumn').val();
                var newValue = $('#bulkEditValue').val().trim();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: 'Asnjë zgjedhje!',
                        text: 'Ju duhet të zgjidhni të paktën një rresht për të edituar.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                if (!selectedColumn || !newValue) {
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Ju lutem zgjidhni kolonën dhe futni vlerën e re.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm the bulk edit action
                Swal.fire({
                    title: 'Jeni i sigurt?',
                    text: `Dëshironi të ndryshoni kolonën "${getColumnHeader(selectedColumn)}" për ${selectedIds.length} rreshta?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Po, vazhdo',
                    cancelButtonText: 'Anulo',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading spinner
                        Swal.fire({
                            title: 'Duke përditësuar...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request for bulk update
                        $.ajax({
                            url: 'bulk_update_newKont.php', // New API endpoint
                            method: 'POST',
                            data: {
                                ids: selectedIds,
                                column: selectedColumn,
                                value: newValue
                            },
                            dataType: 'json',
                            success: function(response) {
                                Swal.close(); // Close the loading spinner
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: 'Rekordet janë përditësuar me sukses.',
                                        icon: 'success',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    }).then(() => {
                                        // Refresh the active table and charts
                                        refreshTable();
                                        fetchAndRenderData();
                                        // Reset selections
                                        $('.row-select').prop('checked', false);
                                        $('#select-all').prop('checked', false);
                                        toggleBulkEditButton();
                                        // Hide the modal
                                        $('#bulkEditModal').modal('hide');
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gabim!',
                                        text: response.message || 'Ndodhi një gabim gjatë përditësimit të rekordit.',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.close(); // Close the loading spinner
                                Swal.fire({
                                    title: 'Gabim!',
                                    text: 'Ndodhi një gabim gjatë përpunimit të kërkesës.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    }
                });
            });

            // Function to get the correct column header
            function getColumnHeader(column) {
                var headers = {
                    'select': 'Zgjidh',
                    'invoice_date': 'Data e faturës',
                    'invoice_number': 'Numri i faturës',
                    'description': 'Përshkrimi',
                    'category': 'Kategoria',
                    'company_name': 'Emri i kompanisë',
                    'document_path': 'Dokumenti',
                    'vlera_faktura': 'Vlera e faturës'
                };
                return headers[column] || column;
            }

            // Initial call to ensure the Bulk Edit button is in the correct state
            toggleBulkEditButton();
        });

        // Modal Handling for Document Preview
        $('#documentModal').on('show.bs.modal', function(event) {
            const triggerLink = $(event.relatedTarget); // The element that triggered the modal
            const filePathEncoded = triggerLink.data('file');
            const fileNameEncoded = triggerLink.data('name');
            // Decode URI components
            const filePath = decodeURIComponent(filePathEncoded);
            const fileName = decodeURIComponent(fileNameEncoded);
            // Update modal title
            $('#documentName').text(fileName);
            // Reset modal content
            $('#documentImage').hide();
            $('#documentPDF').hide();
            $('#documentMessage').hide();
            // Set download links
            $('#downloadLinkFooter').attr('href', filePath);
            $('#downloadLinkBody').attr('href', filePath);
            // Determine file type using a regex to extract extension
            const extensionMatch = filePath.match(/\.([^.?#]+)(?:[\?#]|$)/);
            const fileExtension = extensionMatch ? extensionMatch[1].toLowerCase() : '';
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(fileExtension)) {
                // It's an image
                $('#documentImage').attr('src', filePath).show();
            } else if (fileExtension === 'pdf') {
                // It's a PDF
                $('#documentPDF').attr('src', filePath).show();
            } else {
                // Other file types
                $('#documentMessage').show();
            }
        });

        // Function to fetch and render data based on filters
        function fetchAndRenderData() {
            var startDate = $('#filterStartDate').val();
            var endDate = $('#filterEndDate').val();
            var category = $('#filterCategory').val();
            var company = $('#filterCompany').val();
            var registrant = $('#filterRegistrant').val();
            // Show loading spinner
            Swal.fire({
                title: 'Po ngarkohet...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: 'api/get_methods/get_profit_data.php',
                type: 'POST',
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    category: category,
                    company: company,
                    registrant: registrant
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Process and display totals
                        displayTotals(response.data);
                        // Render charts
                        renderCharts(response.data);
                        Swal.close(); // Close the loading spinner
                    } else {
                        Swal.fire({
                            title: 'Gabim',
                            text: response.message || 'Ndodhi një gabim gjatë marrjes së të dhënave të raportit.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching profit data:', error);
                    Swal.fire({
                        title: 'Gabim',
                        text: 'Ndodhi një gabim gjatë marrjes së të dhënave të raportit.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Function to display totals
        function displayTotals(data) {
            var categoryTotals = data.category_totals;
            var companyTotals = data.company_totals;
            // Display detailed totals by category
            var detailedCategoryHTML = '';
            categoryTotals.forEach(function(record) {
                var category = record.category;
                var total = parseFloat(record.total_shuma).toFixed(2);
                detailedCategoryHTML += `<tr>
                        <td>${capitalizeFirstLetter(category)}</td>
                        <td>€${total}</td>
                    </tr>`;
            });
            $('#detailedTotalByCategory').html(detailedCategoryHTML);
            // Display detailed totals by company
            var detailedCompanyHTML = '';
            companyTotals.forEach(function(record) {
                var company = record.company_name;
                var total = parseFloat(record.total_shuma).toFixed(2);
                detailedCompanyHTML += `<tr>
                        <td>${capitalizeFirstLetter(company)}</td>
                        <td>€${total}</td>
                    </tr>`;
            });
            $('#detailedTotalByCompany').html(detailedCompanyHTML);
        }

        // Helper function to capitalize first letter
        function capitalizeFirstLetter(string) {
            if (typeof string !== 'string' || string.length === 0) return string;
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        // Function to render ApexCharts
        function renderCharts(data) {
            var categoryTotals = data.category_totals;
            var companyTotals = data.company_totals;
            var monthlyTotals = data.monthly_totals; // Assuming your API provides monthly totals
            // Prepare data for Line Chart (Trend of Expenses Over Time)
            var lineCategories = [];
            var lineSeriesData = [];
            if (monthlyTotals && Array.isArray(monthlyTotals)) {
                monthlyTotals.forEach(function(record) {
                    lineCategories.push(record.month); // e.g., 'Jan', 'Feb', etc.
                    lineSeriesData.push(parseFloat(record.total).toFixed(2));
                });
            }
            // Prepare data for Doughnut Chart (Distribution by Category)
            var doughnutLabels = [];
            var doughnutSeries = [];
            categoryTotals.forEach(function(record) {
                doughnutLabels.push(record.category);
                doughnutSeries.push(parseFloat(record.total_shuma).toFixed(2));
            });
            // Prepare data for Bar Chart (Expenses by Company)
            var barLabels = [];
            var barSeriesData = [];
            companyTotals.forEach(function(record) {
                barLabels.push(record.company_name);
                barSeriesData.push(parseFloat(record.total_shuma).toFixed(2));
            });
            // Initialize or Update Line Chart
            if (!lineChart) {
                var lineOptions = {
                    chart: {
                        type: 'line',
                        height: 350
                    },
                    series: [{
                        name: 'Shpenzimet',
                        data: lineSeriesData
                    }],
                    xaxis: {
                        categories: lineCategories,
                        title: {
                            text: 'Muaji'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '€'
                        }
                    },
                    title: {
                        text: 'Trend i Shpenzimeve',
                        align: 'center'
                    }
                };
                lineChart = new ApexCharts(document.querySelector("#lineChart"), lineOptions);
                lineChart.render();
            } else {
                lineChart.updateOptions({
                    xaxis: {
                        categories: lineCategories
                    },
                    series: [{
                        name: 'Shpenzimet',
                        data: lineSeriesData
                    }]
                });
            }
            // Initialize or Update Doughnut Chart
            if (!doughnutChart) {
                var doughnutOptions = {
                    chart: {
                        type: 'donut', // Changed from 'pie' to 'donut'
                        height: 350
                    },
                    series: doughnutSeries,
                    labels: doughnutLabels,
                    title: {
                        text: 'Përqendrimi sipas Kategorisë',
                        align: 'center'
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };
                doughnutChart = new ApexCharts(document.querySelector("#doughnutChart"), doughnutOptions);
                doughnutChart.render();
            } else {
                doughnutChart.updateOptions({
                    labels: doughnutLabels,
                    series: doughnutSeries
                });
            }
            // Initialize or Update Bar Chart
            if (!barChart) {
                var barOptions = {
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    series: [{
                        name: 'Shpenzimet',
                        data: barSeriesData
                    }],
                    xaxis: {
                        categories: barLabels,
                        title: {
                            text: 'Kompania'
                        }
                    },
                    yaxis: {
                        title: {
                            text: '€'
                        }
                    },
                    title: {
                        text: 'Shpenzimet sipas Kompanisë',
                        align: 'center'
                    }
                };
                barChart = new ApexCharts(document.querySelector("#barChart"), barOptions);
                barChart.render();
            } else {
                barChart.updateOptions({
                    xaxis: {
                        categories: barLabels
                    },
                    series: [{
                        name: 'Shpenzimet',
                        data: barSeriesData
                    }]
                });
            }
        }

        // Function to handle updating values via AJAX (Duplicate Function Removed)
        // (Note: The function is already defined above. Remove duplicate definitions to prevent conflicts.)
    </script>
    <script>
        // Bulk Edit Functionality
        $(document).ready(function() {
            // Function to toggle the Bulk Edit button
            function toggleBulkEditButton() {
                var selectedCount = $('.row-select:checked').length;
                if (selectedCount > 0 && selectedCount <= 10) {
                    $('#bulkEditButton').prop('disabled', false);
                } else {
                    $('#bulkEditButton').prop('disabled', true);
                    if (selectedCount > 10) {
                        Swal.fire({
                            title: 'Shumë të zgjedhura!',
                            text: 'Ju mund të zgjidhni deri në 10 rreshta për një herë.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                }
            }

            // Monitor checkbox changes
            $('body').on('change', '.row-select', function() {
                var selectedCount = $('.row-select:checked').length;
                if (selectedCount > 10) {
                    $(this).prop('checked', false);
                    toggleBulkEditButton();
                } else {
                    toggleBulkEditButton();
                }
            });

            // Handle "Select All" checkbox functionality
            $('body').on('change', '#select-all', function() {
                var isChecked = $(this).is(':checked');
                $('.row-select').prop('checked', isChecked);
                toggleBulkEditButton();
            });

            // Handle Bulk Edit Button Click
            $('#bulkEditButton').on('click', function() {
                var selectedCount = $('.row-select:checked').length;
                if (selectedCount === 0) {
                    Swal.fire({
                        title: 'Asnjë zgjedhje!',
                        text: 'Ju duhet të zgjidhni të paktën një rresht për të edituar.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }
                $('#bulkEditModal').modal('show');
            });

            // Handle Column Selection to Adjust Input Type
            $('#bulkEditColumn').on('change', function() {
                var selectedColumn = $(this).val();
                var container = $('#bulkEditValueContainer');
                container.empty(); // Clear previous input

                if (selectedColumn === 'invoice_date') {
                    container.append(`
                        <label for="bulkEditValue" class="form-label">Data e Re</label>
                        <input type="text" id="bulkEditValue" class="form-control flatpickr-date" placeholder="Zgjidhni datën" required>
                    `);
                    flatpickr("#bulkEditValue", {
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        locale: "sq"
                    });
                } else if (selectedColumn === 'category') {
                    container.append(`
                        <label for="bulkEditValue" class="form-label">Kategoria e Re</label>
                        <select id="bulkEditValue" class="form-select" required>
                            <option value="" disabled selected>Zgjidhni një kategori</option>
                            <option value="Shpenzimet">Shpenzimet</option>
                            <option value="Investimet">Investimet</option>
                            <option value="Obligime">Obligime</option>
                            <option value="Pagesa KS">Pagesa KS</option>
                            <option value="Pagesa AL">Pagesa AL</option>
                            <option value="Tjetër">Tjetër</option>
                        </select>
                    `);
                } else {
                    // Default input for text fields
                    container.append(`
                        <label for="bulkEditValue" class="form-label">Vlera e Re</label>
                        <input type="text" id="bulkEditValue" class="form-control" placeholder="Shkruani vlerën e re" required>
                    `);
                }
            });

            // Handle Bulk Edit Form Submission
            $('#bulkEditForm').on('submit', function(e) {
                e.preventDefault();

                var selectedIds = [];
                $('.row-select:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });

                var selectedColumn = $('#bulkEditColumn').val();
                var newValue = $('#bulkEditValue').val().trim();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: 'Asnjë zgjedhje!',
                        text: 'Ju duhet të zgjidhni të paktën një rresht për të edituar.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                if (!selectedColumn || !newValue) {
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Ju lutem zgjidhni kolonën dhe futni vlerën e re.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                // Confirm the bulk edit action
                Swal.fire({
                    title: 'Jeni i sigurt?',
                    text: `Dëshironi të ndryshoni kolonën "${getColumnHeader(selectedColumn)}" për ${selectedIds.length} rreshta?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Po, vazhdo',
                    cancelButtonText: 'Anulo',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading spinner
                        Swal.fire({
                            title: 'Duke përditësuar...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request for bulk update
                        $.ajax({
                            url: 'bulk_update_newKont.php', // New API endpoint
                            method: 'POST',
                            data: {
                                ids: selectedIds,
                                column: selectedColumn,
                                value: newValue
                            },
                            dataType: 'json',
                            success: function(response) {
                                Swal.close(); // Close the loading spinner
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: 'Rekordet janë përditësuar me sukses.',
                                        icon: 'success',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    }).then(() => {
                                        // Refresh the active table and charts
                                        refreshTable();
                                        fetchAndRenderData();
                                        // Reset selections
                                        $('.row-select').prop('checked', false);
                                        $('#select-all').prop('checked', false);
                                        toggleBulkEditButton();
                                        // Hide the modal
                                        $('#bulkEditModal').modal('hide');
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gabim!',
                                        text: response.message || 'Ndodhi një gabim gjatë përditësimit të rekordit.',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        },
                                        buttonsStyling: false
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.close(); // Close the loading spinner
                                Swal.fire({
                                    title: 'Gabim!',
                                    text: 'Ndodhi një gabim gjatë përpunimit të kërkesës.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    }
                });
            });

            // Function to get the correct column header
            function getColumnHeader(column) {
                var headers = {
                    'select': 'Zgjidh',
                    'invoice_date': 'Data e faturës',
                    'invoice_number': 'Numri i faturës',
                    'description': 'Përshkrimi',
                    'category': 'Kategoria',
                    'company_name': 'Emri i kompanisë',
                    'document_path': 'Dokumenti',
                    'vlera_faktura': 'Vlera e faturës'
                };
                return headers[column] || column;
            }

            // Initial call to ensure the Bulk Edit button is in the correct state
            toggleBulkEditButton();
        });
    </script>
</body>

</html>