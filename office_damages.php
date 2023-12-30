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
                                Prishjet
                            </a>
                        </li>
                </nav>
                <!-- Button trigger modal -->
                <button type="button" class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fi fi-rr-add"></i> &nbsp; Raporto prishje
                </button>
                <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-3">
                    <i class="fi fi-rr-trash"></i> &nbsp; Fshij
                </button>
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
                                    <hr>
                                    <div class="text-end">
                                        <button type="submit" class="input-custom-css px-3 py-2">Raporto</button>
                                    </div>

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
                                <th scope='col'>ID</th>
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
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('damageForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Serialize the form data
            var formData = new FormData(this);

            // Send an AJAX request to process_damage.php
            fetch('process_damage.php', {
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
                            window.location = 'office_damages.php'; // Redirect to another page if needed
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
                    data: 'id',
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