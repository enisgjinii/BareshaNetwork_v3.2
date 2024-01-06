<?php


ob_start();
include 'partials/header.php';
if (isset($_GET['id'])) {

    $gid = $_GET['id'];
    $conn->query("UPDATE rrogat SET lexuar='1' WHERE id='$gid'");
}
if (isset($_POST['ruaj'])) {
    $emri = mysqli_real_escape_string($conn, $_POST['emri']);
    $merreperemer = $conn->query("SELECT * FROM klientet WHERE id='$emri'");
    $merreperemer2 = mysqli_fetch_array($merreperemer);

    $emrifull = $merreperemer2['emri'];
    $data = mysqli_real_escape_string($conn, $_POST['data']);
    $fatura = mysqli_real_escape_string($conn, $_POST['fatura']);

    $gjendjaFatures = mysqli_real_escape_string($conn, $_POST['gjendjaFatures']);

    if ($conn->query("INSERT INTO fatura (emri, emrifull, data, fatura,gjendja_e_fatures) VALUES ('$emri', '$emrifull', '$data','$fatura','$gjendjaFatures')")) {
?>
        <meta http-equiv="refresh" content="0;URL='shitje.php?fatura=<?php echo $fatura; ?>'" />
<?php
    } else {
        echo "Gabim: " . $conn->error;
    }
}

if (isset($_GET['fshij'])) {
    $fshijid = $_GET['fshij'];
    $mfsh4 = $conn->query("SELECT * FROM fatura WHERE fatura='$fshijid'");
    $mfsh2 = mysqli_fetch_array($mfsh4);
    $emr = $mfsh2['emri'];
    $fatura2 = $mfsh2['fatura'];
    $data2 = $mfsh2['data'];
    if ($conn->query("INSERT INTO draft (emri, data, fatura) VALUES ('$emr', '$data2','$fatura2')")) {
        $conn->query("DELETE FROM fatura WHERE fatura='$fshijid'");
        $shdraft = $conn->query("SELECT * FROM shitje WHERE fatura='$fshijid'");
        while ($draft = mysqli_fetch_array($shdraft)) {
            $shemertimi = $draft['emertimi'];
            $shqmimi = $draft['qmimi'];
            $shperqindja = $draft['perqindja'];
            $shklienti = $draft['klientit'];
            $shmbetja = $draft['mbetja'];
            $shtotali = $draft['totali'];
            $shfatura = $draft['fatura'];
            $shdata = $draft['data'];
            if ($conn->query("INSERT INTO shitjedraft (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES ('$shemertimi', '$shqmimi', '$shperqindja', '$shklienti', '$shmbetja', '$shtotali', '$shfatura', '$shdata')")) {
                $conn->query("DELETE FROM shitje WHERE fatura='$fshijid'");
            }
        }
    } else {
        echo '<script>alert("' . $conn->error . '");</script>';
    }
}

?>

<?php
include('conn-d.php');

$data_array = array();

$query = "SELECT * FROM fatura";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

