<?php
include 'partials/header.php';
function sanitize_input($conn, $input)
{
    return mysqli_real_escape_string($conn, $input);
}
if (isset($_GET['id'])) {
    $gid = sanitize_input($conn, $_GET['id']);
    $stmt = $conn->prepare("UPDATE rrogat SET lexuar = ? WHERE id = ?");
    $status = 1;
    $stmt->bind_param("ii", $status, $gid);
    $stmt->execute();
    $stmt->close();
}
if (isset($_POST['ruaj'])) {
    $emri = sanitize_input($conn, $_POST['emri']);
    $stmt = $conn->prepare("SELECT emri FROM klientet WHERE id = ?");
    $stmt->bind_param("i", $emri);
    $stmt->execute();
    $stmt->bind_result($emrifull);
    $stmt->fetch();
    $stmt->close();
    $data = sanitize_input($conn, $_POST['data']);
    $fatura = sanitize_input($conn, $_POST['fatura']);
    $stmt = $conn->prepare("INSERT INTO fatura (emri, emrifull, data, fatura) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $emri, $emrifull, $data, $fatura);
    if ($stmt->execute()) {
        echo "<meta http-equiv='refresh' content='0;URL=\'shitje.php?fatura={$fatura}\' />";
    } else {
        echo "Gabim: " . $conn->error;
    }
    $stmt->close();
}
if (isset($_GET['fshij'])) {
    $fshijid = sanitize_input($conn, $_GET['fshij']);
    $stmt = $conn->prepare("SELECT emri, fatura, data FROM fatura WHERE fatura = ?");
    $stmt->bind_param("s", $fshijid);
    $stmt->execute();
    $stmt->bind_result($emr, $fatura2, $data2);
    $stmt->fetch();
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO draft (emri, data, fatura) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $emr, $data2, $fatura2);
    if ($stmt->execute()) {
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM fatura WHERE fatura = ?");
        $stmt->bind_param("s", $fshijid);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("SELECT emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data FROM shitje WHERE fatura = ?");
        $stmt->bind_param("s", $fshijid);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($draft = $result->fetch_assoc()) {
            $insertStmt = $conn->prepare("INSERT INTO shitjedraft (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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
                $deleteStmt = $conn->prepare("DELETE FROM shitje WHERE fatura = ?");
                $deleteStmt->bind_param("s", $fshijid);
                $deleteStmt->execute();
                $deleteStmt->close();
            }
            $insertStmt->close();
        }
        $stmt->close();
    } else {
        echo "<script>alert('{$conn->error}');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="tcal.css" />
    <script src="tcal.js"></script>
</head>
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
                        <select name="emri" class="form-select shadow-sm rounded-5 my-2">
                            <?php
                            $result = $conn->query("SELECT id, emri FROM klientet WHERE blocked = '0'");
                            while ($client = $result->fetch_assoc()) {
                                echo "<option value='{$client['id']}'>{$client['emri']}</option>";
                            }
                            ?>
                        </select>
                        <label for="datas">Data:</label>
                        <input type="text" name="data" class="form-control shadow-sm rounded-5 my-2" value="<?= date("Y-m-d") ?>">
                        <label for="imei">Fatura:</label>
                        <input type="text" name="fatura" class="form-control shadow-sm rounded-5 my-2" value="<?= date('dmYhis') ?>" readonly>
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
                <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><span class="text-reset">Financat</span></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="text-reset">Pagesat Youtube</span>
                        </li>
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
                                    <input type="text" name="fatura" id="fatura" class="form-control shadow-sm rounded-5 my-2" placeholder="Shëno numrin e faturës">
                                    <label>Përshkrimi:</label>
                                    <textarea name="pershkrimi" id="pershkrimi" class="form-control shadow-sm rounded-5 my-2"></textarea>
                                    <label>Shuma:</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">€</span>
                                        <input type="text" name="shuma" id="shuma" class="form-control shadow-sm rounded-5 my-2" placeholder="0" aria-label="Shuma">
                                        <span class="input-group-text">.00</span>
                                    </div>
                                    <label>Mënyra e Pagesës</label>
                                    <select name="menyra" id="menyra" class="form-select shadow-sm rounded-5 my-2">
                                        <?php
                                        $methods = ["BANK", "CASH", "PayPal", "Ria", "MoneyGram", "WesternUnion"];
                                        foreach ($methods as $method) {
                                            echo "<option value='{$method}'>{$method}</option>";
                                        }
                                        ?>
                                    </select>
                                    <label>Data</label>
                                    <input type="text" name="data" id="data" value="<?= date("Y-m-d") ?>" class="form-control shadow-sm rounded-5 my-2">
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
                                        <th></th>
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
                                                className: 'input-custom-css px-3 py-2'
                                            },
                                            {
                                                extend: 'copyHtml5',
                                                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                                                titleAttr: 'Kopjo tabelen në formatin Clipboard',
                                                className: 'input-custom-css px-3 py-2'
                                            },
                                            {
                                                extend: 'excelHtml5',
                                                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                                                titleAttr: 'Eksporto tabelen në formatin Excel',
                                                className: 'input-custom-css px-3 py-2',
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
                                                className: 'input-custom-css px-3 py-2'
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
                                                }
                                            });
                                            setTimeout(function() {
                                                $('#alert_message').html('');
                                            }, 5000);
                                        }
                                    });
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
    <?php include 'partials/footer.php'; ?>
</body>
</html>