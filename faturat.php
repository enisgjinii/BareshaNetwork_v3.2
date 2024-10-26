<?php
include 'partials/header.php';

// Ensure $conn is defined in header.php
if (!isset($conn)) {
    die("Database connection not established.");
}

function sanitize_input($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}

if (isset($_GET['id'])) {
    $gid = sanitize_input($conn, $_GET['id']);
    $stmt = $conn->prepare("UPDATE rrogat SET lexuar = ? WHERE id = ?");
    if ($stmt) {
        $status = 1;
        $stmt->bind_param("ii", $status, $gid);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

if (isset($_POST['ruaj'])) {
    $emri_id = sanitize_input($conn, $_POST['emri']);

    // Fetch emrifull based on emri_id
    $stmt = $conn->prepare("SELECT emri FROM klientet WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $emri_id);
        $stmt->execute();
        $stmt->bind_result($emrifull);
        if (!$stmt->fetch()) {
            $emrifull = '';
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $data = sanitize_input($conn, $_POST['data']);
    $fatura = sanitize_input($conn, $_POST['fatura']);

    $stmt = $conn->prepare("INSERT INTO fatura (emri, emrifull, data, fatura) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssis", $emri_id, $emrifull, $data, $fatura);
        if ($stmt->execute()) {
            // Corrected the meta refresh syntax
            echo "<meta http-equiv='refresh' content='0;URL=shitje.php?fatura={$fatura}' />";
        } else {
            echo "Gabim: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

if (isset($_GET['fshij'])) {
    $fshijid = sanitize_input($conn, $_GET['fshij']);

    // Fetch existing fatura details
    $stmt = $conn->prepare("SELECT emri, fatura, data FROM fatura WHERE fatura = ?");
    if ($stmt) {
        $stmt->bind_param("s", $fshijid);
        $stmt->execute();
        $stmt->bind_result($emr, $fatura2, $data2);
        if ($stmt->fetch()) {
            // Insert into draft
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO draft (emri, data, fatura) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $emr, $data2, $fatura2);
                if ($stmt->execute()) {
                    $stmt->close();

                    // Delete from fatura
                    $stmt = $conn->prepare("DELETE FROM fatura WHERE fatura = ?");
                    if ($stmt) {
                        $stmt->bind_param("s", $fshijid);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        echo "<script>alert('Error preparing delete statement: {$conn->error}');</script>";
                    }

                    // Fetch related shitje entries
                    $stmt = $conn->prepare("SELECT emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data FROM shitje WHERE fatura = ?");
                    if ($stmt) {
                        $stmt->bind_param("s", $fshijid);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($draft = $result->fetch_assoc()) {
                            $insertStmt = $conn->prepare("INSERT INTO shitjedraft (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            if ($insertStmt) {
                                $insertStmt->bind_param(
                                    "sddsdiss",
                                    $draft['emertimi'],
                                    $draft['qmimi'],
                                    $draft['perqindja'],
                                    $draft['klientit'],
                                    $draft['mbetja'],
                                    $draft['totali'],
                                    $draft['fatura'],
                                    $draft['data']
                                );
                                if ($insertStmt->execute()) {
                                    // Delete the original shitje entry
                                    $deleteStmt = $conn->prepare("DELETE FROM shitje WHERE fatura = ?");
                                    if ($deleteStmt) {
                                        $deleteStmt->bind_param("s", $fshijid);
                                        $deleteStmt->execute();
                                        $deleteStmt->close();
                                    } else {
                                        echo "<script>alert('Error preparing delete shitje statement: {$conn->error}');</script>";
                                    }
                                } else {
                                    echo "<script>alert('Error executing insert into shitjedraft: {$insertStmt->error}');</script>";
                                }
                                $insertStmt->close();
                            } else {
                                echo "<script>alert('Error preparing insert into shitjedraft: {$conn->error}');</script>";
                            }
                        }
                        $stmt->close();
                    } else {
                        echo "<script>alert('Error preparing select shitje statement: {$conn->error}');</script>";
                    }
                } else {
                    echo "<script>alert('Error executing insert into draft: {$stmt->error}');</script>";
                }
            } else {
                echo "<script>alert('Error preparing insert into draft statement: {$conn->error}');</script>";
            }
        } else {
            echo "<script>alert('Fatura not found.');</script>";
            $stmt->close();
        }
    } else {
        echo "<script>alert('Error preparing select fatura statement: {$conn->error}');</script>";
    }
}
?>

<body>
    <!-- Modal for Creating New Fatura -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Faturë e Re</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="emri">Emri & Mbiemri</label>
                        <select name="emri" class="form-select shadow-sm rounded-5 my-2" required>
                            <option value="" disabled selected>Zgjidhni një klient</option>
                            <?php
                            $result = $conn->query("SELECT id, emri FROM klientet WHERE blocked = '0'");
                            if ($result) {
                                while ($client = $result->fetch_assoc()) {
                                    echo "<option value='{$client['id']}'>{$client['emri']}</option>";
                                }
                            } else {
                                echo "<option value=''>Nuk u gjet asnjë klient</option>";
                            }
                            ?>
                        </select>
                        <label for="data">Data:</label>
                        <input type="date" name="data" class="form-control shadow-sm rounded-5 my-2" value="<?= date("Y-m-d") ?>" required>
                        <label for="fatura">Fatura:</label>
                        <input type="text" name="fatura" class="form-control shadow-sm rounded-5 my-2" value="<?= date('dmYHis') ?>" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                        <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Main Panel -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb -->
                <nav class="bg-white px-2 rounded-5 my-3" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Financat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pagesat Youtube</li>
                    </ol>
                </nav>
                <!-- Modal for Adding Payment -->
                <div class="modal fade" id="pagesmodal" tabindex="-1" aria-labelledby="pagesModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="user_form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Shto Pagesë</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label>Fatura:</label>
                                    <input type="text" name="fatura" id="fatura" class="form-control shadow-sm rounded-5 my-2" placeholder="Shëno numrin e faturës" required>
                                    <label>Përshkrimi:</label>
                                    <textarea name="pershkrimi" id="pershkrimi" class="form-control shadow-sm rounded-5 my-2" required></textarea>
                                    <label>Shuma:</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">€</span>
                                        <input type="number" step="0.01" name="shuma" id="shuma" class="form-control shadow-sm rounded-5 my-2" placeholder="0.00" aria-label="Shuma" required>
                                        <span class="input-group-text">.00</span>
                                    </div>
                                    <label>Mënyra e Pagesës</label>
                                    <select name="menyra" id="menyra" class="form-select shadow-sm rounded-5 my-2" required>
                                        <option value="" disabled selected>Zgjidhni një metodë</option>
                                        <?php
                                        $methods = ["BANK", "CASH", "PayPal", "Ria", "MoneyGram", "WesternUnion"];
                                        foreach ($methods as $method) {
                                            echo "<option value='{$method}'>{$method}</option>";
                                        }
                                        ?>
                                    </select>
                                    <label>Data</label>
                                    <input type="date" name="data" id="data" value="<?= date("Y-m-d") ?>" class="form-control shadow-sm rounded-5 my-2" required>
                                    <div id="mesg" class="text-danger"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                                    <input type="button" name="ruajp" id="btnruaj" class="btn btn-primary" value="Ruaj">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Data Table Card -->
                <div class="card shadow-none border rounded-5">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="employeeList" class="table w-100 table-bordered">
                                <thead class="bg-light">
                                    <tr class="text-dark">
                                        <th>Emri</th>
                                        <th>Emri Artistik</th>
                                        <th>Fatura</th>
                                        <th>Data</th>
                                        <th>Shuma</th>
                                        <th>Sh.Paguar</th>
                                        <th>Obligim</th>
                                        <th>Aksion</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <script>
                                $(document).ready(function() {
                                    $('#btnruaj').click(function() {
                                        $.ajax({
                                            url: 'api/insertpages.php',
                                            type: 'POST',
                                            data: $('#user_form').serialize() + '&btn_save=btn_save',
                                            success: function(response) {
                                                $('#mesg').text(response);
                                                $('#employeeList').DataTable().ajax.reload();
                                                // Optionally, hide the modal after successful save
                                                if (response.trim() === "Success") {
                                                    $('#pagesmodal').modal('hide');
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                $('#mesg').text("Error: " + error);
                                            }
                                        });
                                    });

                                    var dataTables = $('#employeeList').DataTable({
                                        responsive: false,
                                        order: [
                                            [3, "desc"]
                                        ],
                                        ajax: {
                                            url: "api/get_methods/get_ajax_fatura.php",
                                            type: "POST"
                                        },
                                        columns: [{
                                                data: "emrifull"
                                            },
                                            {
                                                data: "emriartikullit"
                                            },
                                            {
                                                data: "fatura"
                                            },
                                            {
                                                data: "data"
                                            },
                                            {
                                                data: "shuma"
                                            },
                                            {
                                                data: "shuma_e_paguar"
                                            },
                                            {
                                                data: "obli"
                                            },
                                            {
                                                data: "aksion"
                                            }
                                        ],
                                        lengthMenu: [
                                            [10, 25, 50, -1],
                                            [10, 25, 50, "Të gjitha"]
                                        ],
                                        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                                            "<'row'<'col-md-12'tr>>" +
                                            "<'row'<'col-md-6'><'col-md-6'p>>",
                                        buttons: [{
                                                extend: 'pdfHtml5',
                                                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                                                titleAttr: 'Eksporto tabelen në formatin PDF',
                                                className: 'btn btn-danger'
                                            },
                                            {
                                                extend: 'copyHtml5',
                                                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                                                titleAttr: 'Kopjo tabelen në formatin Clipboard',
                                                className: 'btn btn-secondary'
                                            },
                                            {
                                                extend: 'excelHtml5',
                                                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                                                titleAttr: 'Eksporto tabelen në formatin Excel',
                                                className: 'btn btn-success',
                                                exportOptions: {
                                                    modifier: {
                                                        search: 'applied',
                                                        order: 'applied',
                                                        page: 'all'
                                                    }
                                                }
                                            },
                                            {
                                                extend: 'print',
                                                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                                                titleAttr: 'Printo tabelën',
                                                className: 'btn btn-info'
                                            }
                                        ],
                                        initComplete: function() {
                                            var btns = $('.dt-buttons');
                                            btns.removeClass('dt-buttons btn-group');
                                            $('div.dataTables_length select').addClass('form-select').css({
                                                'width': 'auto',
                                                'margin': '0 8px',
                                                'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                                                'line-height': '1.5',
                                                'border': '1px solid #ced4da',
                                                'border-radius': '0.25rem'
                                            });
                                        },
                                        fixedHeader: true,
                                        language: {
                                            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
                                        },
                                        stripeClasses: ['stripe-color']
                                    });

                                    // Delete functionality
                                    $(document).on('click', '.delete', function() {
                                        var id = $(this).attr("id");
                                        if (confirm("A jeni i sigurt që doni ta hiqni këtë?")) {
                                            $.ajax({
                                                url: "api/deletefat.php",
                                                method: "POST",
                                                data: {
                                                    id: id
                                                },
                                                success: function(data) {
                                                    $('#alert_message').html('<div class="alert alert-success">' + data + '</div>');
                                                    dataTables.ajax.reload();
                                                },
                                                error: function(xhr, status, error) {
                                                    $('#alert_message').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                                                }
                                            });
                                            setTimeout(function() {
                                                $('#alert_message').html('');
                                            }, 5000);
                                        }
                                    });

                                    // Open modal and populate fields
                                    $(document).on('click', '.open-modal', function() {
                                        var fatura = $(this).closest('tr').find('td').eq(2).text().trim();
                                        var obli = $(this).closest('tr').find('td').eq(6).text().trim();
                                        $('#fatura').val(fatura);
                                        $('#shuma').val(obli);
                                        var myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
                                        myModal.show();
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Message Placeholder -->
    <div id="alert_message" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11;">
        <!-- Alerts will be injected here -->
    </div>

    <?php include 'partials/footer.php'; ?>
</body>