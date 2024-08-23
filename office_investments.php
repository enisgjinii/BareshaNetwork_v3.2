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
                            Investimet e objektit
                        </a>
                    </li>
                </ol>
            </nav>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fi fi-rr-add"></i> &nbsp; Regjistro
            </button>
            <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-3">
                <i class="fi fi-rr-trash"></i> &nbsp; Fshij
            </button>
            <!-- Modal -->
            <div class="modal fade text-dark" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Shto investim</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-4"> <!-- Added margin-bottom for spacing -->
                                <form action="process_investment.php" method="post" enctype="multipart/form-data" id="investmentForm">
                                    <!-- Supplier Name -->
                                    <div class="mb-2">
                                        <label for="supplier_name" class="form-label">Emri i furnizuesit</label>
                                        <input type="text" class="form-control rounded-5 border border-2" name="supplier_name" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                                    </div>
                                    <!-- Invoice Number -->
                                    <div class="mb-2">
                                        <label for="invoice_number" class="form-label">Numri i faturës</label>
                                        <input type="text" class="form-control rounded-5 border border-2" name="invoice_number" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                                    </div>
                                    <!-- Invoice Amount -->
                                    <div class="mb-2">
                                        <label for="invoice_amount" class="form-label">Vlera e faturës</label>
                                        <input type="text" class="form-control rounded-5 border border-2" name="invoice_amount" required pattern="[0-9]*\.?[0-9]+" title="Ju lutem vendosni një vlerë numerike" oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë me një numër të vlefshëm')" oninput="this.setCustomValidity('')">
                                    </div>
                                    <!-- Invoice Date -->
                                    <div class="mb-2">
                                        <label for="invoice_date" class="form-label">Data e faturës</label>
                                        <input type="date" id="invoice_date" class="form-control rounded-5 border border-2" name="invoice_date" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                                        <script>
                                            $("#invoice_date").flatpickr({
                                                dateFormat: "Y-m-d",
                                                maxDate: "today"
                                            })
                                        </script>
                                    </div>
                                    <!-- Payment Status -->
                                    <div class="mb-2">
                                        <label for="payment_status" class="form-label">Statusi i pagesës</label>
                                        <select class="form-select" name="payment_status" id="payment_status" required oninvalid="this.setCustomValidity('Ju lutem plotësoni këtë fushë')" oninput="this.setCustomValidity('')">
                                            <option value="E p&euml;rfunduar">E përfunduar</option>
                                            <option value="E pa-kryer">E pa-kryer</option>
                                        </select>
                                        <script>
                                            new Selectr('#payment_status', {
                                                searchable: true,
                                                width: 300
                                            });
                                        </script>
                                    </div>
                                    <!-- Upload Invoice Scan -->
                                    <div class="mb-3">
                                        <label for="invoice_scan" class="form-label">Ngarkoni faturen (PDF, JPG, PNG)</label>
                                        <input type="file" class="form-control" name="invoice_scan" accept=".pdf, .jpg, .png" required oninvalid="this.setCustomValidity('Ju lutem zgjedhni një dokument')" oninput="this.setCustomValidity('')">
                                    </div>
                                    <!-- Submit Button -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm rounded-5" style="text-decoration: none;text-transform: none;" data-bs-dismiss="modal">Mbylle</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-5 text-white" style="text-decoration: none;text-transform: none;">Regjistro Investimin</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Table for Displaying Investments -->
            <div class="card p-3 d-none d-lg-block d-xl-block d-md-none rounded-5 text-dark">
                <table class='table table-bordered' id="investmentsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-dark" scope='col'></th>
                            <td class="text-dark" scope='col'>Id</td>
                            <th class="text-dark" scope='col'>Emri i furnitorit</th>
                            <th class="text-dark" scope='col'>Numri i faturës</th>
                            <th class="text-dark" scope='col'>Vlera e faturës</th>
                            <th class="text-dark" scope='col'>Data e faturës</th>
                            <th class="text-dark" scope='col'>Statusi i pagesës</th>
                            <th class="text-dark" scope='col'>Dokumenti</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="card p-3 d-md-block d-lg-none d-xl-none rounded-5 text-dark">
                <ul class="list-group list-group-flush">
                    <?php
                    include 'conn-d.php';
                    $sql = "SELECT * FROM investments ORDER BY id DESC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Emri i Furnizuesit:</strong> <?php echo $row['supplier_name']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Numri i Faturës:</strong> <?php echo $row['invoice_number']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Vlera e Faturës:</strong> <?php echo $row['invoice_amount']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Data e Faturës:</strong> <?php echo $row['invoice_date']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Statusi i Pagesës:</strong> <?php echo $row['payment_status']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Skaneri i Faturës:</strong> <?php echo $row['invoice_scan_path']; ?>
                                    </div>
                                    <!-- Add more fields as needed -->
                                </div>
                            </li>
                    <?php
                        }
                    } else {
                        echo "<li class='list-group-item'>Nuk ka të dhëna</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('investmentForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            // Serialize the form data
            var formData = new FormData(this);
            // Send an AJAX request to process_investment.php
            fetch('api/post_methods/post_investment.php', {
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
                            window.location.href = 'office_investments.php'; // Redirect to another page if needed
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
        var dataTable = $('#investmentsTable').DataTable({
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
                url: 'api/get_methods/get_investments.php', // Replace with your server-side script to fetch data
                type: 'POST',
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    defaultContent: '<input type="checkbox" class="deleteCheckbox">'
                }, {
                    data: "id",
                },
                {
                    data: 'supplier_name'
                },
                {
                    data: 'invoice_number'
                },
                {
                    data: 'invoice_amount'
                },
                {
                    data: 'invoice_date'
                },
                {
                    data: 'payment_status'
                },
                {
                    data: 'invoice_scan_path',
                    render: function(data) {
                        return '<a class="input-custom-css px-3 py-2" style="text-decoration: none;text-transform: none;" href="' + data + '" target="_blank">Shiko dokumentin</a>';
                    }
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/sq.json',
            }
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
            // Show a confirmation dialog with SweetAlert2
            Swal.fire({
                title: 'A jeni të sigurt që dëshironi të fshini këto rreshta?',
                text: 'Ky veprim nuk mund të kthehet mbrapa!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Po, fshij!',
                cancelButtonText: 'Anulo'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If the user confirms, perform the deletion using AJAX
                    $.ajax({
                        url: 'api/delete_methods/delete_investment.php',
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
                                    title: 'Gabim',
                                    text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                                });
                            }
                        },
                        error: function() {
                            // Show error message with SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Gabim',
                                text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?php include 'partials/footer.php'; ?>