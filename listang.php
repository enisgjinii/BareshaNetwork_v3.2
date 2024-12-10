<?php
include 'partials/header.php';

// Function to sanitize input data
function sanitize($data)
{
    return htmlspecialchars(strip_tags($data));
}

if (isset($_GET['import'])) {
    // Sanitize the 'import' parameter to prevent URL manipulation
    $importParam = sanitize($_GET['import']);
    $url = 'https://bareshamusic.sourceaudio.com/api/import/upload?' . http_build_query([
        'token' => '6636-66f549fbe813b2087a8748f2b8243dbc',
        'url' => "http://panel.bareshaoffice.com/{$importParam}"
    ]);

    // Handle potential errors during the API request
    $response = @file_get_contents($url);
    if ($response === FALSE) {
        echo "<script>alert('Gabim gjatë importimit të të dhënave. Ju lutem provoni më vonë.');</script>";
    } else {
        $cdata = json_decode($response, true);
        echo "<script>alert('" . (isset($cdata['error']) ? sanitize($cdata['error']) : sanitize($cdata['status'])) . "');</script>";
    }
}

$breadcrumbItems = [
    ['text' => 'Videot & Ngarkimi', 'link' => '#'],
    ['text' => 'Lista e këngëve', 'link' => __FILE__, 'active' => true]
];