while ($data_row = mysqli_fetch_array($result)) {
    $id = $data_row['id'];
    $emri_artikullit = $data_row['emrifull'];
    $fatura = $data_row['fatura'];
    $pagesa_e_mbetur = $data_row['klientit'];
    $totali = $data_row['mbetja'];
    $pagesa = $data_row['totali'];
    $data = $data_row['data'];
    $dda = $data_row['data'];
    $date = date_create($dda);
    $dats = date_format($date, 'Y-m-d');
    $sid = $data_row['fatura'];

    $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$sid'");
    $qq4 = mysqli_fetch_array($q4);
    $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$sid'");
    $merrep = mysqli_fetch_array($merrpagesen);
    $shuma = $qq4["sum"];
    $shuma_e_paguar = $merrep['sum'];
    $obli = $qq4['sum'] - $merrep['sum'];

    if ($obli !== 0 && $obli > 0) {
        $data_array[] = array(
            'emrifull' => $emri_artikullit,
            'emriartikullit' => $emri_artikullit,
            'fatura' => $fatura . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
            'data' => $data_row['data'],
            'shuma' => $shuma,
            'shuma_e_paguar' => $shuma_e_paguar,
            'obli' => $obli,
            'aksion' => "<a class='btn btn-primary btn-sm py-2 rounded-5 text-white' href='shitje.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a> <a class='btn btn-success btn-sm py-2 rounded-5 text-white' target='_blank' href='fatura.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2 rounded-5 text-white' id='$sid'><i class='fi fi-rr-trash'></i></a>"
        );
    }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="tcal.css" />
<script type="text/javascript" src="tcal.js"></script>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fatur e re</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">

                    <label for="emri">Emri & Mbiemri</label>
                    <select name="emri" class="form-select shadow-sm rounded-5 my-2">
                        <?php
                        $gsta = $conn->query("SELECT * FROM klientet WHERE blocked='0'");
                        while ($gst = mysqli_fetch_array($gsta)) {
                        ?>
                            <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?></option>
                        <?php } ?>

                    </select>
                    <label for="datas">Data:</label>
                    <input type="text" name="data" class="form-control shadow-sm rounded-5 my-2" value="<?php echo date("Y-m-d"); ?>">
                    <label for="imei">Fatura:</label>

                    <input type="text" name="fatura" class="form-control shadow-sm rounded-5 my-2" value="<?php echo date('dmYhis'); ?>" readonly>

                    <?php

                    if ($_SESSION['acc'] == '1') {
                    ?>
                        <label for="gjendjaFatures">Zgjidhni gjendjen e fatur&euml;s:</label>
                        <select name="gjendjaFatures" id="gjendjaFatures" class="form-select shadow-sm rounded-5 my-2">
                            <option value="Rregullt">Rregullt</option>
                            <option value="Pa rregullt">Pa rregullt</option>
                        </select>
                    <?php
                    } else {
                    }
                    ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
                </form>
            </div>
        </div>
    </div>
</div>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Pagesat Youtube</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Financat</a>
                            <span>/</span>
                            <a href="faturat.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Pagesat Youtube</u></a>
                            <div class="modal fade" id="videoUdhezuese" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Video udh&euml;zuese</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h6>
                    </nav>
                </div>
                <div class="modal fade" id="pagesmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Shto Pages&euml;</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="user_form">
                                    <label>Fatura:</label>
                                    <input type="text" name="fatura" id="fatura" class="form-control shadow-sm rounded-5 my-2" value="" placeholder="Sh&euml;no numrin e fatur&euml;s">
                                    <label>P&euml;rshkrimi:</label>
                                    <textarea class="form-control shadow-sm rounded-5 my-2" name="pershkrimi" id="pershkrimi"></textarea>
                                    <label>Shuma:</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">&euro;</span>
                                        </div>
                                        <input type="text" name="shuma" id="shuma" class="form-control shadow-sm rounded-5 my-2" placeholder="0" aria-label="Shuma">
                                        <div class="input-group-append">
                                            <span class="input-group-text">.00</span>
                                        </div>
                                    </div>
                                    <label>M&euml;nyra e pages&euml;s</label>
                                    <select name="menyra" id="menyra" class="form-select shadow-sm rounded-5 my-2">
                                        <option value="BANK">BANK</option>
                                        <option value="CASH">CASH</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Ria">Ria</option>
                                        <option value="MoneyGram">Money Gram</option>
                                        <option value="WesternUnion">Western Union</option>
                                    </select>
                                    <label>Data</label>
                                    <input type="text" name="data" id="data" value="<?php echo date("Y-m-d"); ?>" class="form-control shadow-sm rounded-5 my-2">

                                    <input type="checkbox" name="kategorizimi[]" value="null" style="display:none;">
                                    <table class="table table-bordered mt-3">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Emri i kategoris&euml;</th>
                                                <th>Zgjedhe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    Biznes
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="kategorizimi[]" value="Biznes">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Personal
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="kategorizimi[]" value="Personal">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>



                                    <!-- <input type="checkbox" name="kategorizimi[]" value="Biznes" id="biznes">
                  <label for="biznes">Biznes</label>

                  <input type="checkbox" name="kategorizimi[]" value="Personal" id="personal">
                  <label for="personal">Personal</label> -->


                                    <div id="mesg" style="color:red;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                                <input type="button" name="ruajp" id="btnruaj" class="btn btn-primary" value="Ruaj">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 style="text-transform: none;" class="card-title">Veglat p&euml;r aksesim t&euml; shpejt&euml;</h4>
                    <div class="">
                        <button style="text-transform: none;" class="btn btn-sm btn-primary mt-3 text-white shadow-sm rounded-5 mt-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e re</button>
                        <button style="text-transform: none;" class="btn btn-sm btn-primary mt-3 text-white shadow-sm rounded-5 mt-2" data-bs-toggle="modal" data-bs-target="#pagesmodal"><i class="fi fi-rr-badge-dollar fa-lg"></i>&nbsp; Pages&euml; e re</button>
                    </div>
                </div>






                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 style="text-transform: none;" class="card-title">Filtro t&euml; dh&euml;nat</h4>

                    <form method="POST">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="start_date">Prej :</label>
                                <input type="date" class="form-control shadow-sm rounded-5 my-2 shadow-sm rounded-5 mt-2" id="start_date" name="start_date">
                            </div>
                            <div class="col mb-3">
                                <label for="end_date">Deri :</label>
                                <input type="date" class="form-control shadow-sm rounded-5 my-2 shadow-sm rounded-5 mt-2" id="end_date" name="end_date">
                            </div>

                        </div>
                        <div class="col-md-4 mb-3">
                            <button style="text-transform: capitalize;" type="submit" name="submit" class="btn btn-sm btn-primary mt-3 text-white shadow-sm rounded-5 mt-2 me-3"><i class="fi fi-rr-filter"></i><br> <span class="mt-1">filtro</span> </button>

                        </div>
                    </form>
                </div>
                <?php
                include 'conn-d.php';



                if (isset($_POST['submit'])) {
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    // Add the filter to the query using prepared statements
                    $query = "SELECT * FROM fatura WHERE data >= ? AND data <= ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $start_date, $end_date);
                    $stmt->execute();
                    $result = $stmt->get_result();
                ?>
                    <div class="p-5 shadow-sm rounded-5 mb-4 card w-50">
                        <p>Ju keni zgjedhur filtrimin mes datave t&euml; m&euml;poshtme</p>
                        <div class="row">
                            <div class="col">

                                <span class="badge rounded-pill text-bg-primary text-white w-100 shadow-sm">
                                    <i class="fi fi-rr-calendar-lines"></i>
                                    <br><br><?php echo $start_date ?></span>
                            </div>
                            <div class="col">
                                <span class="badge rounded-pill text-bg-primary text-white w-100 shadow-sm"><i class="fi fi-rr-calendar-lines"></i>
                                    <br><br><?php echo $end_date ?></span>
                            </div>
                        </div>

                    </div>
                    <div id="alert_message"></div>
                <?php } else {
                    // SELECT * FROM fatura ORDER BY data,id DESCDESC ORDER BY Date DESC, ID DESC
                    $query = " SELECT * FROM fatura ORDER BY data DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                }
                ?>
                <div class="card shadow-sm rounded-5">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <?php
                                include('conn-d.php');

                                $query = "SELECT * FROM fatura";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                ?>

                                <!-- <table id="employeeList" class="table w-100 table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Emri</th>
                                            <th>Emri artistik</th>
                                            <th>Fatura</th>
                                            <th>Data</th>
                                            <th>Shuma</th>
                                            <th>Sh.Paguar</th>
                                            <th>Obligim</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table> -->

                                <table id="employeeList" class="table w-100 table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Emri Artikullit</th>
                                            <th>Fatura</th>
                                            <th>Data</th>
                                            <th>Shuma</th>
                                            <th>Shuma e Paguar</th>
                                            <th>Obli</th>
                                            <th>Aksion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data_array as $data_row) : ?>
                                            <tr>
                                                <td><?php echo $data_row['emrifull']; ?></td>
                                                <td><?php echo $data_row['fatura']; ?></td>
                                                <td><?php echo $data_row['data']; ?></td>
                                                <td><?php echo $data_row['shuma']; ?></td>
                                                <td><?php echo $data_row['shuma_e_paguar']; ?></td>
                                                <td><?php echo $data_row['obli']; ?></td>
                                                <td><?php echo $data_row['aksion']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $('#btnruaj').click(function() {
                                            var data = $('#user_form').serialize() + '&btn_save=btn_save';
                                            $.ajax({
                                                url: 'api/insertpages.php',
                                                type: 'POST',
                                                data: data,
                                                success: function(response) {
                                                    Swal.fire({
                                                        title: 'Success',
                                                        text: response,
                                                        icon: 'success',
                                                        confirmButtonText: 'OK',
                                                        timer: 3000 // auto close timer in milliseconds
                                                    }).then(() => {
                                                        // Do any other required actions here after clicking 'OK'
                                                    });
                                                    setTimeout(function() {
                                                        // Close the Swal modal after a certain time (e.g., 5 seconds)
                                                        $('#pagesmodal').modal('hide'); // Close the Bootstrap modal after a certain time (e.g., 5 seconds)
                                                        // Do any other required actions here after the modals are closed
                                                    }, 5000);

                                                },
                                                error: function(xhr, status, error) {
                                                    Swal.fire({
                                                        title: 'Error',
                                                        text: error,
                                                        icon: 'error',
                                                        confirmButtonText: 'OK',
                                                        timer: 1500 // auto close timer in milliseconds
                                                    });
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <!-- <script src="js/fetch.js"></script> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<?php include 'partials/footer.php'; ?>

<script>
    $(document).ready(function() {

        $('#employeeList').DataTable({
            responsive: false,
            order: [
                [3, "desc"]
            ],
            
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "T&euml; gjitha"]
            ],
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' +
                'Brtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: 'Eksporto tabelen ne formatin PDF',
                    className: 'btn btn-light border shadow-2 me-2'
                },
                {
                    extend: 'copyHtml5',
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                    className: 'btn btn-light border shadow-2 me-2'
                },
                {
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
                },
                {
                    extend: 'print',
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: 'Printo tabel&euml;n',
                    className: 'btn btn-light border shadow-2 me-2'
                },
                {
                    text: '<i class="fi fi-rr-circle-user fa-lg"></i>&nbsp;&nbsp; Shfaqe kolonen emri artistik',
                    titleAttr: 'Shfaqe kolonen emri artistik',
                    className: 'btn btn-light border shadow-2 me-2',
                    id: 'toggle-artikullit-btn',
                    action: function(e, node, config) {
                        // toggle the visibility of the column
                        dataTables.column(1).visible(!dataTables.column(1).visible());
                    }
                }
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
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color']
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: 'A jeni i sigurt q&euml; doni ta fshini k&euml;t&euml;?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Po, fshije'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "api/deletefat.php",
                        method: "POST",
                        data: {
                            id: id
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Fatura &euml;sht&euml; fshir&euml; me sukses',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $('#employeeList').DataTable().ajax.reload(null, false); // Reload the DataTable
                        }
                    });
                }
            });
        });
        $('#btnruaj').click(function() {
            $('#employeeList').DataTable().ajax.reload();
        });
    });


    $(document).on('click', '.open-modal', function() {
        var fatura = $(this).parent().text().trim();
        var shuma = $(this).parent().next().next().text().trim();
        var shuma_e_paguar = $(this).parent().next().next().next().text().trim();
        var oblgimi = $(this).parent().next().next().next().next().text().trim();
        $('#fatura').val(fatura);
        $('#shuma').val(shuma);

        if (shuma_e_paguar == 0) {
            $('#shuma').val(shuma);
        } else {
            $('#shuma').val(oblgimi);
        }
        var myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
        myModal.show();
    });
</script>