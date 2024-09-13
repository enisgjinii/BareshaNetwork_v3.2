<?php
include 'partials/header.php';
include 'conn-d.php';

// Fetch data for expenses chart
$sql = "SELECT DATE(created_at) AS date, SUM(shuma) AS total_shuma FROM expenses GROUP BY DATE(created_at)";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$expenses = [];
$total = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
        $total += $row['total_shuma'];
    }
}
?>
<!-- Include Bootstrap CSS and JS if not already included in header.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Optionally include PDF.js if handling PDFs more robustly -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Shpenzimet e objektit</a></li>
                </ol>
            </nav>
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
            <div class="p-3 shadow-sm mb-4 card">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 active" style="text-decoration: none;text-transform: none" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">Të gjitha</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5" style="text-decoration: none;text-transform: none" id="pills-investime-tab" data-bs-toggle="pill" data-bs-target="#pills-investime" type="button" role="tab" aria-controls="pills-investime" aria-selected="true">Investime</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5" style="text-decoration: none;text-transform: none" id="pills-obligimet-tab" data-bs-toggle="pill" data-bs-target="#pills-obligimet" type="button" role="tab" aria-controls="pills-obligimet" aria-selected="false">Obligime</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5" style="text-decoration: none;text-transform: none" id="pills-shpenzimet-tab" data-bs-toggle="pill" data-bs-target="#pills-shpenzimet" type="button" role="tab" aria-controls="pills-shpenzimet" aria-selected="false">Shpenzimet</button>
                    </li>
                    <!-- Tjeter -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5" style="text-decoration: none;text-transform: none" id="pills-tjeter-tab" data-bs-toggle="pill" data-bs-target="#pills-tjeter" type="button" role="tab" aria-controls="pills-tjeter" aria-selected="false">Tjetër</button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <?php
                    // Define tabs and queries
                    $tabs = [
                        'all' => ['name' => 'All', 'query' => "SELECT * FROM invoices_kont", 'active' => true],
                        'investime' => ['name' => 'Investimet', 'query' => "SELECT * FROM invoices_kont WHERE category = 'Investimet'"],
                        'obligimet' => ['name' => 'Obligime', 'query' => "SELECT * FROM invoices_kont WHERE category = 'Obligime'"],
                        'shpenzimet' => ['name' => 'Shpenzimet', 'query' => "SELECT * FROM invoices_kont WHERE category = 'Shpenzimet'"],
                        'tjeter' => ['name' => 'Tjetër', 'query' => "SELECT * FROM invoices_kont WHERE category = 'Tjetër'"]
                    ];
                    // Define column labels
                    $columns = [
                        'id' => 'ID',
                        'invoice_date' => 'Data e faturës',
                        'description' => 'Përshkrimi',
                        'category' => 'Kategoria',
                        'company_name' => 'Emri i kompanisë',
                        'document_path' => 'Path-i i dokumentit',
                        // 'created_at' => 'Krijuar në',
                        'vlera_faktura' => 'Vlera e fatures',
                        'action' => 'Veprim'
                    ];
                    // Function to render table rows with modal triggers
                    function renderRow($row, $columns)
                    {
                        foreach (array_keys($columns) as $column) {
                            if ($column == 'document_path') {
                                $filePath = htmlspecialchars($row[$column], ENT_QUOTES, 'UTF-8');
                                $fileName = htmlspecialchars($row[$column], ENT_QUOTES, 'UTF-8');
                                echo "<td>
                                        <a href='#' 
                                           class='view-document input-custom-css px-3 py-2' 
                                           data-bs-toggle='modal' 
                                           data-bs-target='#documentModal' 
                                           data-file='{$filePath}' 
                                           data-name='{$fileName}'>
                                            View
                                        </a>
                                      </td>";
                            } elseif ($column != 'action') {
                                echo "<td>" . htmlspecialchars($row[$column], ENT_QUOTES, 'UTF-8') . "</td>";
                            }
                        }
                        // Action buttons
                        echo "<td>";
                        echo "<button onclick='confirmDelete({$row['id']})' style='text-decoration: none;text-transform: none' class='input-custom-css px-3 py-2'><i class='fi fi-rr-trash'></i></button>";
                        echo "</td>";
                    }
                    // Loop through tabs to render content
                    foreach ($tabs as $key => $tab) {
                        $activeClass = $tab['active'] ? 'show active' : '';
                        echo "<div class='tab-pane fade {$activeClass}' id='pills-{$key}' role='tabpanel' aria-labelledby='pills-{$key}-tab' tabindex='0'>";
                        try {
                            $result = $conn->query($tab['query']);
                            if (!$result) {
                                throw new Exception("Database query failed: " . $conn->error);
                            }
                            // Render table headers
                            echo "<table class='table table-border' id='table-{$key}'>";
                            echo "<thead class='table-light'><tr>";
                            foreach ($columns as $columnName) {
                                echo "<th>{$columnName}</th>";
                            }
                            echo "</tr></thead><tbody>";
                            // Render table rows
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    renderRow($row, $columns); // Call reusable function to render row
                                    echo "</tr>";
                                }
                            }
                            echo "</tbody></table></div>";
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>Ndodhi një gabim: " . $e->getMessage() . "</div>";
                        }
                    }
                    ?>
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
                <!-- Content will be injected here -->
                <div id="documentContent" class="text-center">
                    <!-- For images -->
                    <img id="documentImage" src="" alt="Document Image" class="img-fluid" style="display: none; max-height: 80vh;">

                    <!-- For PDFs -->
                    <iframe id="documentPDF" src="" width="100%" height="600px" style="display: none;"></iframe>

                    <!-- For other types -->
                    <p id="documentMessage" style="display: none;">Preview not available. <a id="downloadLink" href="#" download>Download</a></p>
                </div>
            </div>
            <div class="modal-footer">
                <a id="downloadLinkFooter" href="#" class="btn btn-primary" download>Download</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variable to store DataTables instances
    var dataTables = {};

    function confirmDelete(id) {
        Swal.fire({
            title: 'A jeni i sigurt?',
            text: "Ju nuk do të jeni në gjendje ta ktheni këtë!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Po, fshijeni!'
        }).then((result) => {
            if (result.isConfirmed) {
                // First, fetch all information
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
                                'Skedari juaj është fshirë.',
                                'success'
                            ).then(() => {
                                // Refresh the table instead of reloading the page
                                refreshTable();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'Pati një problem me fshirjen e skedarit.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
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
    $(document).ready(function() {
        var tableIds = ['table-all', 'table-investime', 'table-obligimet', 'table-shpenzimet', 'table-tjeter'];
        tableIds.forEach(function(tableId) {
            dataTables[tableId] = $('#' + tableId).DataTable({
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                stripeClasses: ["stripe-color"],
                responsive: true,
                ajax: {
                    url: 'api/get_methods/get_table_data.php',
                    data: function(d) {
                        d.category = tableId.replace('table-', '');
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'invoice_date',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="editable" data-column="invoice_date" data-id="' + row.id + '">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'description',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="editable" data-column="description" data-id="' + row.id + '">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'category',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="editable" data-column="category" data-id="' + row.id + '">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'company_name',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="editable" data-column="company_name" data-id="' + row.id + '">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'document_path',
                        render: function(data, type, row) {
                            return '<a href="#" style="text-decoration: none;text-transform: none" class="view-document input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#documentModal" data-file="' + data + '" data-name="' + data + '">Shiko dokumentin</a>';
                        }
                    },
                    {
                        data: 'vlera_faktura',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="editable" data-column="vlera_faktura" data-id="' + row.id + '">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<button onclick="confirmDelete(' + row.id + ')" style="text-decoration: none;text-transform: none" class="input-custom-css px-3 py-2"><i class="fi fi-rr-trash"></i></button>';
                        }
                    }
                ],
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
                buttons: [ /* Buttons Configuration */ ]
            });
        });
        $('body').on('click', '.editable', function() {
            var $this = $(this);
            var currentValue = $this.text().trim(); // Ensuring there's no extra whitespace
            var column = $this.data('column');
            var id = $this.data('id');
            var columnHeader = getColumnHeader(column); // Ensure this function is defined to fetch the column header
            Swal.fire({
                title: 'Ndrysho ' + columnHeader,
                html: `
            <div class="form-group">
                <label for="swal-input" class="form-label">${columnHeader}</label>
                <input id="swal-input" class="swal2-input form-control" placeholder="Enter ${columnHeader}" value="${currentValue}">
                <small class="form-text text-muted">Ndryshoni vlerën në fushën e tekstit dhe klikoni ruaj.</small>
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
                        title: 'Updated!',
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
                    title: 'Error!',
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
                'description': 'Përshkrimi',
                'category': 'Kategoria',
                'company_name': 'Emri i kompanisë',
                'document_path': 'Path-i i dokumentit',
                'vlera_faktura': 'Vlera e fatures'
            };
            return headers[column] || column;
        }
        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            var targetTable = $(e.target).attr("href").replace("#pills-", "table-");
            if (dataTables[targetTable]) {
                dataTables[targetTable].ajax.reload();
                dataTables[targetTable].columns.adjust().responsive.recalc();
            }
        });
    });
    // Function to update editable fields via AJAX
    function updateValue(id, column, value, $element, resolve) {
        $.ajax({
            url: 'api/edit_methods/update_newKont.php',
            method: 'POST',
            data: {
                id: id,
                column: column,
                value: value
            },
            success: function(response) {
                if (response.success) {
                    $element.text(value);
                    resolve();
                } else {
                    Swal.showValidationMessage('Dështoi përditësimi: ' + response.message);
                }
            },
            error: function() {
                Swal.showValidationMessage('Ndodhi një gabim gjatë përditësimit.');
            }
        });
    }
</script>

<!-- JavaScript for handling the document preview modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const documentModal = document.getElementById('documentModal');
        const documentName = document.getElementById('documentName');
        const documentImage = document.getElementById('documentImage');
        const documentPDF = document.getElementById('documentPDF');
        const documentMessage = document.getElementById('documentMessage');
        const downloadLink = document.getElementById('downloadLink');
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
            downloadLink.href = filePath;
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
                documentMessage.querySelector('#downloadLink').href = filePath;
            }
        });
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
</style>