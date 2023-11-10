<?php include 'partials/header.php';
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
    if ($conn->query("INSERT INTO fatura (emri, emrifull, data, fatura) VALUES ('$emri', '$emrifull', '$data','$fatura')")) {
?>
        <meta http-equiv="refresh" content="0;URL='shitje.php?fatura=<?php echo $fatura; ?>'" />
<?php
    } else {
        echo "Gabim: " . $conn->error;
    }
}
if ($_SESSION['acc'] == '1') {
} elseif ($_SESSION['acc'] == '3') {
} else {
    die('<script>alert("Nuk keni Akses ne kete sektor")</script>');
    echo '<meta http-equiv="refresh" content="0;URL=index.php/" /> ';
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
                    <select name="emri" class="form-control">
                        <?php
                        $gsta = $conn->query("SELECT * FROM klientet WHERE blocked='0'");
                        while ($gst = mysqli_fetch_array($gsta)) {
                        ?>
                            <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?></option>
                        <?php } ?>

                    </select>
                    <label for="datas">Data:</label>
                    <input type="text" name="data" class="form-control" value="<?php echo date("Y-m-d"); ?>">
                    <label for="imei">Fatura:</label>

                    <input type="text" name="fatura" class="form-control" value="<?php echo date('dmYhis'); ?>" readonly>

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
                    <h4 class="font-weight-bold text-gray-800 mb-4">Pagesat e p&euml;rfunduara n&euml; platform&euml;n Youtube</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Financat</a>
                            <span>/</span>
                            <a href="faturat.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Pagesat Youtube</u></a>
                        
                            <!-- Modal -->
                            <div class="modal fade" id="videoUdhezuese" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Video udh&euml;zuese</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <video width="100%" height="315" controls>
                                                <source src="assets/video-udh&euml;zuese.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>

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
                <div class="table-responsive">
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
                                        <input type="text" name="fatura" id="fatura" class="form-control" value="" placeholder="Sh&euml;no numrin e fatur&euml;s">
                                        <label>P&euml;rshkrimi:</label>
                                        <textarea class="form-control" name="pershkrimi" id="pershkrimi"></textarea>
                                        <label>Shuma:</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">&euro;</span>
                                            </div>
                                            <input type="text" name="shuma" id="shuma" class="form-control" placeholder="0" aria-label="Shuma">
                                            <div class="input-group-append">
                                                <span class="input-group-text">.00</span>
                                            </div>
                                        </div>
                                        <label>M&euml;nyra e pages&euml;s</label>
                                        <select name="menyra" id="menyra" class="form-select">
                                            <option value="BANK">BANK</option>
                                            <option value="CASH">CASH</option>
                                            <option value="PayPal">PayPal</option>
                                            <option value="Ria">Ria</option>
                                            <option value="MoneyGram">Money Gram</option>
                                            <option value="WesternUnion">Western Union</option>
                                        </select>
                                        <label>Data</label>
                                        <input type="text" name="data" id="data" value="<?php echo date("Y-m-d"); ?>" class="form-control">
                                        <hr>
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
                </div>
                <br>




                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 class="card-title">Filtro t&euml; dh&euml;nat</h4>

                    <form method="POST">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="start_date">Prej :</label>
                                <input type="date" class="form-control shadow-sm rounded-5 mt-2" id="start_date" name="start_date">
                            </div>
                            <div class="col mb-3">
                                <label for="end_date">Deri :</label>
                                <input type="date" class="form-control shadow-sm rounded-5 mt-2" id="end_date" name="end_date">
                            </div>

                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" name="submit" class="btn btn-sm btn-primary mt-3 text-white shadow-sm rounded-5 mt-2"><i class="fi fi-rr-filter"></i></button>
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
                <?php } else {
                    $query = "SELECT * FROM fatura ORDER BY id DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                }
                ?>
                <div class="card shadow-sm rounded-5">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-responsive table-bordered">
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

                                    <?php


                                    while ($row = mysqli_fetch_array($result)) {
                                        $id = $row['id'];
                                        // $emri = $row['emri']; - Kjo eshte rubrik per emer por po e shfaq pjesen e ID-s&euml;
                                        $emri_artikullit = $row['emrifull'];
                                        $fatura = $row['fatura'];
                                        $pagesa_e_mbetur = $row['klientit'];
                                        $totali = $row['mbetja'];
                                        $pagesa = $row['totali'];
                                        $data = $row['data'];

                                        $dda = $row['data'];
                                        $date = date_create($dda);
                                        $dats = date_format($date, 'Y-m-d');
                                        $sid = $row['fatura'];

                                        $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$sid'");
                                        $qq4 = mysqli_fetch_array($q4);

                                        $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$sid'");
                                        $merrep = mysqli_fetch_array($merrpagesen);

                                        $klientiid = $row['emri'];

                                        $queryy = "SELECT * FROM klientet WHERE id=" . $klientiid . " ";
                                        $mkl = $conn->query($queryy);
                                        $k4 = mysqli_fetch_array($mkl);

                                        $obli = $qq4['sum'] - $merrep['sum'];

                                        if ($qq4['sum'] > $merrep['sum']) {
                                            $pagesaaa = '<span class="badge rounded-pill text-bg-danger text-white w-100">' . $row["emrifull"] . '</span>';
                                        } else {
                                            $pagesaaa = '<span class="badge rounded-pill text-bg-success text-white w-100">' . $row["emrifull"] . '</span>';
                                        }

                                        $shuma = $qq4["sum"];
                                        $shuma_e_paguar = $merrep['sum'];

                                        if ($obli == '0' or $obli < 0) {

                                            echo "
                                            <tr>
                                                <td>$pagesaaa</td>
                                                <td>$emri_artikullit</td>
                                                <td>$fatura</td>
                                                <td>$data</td>
                                                <td>$shuma</td>
                                                <td>$shuma_e_paguar</td>
                                                <td>$obli</td>
                                                
                                                <td>
        <a class='btn btn-primary btn-sm py-2' href='shitje.php?fatura=$sid' target='_blank'><i class='fi fi-rr-edit'></i></a>
        <a class='btn btn-success btn-sm py-2' target='_blank' href='fatura.php?invoice=$sid'><i class='fi fi-rr-print'></i></a> 
        <a type='button' name='delete' class='btn btn-danger btn-xs delete py-2' id='$sid'><i class='fi fi-rr-trash'></i></a> 
        </td>
                                            </tr>
                                            ";
                                        }
                                    }

                                    ?>
                                </tbody>
                                <tfoot class="bg-light">
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
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>




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

                    <tfoot class="bg-light">
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
                    </tfoot>
                </table> -->
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#btnruaj').click(function() {
                            var data = $('#user_form').serialize() + '&btn_save=btn_save';
                            $.ajax({
                                url: 'api/insertpages.php',
                                type: 'POST',
                                data: data,
                                success: function(response) {
                                    $('#mesg').text(response);
                                }
                            });
                        });
                    });
                </script>
                <script src="js/fetch_done_youtube.js"></script>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    $(document).ready(function() {

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
                lengthSelect.addClass('form-select'); // add Bootstrap form-select class
                lengthSelect.css({
                    'width': 'auto', // adjust width to fit content
                    'margin': '0 8px', // add some margin around the element
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem', // adjust padding to match Bootstrap's styles
                    'line-height': '1.5', // adjust line-height to match Bootstrap's styles
                    'border': '1px solid #ced4da', // add border to match Bootstrap's styles
                    'border-radius': '0.25rem', // add border radius to match Bootstrap's styles
                }); // adjust width to fit content
            },
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' + // length menu and search input layout with margin bottom
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

        });
    });

    $(document).on('click', '.delete', function() {
        var id = $(this).attr("id");
        if (confirm("A jeni i sigurt q&euml; doni ta hiqni k&euml;t&euml;?")) {
            $.ajax({
                url: "api/deletefat.php",
                method: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    $('#alert_message').html('<div class="alert alert-success">' + data + '</div>');
                    $('#user_data').DataTable().destroy();
                    fetch_data();
                }
            });
            setInterval(function() {
                $('#alert_message').html('');
            }, 5000);
        }
    });
</script>