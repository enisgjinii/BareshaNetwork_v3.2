<?php include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="authenticated_channels.php" class="text-reset" style="text-decoration: none;">Kanalet e autentifikuara</a></li>
                </ol>
            </nav>
            <div class="card p-3">
                <div class="table-responsive">
                    <table class="table table-hover w-full" id="authenticated_channels">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID-ja kanalit</th>
                                <th>Emri kanalit</th>
                                <th>Krijimi i kanalit ne databazë</th>
                                <th>Veprim</th> <!-- Add a new column for action -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM refresh_tokens ORDER BY id DESC";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>{$row['id']}</td><td>{$row['channel_id']}</td><td>{$row['channel_name']}</td><td>{$row['created_at']}</td><td><button style='text-transform:none;' class='btn btn-danger rounded-5 py-1 px-2 text-white btn-sm delete-btn' data-id='{$row['id']}'>
                                <i class='fi fi-rr-trash'></i></button></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#authenticated_channels').DataTable({
            responsive: false,
            searching: true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Të gjitha"]
            ],
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('').removeClass('dt-buttons btn-group');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select').css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem'
                });
            },
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: "Kopjo tabelen ne formatin Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                },
                {
                    extend: "excelHtml5",
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                    titleAttr: "Eksporto tabelen ne formatin Excel",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    exportOptions: {
                        modifier: {
                            search: "applied",
                            order: "applied",
                            page: "all"
                        }
                    }
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                },
            ],
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            stripeClasses: ['stripe-color'],
            columnDefs: [{
                "targets": [0, 1, 2, 3],
                "render": function(data, type, row) {
                    return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
                }
            }],
        });

        // Add event listener for delete button click
        $('#authenticated_channels').on('click', '.delete-btn', function() {
            var rowId = $(this).data('id');
            var confirmation = confirm("Are you sure you want to delete this entry?");
            if (confirmation) {
                $.ajax({
                    url: 'delete_auth_channel.php', // Change the URL to your PHP script for deleting
                    type: 'POST',
                    data: {
                        id: rowId
                    },
                    success: function(response) {
                        // Remove the row from the table
                        $('#authenticated_channels').DataTable().row($(this).closest('tr')).remove().draw(false);
                        console.log("Row deleted successfully");
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
</script>
<?php include 'partials/footer.php'; ?>