$tableHeaders = ['Id', 'Këngëtari', 'Informacioni', 'Rrjete sociale', 'Klienti', 'Info Shtes'];
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5 mb-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbItems as $item) : ?>
                        <li class="breadcrumb-item <?= isset($item['active']) && $item['active'] ? 'active' : '' ?>" <?= isset($item['active']) && $item['active'] ? 'aria-current="page"' : '' ?>>
                            <a href="<?= $item['link'] ?>" class="text-reset" style="text-decoration: none;"><?= $item['text'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>

            <!-- Button to Open Deleted Records Modal -->
            <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#deletedNgarkimiModal">
                Lista e këngëve të fshira
            </button>

            <!-- Deleted Records Modal -->
            <div class="modal fade" id="deletedNgarkimiModal" tabindex="-1" aria-labelledby="deletedNgarkimiModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deletedNgarkimiModalLabel">Lista e këngëve të fshira</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                        </div>
                        <div class="modal-body">
                            <table id="deletedRecordsTable" class="table table-bordered table-sm" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-dark">ID</th>
                                        <th class="text-dark">Rekordi i fshirë</th>
                                        <th class="text-dark">Koha e fshirjes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Empty state message -->
                                    <tr id="deletedRecordsEmpty" style="display: none;">
                                        <td colspan="3" class="text-center">Nuk ka rekorde të fshira për të shfaqur.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="editForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ndrysho Regjistrin</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form fields with placeholders -->
                                <input type="hidden" name="id" id="edit-id">
                                <div class="mb-3">
                                    <label for="edit-kengetari" class="form-label">Këngëtari</label>
                                    <input type="text" class="form-control" id="edit-kengetari" name="kengetari" placeholder="Shkruani emrin e këngëtarit" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-emri" class="form-label">Emri</label>
                                    <input type="text" class="form-control" id="edit-emri" name="emri" placeholder="Shkruani emrin" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit-teksti" class="form-label">Teksti</label>
                                    <input type="text" class="form-control" id="edit-teksti" name="teksti" placeholder="Shkruani tekstin">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-muzika" class="form-label">Muzika</label>
                                    <input type="text" class="form-control" id="edit-muzika" name="muzika" placeholder="Shkruani muzikën">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-orkestra" class="form-label">Orkestra</label>
                                    <input type="text" class="form-control" id="edit-orkestra" name="orkestra" placeholder="Shkruani orkestra">
                                </div>
                                <div class="mb-3">
                                    <label for="edit-data" class="form-label">Data</label>
                                    <input type="date" class="form-control" id="edit-data" name="data" required>
                                </div>
                                <!-- Add other fields as needed with appropriate placeholders -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
                                <button type="submit" class="btn btn-primary">Ruaj Ndryshimet</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Collapsible Filter Section -->
            <div class="card mb-3">
                <div class="card-header" id="filterHeader" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false" aria-controls="filterSection" style="cursor: pointer;">
                    <h5 class="mb-0">Filtër</h5>
                </div>
                <div id="filterSection" class="collapse">
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label for="startDate" class="form-label">Data e Fillimit:</label>
                                <input type="date" id="startDate" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label for="endDate" class="form-label">Data e Mbarimit:</label>
                                <input type="date" id="endDate" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label for="artistFilter" class="form-label">Këngëtari:</label>
                                <input type="text" id="artistFilter" class="form-control" placeholder="Kërko këngëtarin" />
                            </div>
                            <div class="col-md-3">
                                <label for="clientFilter" class="form-label">Klienti:</label>
                                <input type="text" id="clientFilter" class="form-control" placeholder="Kërko klientin" />
                            </div>
                            <div class="col-12">
                                <button id="filterButton" class="btn btn-primary">Filtro</button>
                                <button id="resetButton" type="button" class="btn btn-secondary">Rifillo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Table with Empty State -->
            <div class="card rounded-5 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table w-100">
                            <thead class="bg-light">
                                <tr>
                                    <?php foreach ($tableHeaders as $header) : ?>
                                        <th class="text-dark"><?= $header ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Empty state message -->
                                <tr id="mainTableEmpty" style="display: none;">
                                    <td colspan="<?= count($tableHeaders) ?>" class="text-center">Nuk ka rekorde për të shfaqur.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Styles for Expandable Content -->
<style>
    .expandable-content {
        max-height: 120px;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .expandable-content.expanded {
        max-height: none;
    }
</style>

<?php include 'partials/footer.php'; ?>

<!-- JavaScript Enhancements -->
<script>
    $(document).ready(function() {
        const commonButtonClass = "btn btn-light btn-sm bg-light border me-2 rounded-5";
        const commonButtonSettings = {
            pdfHtml5: {
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: "Eksporto tabelen ne formatin PDF"
            },
            copyHtml5: {
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: "Kopjo tabelen ne formatin Clipboard"
            },
            excelHtml5: {
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: "Eksporto tabelen ne formatin Excel"
            },
            print: {
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: "Printo tabelën"
            }
        };
        const commonDTSettings = {
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'i<'col-md-6'><'col-md-6'p>>",
            buttons: Object.entries(commonButtonSettings).map(([key, value]) => ({
                extend: key,
                ...value,
                className: commonButtonClass,
                ...(key === 'excelHtml5' ? {
                    exportOptions: {
                        modifier: {
                            search: "applied",
                            order: "applied",
                            page: "all" // Ensure all pages are exported when exporting to Excel
                        }
                    }
                } : {})
            })),
            initComplete: function() {
                $(".dt-buttons").removeClass("dt-buttons btn-group");
                $("div.dataTables_length select").addClass("form-select").css({
                    width: "auto",
                    margin: "0 8px",
                    padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                    lineHeight: "1.5",
                    border: "1px solid #ced4da",
                    borderRadius: "0.25rem"
                });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            stripeClasses: ['stripe-color'],
            // Add responsive design
            responsive: true,
            // Set initial page length (optional)
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Të gjitha"]
            ]
        };

        // Initialize Main DataTable
        const table = $('#example').DataTable({
            ...commonDTSettings,
            order: [
                [0, 'desc']
            ],
            searching: true,
            ajax: {
                url: 'api/get_methods/get_music.php',
                type: 'POST',
                dataType: 'json',
                dataSrc: function(json) {
                    if (!json.data || json.data.length === 0) {
                        $('#mainTableEmpty').show();
                    } else {
                        $('#mainTableEmpty').hide();
                    }
                    return json.data || [];
                },
                data: function(d) {
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                    d.artist = $('#artistFilter').val();
                    d.client = $('#clientFilter').val();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Ka ndodhur një gabim gjatë ngarkimit të të dhënave. Ju lutem provoni më vonë.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            columns: [{
                    data: "id"
                },
                {
                    data: 'kengetari',
                    render: (data, type, row) => {
                        if (type === 'display') {
                            return `
                                <p>${data}</p>
                                <button class="btn btn-primary text-white px-2 py-1 rounded-5 edit-btn" data-id="${row.id}" title="Ndrysho">
                                    <i class="fi fi-rr-edit"></i>
                                </button>
                                <button class="btn btn-danger text-white px-2 py-1 rounded-5 delete-btn" data-id="${row.id}" title="Fshije">
                                    <i class="fi fi-rr-trash"></i>
                                </button>`;
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: (data, type, row) => {
                        if (type !== 'display') return Object.values(row).join(' - ');
                        const info = ['emri', 'teksti', 'muzika', 'orkestra', 'co', 'veper', 'data', 'gjuha', 'postuar_nga']
                            .map(key => `<p><strong>${capitalizeFirstLetter(key)}:</strong> ${row[key] || 'N/A'}</p>`).join('');
                        return `<div class="expandable-content">${info}</div><button class="input-custom-css px-3 py-2 expand-btn">Shfaq më shumë</button>`;
                    }
                },
                {
                    data: null,
                    render: (data, type, row) => {
                        if (type !== 'display') return `${row.facebook || 'N/A'} - ${row.instagram || 'N/A'} - ${row.linku || 'N/A'} - ${row.linkuplat || 'N/A'} - ${row.platformat || 'N/A'}`;
                        const icons = {
                            'Spotify': 'fab fa-spotify',
                            'Youtube Music': 'fab fa-youtube',
                            'iTunes': 'fab fa-itunes',
                            'Apple Music': 'fab fa-apple',
                            'TikTok': 'fab fa-tiktok',
                            'Instagram Stories': 'fab fa-instagram',
                            'Tidal': 'fab fa-tidal',
                            'Amazon Music': 'fab fa-amazon',
                            'Pandora': 'fab fa-pandora',
                            'AudioMack': 'fas fa-music'
                        };
                        const platformIconsHTML = row.platformat ? row.platformat.split(', ')
                            .map(name => `<i class="${icons[name] || 'fas fa-question'} fa-lg" title="${name}"></i>`).join(' ') : 'N/A';
                        return `
                            <p><strong>Facebook:</strong> ${row.facebook || 'N/A'}</p>
                            <p><strong>Instagram:</strong> ${row.instagram || 'N/A'}</p>
                            <p><strong>Linku Youtube:</strong> ${row.linku || 'N/A'}</p>
                            <p><strong>Linku Platform:</strong> ${row.linkuplat || 'N/A'}</p>
                            <br>
                            <p style='white-space: normal;'>${platformIconsHTML}</p>
                        `;
                    }
                },
                {
                    data: 'klienti_emri',
                    render: (data, type, row) => data || 'N/A'
                },
                {
                    data: 'infosh',
                    render: (data, type, row) => data || 'N/A'
                }
            ],
            columnDefs: [{
                targets: [2, 3, 4, 5],
                render: (data, type, row) => type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data
            }]
        });

        // Function to capitalize first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        // Event listener for the filter button
        $('#filterButton').on('click', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // Event listener for the reset button
        $('#resetButton').on('click', function() {
            $('#filterForm')[0].reset();
            table.ajax.reload();
        });

        // Handle expand button click
        $('#example').on('click', '.expand-btn', function() {
            const $content = $(this).prev('.expandable-content');
            const isExpanded = $content.hasClass('expanded');
            $content.toggleClass('expanded').css('max-height', isExpanded ? '120px' : 'none');
            $(this).text(isExpanded ? 'Shfaq më shumë' : 'Mbyll');
        });

        // Handle delete button click
        $('#example').on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'A jeni i sigurt që dëshironi ta fshini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Po, fshije!',
                cancelButtonText: 'Anulo'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api/delete_methods/delete_ngarkimi.php',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            try {
                                const res = JSON.parse(response);
                                if (res.success) {
                                    const currentPage = table.page.info().page;
                                    table.ajax.reload(() => table.page(currentPage).draw('page'));
                                    Swal.fire({
                                        title: 'Fshirja është kryer me sukses!',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gabim!',
                                        text: res.message || 'Ka ndodhur një problem gjatë fshirjes së regjistrit.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            } catch (e) {
                                console.error('Response parsing error:', e);
                                Swal.fire({
                                    title: 'Gabim!',
                                    text: 'Ka ndodhur një problem gjatë përpunimit të përgjigjes së serverit.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            Swal.fire({
                                title: 'Gabim!',
                                text: 'Ka ndodhur një problem gjatë fshirjes së regjistrit.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        // Handle edit button click
        $('#example').on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const rowData = table.row($(this).parents('tr')).data();

            if (!rowData) {
                Swal.fire({
                    title: 'Gabim!',
                    text: 'Të dhënat për këtë regjistër nuk janë të disponueshme.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Populate the form fields with rowData
            $('#edit-id').val(rowData.id);
            $('#edit-kengetari').val(rowData.kengetari);
            $('#edit-emri').val(rowData.emri);
            $('#edit-teksti').val(rowData.teksti);
            $('#edit-muzika').val(rowData.muzika);
            $('#edit-orkestra').val(rowData.orkestra);
            $('#edit-data').val(rowData.data);
            // Continue for other fields as needed

            // Open the modal
            $('#editModal').modal('show');
        });

        // Handle form submission
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            // Validate required fields before submission
            if (!$('#edit-kengetari').val() || !$('#edit-emri').val() || !$('#edit-data').val()) {
                Swal.fire({
                    title: 'Gabim!',
                    text: 'Ju lutem plotësoni të gjitha fushat e kërkuara.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: 'api/edit_methods/update_ngarkimi.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.success) {
                            $('#editModal').modal('hide');
                            Swal.fire({
                                title: 'Përditësimi është kryer me sukses!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                title: 'Gabim!',
                                text: res.message || 'Ka ndodhur një problem gjatë përditësimit të regjistrit.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    } catch (e) {
                        console.error('Response parsing error:', e);
                        Swal.fire({
                            title: 'Gabim!',
                            text: 'Ka ndodhur një problem gjatë përpunimit të përgjigjes së serverit.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Ka ndodhur një problem gjatë përditësimit të regjistrit.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Initialize Deleted Records DataTable
        $('#deletedRecordsTable').DataTable({
            ...commonDTSettings,
            processing: true,
            serverSide: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Të gjitha"]
            ],
            paging: true,
            ajax: {
                url: "fetch_deleted_records.php",
                type: "POST",
                dataSrc: function(json) {
                    if (!json.data || json.data.length === 0) {
                        $('#deletedRecordsEmpty').show();
                    } else {
                        $('#deletedRecordsEmpty').hide();
                    }
                    return json.data || [];
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Ka ndodhur një gabim gjatë ngarkimit të të dhënave të fshira. Ju lutem provoni më vonë.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            columns: [{
                    data: 0
                },
                {
                    data: 1,
                    render: (data, type, row) => {
                        if (type === 'display' && data !== null) {
                            try {
                                const parsedData = JSON.parse(data);
                                return Object.entries(parsedData)
                                    .map(([key, value]) => `<p><strong>${capitalizeFirstLetter(key)}:</strong> ${value}</p>`)
                                    .join('');
                            } catch (e) {
                                console.error('JSON Parsing Error:', e);
                                return 'N/A';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 2
                }
            ],
            columnDefs: [{
                targets: 1,
                render: (data, type, row) => type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data
            }]
        });

        // Handle modal hidden event to reset forms and states
        $('#editModal').on('hidden.bs.modal', function() {
            $('#editForm')[0].reset();
        });
    });
</script>