<?php
include 'partials/header.php';
if (isset($_GET['import'])) {
    $url = 'https://bareshamusic.sourceaudio.com/api/import/upload?' . http_build_query([
        'token' => '6636-66f549fbe813b2087a8748f2b8243dbc',
        'url' => "http://panel.bareshaoffice.com/{$_GET['import']}"
    ]);
    $cdata = json_decode(file_get_contents($url), true);
    echo "<script>alert('" . ($cdata['error'] ?? $cdata['status']) . "');</script>";
}
$breadcrumbItems = [
    ['text' => 'Videot / Ngarkimi', 'link' => '#'],
    ['text' => 'Lista e këngëve', 'link' => __FILE__, 'active' => true]
];
$tableHeaders = ['Id', 'Këngëtari', 'Informacioni', 'Rrjete sociale', 'Klienti', 'Info Shtes'];
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbItems as $item) : ?>
                        <li class="breadcrumb-item <?= $item['active'] ?? false ? 'active' : '' ?>" <?= $item['active'] ?? false ? 'aria-current="page"' : '' ?>>
                            <a href="<?= $item['link'] ?>" class="text-reset" style="text-decoration: none;"><?= $item['text'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#deletedNgarkimiModal">Lista e këngëve të fshira</button>
            <div class="modal fade" id="deletedNgarkimiModal" tabindex="-1" aria-labelledby="deletedNgarkimiModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deletedNgarkimiModalLabel">Lista e këngëve të fshira</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card rounded-5 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table w-100">
                            <thead class="bg-light">
                                <tr><?php foreach ($tableHeaders as $header) : ?><th class="text-dark"><?= $header ?></th><?php endforeach; ?></tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
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
                            page: "all"
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
            stripeClasses: ['stripe-color']
        };
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
                dataSrc: 'data'
            },
            columns: [{
                    data: "id"
                },
                {
                    data: 'kengetari',
                    render: (data, type, row) => type === 'display' ? `<p>${data}</p><button class="btn btn-danger text-white px-2 py-1 rounded-5 delete-btn" data-id="${row.id}"><i class="fi fi-rr-trash"></i></button>` : data
                },
                {
                    data: null,
                    render: (data, type, row) => {
                        if (type !== 'display') return Object.values(row).join(' - ');
                        const info = ['emri', 'teksti', 'muzika', 'orkestra', 'co', 'veper', 'data', 'gjuha', 'postuar_nga']
                            .map(key => `<p><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${row[key]}</p>`).join('');
                        return `<div class="expandable-content">${info}</div><button class="input-custom-css px-3 py-2 expand-btn">Shfaq më shumë</button>`;
                    }
                },
                {
                    data: null,
                    render: (data, type, row) => {
                        if (type !== 'display') return `${row.facebook} - ${row.instagram} - ${row.linku} - ${row.linkuplat} - ${row.platformat}`;
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
                        const platformIconsHTML = row.platformat.split(', ')
                            .map(name => `<i class="${icons[name] || 'fas fa-question'} fa-lg"></i>`).join(' ');
                        return `
                    <p><strong>Facebook:</strong> ${row.facebook}</p>
                    <p><strong>Instagram:</strong> ${row.instagram}</p>
                    <p><strong>Linku Youtube:</strong> ${row.linku}</p>
                    <p><strong>Linku Platform:</strong> ${row.linkuplat}</p>
                    <br>
                    <p style='white-space: normal;'>${platformIconsHTML}</p>
                `;
                    }
                },
                {
                    data: 'klienti_emri'
                },
                {
                    data: 'infosh'
                }
            ],
            columnDefs: [{
                targets: [2, 3, 4, 5],
                render: (data, type, row) => type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data
            }]
        });
        $('#example').on('click', '.expand-btn', function() {
            const $content = $(this).prev('.expandable-content');
            const isExpanded = $content.hasClass('expanded');
            $content.toggleClass('expanded').css('max-height', isExpanded ? '100px' : 'none');
            $(this).text(isExpanded ? 'Shfaq më shumë' : 'Mbyll');
        }).on('click', '.delete-btn', function() {
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
                            const currentPage = table.page.info().page;
                            table.ajax.reload(() => table.page(currentPage).draw('page'));
                            Swal.fire({
                                title: 'Fshirja është kryer me sukses!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
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
        $('#deletedRecordsTable').DataTable({
            ...commonDTSettings,
            processing: true,
            serverSide: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            paging: true,
            ajax: {
                url: "fetch_deleted_records.php",
                type: "POST"
            },
            columns: [{
                data: 0
            }, {
                data: 1
            }, {
                data: 2
            }],
            columnDefs: [{
                targets: 1,
                render: (data, type, row) => {
                    if (type === 'display' && data !== null) {
                        return Object.entries(JSON.parse(data))
                            .map(([key, value]) => `<p><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${value}</p>`)
                            .join('');
                    }
                    return data;
                }
            }]
        });
    });
</script>