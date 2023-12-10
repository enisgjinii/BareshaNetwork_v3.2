<?php include 'partials/header.php' ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container"> <!-- Added 'mt-4' for top margin -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="text-reset" style="text-decoration: none;">Objekti</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="office_damages.php" class="text-reset" style="text-decoration: none;">Prishjet</a></li>
                </ol>
            </nav>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fi fi-rr-add"></i> &nbsp; Raporto prishje
            </button>
            <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-2">Fshi rreshtat e përzgjedhur</button>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Raporto prishje</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form for reporting office damages -->
                            <form id="damageForm" action="process_damage.php" method="post">
                                <div class="mb-3">
                                    <label for="damageType" class="form-label">Lloji i prishjes</label>
                                    <input type="text" class="form-control rounded-5 border border-2" id="damageType" name="damageType" required>
                                </div>
                                <div class="mb-3">
                                    <label for="damageDescription" class="form-label">Përshkrimi i prishjes</label>
                                    <textarea class="form-control rounded-5 border border-2" id="damageDescription" name="damageDescription" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="damageDate" class="form-label">Data e prishjes</label>
                                    <input type="date" class="form-control rounded-5 border border-2" id="damageDate" name="damageDate" required>
                                </div>
                                <div class="mb-3">
                                    <label for="reporterName" class="form-label">Emri i raportuesit</label>
                                    <select name="reporterName" id="reporterName" class="form-select rounded-5">
                                        <?php
                                        $result = $conn->query("SELECT * FROM googleauth");
                                        while ($row = mysqli_fetch_array($result)) {
                                            $reporterFullName = $row['firstName'] . " " . $row['last_name'];
                                            echo '<option value="' . $row['id'] . '">' . $reporterFullName . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Raporto</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Table for Displaying Investments -->
            <div class="card p-3">
                <table class='table table-bordered' id="damagesTable">
                    <thead class="table-light">
                        <tr>
                            <th scope='col'></th>
                            <th scope='col'>Lloji</th>
                            <th scope='col'>Pershkrimi</th>
                            <th scope='col'>Data</th>
                            <th scope='col'>Raportuesi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var dataTable = $('#damagesTable').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select');
                lengthSelect.css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem',
                });
            },
            buttons: [
                // Your button configurations here
            ],
            stripeClasses: ['stripe-color'],
            ajax: {
                url: 'fetch_damages.php', // Replace with your server-side script to fetch data
                type: 'POST',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    defaultContent: '<input type="checkbox" class="deleteCheckbox">'
                },
                {
                    data: 'damage_type',
                    title: 'Lloji i prishjes'
                },
                {
                    data: 'damage_description',
                    title: 'Përshkrimi i prishjes'
                },
                {
                    data: 'damage_date',
                    title: 'Data e prishjes'
                },
                {
                    data: null,
                    title: 'Emri i raportuesit',
                    render: function(data, type, row) {
                        return row.firstName + ' ' + row.last_name;
                    }
                },
            ],




        });

        $('#deleteRowsBtn').on('click', function() {
            // Get all checked checkboxes
            var checkboxes = $('.deleteCheckbox:checked');

            // Get the IDs of the selected rows
            var ids = checkboxes.map(function() {
                return dataTable.row($(this).closest('tr')).data().id;
            }).get();

            // Perform deletion using AJAX
            $.ajax({
                url: 'delete_damage.php',
                method: 'POST',
                data: {
                    ids: ids
                },
                dataType: 'json',
                success: function(response) {
                    // Check if the deletion was successful
                    if (response.success) {
                        // Update DataTable
                        dataTable.ajax.reload();

                        // Show success message with SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Rreshtat janë fshirë',
                            text: 'Rreshtat e përzgjedhura janë fshirë me sukses.',
                        });
                    } else {
                        // Show error message with SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                        });
                    }
                },
                error: function() {
                    // Show error message with SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                    });
                }
            });
        });
    });
</script>

<?php include 'partials/footer.php'; ?>