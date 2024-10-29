<?php include 'partials/header.php'; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5 mb-4" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#" class="text-reset text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="Kthehu te kontratat">Kontratat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset text-decoration-none">Ofertat (Këngë)</a>
                    </li>
                </ol>
            </nav>

            <!-- Offer Insertion Form -->
            <form id="myForm" method="POST" action="insertoOfert.php" novalidate>
                <div class="card p-5 rounded-5 mb-4 bordered">
                    <div class="row g-3">
                        <!-- Offer Name -->
                        <div class="col-md-6">
                            <label for="emri_ofertes" class="form-label">Emri i Ofertes</label>
                            <input type="text" name="emri_ofertes" id="emri_ofertes" class="form-control border-2 rounded-5" placeholder="Shkruani emrin e ofertës" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni emrin unik të ofertës">
                        </div>

                        <!-- Client Name -->
                        <div class="col-md-6">
                            <label for="emri_klientit" class="form-label">Emri i Klientit</label>
                            <input type="text" name="emri_klientit" id="emri_klientit" class="form-control border-2 rounded-5" placeholder="Shkruani emrin e klientit" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni emrin e klientit për ofertën">
                        </div>

                        <!-- Duration -->
                        <div class="col-md-6">
                            <label for="kohezgjatjaReg" class="form-label">Kohëzgjatja</label>
                            <select class="form-select shadow-sm rounded-5" name="kohezgjatja" id="kohezgjatjaReg" data-bs-toggle="tooltip" data-bs-placement="top" title="Zgjidhni kohëzgjatjen e ofertës">
                                <option value="3_mujore">3 Mujore</option>
                                <option value="6_mujore">6 Mujore</option>
                                <option value="12_mujore">12 Mujore</option>
                                <option value="custom">Personalizuar</option>
                            </select>
                        </div>

                        <!-- Current Date -->
                        <div class="col-md-6">
                            <label for="dataAktuale" class="form-label">Data</label>
                            <input type="text" name="dataAktuale" id="dataAktuale" class="form-control border-2 rounded-5" readonly value="<?php echo date("d-m-Y"); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Data aktuale e krijimit të ofertës">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <label for="pershkrimi_ofertes" class="form-label">Përshkrimi i Ofertës</label>
                            <textarea class="form-control rounded-5 border-2" id="pershkrimi_ofertes" name="pershkrimi_ofertes" rows="5" placeholder="Shkruani përshkrimin e ofertës" data-bs-toggle="tooltip" data-bs-placement="top" title="Vendosni një përshkrim të detajuar për ofertën"></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2" name="submitButton" id="submitButton" data-bs-toggle="tooltip" data-bs-placement="top" title="Dërgo ofertën">
                            <i class="fi fi-rr-paper-plane me-2"></i>Dërgo
                        </button>
                    </div>
                </div>
            </form>

            <!-- Offers Table -->
            <div class="card p-5 rounded-5 bordered mb-4 text-dark">
                <table id="example" class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Emri i Ofertes</th>
                            <th>Emri Klientit</th>
                            <th>Kohëzgjatja</th>
                            <th>Përshkrimi i Ofertës</th>
                            <th>Data</th>
                            <th>Vepro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch offers from the database
                        $query = $conn->query("SELECT * FROM ofertat ORDER BY id DESC");
                        while ($row = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['emri_ofertes']); ?></td>
                                <td><?php echo htmlspecialchars($row['klienti']); ?></td>
                                <td>
                                    <?php
                                    // Display duration in a user-friendly format
                                    $durations = [
                                        '3_mujore' => '3 Mujore',
                                        '6_mujore' => '6 Mujore',
                                        '12_mujore' => '12 Mujore'
                                    ];
                                    echo isset($durations[$row['kohezgjatja']]) ? $durations[$row['kohezgjatja']] : htmlspecialchars($row['kohezgjatja']);
                                    ?>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($row['pershkrimi_ofertes'])); ?></td>
                                <td><?php echo htmlspecialchars($row['data']); ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-primary btn-sm rounded-5 me-2" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Ndrysho këtë ofertë">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>

                                    <!-- View Button -->
                                    <a href="ofertaFile.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm rounded-5 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Shiko detajet e ofertës">
                                        <i class="fi fi-rr-eye"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <a href="#" class="btn btn-danger btn-sm rounded-5" onclick="return konfirmoFshirjen(<?php echo $row['id']; ?>)" data-bs-toggle="tooltip" data-bs-placement="top" title="Fshij këtë ofertë">
                                        <i class="fi fi-rr-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Ndrysho të Dhënat e Ofertës "<?php echo htmlspecialchars($row['emri_ofertes']); ?>"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                                        </div>
                                        <!-- Modal Body -->
                                        <div class="modal-body">
                                            <form method="POST" action="editOferta.php" novalidate>
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                                <!-- Offer Name -->
                                                <div class="mb-3">
                                                    <label for="emri_ofertes_<?php echo $row['id']; ?>" class="form-label">Emri i Ofertes</label>
                                                    <input type="text" class="form-control border-2 rounded-5" id="emri_ofertes_<?php echo $row['id']; ?>" name="emri_ofertes" value="<?php echo htmlspecialchars($row['emri_ofertes']); ?>" required>
                                                </div>

                                                <!-- Client Name -->
                                                <div class="mb-3">
                                                    <label for="klienti_<?php echo $row['id']; ?>" class="form-label">Klienti</label>
                                                    <input type="text" class="form-control border-2 rounded-5" id="klienti_<?php echo $row['id']; ?>" name="klienti" value="<?php echo htmlspecialchars($row['klienti']); ?>" required>
                                                </div>

                                                <!-- Duration -->
                                                <div class="mb-3">
                                                    <label for="kohezgjatja2_<?php echo $row['id']; ?>" class="form-label">Kohëzgjatja</label>
                                                    <select name="kohezgjatja2" id="kohezgjatja2_<?php echo $row['id']; ?>" class="form-select rounded-5 p-2" required>
                                                        <?php
                                                        // Define duration options
                                                        $kohezgjatja_options = [
                                                            '3_mujore' => '3 Mujore',
                                                            '6_mujore' => '6 Mujore',
                                                            '12_mujore' => '12 Mujore'
                                                        ];
                                                        foreach ($kohezgjatja_options as $value => $label) {
                                                            $selected = ($value === $row['kohezgjatja']) ? 'selected' : '';
                                                            echo "<option value=\"$value\" $selected>$label</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-success px-4" data-bs-toggle="tooltip" data-bs-placement="top" title="Ruaj ndryshimet">
                                                        <i class="fi fi-rr-check me-2"></i>Ruaj
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } // End of while loop 
                        ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th>Emri i Ofertes</th>
                            <th>Emri Klientit</th>
                            <th>Kohëzgjatja</th>
                            <th>Përshkrimi i Ofertës</th>
                            <th>Data</th>
                            <th>Vepro</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<!-- Initialize Bootstrap Tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<!-- Initialize Selectr for Duration Select -->
