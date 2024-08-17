<?php include 'partials/header.php' ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="text-reset" style="text-decoration: none;">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Ofertat (Këngë)</a>
                    </li>
                </ol>
            </nav>
            <form id="myForm" method="POST" action="insertoOfert.php">
                <div class="p-5 rounded-5 bordered mb-4 card">
                    <div class="row">
                        <div class="col">
                            <label for="emri_ofertes" class="form-label">Emri i ofertes</label>
                            <input type="text" name="emri_ofertes" id="emri_ofertes" class="form-control border border-2 rounded-5">
                        </div>
                        <div class="col">
                            <label for="emri_klientit" class="form-label">Emri klientit</label>
                            <input type="text" name="emri_klientit" id="emri_klientit" class="form-control border border-2 rounded-5">
                        </div>
                        <div class="col">
                            <label for="koh&euml;zgjatja" class="form-label">Koh&euml;zgjatja</label>
                            <select class="form-select shadow-sm rounded-5 mt-1" name="koh&euml;zgjatja" id="kohezgjatjaReg">
                                <option value="3_mujore">3 Mujore</option>
                                <option value="6_mujore">6 Mujore</option>
                                <option value="12_mujore">12 Mujore</option>
                                <option value="custom">Personalizuar</option>
                            </select>
                            <script>
                                new Selectr('#kohezgjatjaReg', {
                                    searchable: true,
                                    width: 300
                                });
                                document.getElementById('kohezgjatjaReg').addEventListener('change', function() {
                                    var selectedOption = this.value;
                                    if (selectedOption === 'custom') {
                                        Swal.fire({
                                            title: 'Shëno koh&euml;zgjatjen e personalizuar:',
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
                                                var customBankName = result.value;
                                                // Add the custom bank name as an option
                                                var selectElement = document.getElementById('kohezgjatjaReg');
                                                var customOption = document.createElement('option');
                                                customOption.value = customBankName;
                                                customOption.textContent = customBankName;
                                                selectElement.appendChild(customOption);
                                                // Select the newly added custom bank name
                                                selectElement.value = customBankName;
                                            }
                                        });
                                    }
                                });
                            </script>
                        </div>
                        <div class="col">
                            <label for="data" class="form-label">Data</label>
                            <input type="text" name="dataAktuale" id="dataAktuale" class="form-control border border-2 rounded-5" readonly value="<?php echo date("d-m-Y"); ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="koh&euml;zgjatja" class="form-label">P&euml;rshkrimi i ofert&euml;s</label>
                            <textarea class="form-control rounded-5 border border-2" id="pershkrimi_ofertes" name="pershkrimi_ofertes" rows="9"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="input-custom-css px-3 py-2" style="text-transform:none;" name="submitButton" id="submitButton">
                            <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                            <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                        </button>
                    </div>
                </div>
            </form>
            <div class="p-5 rounded-5 bordered mb-4 card text-dark">
                <table id="example" class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-dark">
                                Emri i ofertes
                            </th>
                            <th class="text-dark">
                                Emri klientit
                            </th>
                            <th class="text-dark">
                                Koh&euml;zgjatja
                            </th>
                            <th class="text-dark">
                                P&euml;rshkrimi i ofert&euml;s
                            </th>
                            <th class="text-dark">
                                Data
                            </th>
                            <th class="text-dark">
                                Vepro
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->query("SELECT * FROM ofertat ORDER BY id desc");
                        while ($row = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td>
                                    <?php echo $row['emri_ofertes']; ?>
                                </td>
                                <td>
                                    <?php echo $row['klienti']; ?>
                                </td>
                                <td>
                                    <?php
                                    switch ($row['kohezgjatja']) {
                                        case '3_mujore':
                                            echo '3 Mujore';
                                            break;
                                        case '6_mujore':
                                            echo '6 Mujore';
                                            break;
                                        case '12_mujore':
                                            echo '12 Mujore';
                                            break;
                                        default:
                                            echo $row['kohezgjatja'];
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo $row['pershkrimi_ofertes']
                                    ?>
                                </td>
                                <td>
                                    <?php echo $row['data'] ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary rounded-5 text-white" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel">Ndrysho t&euml; dh&euml;nat e ofert&euml;s "<?php echo $row['emri_ofertes']; ?>"</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="editOferta.php">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <div class="form-group">
                                                            <label for="emri_ofertes">Emri i ofertes</label>
                                                            <input type="text" class="form-control border border-2 rounded-5" id="emri_ofertes" name="emri_ofertes" value="<?php echo $row['emri_ofertes']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="klienti">Klienti</label>
                                                            <input type="text" class="form-control border border-2 rounded-5" id="klienti" name="klienti" value="<?php echo $row['klienti']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <label for="kohezgjatja">Kohezgjatja</label>
                                                                <select name="kohezgjatja2" id="kohezgjatja2" class="form-select rounded-5 mt-1 p-3">
                                                                    <?php
                                                                    // Define the options for the dropdown menu
                                                                    $kohezgjatja_options = array(
                                                                        '3_mujore' => '3 Mujore',
                                                                        '6_mujore' => '6 Mujore',
                                                                        '12_mujore' => '12 Mujore'
                                                                    );
                                                                    // Loop through the options and add them to the dropdown menu
                                                                    foreach ($kohezgjatja_options as $value => $label) {
                                                                        // Check if the current option matches the value from the database
                                                                        $selected = ($value == $row['kohezgjatja']) ? 'selected' : '';
                                                                        echo '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="input-custom-css px-3 py-2" style="text-transform:none;">
                                                            <span style="display:inline-block;vertical-align:middle;">Modifiko te dhenat</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="ofertaFile.php?id=<?php echo $row['id']; ?>" class="btn btn-primary rounded-5 text-white"><i class="fi fi-rr-eye"></i></a>
                                    <a href="#" class="btn btn-danger rounded-5 text-white" onclick="return konfirmoFshirjen(<?php echo $row['id']; ?>)"><i class="fi fi-rr-trash"></i></a>

                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th class="text-dark">
                                Emri i ofertes
                            </th>
                            <th class="text-dark">
                                Emri klientit
                            </th>
                            <th class="text-dark">
                                Koh&euml;zgjatja
                            </th>
                            <th class="text-dark">
                                P&euml;rshkrimi i ofert&euml;s
                            </th>
                            <th class="text-dark">
                                Data
                            </th>
                            <th class="text-dark">
                                Vepro
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php' ?>
<script>
    $('#example').DataTable({
        responsive: false,
        search: {
            return: true,
        },
        order: false,
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row'<'col-md-6'><'col-md-6'p>>",
        buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            },
            {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabelen',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }
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
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],
    })
</script>
<script>
    document.querySelector('#myForm').addEventListener('submit', function(event) {
        // Prevent the form from submitting by default
        event.preventDefault();
        // Retrieve form inputs
        var emri_ofertes = document.getElementById('emri_ofertes').value.trim();
        var emri_klientit = document.getElementById('emri_klientit').value.trim();
        var pershkrimi_ofertes = document.getElementById('pershkrimi_ofertes').value.trim();
        // Validation checks
        if (emri_ofertes === '' || emri_klientit === '' || pershkrimi_ofertes === '') {
            // Show error message if any required field is empty
            Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: 'Ju lutem plotësoni të gjitha fushat e detyrueshme!'
            });
            return; // Exit the function early
        }
        // If all validation passes, submit the form
        this.submit(); // Submit the actual form
    });
</script>
<script>
    function konfirmoFshirjen(id) {
        // Shfaq dialogun e konfirmimit të Sweet Alert 2
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
                // Nëse është konfirmuar, vazhdo me fshirjen
                window.location.href = "deleteOferta.php?id=" + id;
            }
        });
        // Parandalon veprimin parazgjedhës të lidhjes
        return false;
    }
</script>