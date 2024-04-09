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
                <div class="table-responsive d-none d-lg-block"> <!-- Hide on XS, SM, MD, show on LG, XL -->
                    <table class="table w-full" id="authenticated_channels">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>ID-ja kanalit</th>
                                <th>Emri i kanalit</th>
                                <th>Emri i regjistruar si klient</th>
                                <th>Data e krijimit</th>
                                <th>Veprim</th> <!-- Add a new column for action -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT rt.*, k.emri AS klient_emri 
        FROM refresh_tokens rt 
        LEFT JOIN klientet k ON rt.channel_id = k.youtube 
        ORDER BY rt.id DESC";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td>
                                        <?php echo $row['id']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['channel_id']; ?>
                                        <br><br>
                                        <a href="kanali.php?id=<?php echo $row['channel_id']; ?>" class="input-custom-css px-3 py-2 mt-3" style="text-transform:none;text-decoration:none"><i class="fi fi-rr-user"></i> Shiko kanalin</a>
                                    </td>
                                    <td><?php echo $row['channel_name']; ?></td>
                                    <td>
                                        <?php
                                        if (!empty($row['klient_emri'])) {
                                            echo $row['klient_emri'];
                                        } else {
                                            echo '<span class="badge bg-warning rounded-pill">Ky klient nuk posedon regjistrim te kanalit te Youtubes në listën e klientëve, të lutem rishikoje</span>';
                                        }
                                        ?>
                                    </td>

                                    <td><?php echo $row['created_at']; ?></td>
                                    <td>
                                        <button style="text-transform:none;" class="input-custom-css px-3 py-2 delete-btn" data-id="<?php echo $row['id']; ?>">
                                            <i class="fi fi-rr-trash"></i> Fshij
                                        </button>
                                        <button class="input-custom-css px-3 py-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_<?php echo $row['id']; ?>" aria-controls="offcanvas_<?php echo $row['id']; ?>">
                                            <i class="fi fi-rr-eye"></i> Trego
                                        </button>
                                        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_<?php echo $row['id']; ?>" aria-labelledby="offcanvas_<?php echo $row['id']; ?>_label">
                                            <div class="offcanvas-header">
                                                <h5 class="offcanvas-title" id="offcanvas_<?php echo $row['id']; ?>_label">
                                                    Të dhënat për <?php echo $row['channel_name']; ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                            </div>
                                            <div class="offcanvas-body">
                                                <ul class="list-group">
                                                    <?php
                                                    $get_client_infos = "SELECT * FROM klientet WHERE youtube = '" . $row['channel_id'] . "'";
                                                    $client_result = $conn->query($get_client_infos); // Use a different variable for the inner query result
                                                    while ($client_row = $client_result->fetch_assoc()) {
                                                    ?>
                                                        <li class="list-group-item">
                                                            <strong>ID:</strong> <?php echo $client_row['id']; ?><br>
                                                            <strong>Emri:</strong> <?php echo $client_row['emri']; ?><br>
                                                            <strong>Monetizuar:</strong> <?php echo $client_row['monetizuar']; ?><br>
                                                            <strong>YouTube:</strong> <?php echo $client_row['youtube']; ?><br>
                                                            <strong>Perqindja:</strong> <?php echo $client_row['perqindja']; ?><br>
                                                            <strong>Ads:</strong> <?php echo $client_row['ads']; ?><br>
                                                            <strong>FB:</strong> <?php echo $client_row['fb']; ?><br>
                                                            <strong>IG:</strong> <?php echo $client_row['ig']; ?><br>
                                                            <strong>Adresa:</strong> <?php echo $client_row['adresa']; ?><br>
                                                            <strong>Kategoria:</strong> <?php echo $client_row['kategoria']; ?><br>
                                                        </li>
                                                        <br>
                                                        <div>
                                                            <a href="editk.php?id=<?php echo $client_row['id']; ?>" class="input-custom-css px-3 py-2 mt-3" style="text-transform:none;text-decoration:none">Shiko të dhënat e përgjithshme</a>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Display as list on XS -->
                <div class="d-block d-lg-none">
                    <?php
                    $sql = "SELECT * FROM refresh_tokens ORDER BY id DESC";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                    ?>
                        <div class="card mb-3 m-0 p-0">
                            <div class="card-body">
                                <h5 class="card-title">Informacioni i Kanalit</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>ID:</strong> <?php echo $row['id']; ?></li>
                                    <li class="list-group-item"><strong>ID-ja e Kanalit:</strong> <?php echo $row['channel_id']; ?></li>
                                    <li class="list-group-item"><strong>Emri i Kanalit:</strong> <?php echo $row['channel_name']; ?></li>
                                    <li class="list-group-item"><strong>Krijimi i Kanalit në Bazë të të Dhënave:</strong> <?php echo $row['created_at']; ?></li>
                                </ul>
                                <button style="text-transform:none;" class="input-custom-css px-3 py-2 delete-btn" data-id="<?php echo $row['id']; ?>"><i class="fi fi-rr-trash"></i> Fshij</button>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
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
        // Add event listener for delete button click (for both table and list)
        $(document).on('click', '.delete-btn', function() {
            var rowId = $(this).data('id');
            var deleteButton = $(this); // Store reference to the button
            Swal.fire({
                title: 'Jeni të sigurtë?',
                text: 'Jeni të sigurtë që dëshironi të fshini këtë regjistrim?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Po, fshije!',
                cancelButtonText: 'Anulo'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_auth_channel.php',
                        type: 'POST',
                        data: {
                            id: rowId
                        },
                        success: function(response) {
                            // Remove the row from the table or list
                            deleteButton.closest('tr, .card').remove();
                            Swal.fire(
                                'Fshirë!',
                                'Regjistrimi është fshirë me sukses.',
                                'success'
                            );
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire(
                                'Gabim!',
                                'Diçka shkoi gabim. Ju lutemi, provoni përsëri më vonë.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
<?php include 'partials/footer.php'; ?>