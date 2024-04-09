<?php
include 'partials/header.php';
// Retrieve data from the "ascap" table
$sql = "SELECT * FROM ascap";
$result = $conn->query($sql);
require_once 'vendor/autoload.php';
?>
<!-- Modal-i për Redaktim -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edito regjistrimin <span id="idOfRow" name="idOfRow"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    <input type="hidden" id="editId" name="id">
                    <div class="mb-3">
                        <label for="editEmri" class="form-label">Emri</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="editEmri" name="emri">
                    </div>
                    <div class="mb-3">
                        <label for="editMbiemri" class="form-label">Mbiemri</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="editMbiemri" name="mbiemri">
                    </div>
                    <div class="mb-3">
                        <label for="editEmriIKenges" class="form-label">Emri i Këngës</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="editEmriIKenges" name="emri_i_kenges">
                    </div>
                    <div class="mb-3">
                        <label for="editShenim" class="form-label">Shënimi</label>
                        <textarea class="form-control rounded-5 border border-2" id="editShenim" name="shenim"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                <button type="button" class="input-custom-css px-3 py-2" id="updateBtn">Përditëso</button>
            </div>
        </div>
    </div>
</div>

<!-- Your JavaScript code -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="investime.php" class="text-reset" style="text-decoration: none;">
                            Investime
                        </a>
                    </li>
                </ol>
            </nav>
            <div class="row mb-2">
                <div>
                    <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" id="pills-tab" role="tablist" style="width: fit-content;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 active" style="text-transform: none;" id="pills-shto_investim-tab" data-bs-toggle="pill" data-bs-target="#pills-shto_investim" type="button" role="tab" aria-controls="pills-shto_investim" aria-selected="true">Shto investim</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link  rounded-5" style="text-transform: none;" id="pills-lista_e_investimeve-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_e_investimeve" type="button" role="tab" aria-controls="pills-lista_e_investimeve" aria-selected="false">Lista e investimeve</button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="p-5 rounded-5 shadow-sm mb-4 card">

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-shto_investim" role="tabpanel" aria-labelledby="pills-shto_investim-tab">
                        <form id="myForm" action="insert_investim.php" method="post">
                            <div class="row my-3">
                                <div class="col">
                                    <label class="form-label" for="emri">Emri</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="emri" id="emri">
                                </div>
                                <div class="col">
                                    <label class="form-label" for="mbiemri">Mbiemri</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="mbiemri" id="mbiemri">
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col">
                                    <label class="form-label" for="emri_i_kenges">Emri i kenges</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="emri_i_kenges" id="emri_i_kenges">
                                </div>
                                <div class="col">
                                    <label class="form-label" for="shenim">Shenim</label>
                                    <textarea class="form-control rounded-5 border border-2" name="shenim" id="tinymce-editor"></textarea>
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
                                        <th>Veprim</th> <!-- Add a new column for the delete button -->
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
                [10, 25, 50, "Te gjitha"]
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
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-sm btn-light border rounded-5 me-2',
                filename: 'lista_e_investimeve'
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'btn btn-sm btn-light border rounded-5 me-2',
                filename: 'lista_e_investimeve'
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin Excel',
                className: 'btn btn-sm btn-light border rounded-5 me-2',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied',
                        page: 'all'
                    }
                },
                filename: 'lista_e_investimeve'
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabel&euml;n',
                className: 'btn btn-sm btn-light border rounded-5 me-2',
                filename: 'lista_e_investimeve'
            }, ],
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
            order: [
                [0, "desc"]
            ],
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
                        // Check if the shenim data is not empty
                        if (data.trim() !== '') {
                            // Create the View button with data attributes to hold the shenim content
                            var viewBtn = '<button class="input-custom-css px-3 py-2 view-btn" data-shenim="' + data + '"><i class="fi fi-rr-eye"></i></button>';
                            return viewBtn;
                        } else {
                            // Return empty string if shenim is empty
                            return '';
                        }
                    }
                },

                {
                    // Use the 'render' function to create the delete button
                    // 'data' parameter contains the full row data
                    data: null,
                    render: function(data, type, row) {
                        // Return the buttons HTML
                        var editBtn = '<button class="input-custom-css px-3 py-2 edit-btn" data-id="' + data.id + '"><i class="fi fi-rr-edit"></i></button>';
                        var deleteBtn = '<button class="input-custom-css px-3 py-2 delete-btn" data-id="' + data.id + '"><i class="fi fi-rr-trash"></i></button>';
                        return editBtn + ' ' + deleteBtn;
                    }
                }
            ],
        }); // Handle the delete button click using AJAX
        $('#example').on('click', '.delete-btn', function() {
            var rowId = $(this).data('id');
            // Show a confirmation dialog before proceeding with the deletion
            Swal.fire({
                title: 'A je i sigurt?',
                text: 'Ju nuk do të jeni në gjendje ta ktheni këtë!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Po, fshije!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with the deletion
                    $.ajax({
                        url: 'delete_investimi.php',
                        type: 'POST',
                        data: {
                            id: rowId
                        },
                        success: function(data) {
                            if (data.status === 'success') {
                                // Refresh the DataTable after successful delete
                                dataTables.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gabim!',
                                    text: data.message,
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gabim!',
                                text: 'Ndodhi një gabim gjatë fshirjes së regjistrimit.',
                            });
                        },
                    });
                }
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
                    $('#idOfRow').text(data.id);
                    $('#editEmri').val(data.emri);
                    $('#editMbiemri').val(data.mbiemri);
                    $('#editEmriIKenges').val(data.emri_i_kenges);
                    $('#editShenim').val(data.shenim);
                    // tinymce.get('editShenim').setContent(data.shenim);
                    // Show the modal
                    $('#editModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim!',
                        text: 'Ndodhi një gabim gjatë marrjes së të dhënave për rreshtin.',
                    });
                },
            });
        });

        $('#updateBtn').click(function() {
            // Create a new FormData object and append the form data to it
            var formData = new FormData($('#updateForm')[0]);
            // Get the content of the TinyMCE editor as plain text
            var shenimContent = $('#editShenim').val();
            // formData.append('shenim', shenimContent); // Append the shenim content to the formData
            formData.append('shenim', shenimContent);

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
                            title: 'Sukses!',
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
                            title: 'Gabim!',
                            text: data.message,
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim!',
                        text: 'Ndodhi një gabim gjatë përditësimit të regjistrimit.',
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
            event.preventDefault(); // Parandalon formën të dërgojë normalisht

            const formData = new FormData(event.target);

            $.ajax({
                url: "insert_investim.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status === "success") {
                        // Trego njoftimin e suksesit me SweetAlert 2
                        Swal.fire({
                            icon: "success",
                            title: "Sukses!",
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            // Rifresko DataTable për të reflektuar ndryshimet pas shtimit të suksesshëm
                            $('#example').DataTable().ajax.reload(null, false);
                            // Pastro fushat e formës
                            $('#myForm')[0].reset();
                        });
                    } else {
                        // Trego njoftimin e gabimit me SweetAlert 2
                        Swal.fire({
                            icon: "error",
                            title: "Gabim!",
                            text: data.message,
                        });
                    }
                },
                error: function() {
                    // Trego njoftimin e gabimit për dështimin e kërkesës AJAX
                    Swal.fire({
                        icon: "error",
                        title: "Gabim!",
                        text: "Ndodhi një gabim gjatë dërgimit të formës.",
                    });
                },
            });
        });

    });
</script>