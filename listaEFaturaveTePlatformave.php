<?php include 'partials/header.php';




// // Check if the id parameter is set
// if (isset($_GET['id'])) {
//     // Store the id parameter in a variable
//     $gid = $_GET['id'];
//     // Update the rrogat table and set the lexuar column to 1 where the id matches
//     $conn->query("UPDATE rrogat SET lexuar='1' WHERE id='$gid'");
// }



// Check if the form has been submitted
if (isset($_POST['ruaj'])) {
    // Escape special characters from the form data
    $clientId = mysqli_real_escape_string($conn, $_POST['emri']);
    // Query the database to get the client's full name
    $clientQuery = $conn->query("SELECT * FROM klientet WHERE id='$clientId'");
    $clientResult = mysqli_fetch_array($clientQuery);
    $fullName = $clientResult['emri'];
    // Escape special characters from the form data
    $date = mysqli_real_escape_string($conn, $_POST['data']);
    $invoiceNumber = mysqli_real_escape_string($conn, $_POST['fatura']);
    // Insert the data into the database
    if ($conn->query("INSERT INTO faturaplatformes (emri, emrifull, data, fatura) VALUES ('$clientId', '$fullName', '$date','$invoiceNumber')")) {
        // Redirect the user to the invoice page
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.5/dist/sweetalert2.min.js"></script>
        <script>
            var seconds = 5;
            var timerInterval = setInterval(function () {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timerInterval);
                    window.location.href = 'shitjePlatforma.php?fatura=<?php echo $invoiceNumber; ?>';
                }
                document.getElementById('countdown').innerHTML = seconds;
            }, 1000);
            // show a sweet alert
            Swal.fire({
                title: 'Duke ridrejtuar brenda <span id="countdown">5</span> sekondave',
                icon: 'success',
                showCancelButton: false,
                showConfirmButton: false
            });
        </script>


        <?php
    } else {
        // Display an error message if something went wrong
        echo "Gabim: " . $conn->error;
    }
}
// Check if the user has access to this page
if ($_SESSION['acc'] == '1' || $_SESSION['acc'] == '3') {
    // Allow access
} else {
    // Deny access and redirect the user
    die('<script>alert("Nuk keni Akses ne kete sektor")</script>');
    echo '<meta http-equiv="refresh" content="0;URL=index.php/" /> ';
}
// Check if the delete request has been sent
if (isset($_GET['fshij'])) {
    // Get the invoice number from the request
    $deleteId = $_GET['fshij'];
    // Query the database to get the invoice data
    $deleteQuery = $conn->query("SELECT * FROM faturaplatformes WHERE fatura='$deleteId'");
    $deleteResult = mysqli_fetch_array($deleteQuery);
    $clientId = $deleteResult['emri'];
    $invoiceNumber = $deleteResult['fatura'];
    $date = $deleteResult['data'];
    // Insert the invoice data into the draft table
    if ($conn->query("INSERT INTO draftplatforma (emri, data, fatura) VALUES ('$clientId', '$date','$invoiceNumber')")) {
        // Delete the invoice from the invoice table
        $conn->query("DELETE FROM faturaplatformes WHERE fatura='$deleteId'");
        // Query the database to get the sale data
        $saleQuery = $conn->query("SELECT * FROM shitjeplatforma WHERE fatura='$deleteId'");
        // Loop through the sale data
        while ($draft = mysqli_fetch_array($saleQuery)) {
            $itemName = $draft['emertimi'];
            $price = $draft['qmimi'];
            $discount = $draft['perqindja2'];
            $client = $draft['klientit'];
            $remaining = $draft['mbetja'];
            $total = $draft['totali'];
            $invoice = $draft['fatura'];
            $date = $draft['data'];
            // Insert the sale data into the draft table
            if ($conn->query("INSERT INTO shitjedraftplatforma (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES ('$itemName', '$price', '$discount', '$client', '$remaining', '$total', '$invoice', '$date')")) {
                // Delete the sale data from the sale table
                $conn->query("DELETE FROM shitjePlatforma WHERE fatura='$deleteId'");
            }
        }
    } else {
        // Display an error message if something went wrong
        echo '<script>alert("' . $conn->error . '");</script>';
    }
}


$inicialetEFatures = "BN";
$numriIFatures = $inicialetEFatures . date('dmYhi');
?>





