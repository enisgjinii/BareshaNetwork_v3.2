<?php include 'partials/header.php';
if (isset($_GET['import'])) {
    $linkuof = $_GET['import'];
    $curl = curl_init('https://bareshamusic.sourceaudio.com/api/import/upload?token=6636-66f549fbe813b2087a8748f2b8243dbc&url=http://panel.bareshaoffice.com/' . $linkuof);
    curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => true));
    $cdata = json_decode(curl_exec($curl), true);
    curl_close($curl);
    if ($cdata['error']) {
        echo '<script>alert("' . $cdata['error'] . '");</script>';
    } else {
        echo '<script>alert("' . $cdata['status'] . '");</script>';
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Videot / Ngarkimi</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                            Lista e këngëve
                        </a>
                    </li>
            </nav>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#deletedNgarkimiModal">
                Lista e këngëve të fshira
            </button>
            <!-- Modal -->
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
                                        <th>ID</th>
                                        <th>Rekordi i fshirë</th>
                                        <th>Koha e fshirjes</th>
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
                                <tr>
                                    <th>Id</th>
                                    <th>K&euml;ng&euml;tari</th>
                                    <th>Informacioni</th>
                                    <!-- <th>Emri</th> -->
                                    <!-- <th>T.Shkruesi</th> -->
                                    <!-- <th>Muzika</th> -->
                                    <!-- <th>Orkesetra</th> -->
                                    <!-- <th>C/O</th> -->
                                    <th>Rrjete sociale</th>
                                    <!-- <th>Veper nga Koha</th> -->
                                    <th>Klienti</th>
                                    <!-- <th>Platformat Tjera</th> -->
                                    <!-- <th style="color:green;">Linku</th>
                                        <th style="color:green;">Linku Plat.</th> -->
                                    <!-- <th>Data</th> -->
                                    <!-- <th>Gjuha</th> -->
                                    <th>Info Shtes</th>
                                    <!-- <th>Postuar Nga</th> -->
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    $(document).ready(function() {
        var table = $('#example').DataTable({
            // responsive: true,
            order: [
                [0, 'desc'] // Default sorting on the first column in ascending order
            ],
            searching: true,
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: "Kopjo tabelen ne formatin Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
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
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
            ],
            initComplete: function() {
                var btns = $(".dt-buttons");
                btns.addClass("").removeClass("dt-buttons btn-group");
                var lengthSelect = $("div.dataTables_length select");
                lengthSelect.addClass("form-select");
                lengthSelect.css({
                    width: "auto",
                    margin: "0 8px",
                    padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                    lineHeight: "1.5",
                    border: "1px solid #ced4da",
                    borderRadius: "0.25rem",
                });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            stripeClasses: ['stripe-color'],
            ajax: {
                url: 'fetch_music.php',
                type: 'POST',
                dataType: 'json',
                dataSrc: 'data'
            },
            columns: [{
                    data: "id"
                },
                {
                    data: 'kengetari',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Add paragraph with kengetari data and trash icon button
                            return `<p>${data}</p><button class="btn btn-danger text-white px-2 py-1 rounded-5 delete-btn" data-id="${row.id}"><i class="fi fi-rr-trash"></i></button>`;
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const paragraphHTML = `
                <p><strong>Emri:</strong> ${row.emri}</p>
                <p><strong>Teksti:</strong> ${row.teksti}</p>
                <p><strong>Muzika:</strong> ${row.muzika}</p>
                <p><strong>Orkestra:</strong> ${row.orkestra}</p>
                <p><strong>C/O:</strong> ${row.co}</p>
                <p><strong>Veper nga koha:</strong> ${row.veper}</p>
                <p><strong>Data:</strong> ${row.data}</p>
                <p><strong>Gjuha:</strong> ${row.gjuha}</p>
                <p><strong>Postuar nga:</strong> ${row.postuar_nga}</p>
            `;
                            return paragraphHTML;
                        } else {
                            return `${row.emri} - ${row.teksti} - ${row.muzika} - ${row.orkestra} - ${row.co} - ${row.veper}- ${row.data} - ${row.gjuha} - ${row.postuar_nga}`;
                        }
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Define icon classes for each platform
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
                                'AudioMack': 'fas fa-music',
                                // Add more platforms as needed
                            };
                            // Split the 'platformat' data into individual platform names
                            const platformNames = row.platformat.split(', ');
                            // Generate HTML for platform icons
                            const platformIconsHTML = platformNames.map(platformName => {
                                const iconClass = icons[platformName] || 'fas fa-question'; // Default icon if platform not found
                                return `<i class="${iconClass} fa-lg"></i>`;
                            }).join(' ');
                            const paragraphHTML = `
                <p><strong>Facebook:</strong> ${row.facebook}</p>
                <p><strong>Instagram:</strong> ${row.instagram}</p>
                <p><strong>Linku Youtube:</strong> ${row.linku}</p>
                <p><strong>Linku Platform:</strong> ${row.linkuplat}</p>
                <br>
                <p style='white-space: normal;'>${platformIconsHTML}</p>
            `;
                            return paragraphHTML;
                        } else {
                            return `${row.facebook} - ${row.instagram} - ${row.linku} - ${row.linkuplat} - ${row.platformat}`;
                        }
                    }
                }, {
                    data: 'klienti_emri'
                }, {
                    data: 'infosh'
                },
            ],
            columnDefs: [{
                "targets": [2, 3, 4, 5], // Indexes of the original columns you want to hide
                "render": function(data, type, row) {
                    // Apply the style to the specified columns
                    return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
                }
            }],
        });
        // Dëgjuesi i eventit për butonin e fshirjes
        $('#example').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            // Shfaq dialogun e konfirmimit
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
                    // Nëse është konfirmuar, dërgoni një kërkesë AJAX për të fshirë regjistrin
                    $.ajax({
                        url: 'delete_ngarkimi.php', // Ndryshoni këtë me URL-në e skriptit tuaj të fshirjes
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            const currentPage = table.page.info().page;
                            // Reload table data
                            table.ajax.reload(function() {
                                // After reload, set the table to the saved current page
                                table.page(currentPage).draw('page');
                            });
                            // Shfaqni një njoftim për suksesin e fshirjes
                            Swal.fire({
                                title: 'Fshirja është kryer me sukses!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500 // Njoftimi do të zhduket pas 1.5 sekondave
                            });
                        },
                        error: function(xhr, status, error) {
                            // Trajtoni gabimin
                            console.error(xhr.responseText);
                            // Shfaqni një njoftim për gabimin
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
    });
    $(document).ready(function() {
        $('#deletedRecordsTable').DataTable({
            "processing": true,
            "serverSide": true,
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: "Kopjo tabelen ne formatin Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
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
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
            ],
            initComplete: function() {
                var btns = $(".dt-buttons");
                btns.addClass("").removeClass("dt-buttons btn-group");
                var lengthSelect = $("div.dataTables_length select");
                lengthSelect.addClass("form-select");
                lengthSelect.css({
                    width: "auto",
                    margin: "0 8px",
                    padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                    lineHeight: "1.5",
                    border: "1px solid #ced4da",
                    borderRadius: "0.25rem",
                });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            stripeClasses: ['stripe-color'],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            paging: true,
            "ajax": {
                "url": "fetch_deleted_records.php",
                "type": "POST",
            },
            "columns": [{
                    "data": 0
                }, // Index of the "id" column
                {
                    "data": 1
                }, // Index of the "deleted_record" column
                {
                    "data": 2
                } // Index of the "deleted_at" column
            ],
            "columnDefs": [{
                "targets": 1, // Index of the "deleted_record" column
                "render": function(data, type, row) {
                    if (type === 'display' && data !== null) {
                        var rowData = JSON.parse(data); // Parse the JSON string
                        var html = '';
                        for (var key in rowData) {
                            var capitalizedKey = key.charAt(0).toUpperCase() + key.slice(1);
                            html += '<p><strong>' + capitalizedKey + ':</strong> ' + rowData[key] + '</p>';
                        }
                        return html;
                    }
                    return data;
                }
            }]
        });
    });
</script>