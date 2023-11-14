<?php
include 'partials/header.php';

// Retrieve data from the "ascap" table
$sql = "SELECT * FROM ascap";
$result = $conn->query($sql);
require_once 'vendor/autoload.php';

?>
<!-- Your HTML code -->

<!-- Add the Bootstrap modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add your update form here -->
                <form id="updateForm">
                    <input type="hidden" id="editId" name="id">
                    <div class="mb-3">
                        <label for="editEmri" class="form-label">Emri</label>
                        <input type="text" class="form-control" id="editEmri" name="emri">
                    </div>
                    <div class="mb-3">
                        <label for="editMbiemri" class="form-label">Mbiemri</label>
                        <input type="text" class="form-control" id="editMbiemri" name="mbiemri">
                    </div>
                    <div class="mb-3">
                        <label for="editEmriIKenges" class="form-label">Emri i kenges</label>
                        <input type="text" class="form-control" id="editEmriIKenges" name="emri_i_kenges">
                    </div>
                    <div class="mb-3">
                        <label for="editShenim" class="form-label">Shenim</label>
                        <textarea class="form-control" id="editShenim" name="shenim"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Your JavaScript code -->

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 shadow-sm mb-4 card">
                <h4 class="font-weight-bold text-gray-800 mb-4">Investime</h4>
                <nav class="d-flex">
                    <h6 class="mb-0">
                        <a href="investime.php" class="text-reset">Investime</a>
                    </h6>
                </nav>
            </div>
            <div class="p-5 rounded-5 shadow-sm mb-4 card">

                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 active" style="text-transform: none;" id="pills-shto_investim-tab" data-bs-toggle="pill" data-bs-target="#pills-shto_investim" type="button" role="tab" aria-controls="pills-shto_investim" aria-selected="true">Shto investim</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link  rounded-5" style="text-transform: none;" id="pills-lista_e_investimeve-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_investimeve" type="button" role="tab" aria-controls="pills-lista_e_investimeve" aria-selected="false">Lista e investimeve</button>
                    </li>

                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-shto_investim" role="tabpanel" aria-labelledby="pills-shto_investim-tab">
                        <form id="myForm" action="insert_investim.php" method="post">
                            <div class="row my-3">
                                <div class="col">
                                    <label class="form-label" for="emri">Emri</label>
                                    <input type="text" class="form-control shadow-sm rounded-5 border" name="emri" id="emri">
                                </div>
                                <div class="col">
                                    <label class="form-label" for="mbiemri">Mbiemri</label>
                                    <input type="text" class="form-control shadow-sm rounded-5 border" name="mbiemri" id="mbiemri">
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col">
                                    <label class="form-label" for="emri_i_kenges">Emri i kenges</label>
                                    <input type="text" class="form-control shadow-sm rounded-5 border" name="emri_i_kenges" id="emri_i_kenges">
                                </div>
                                <div class="col">
                                    <label class="form-label" for="shenim">Shenim</label>
                                    <textarea class="form-control shadow-sm rounded-5 border" name="shenim" id="tinymce-editor"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-5 shadow-sm text-white" style="text-transform: none;">Ruaj t&euml; dh&euml;nat</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="pills-lista_e_investimeve" role="tabpanel" aria-labelledby="pills-lista_e_investimeve-tab">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <!-- Add other columns here based on your database table -->
                                        <th>Emri</th>
                                        <th>Mbiemri</th>
                                        <th>Emri i kenges</th>
                                        <th>Shenim</th>
                                        <th>Action</th> <!-- Add a new column for the delete button -->

                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>













            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Shihe kolonen shenimi me te detajuar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-5 shadow-sm" data-bs-dismiss="modal">Mbylle</button>
            </div>
        </div>
    </div>
</div>


<?php include 'partials/footer.php' ?>