<!-- Modali per krijimin e fatures se re te platformes -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fatur&euml; e re</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">

                    <label for="emri">Emri dhe mbiemri</label>
                    <!-- HTML code -->
                    <input type="text" id="searchInput" class="form-control shadow-sm rounded-5 my-2"
                        placeholder="Search">
                    <select name="emri" id="selectEmri" class="form-select shadow-sm rounded-5 my-2" multiple>
                        <?php
                        $gsta = $conn->query("SELECT * FROM klientet WHERE blocked='0' ORDER BY emri ASC");
                        while ($gst = mysqli_fetch_array($gsta)) {
                            ?>
                            <option class="rounded-5 p-3 mt-1" value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <!-- JavaScript code -->
                    <script>
                        const searchInput = document.getElementById('searchInput');
                        const selectEmri = document.getElementById('selectEmri');

                        searchInput.addEventListener('input', () => {
                            const searchValue = searchInput.value.toLowerCase();
                            const options = selectEmri.options;

                            for (let i = 0; i < options.length; i++) {
                                const optionText = options[i].textContent.toLowerCase();
                                if (optionText.includes(searchValue)) {
                                    options[i].style.display = '';
                                } else {
                                    options[i].style.display = 'none';
                                }
                            }
                        });
                    </script>


                    <label for="datas">Data:</label>
                    <input type="text" name="data" class="form-control shadow-sm rounded-5 my-2"
                        value="<?php echo date("Y-m-d"); ?>">
                    <label for="imei">Fatura:</label>

                    <input type="text" name="fatura" class="form-control shadow-sm rounded-5 my-2"
                        value="<?php echo $numriIFatures ?>" readonly>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modali per krijimin e pageses se re te platformes | END -->





<div class="modal fade" id="pagesmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Shto Pages&euml;</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user_form">
                    <label>Fatura:</label>
                    <input type="text" name="fatura" id="fatura" class="form-control shadow-sm rounded-5 my-2" value=""
                        placeholder="Sh&euml;no numrin e fatur&euml;s">
                    <label>P&euml;rshkrimi:</label>
                    <textarea class="form-control shadow-sm rounded-5 my-2" name="pershkrimi"
                        id="pershkrimi"></textarea>
                    <label>Shuma:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">&euro;</span>
                        </div>
                        <input type="text" name="shuma" id="shuma" class="form-control shadow-sm rounded-5 my-2"
                            placeholder="0" aria-label="Shuma">
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
                    <input type="text" name="data" id="data" value="<?php echo date("Y-m-d"); ?>"
                        class="form-control shadow-sm rounded-5 my-2">
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
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-9">
                    <div class="p-5 mb-4 card rounded-5 shadow-sm">
                        <h4 class="font-weight-bold text-gray-800 mb-4">Lista e faturave</h4>
                        <nav class="d-flex">
                            <h6 class="mb-0">
                                <a href="" class="text-reset">Platformat</a>
                                <span>/</span>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-reset" data-bs-placement="top"
                                    data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Lista e faturave</u></a>
                                <br>
                            </h6>
                        </nav>
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-5 mb-4 card rounded-5 shadow-sm">
                        <h4 style="text-transform: none;" class="card-title">Veglat p&euml;r aksesim t&euml; shpejt&euml;</h4>
                        <div class="">
                            <button style="text-transform: none;"
                                class="btn btn-sm btn-primary text-white shadow-sm rounded-5 " data-bs-toggle="modal"
                                data-bs-target="#exampleModal"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml;
                                e re</button>
                            <button style="text-transform: none;"
                                class="btn btn-sm btn-primary text-white shadow-sm rounded-5 " data-bs-toggle="modal"
                                data-bs-target="#pagesmodal"><i class="fi fi-rr-badge-dollar fa-lg"></i>&nbsp; Pages&euml; e
                                re</button>
                        </div>
                    </div>
                </div>
            </div>










            <div class="rounded-5 shadow-sm mb-4 card">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="example" class="table w-100 table-bordered">
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
                        <tbody></tbody>

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
            </div></div>
        </div>
    </div>
    <!-- content-wrapper ends -->


    <?php include 'partials/footer.php'; ?>


    <script>
        $(document).ready(function () {

            var dataTables = $('#example').DataTable({
                responsive: false,
                order: false,
                "ajax": {
                    url: "ajax_faturaPlatformes.php",
                    type: "POST",
                    data: {
                        acc: '<?php echo $_SESSION["acc"]; ?>'

                    }
                },
                "columns": [{
                    "data": "emrifull",
                },
                {
                    "data": "emriartikullit"
                },
                {
                    "data": "fatura"
                },
                {
                    "data": "data"
                },
                {
                    "data": "shuma"
                },
                {
                    "data": "shuma_e_paguar"
                },
                {
                    "data": "obli"
                },
                {
                    "data": "aksion"
                }
                ],


                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "T&euml; gjitha"]
                ],
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
                }],
                initComplete: function () {
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
                fixedHeader: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
                },
                stripeClasses: ['stripe-color'],

            });

            $(document).on('click', '.delete', function () {
                var id = $(this).attr("id");
                if (confirm("A jeni i sigurt q&euml; doni ta hiqni k&euml;t&euml;?")) {
                    $.ajax({
                        url: "api/deletefatPlatforma.php",
                        method: "POST",
                        data: {
                            id: id
                        },
                        success: function (data) {
                            $('#alert_message').html('<div class="alert alert-success">' + data + '</div>');
                            $('#example').DataTable().destroy();
                            fetch_data();
                        }
                    });
                    setInterval(function () {
                        $('#alert_message').html('');
                    }, 5000);
                }
            });

            // Make this table employeeList to be reloaded when i press button Ruaj
            $('#btnruaj').click(function () {
                $('#example').DataTable().ajax.reload();
            });
        });


        $(document).on('click', '.open-modal', function () {
            // Get the fatura value from the parent table cell's text
            var fatura = $(this).parent().text().trim();
            var shuma = $(this).parent().next().next().text().trim();
            var shuma_e_paguar = $(this).parent().next().next().next().text().trim();
            var oblgimi = $(this).parent().next().next().next().next().text().trim();

            // Fill the fatura input field with the retrieved data
            $('#fatura').val(fatura);
            $('#shuma').val(shuma);

            if (shuma_e_paguar == 0) {
                $('#shuma').val(shuma);
            } else {
                $('#shuma').val(oblgimi);
            }


            // Show the modal - this depends on the version of Bootstrap you are using
            // Bootstrap 4:
            // $('#myModal').modal('show');
            // Bootstrap 5:
            var myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
            myModal.show();
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#btnruaj').click(function () {
                var data = $('#user_form').serialize() + '&btn_save=btn_save';
                $.ajax({
                    url: 'api/insertimi_pagesave_platformave.php',
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        $('#mesg').text(response);
                    }
                });
            });
        });
    </script>