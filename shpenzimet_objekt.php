<?php
include 'partials/header.php';
include 'conn-d.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Shpenzimet e objektit</a></li>
                </ol>
            </nav>
            <!-- Add Expense Button -->
            <div class="row mb-2">
                <div>
                    <a type="button" style="text-decoration: none" class="input-custom-css px-3 py-2 position-relative" href="post_newKont.php">
                        Shto shpenzim
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            BETA
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </a>
                </div>
            </div>
            <!-- Main Tabs -->
            <ul class="nav nav-pills" id="myTab" role="tablist">
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
                    <div class="p-3 shadow-sm mb-4 card">
                        <!-- Sub Tabs for Tables -->
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <?php
                            $tabs = [
                                'all' => 'Të gjitha',
                                'investimet' => 'Investimet',
                                'obligimet' => 'Obligime',
                                'shpenzimet' => 'Shpenzimet',
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
                            // Define column labels
                            $columns = [
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
                                echo "<table class='table table-border table-striped' id='table-{$key}'>";
                                echo "<thead class='table-light'><tr>";
                                foreach ($columns as $columnName) {
                                    echo "<th>{$columnName}</th>";
                                }
                                echo "</tr></thead><tbody></tbody></table></div></div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Raportet Tab Pane -->
                <div class="tab-pane fade" id="raportet-tab-pane" role="tabpanel" aria-labelledby="raportet-tab" tabindex="0">
                    <div class="p-3 shadow-sm mb-4 card">
                        <h3 class="mb-4">Raportet Financiare</h3>
                        <!-- Filters Section -->
                        <div class="row g-3 d-flex align-items-center justify-content-between">
                            <div class="col-md-2">
                                <label for="filterStartDate" class="form-label">Data e Fillimit</label>
                                <input type="date" id="filterStartDate" class="form-control" placeholder="Data e Fillimit">
                            </div>
                            <div class="col-md-2">
                                <label for="filterEndDate" class="form-label">Data e Mbarimit</label>
                                <input type="date" id="filterEndDate" class="form-control" placeholder="Data e Mbarimit">
                            </div>
                            <div class="col-md-2">
                                <label for="filterCategory" class="form-label">Kategoria</label>
                                <select id="filterCategory" class="form-select">
                                    <option value="">Të gjitha</option>
                                    <option value="Shpenzimet">Shpenzimet</option>
                                    <option value="Investimet">Investimet</option>
                                    <option value="Obligime">Obligime</option>
                                    <option value="Tjetër">Tjetër</option>
                                </select>
                            </div>
                            <div class="col-md-2">
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
                            <div class="col-md-2">
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
                        <div class="row my-2">
                            <div class="col-md-2 d-flex">
                                <button id="applyFilters" class="input-custom-css px-3 py-2 me-2 d-flex align-items-center">
                                    <i class="fi fi-rr-filter"></i>
                                </button>
                                <button id="resetFilters" class="input-custom-css px-3 py-2 d-flex align-items-center">
                                    <i class="fi fi-rr-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Initialize Selectr for Select Elements -->
                        <script>
                            new Selectr('#filterCategory', {
                                searchable: true,
                                width: 300
                            });
                            new Selectr('#filterCompany', {
                                searchable: true,
                                width: 300
                            });
                            new Selectr('#filterRegistrant', {
                                searchable: true,
                                width: 300
                            });
                        </script>
                        <!-- Detailed Totals Section -->
                        <div class="row mb-4 g-3">
                            <!-- Total by Category -->
                            <div class="col-md-6">
                                <div class="card rounded-5">
                                    <div class="card-header">
                                        <h5 class="mb-0">Total sipas Kategorisë</h5>
                                    </div>
                                    <div class="card-body p-2">
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
                            <!-- Total by Company -->
                            <div class="col-md-6">
                                <div class="card rounded-5">
                                    <div class="card-header">
                                        <h5 class="mb-0">Total sipas Kompanisë</h5>
                                    </div>
                                    <div class="card-body p-2">
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
                        <!-- Charts Section Removed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<!-- Document Preview Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span id="documentName">Document</span> Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="documentContent" class="text-center">
                    <img id="documentImage" src="" alt="Document Image" class="img-fluid" style="display: none; max-height: 80vh;">
                    <iframe id="documentPDF" src="" width="100%" height="600px" style="display: none;"></iframe>
                    <p id="documentMessage" style="display: none;">
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
<!-- Scripts -->
<script>
    // JavaScript code for handling tables and modals
    // Global variable to store DataTables instances
    var dataTables = {};

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
                // First, fetch all information (if needed)
                fetch(`api/delete_methods/delete_newKont.php?id=${id}&action=fetch`)
                    .then(response => response.json())
                    .then(data => {
                        // Now delete the record
                        return fetch(`api/delete_methods/delete_newKont.php?id=${id}&action=delete`);
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire(
                                'U fshi!',
                                'Rekordi është fshirë me sukses.',
                                'success'
                            ).then(() => {
                                // Refresh the table instead of reloading the page
                                refreshTable();
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
        var activeTab = $('ul.nav-pills .active').attr('id');
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
                            // Refresh the table
                            refreshTable();
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
        // Initialize DataTables with Responsive Extension
        var tableIds = ['table-all', 'table-investimet', 'table-obligimet', 'table-shpenzimet', 'table-tjeter'];
        tableIds.forEach(function(tableId) {
            dataTables[tableId] = $('#' + tableId).DataTable({
                responsive: true, // Enable Responsive Extension
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                stripeClasses: ["stripe-color"],
                ajax: {
                    url: 'api/get_methods/get_table_data.php',
                    type: 'POST', // Use POST if your API expects it
                    data: function(d) {
                        d.category = tableId.replace('table-', '');
                    },
                    dataSrc: '',
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
                        return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
                    }
                }],
                columns: [{
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
                            return `<a href="#" class="view-document input-custom-css px-3 py-2 me-2" data-bs-toggle="modal" data-bs-target="#documentModal" data-file="${data}" data-name="${fileName}">
                                    <i class="fi fi-rr-file"></i>
                                </a><button onclick="showReplaceModal(${row.id})" class="input-custom-css px-3 py-2">
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
                order: [
                    [0, 'desc']
                ], // Default ordering by ID descending
                initComplete: function() {
                    var lengthSelect = $("div.dataTables_length select");
                    lengthSelect.addClass("form-select").css({
                        width: "auto",
                        margin: "0 8px",
                        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                        lineHeight: "1.5",
                        border: "1px solid #ced4da",
                        borderRadius: "0.25rem",
                    });
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
        // Editable fields handling
        $('body').on('click', '.editable', function() {
            var $this = $(this);
            var currentValue = $this.text().trim();
            var column = $this.data('column');
            var id = $this.data('id');
            var columnHeader = getColumnHeader(column);
            Swal.fire({
                title: `Ndrysho ${columnHeader}`,
                html: `
                <div class="form-group">
                    <label for="swal-input" class="form-label">${columnHeader}</label>
                    <input id="swal-input" class="swal2-input form-control" placeholder="Enter ${columnHeader}" value="${currentValue}">
                    <small class="form-text text-muted">Ndryshoni vlerën dhe klikoni ruaj.</small>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Ruaj',
                cancelButtonText: 'Anulo',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                        const newValue = document.getElementById('swal-input').value;
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
                    confirmButton: 'input-custom-css px-3 py-2 me-2',
                    cancelButton: 'input-custom-css px-3 py-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Përditësuar!',
                        text: 'Vlera është përditësuar me sukses.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'input-custom-css px-3 py-2'
                        },
                        buttonsStyling: false
                    });
                }
            }).catch(error => {
                Swal.fire({
                    title: 'Gabim!',
                    text: error.message,
                    icon: 'error',
                    customClass: {
                        confirmButton: 'input-custom-css px-3 py-2'
                    },
                    buttonsStyling: false
                });
            });
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
            var targetTable = $(e.target).attr("href").replace("#pills-", "table-");
            if (dataTables['table-' + targetTable]) {
                dataTables['table-' + targetTable].columns.adjust().responsive.recalc();
                dataTables['table-' + targetTable].ajax.reload(null, false);
            }
        });
    });function updateValue(id, column, value, $element, resolve) {
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
                            confirmButton: 'input-custom-css px-3 py-2'
                        },
                        buttonsStyling: false
                    });
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
    document.addEventListener('DOMContentLoaded', function() {
        const documentModal = document.getElementById('documentModal');
        const documentName = document.getElementById('documentName');
        const documentImage = document.getElementById('documentImage');
        const documentPDF = document.getElementById('documentPDF');
        const documentMessage = document.getElementById('documentMessage');
        const downloadLinkBody = document.getElementById('downloadLinkBody');
        const downloadLinkFooter = document.getElementById('downloadLinkFooter');
        documentModal.addEventListener('show.bs.modal', function(event) {
            const triggerLink = event.relatedTarget;
            const filePath = triggerLink.getAttribute('data-file');
            const fileName = triggerLink.getAttribute('data-name');
            // Update modal title
            documentName.textContent = fileName;
            // Reset modal content
            documentImage.style.display = 'none';
            documentPDF.style.display = 'none';
            documentMessage.style.display = 'none';
            // Set download links
            downloadLinkFooter.href = filePath;
            // Determine file type
            const fileExtension = filePath.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'].includes(fileExtension)) {
                // It's an image
                documentImage.src = filePath;
                documentImage.style.display = 'block';
            } else if (fileExtension === 'pdf') {
                // It's a PDF
                documentPDF.src = filePath;
                documentPDF.style.display = 'block';
            } else {
                // Other file types
                documentMessage.style.display = 'block';
                downloadLinkBody.href = filePath;
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                var total = record.total_shuma;
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
                var total = record.total_shuma;
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

        // Initial data load
        fetchAndRenderData();
    });
</script>


<style>
    /* Optional: Custom styles for the modal */
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
</style>