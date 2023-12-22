<?php include 'partials/header.php' ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Objekti</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php echo $SERVER['PHP_SELF']; ?>" class="text-reset" style="text-decoration: none;">
                                Kerkesat
                            </a>
                        </li>
                </nav>
                <!-- Button trigger modal -->
                <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fi fi-rr-add"></i> &nbsp; Shto kërkesë te re
                </button>
                <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-3">
                    <i class="fi fi-rr-trash"></i> &nbsp; Fshij
                </button>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="process_requirement.php" method="post" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Shto investim</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4"> <!-- Added margin-bottom for spacing -->

                                        <!-- Description of the requirement -->
                                        <div class="mb-2">
                                            <label for="description_of_the_requirement" class="form-label">Përshkrimi i kërkesës</label>
                                            <input type="text" class="form-control rounded-5 border border-2" name="description_of_the_requirement" required>
                                        </div>

                                        <!-- Data e parashikuar -->
                                        <div class="mb-2">
                                            <label for="expected_date" class="form-label">Data e parashikuar</label>
                                            <input type="date" class="form-control rounded-5 border border-2" name="expected_date" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-5" style="text-decoration: none;text-transform: none;" data-bs-dismiss="modal">Mbylle</button>
                                    <button type="submit" class="btn btn-primary btn-sm rounded-5 text-white" style="text-decoration: none;text-transform: none;">Regjistro Investimin</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>


                <!-- Table for Displaying Investments -->
                <div class="card p-3">
                    <table class='table table-bordered' id="requirementsTable">
                        <thead class="table-light">
                            <tr>
                                <th scope='col'></th>
                                <th scope='col'>Përshkrimi i kërkesës</th>
                                <th scope='col'>Data e parashikuar</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var dataTable = $('#requirementsTable').DataTable({
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
                url: 'fetch_requirements.php', // Replace with your server-side script to fetch data
                type: 'POST',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    defaultContent: '<input type="checkbox" class="deleteCheckbox">'
                },
                {
                    data: 'description_of_the_requirement'
                },
                {
                    data: 'expected_date'
                }
            ]
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
                url: 'delete_requirement.php',
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