<script>
    new Selectr('#kohezgjatjaReg', {
        searchable: true,
        width: '100%',
        clearable: false,
    });

    document.getElementById('kohezgjatjaReg').addEventListener('change', function() {
        var selectedOption = this.value;
        if (selectedOption === 'custom') {
            Swal.fire({
                title: 'Shëno kohëzgjatjen e personalizuar:',
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Shto',
                cancelButtonText: 'Anulo',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Ju duhet të shënoni diçka!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var customDuration = result.value.trim();
                    if (customDuration) {
                        var selectElement = document.getElementById('kohezgjatjaReg');
                        var customOption = document.createElement('option');
                        customOption.value = customDuration.toLowerCase().replace(/\s+/g, '_');
                        customOption.textContent = customDuration;
                        customOption.selected = true;
                        selectElement.appendChild(customOption);
                        // Refresh Selectr to recognize the new option
                        Selectr('#kohezgjatjaReg').refresh();
                    }
                }
            });
        }
    });
</script>

<!-- Initialize DataTables -->
<script>
    $('#example').DataTable({
        responsive: true,
        search: {
            return: true,
        },
        order: [],
        dom: "<'row'<'col-md-3'l><'col-md-6 text-center'B><'col-md-3'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row'<'col-md-6'i><'col-md-6'p>>",
        buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
                titleAttr: 'Eksporto tabelën në formatin PDF',
                className: 'btn btn-light btn-sm me-2 rounded-5'
            },
            {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
                titleAttr: 'Kopjo tabelën në Clipboard',
                className: 'btn btn-light btn-sm me-2 rounded-5'
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
                titleAttr: 'Eksporto tabelën në formatin Excel',
                className: 'btn btn-light btn-sm me-2 rounded-5'
            },
            {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
                titleAttr: 'Printo tabelën',
                className: 'btn btn-light btn-sm rounded-5'
            }
        ],
        initComplete: function() {
            var btns = $(".dt-buttons");
            btns.removeClass("dt-buttons btn-group").addClass("d-flex justify-content-center mb-3");
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
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],
    });
</script>

<!-- Form Validation and Submission -->
<script>
    document.querySelector('#myForm').addEventListener('submit', function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Retrieve and trim form inputs
        var emri_ofertes = document.getElementById('emri_ofertes').value.trim();
        var emri_klientit = document.getElementById('emri_klientit').value.trim();
        var pershkrimi_ofertes = document.getElementById('pershkrimi_ofertes').value.trim();

        // Validation checks
        if (emri_ofertes === '' || emri_klientit === '' || pershkrimi_ofertes === '') {
            Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: 'Ju lutem plotësoni të gjitha fushat e detyrueshme!'
            });
            return; // Exit if validation fails
        }

        // If validation passes, submit the form
        this.submit();
    });
</script>

<!-- Delete Confirmation -->
<script>
    function konfirmoFshirjen(id) {
        // Display confirmation dialog using SweetAlert2
        Swal.fire({
            icon: 'warning',
            title: 'Jeni i sigurt?',
            text: 'Dëshironi të fshini këtë rresht?',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Po, fshije!',
            cancelButtonText: 'Anulo'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to delete endpoint if confirmed
                window.location.href = "api/delete_methods/delete_oferta.php?id=" + id;
            }
        });
        // Prevent default action
        return false;
    }
</script>