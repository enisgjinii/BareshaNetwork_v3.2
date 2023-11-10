<?php include 'partials/header.php' ?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 shadow-sm mb-4 card">
                <h4 class="font-weight-bold text-gray-800 mb-4">Ofertat</h4>
                <!-- Breadcrumb -->
                <nav class="d-flex">
                    <h6 class="mb-0">
                        <a href="" class="text-reset">Kontrata</a>
                        <span>/</span>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-reset" data-bs-placement="top"
                            data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Ofertat</u></a>
                        <br>
                    </h6>
                </nav>
            </div>
            <form method="POST" action="insertoOfert.php">
                <div class="p-5 rounded-5 shadow-sm mb-4 card">
                    <div class="row">
                        <div class="col">
                            <label for="emri_ofertes">Emri i ofertes</label>
                            <input type="text" name="emri_ofertes" id="emri_ofertes"
                                class="form-control shadow-sm rounded-5">
                        </div>
                        <div class="col">
                            <label for="emri_klientit">Emri klientit</label>
                            <input type="text" name="emri_klientit" id="emri_klientit"
                                class="form-control shadow-sm rounded-5">
                        </div>
                        <div class="col">
                            <label for="koh&euml;zgjatja">Koh&euml;zgjatja</label>
                            <select class="form-select shadow-sm rounded-5 mt-1" name="koh&euml;zgjatja">
                                <option value="3_mujore">3 Mujore</option>
                                <option value="6_mujore">6 Mujore</option>
                                <option value="12_mujore">12 Mujore</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="data">Data</label>
                            <input type="text" name="dataAktuale" id="dataAktuale"
                                class="form-control shadow-sm rounded-5" readonly value="<?php echo date("d-m-Y"); ?>">
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <label for="koh&euml;zgjatja">P&euml;rshkrimi i ofert&euml;s</label>
                            <textarea class="form-control rounded-5 shadow-sm" id="pershkrimi_ofertes"
                                name="pershkrimi_ofertes" rows="9"></textarea>
                        </div>
                    </div>
                    <div class="mt-4">

                        <button type="submit" class="btn btn-light rounded-5 border" style="text-transform:none;"
                            name="submit">
                            <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                            <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                        </button>
                    </div>
                </div>

            </form>

            <div class="p-5 rounded-5 shadow-sm mb-4 card">
                <table id="example" class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>
                                Emri i ofertes
                            </th>
                            <th>
                                Emri klientit
                            </th>
                            <th>
                                Koh&euml;zgjatja
                            </th>
                            <th>
                                P&euml;rshkrimi i ofert&euml;s
                            </th>
                            <th>
                                Data
                            </th>
                            <th>
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
                                    <button type="button" class="btn btn-primary rounded-5 text-white"
                                        data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1"
                                        role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel">P&euml;rshkruaj</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>

                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="editOferta.php">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <div class="form-group">
                                                            <label for="emri_ofertes">Emri i ofertes</label>
                                                            <input type="text" class="form-control shadow-sm rounded-5"
                                                                id="emri_ofertes" name="emri_ofertes"
                                                                value="<?php echo $row['emri_ofertes']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="klienti">Klienti</label>
                                                            <input type="text" class="form-control shadow-sm rounded-5"
                                                                id="klienti" name="klienti"
                                                                value="<?php echo $row['klienti']; ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <label for="kohezgjatja">Kohezgjatja</label>
                                                                <select name="kohezgjatja" id="kohezgjatja"
                                                                    class="form-select rounded-5 mt-1 p-3">
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
                                                        <button type="submit"
                                                            class="btn btn-light rounded-5 float-right border p-2 px-3"
                                                            style="text-transform:none;">
                                                            <i class="fi fi-rr-paper-plane"
                                                                style="display:inline-block;vertical-align:middle;"></i>
                                                            <span
                                                                style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="ofertaFile.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-primary rounded-5 text-white"><i class="fi fi-rr-eye"></i></a>

                                    <a href="deleteOferta.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-danger rounded-5 text-white"
                                        onclick="return confirm('Are you sure you want to delete this row?')"><i
                                            class="fi fi-rr-trash"></i></a>
                                </td>
                            </tr>


                        <?php } ?>
                    </tbody>

                    <tfoot class="bg-light">
                        <tr>
                            <th>
                                Emri i ofertes
                            </th>
                            <th>
                                Emri klientit
                            </th>
                            <th>
                                Koh&euml;zgjatja
                            </th>
                            <th>
                                P&euml;rshkrimi i ofert&euml;s
                            </th>
                            <th>
                                Data
                            </th>
                            <th>
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
        order:false,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'pdfHtml5',
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            titleAttr: 'Eksporto tabelen ne formatin PDF',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'copyHtml5',
            text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            titleAttr: 'Kopjo tabelen ne formatin Clipboard',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'excelHtml5',
            text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
            titleAttr: 'Eksporto tabelen ne formatin CSV',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: 'Printo tabel&euml;n',
            className: 'btn btn-light border shadow-2 me-2'
        }],
        initComplete: function () {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');

        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color']
    })
</script>