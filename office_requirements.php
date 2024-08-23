<?php include 'partials/header.php' ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Objekti</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo $SERVER['PHP_SELF']; ?>" class="text-reset" style="text-decoration: none;">
                            Kerkesat
                        </a>
                    </li>
                </ol>
            </nav>
            <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fi fi-rr-add"></i> &nbsp; Shto kërkesë te re
            </button>
            <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-3">
                <i class="fi fi-rr-trash"></i> &nbsp; Fshij
            </button>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="process_requirement.php" method="post" enctype="multipart/form-data" id="requirementForm">
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
                                        <input type="text" class="form-control rounded-5 border border-2" name="description_of_the_requirement" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                                    </div>
                                    <!-- Data e parashikuar -->
                                    <div class="mb-2">
                                        <label for="expected_date" class="form-label">Data e parashikuar</label>
                                        <input type="date" class="form-control rounded-5 border border-2" name="expected_date" id="expected_date" required>
                                    </div>
                                    <script>
                                        $("#expected_date").flatpickr({
                                            dateFormat: "Y-m-d",
                                        })
                                    </script>
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
            <div class="card p-3 text-dark">
                <table class='table table-bordered' id="requirementsTable">
                    <thead class="table-light text-dark">
                        <tr>
                            <th class="text-dark" scope='col'></th>
                            <th class="text-dark" scope='col'>ID</th>
                            <th class="text-dark" scope='col'>Përshkrimi i kërkesës</th>
                            <th class="text-dark" scope='col'>Data e parashikuar</th>
                            <th class="text-dark" scope='col'>Dërgo stafit apo dikuna tjeter kerkesen</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('requirementForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            // Serialize the form data
            var formData = new FormData(this);
            // Send an AJAX request to process_requirement.php
            fetch('api/post_methods/post_requirement.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    return response.json(); // Parse the response as JSON
                })
                .then(function(data) {
                    // Check the response status
                    if (data.status === 'success') {
                        // Display success message using SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message
                        }).then(function() {
                            // Reload DataTable
                            var dataTable = $('#requirementsTable').DataTable();
                            dataTable.ajax.reload();
                        });
                    } else {
                        // Display error message using SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        });
    });
</script>
<script>
    $(document).ready(function() {
        var dataTable = $('#requirementsTable').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            initComplete: function() {
                // Add classes and style to DataTable elements
                $('.dt-buttons').addClass('button-container').removeClass('dt-buttons btn-group');
                $('div.dataTables_length select').addClass('form-select').css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem',
                });
            },
            buttons: [
                // Add your button configurations here
            ],
            stripeClasses: ['stripe-color'],
            ajax: {
                url: 'api/get_methods/get_requirements.php', // Replace with your server-side script to fetch data
                type: 'POST',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    defaultContent: '<input type="checkbox" class="deleteCheckbox">'
                },
                {
                    data: 'id'
                },
                {
                    data: 'description_of_the_requirement'
                },
                {
                    data: 'expected_date'
                },
                {
                    data: '',
                    render: function(data, type, row) {
                        // Render button and modal dynamically
                        return `
                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#sendRequirementModal${row.id}">
                        <i class="fi fi-rr-paper-plane"></i>
                    </button>
                    <div class="modal fade" id="sendRequirementModal${row.id}" tabindex="-1" aria-labelledby="sendRequirementModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="sendRequirementModalLabel">Send Requirement</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="requirementForm${row.id}">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="requirementDescription${row.id}" class="form-label">Përshkrimi:</label>
                                            <textarea class="form-control rounded-5 border border-2" id="requirementDescription${row.id}" name="requirementDescription" rows="3" readonly>${row.description_of_the_requirement}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="expectedDate${row.id}" class="form-label">Expected Date:</label>
                                            <input type="text" class="form-control rounded-5 border border-2" id="expectedDate${row.id}" name="expectedDate" value="${row.expected_date}" readonly>
                                        </div>
                                        <div class="mb-3">
                                        <label for="email${row.id}" class="form-label">Zgjedh stafin:</label>
                                        <select name="email" id="email${row.id}" class="form-select rounded-5 border border-2">
                                            <?php
                                            $sql = "SELECT email FROM googleauth";
                                            $result = mysqli_query($conn, $sql);
                                            while ($row_email = mysqli_fetch_assoc($result)) {
                                                echo '<option value="' . $row_email['email'] . '">' . $row_email['email'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        </div>
                                        <input type="hidden" name="requirementId" value="${row.id}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                                        <button type="submit" class="input-custom-css px-3 py-2">Dërgo</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `;
                    }
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/sq.json',
            }
        });
        $('#requirementsTable').on('submit', '[id^="requirementForm"]', function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            var rowId = $(this).find('input[name="requirementId"]').val(); // Get the row ID from the form
            $.ajax({
                type: 'POST',
                url: 'send_email_requirement.php', // Path to PHP script handling email sending
                data: formData,
                success: function(response) {
                    // Handle success response
                    Swal.fire({
                        icon: 'success',
                        title: 'Emaili u dërgua me sukses!',
                        text: 'Faleminderit që përdorët shërbimin tonë.',
                        showConfirmButton: false,
                        timer: 2000,
                        background: '#f4f4f4',
                        customClass: {
                            title: 'text-success',
                            popup: 'popup-class',
                            content: 'text-secondary',
                        },
                        allowOutsideClick: false
                    }).then(() => {
                        $('#sendRequirementModal' + rowId).modal('hide'); // Hide modal after successful submission
                    });
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim gjatë dërgimit të emailit',
                        text: 'Ju lutemi, provoni përsëri më vonë.',
                        showConfirmButton: false,
                        timer: 2000,
                        background: '#f4f4f4',
                        customClass: {
                            title: 'text-danger',
                            popup: 'popup-class',
                            content: 'text-secondary',
                        },
                        allowOutsideClick: false
                    });
                }
            });
        });
        $('#deleteRowsBtn').on('click', function() {
            // Get all checked checkboxes
            var checkboxes = $('.deleteCheckbox:checked');
            // Check if any checkboxes are checked
            if (checkboxes.length === 0) {
                // Show alert indicating no items are selected
                Swal.fire({
                    icon: 'warning',
                    title: 'Nuk është zgjedhur asgjë',
                    text: 'Ju lutemi zgjidhni rreshtat që dëshironi të fshini.',
                });
                return; // Exit the function, preventing further execution
            }
            // Get the IDs of the selected rows
            var ids = checkboxes.map(function() {
                return dataTable.row($(this).closest('tr')).data().id;
            }).get();
            // Perform deletion using AJAX
            $.ajax({
                url: 'api/delete_methods/delete_requirement.php',
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