<script>
    $(document).ready(function() {

        // Initialize DataTable with AJAX
        var dataTables = $('#example').DataTable({
            responsive: false,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "T&euml; gjitha"]
            ],
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
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' +
                'Brtip',
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
                titleAttr: 'Eksporto tabelen ne formatin Excel',
                className: 'btn btn-light border shadow-2 me-2',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied',
                        page: 'all'
                    }
                }
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabel&euml;n',
                className: 'btn btn-light border shadow-2 me-2'
            }, ],
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
            ajax: {
                url: 'get_investimi_data.php', // Your PHP script to fetch data
                dataSrc: '', // Since your response is an array, set this to an empty string
            },
            columns: [{
                    data: 'id'
                }, // 'id' should be the name of the column in your database table
                {
                    data: 'emri'
                }, // 'emri' should be the name of the column in your database table
                {
                    data: 'mbiemri'
                }, // 'mbiemri' should be the name of the column in your database table
                {
                    data: 'emri_i_kenges'
                }, // 'emri_i_kenges' should be the name of the column in your database table
                {
                    data: 'shenim',
                    render: function(data, type, row) {
                        // Create the View button with data attributes to hold the shenim content
                        var viewBtn = '<button class="btn btn-primary btn-sm rounded-5 shadow-sm text-white view-btn" data-shenim="' + data + '"><i class="fi fi-rr-eye"></i></button>';
                        return viewBtn;
                    }
                },
                {
                    // Use the 'render' function to create the delete button
                    // 'data' parameter contains the full row data
                    data: null,
                    render: function(data, type, row) {
                        // Return the buttons HTML
                        var editBtn = '<button class="btn btn-primary btn-sm rounded-5 shadow-sm text-white edit-btn" data-id="' + data.id + '"><i class="fi fi-rr-pencil"></i></button>';
                        var deleteBtn = '<button class="btn btn-danger btn-sm rounded-5 shadow-sm text-white delete-btn" data-id="' + data.id + '"><i class="fi fi-rr-trash"></i></button>';
                        return editBtn + ' ' + deleteBtn;
                    }
                }
            ],
        }); // Handle the delete button click using AJAX


        $('#example').on('click', '.delete-btn', function() {
            var rowId = $(this).data('id');

            $.ajax({
                url: 'delete_investimi.php',
                type: 'POST',
                data: {
                    id: rowId
                }, // Send the ID of the row to delete
                success: function(data) {
                    if (data.status === 'success') {
                        // Refresh the DataTable after successful delete
                        dataTables.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Ndodhi nj&euml; gabim gjat&euml; fshirjes s&euml; t&euml; dh&euml;nave.',
                    });
                },
            });
        });

        $('#example').on('click', '.edit-btn', function() {
            var rowId = $(this).data('id');
            // Fetch data for the specific row using AJAX
            $.ajax({
                url: 'get_investimi.php',
                type: 'POST',
                data: {
                    id: rowId
                },
                dataType: 'json',
                success: function(data) {
                    // 'data' contains the fetched data for the row with the specified ID
                    // Populate the modal form with the fetched data
                    $('#editId').val(data.id);
                    $('#editEmri').val(data.emri);
                    $('#editMbiemri').val(data.mbiemri);
                    $('#editEmriIKenges').val(data.emri_i_kenges);
                    tinymce.get('editShenim').setContent(data.shenim);

                    // Show the modal
                    $('#editModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Ndodhi nj&euml; gabim gjat&euml; marrjes s&euml; t&euml; dh&euml;nave p&euml;r rreshtin.',
                    });
                },
            });
        });
        $('#updateBtn').click(function() {
            // Create a new FormData object and append the form data to it
            var formData = new FormData($('#updateForm')[0]);

            // Get the content of the TinyMCE editor as plain text
            var shenimContent = tinymce.get('editShenim').getContent({
                format: 'text'
            });
            formData.append('shenim', shenimContent); // Append the shenim content to the formData

            $.ajax({
                url: 'update_investimi.php',
                type: 'POST',
                data: formData, // Use the FormData object
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Set contentType to false to properly handle FormData
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            // Hide the modal after a successful update
                            $('#editModal').modal('hide');
                            // Refresh the DataTable to reflect the changes
                            $('#example').DataTable().ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating the record.',
                    });
                },
            });
        });


    });
</script><!-- Your HTML code -->

<script>
    $(document).ready(function() {

        // Initialize TinyMCE for the "Shenim" textarea in the modal
        tinymce.init({
            selector: '#editShenim',
            plugins: 'autolink lists link image charmap print preview anchor',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
            height: 300, // Adjust the height of the editor as needed
        });


        tinymce.init({
            selector: '#tinymce-editor',
            plugins: 'autolink lists link image charmap print preview anchor',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
            height: 300, // Adjust the height of the editor as needed
        });

        $('#example tbody').on('click', '.view-btn', function() {
            var shenimContent = $(this).data('shenim');

            // Set the shenim content in the modal
            $('#viewModalBody').html(shenimContent);

            // Show the modal
            $('#viewModal').modal('show');
        });

        // Handle form submission using AJAX
        $("#myForm").submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            const formData = new FormData(event.target);

            $.ajax({
                url: "insert_investim.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status === "success") {
                        // Show SweetAlert 2 success notification
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            // Redirect to investimi.php after successful insertion
                            window.location.href = "investime.php";
                        });
                    } else {
                        // Show SweetAlert 2 error notification
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: data.message,
                        });
                    }
                },
                error: function() {
                    // Show SweetAlert 2 error notification for AJAX request failure
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "An error occurred while submitting the form.",
                    });
                },
            });
        });
    });
</script>