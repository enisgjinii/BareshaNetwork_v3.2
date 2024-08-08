<?php include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="authenticated_channels.php" class="text-reset" style="text-decoration: none;">Strike për platforma</a></li>
                </ol>
            </nav>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Dërgo strike
            </button>
            <!-- Modal -->
            <form id="platformForm" action="process_new_platform_strike.php" method="POST">
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Shto strike në sistem</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="platforma" class="form-label">Zgjedh platformën</label>
                                    <select name="platforma" id="platforma">
                                        <option value="Spotify">Spotify</option>
                                        <option value="Youtube">Youtube</option>
                                        <option value="Facebook">Facebook</option>
                                        <option value="Instagram">Instagram</option>
                                        <option value="Twitter">Twitter</option>
                                        <option value="TikTok">TikTok</option>
                                        <option value="Twitch">Twitch</option>
                                        <option value="LinkedIn">LinkedIn</option>
                                    </select>
                                </div>
                                <div class="mb-3 text-dark" >
                                    <label for="titulli" class="form-label ">Titulli</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="titulli" id="titulli" placeholder="Shëno titullin" required>
                                </div>
                                <div class="mb-3">
                                    <label for="pershkrimi" class="form-label">Pershkrimi</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="pershkrimi" id="pershkrimi" placeholder="Shëno pershkrimin" required>
                                </div>
                                <div class="mb-3">
                                    <label for="data_e_krijimit" class="form-label">Data e shtimit ne sistem</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="data_e_krijimit" id="data_e_krijimit" placeholder="Shëno daten e shtimit ne sistem" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email_used" class="form-label">Emaili qe ka derguar strike</label>
                                    <input type="email" class="form-control rounded-5 border border-2" name="email_used" id="email_used" placeholder="Shëno emailin qe ka derguar strike" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                                <button type="submit" class="input-custom-css px-3 py-2">Shto</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card p-3 rounded-5">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="platformTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-dark">ID</th>
                                <th class="text-dark">Platforma</th>
                                <th class="text-dark">Titulli</th>
                                <th class="text-dark">Pershkrimi</th>
                                <th class="text-dark">Data e shtimit</th>
                                <th class="text-dark">Email Used for sending strike</th>
                                <th class="text-dark">Vepro</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    // Initialize Datatable for platformTable
    $(document).ready(function() {
        // Get the actual date
        var date = new Date();
        // Formath the date
        // 05/24/2022
        var formattedDate = date.toLocaleDateString('en-US', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        })
        // Give space to the date
        // 05 24 2022
        formattedDate = formattedDate.split('/').join(' ');
        var platformTable = $('#platformTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "process_platform_table.php",
            columnDefs: [{
                "targets": [0, 1, 2, 3, 4],
                "render": function(data, type, row) {
                    return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
                },
                "orderable": false
            }],
            "order": [
                [0, "desc"]
            ],
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
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
            buttons: [{
                    extend: "pdf",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    filename: "lista_e_strikeve_" + formattedDate + "", // Set custom filename for PDF
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
                    filename: "lista_e_strikeve_" + formattedDate + "", // Set custom filename for Excel
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    filename: "lista_e_strikeve_" + formattedDate + "",
                },
            ],
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "platforma"
                },
                {
                    "data": "titulli"
                },
                {
                    "data": "pershkrimi"
                },
                {
                    "data": "data_e_krijimit"
                },
                {
                    "data": "email_used"
                },
                {
                    // Add a button
                    "data": null,
                    "render": function(data, type, row) {
                        // Add a button to delete on this page and edit on the edit page
                        return '<a href="edit_strike_platform.php?id=' + data.id + '" class="input-custom-css px-3 py-2" style="text-decoration: none;"><i class="fi fi-rr-edit fa-lg"></i></a> ' +
                            '<a href="#" class="input-custom-css px-3 py-2 delete-btn" data-id="' + data.id + '" style="text-decoration: none;"><i class="fi fi-rr-trash fa-lg"></i></a>';
                    }
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
        });
        $('#platformTable').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            Swal.fire({
                    title: 'A jeni i sigurt?',
                    text: "Ju nuk do të mund ta riktheni këtë!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Po, fshije!',
                    cancelButtonText: 'Anulo',
                    reverseButtons: true,
                    focusCancel: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                                url: 'process_delete_platform.php',
                                type: 'POST', // Use POST for data modifications
                                data: {
                                    id: id
                                },
                                dataType: 'json' // Expect a JSON response
                            })
                            .then(response => {
                                if (!response.success) { // Check for a 'success' property
                                    throw new Error(response.message || 'Fshirja dështoi');
                                }
                                return response;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Gabim: ${error.message}`);
                                throw error; // Re-throw the error to stop the confirmation
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                                title: 'Fshirja u realizua!',
                                text: 'Rreshti është fshirë me sukses.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                timerProgressBar: true
                            })
                            .then(() => {
                                platformTable.ajax.reload(null, false);
                            });
                    }
                })
                .catch((error) => {
                    // Handle errors that might occur during the entire Swal process
                    console.error("Error deleting platform:", error);
                    Swal.fire(
                        'Gabim!',
                        'Ka ndodhur një gabim gjatë fshirjes së platformës.',
                        'error'
                    );
                });
        });
    });
    fetch('platforms_names.json')
        .then(response => response.json())
        .then(data => {
            // Get the select element
            const selectElement = document.getElementById('platforma');
            // Iterate over the platforms and create options
            data.platforms.forEach(platform => {
                const option = document.createElement('option');
                option.value = platform;
                option.textContent = platform;
                selectElement.appendChild(option);
            });
            // Initialize Selectr for the platform selection
            try {
                new Selectr('#platforma', {
                    searchable: true,
                    customOption: true, // Enable custom options
                    placeholder: 'Zgjidh ose shkruaj një opsion të ri'
                });
            } catch (error) {
                console.error('Gabim gjatë inicializimit të Selectr:', error);
                Swal.fire(
                    'Gabim!',
                    'Pati një problem gjatë inicializimit të zgjedhësit të platformës.',
                    'error'
                );
            }
        })
        .catch(error => {
            console.error('Gabim gjatë ngarkimit të platforms_names.json:', error);
            Swal.fire(
                'Gabim!',
                'Pati një problem gjatë ngarkimit të zgjedhësit të platformës.',
                'error'
            );
        });
    // Initialize flatpickr for the creation date
    try {
        flatpickr('#data_e_krijimit', {
            enableTime: true,
            defaultDate: new Date(),
            dateFormat: "Y-m-d H:i",
            maxDate: new Date().fp_incr(0),
        });
    } catch (error) {
        console.error('Gabim gjatë inicializimit të flatpickr për datën e krijimit:', error);
        Swal.fire(
            'Gabim!',
            'Pati një problem gjatë inicializimit të zgjedhësit të datës së krijimit.',
            'error'
        );
    }
    // Initialize flatpickr for the expiration date
    try {} catch (error) {
        console.error('Gabim gjatë inicializimit të flatpickr për datën e skadimit:', error);
        Swal.fire(
            'Gabim!',
            'Pati një problem gjatë inicializimit të zgjedhësit të datës së skadimit.',
            'error'
        );
    }
    // Add event listener for form submission
    document.getElementById('platformForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('process_new_platform_strike.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Rrjeti përgjigjet me statusin ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.sukses) {
                    Swal.fire(
                        'Sukses!',
                        'Të dhënat tuaja janë ruajtur.',
                        'success'
                    );
                    // Clear the form
                    document.getElementById('platformForm').reset();
                    // Reload the table with actual page in pagination $('#platformTable').DataTable
                    const table = $('#platformTable').DataTable();
                    table.ajax.reload();
                } else {
                    Swal.fire(
                        'Gabim!',
                        data.mesazhi || 'Pati një problem në ruajtjen e të dhënave tuaja.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error('Gabim:', error);
                Swal.fire(
                    'Gabim!',
                    'Pati një problem në ruajtjen e të dhënave tuaja.',
                    'error'
                );
            });
    });
</script>