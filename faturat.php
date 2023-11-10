<?php
ob_start();
include 'partials/header.php'; ?>

<?php

if (isset($_POST['ruaj'])) {
    $emri = mysqli_real_escape_string($conn, $_POST['emri']);
    $merreperemer = $conn->query("SELECT * FROM klientet WHERE id='$emri'");
    $merreperemer2 = mysqli_fetch_array($merreperemer);
    $emrifull = $merreperemer2['emri'];
    $data = mysqli_real_escape_string($conn, $_POST['data']);
    $fatura = mysqli_real_escape_string($conn, $_POST['fatura']);
    $gjendjaFatures = mysqli_real_escape_string($conn, $_POST['gjendjaFatures']);
    $linkuIKengesArray = array();
    for ($i = 1; $i <= 5; $i++) {
        $fieldName = "linkuIKenges_" . $i;
        if (isset($_POST[$fieldName])) {
            $linkuIKengesArray[] = mysqli_real_escape_string($conn, $_POST[$fieldName]);
        }
    }
    $kenga = implode(",", $linkuIKengesArray);
    $emri_i_kengetarit = mysqli_real_escape_string($conn, $_POST['emri_i_kengetarit']);
    if ($conn->query("INSERT INTO fatura (emri, emrifull, data, fatura, gjendja_e_fatures) VALUES ('$emri', '$emrifull', '$data','$fatura','$gjendjaFatures')")) {
        header("Location: shitje.php?fatura=$fatura");
        exit;
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


<div class="main-panel" id="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <h2>Faturat</h2>
                <br>
                <div id="alert_message"></div>
                <div class="row mb-4">
                    <div>
                        <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e
                            re</button>
                        <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#pagesmodal"><i class="fi fi-rr-badge-dollar fa-lg"></i>&nbsp; Pages&euml; e
                            re</button>
                    </div>
                </div>
                <div class="row">
                    <div class="w-25 mb-4 position-relative"> <label for="searchInput" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control shadow-sm rounded-5" id="searchInput" placeholder="K&euml;rko">
                            <button class="btn btn-link" id="clearSearchInput" style="display: none;">
                                <i class="fas fa-times-circle" style="color: #999;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="w-25 mb-4">
                        <label for="yearFilter" class="form-label">Items per page</label>
                        <select class="form-control shadow-sm rounded-5" id="lengthMenu">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">T&euml; gjitha</option>
                        </select>
                    </div>
                    <div class="w-25 mb-4">
                        <label for="yearFilter" class="form-label">Year</label>
                        <select class="form-control shadow-sm rounded-5" id="yearFilter">
                            <option value="">All</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                            <option value="2021">2021</option>
                        </select>
                    </div>
                    <div class="w-25 mb-4">
                        <label for="monthFilter" class="form-label">Month</label>
                        <select class="form-control shadow-sm rounded-5" id="monthFilter">
                            <option value="">Select a month</option>
                            <option value="01">Janar</option>
                            <option value="02">Shkurt</option>
                            <option value="03">Mars</option>
                            <option value="04">Prill</option>
                            <option value="05">Maj</option>
                            <option value="06">Qershor</option>
                            <option value="07">Korrik</option>
                            <option value="08">Gusht</option>
                            <option value="09">Shtator</option>
                            <option value="10">Tetor</option>
                            <option value="11">N&euml;ntor</option>
                            <option value="12">Dhjetor</option>
                        </select>
                    </div>
                </div>

                <div class="row" id="searchResults">
                    <div class="col-12 my-2 fade-in-element" id="noResultsMessage" style="display: none; text-align: center;">
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-exclamation-circle" style="font-size: 64px; color: #FF6347;"></i>
                        </div>
                        <div style="margin-bottom: 10px; color: #999; font-size: 18px;">Ou! Nuk u gjet&euml;n rezultate.
                        </div>
                        <div style="color: #666; font-size: 14px;">Provoni t&euml; k&euml;rkoni me nj&euml; fjal&euml; kyçe t&euml; ndryshme ose
                            t&euml; rregulloni filtrat tuaj.</div>
                    </div>

                    <?php

                    // Fetch data from the database
                    $query = "SELECT
                    f.*,
                    k.id AS klient_id,
                    k.emri AS klient_emri,
                    SUM(s.totali) AS shitje_totali,
                    SUM(p.shuma) AS pagesa_totali,
                    GROUP_CONCAT(DISTINCT sh.emertimi) AS shitje_emertimi
                FROM
                    fatura AS f
                LEFT JOIN
                    klientet AS k ON f.emri = k.id
                LEFT JOIN
                    shitje AS s ON f.fatura = s.fatura
                LEFT JOIN
                    pagesat AS p ON f.fatura = p.fatura
                LEFT JOIN
                    shitje AS sh ON f.fatura = sh.fatura
                GROUP BY
                    f.id, k.id, k.emri
                ORDER BY
                    f.id DESC;
                ";


                    $result = $conn->query($query);

                    $consolidatedData = array(); // To store consolidated invoice data

                    while ($row = mysqli_fetch_assoc($result)) {
                        $invoice_fatura = $row['fatura'];
                        $shitje_totali = $row['shitje_totali'];
                        $pagesa_shuma = $row['pagesa_totali'];
                        $emertimi = $row['shitje_emertimi'];
                        $obligim = $shitje_totali - $pagesa_shuma;

                        if ($obligim > 0) {
                            // Add new entry for this invoice in consolidated data
                            $consolidatedData[$invoice_fatura] = array(
                                'fatura' => $invoice_fatura,
                                'shitje_totali' => $shitje_totali,
                                'pagesa_totali' => $pagesa_shuma,
                                'shitje_emertimi' => $emertimi,
                                'klient_emri' => $row['klient_emri'],
                                'klient_id' => $row['klient_id'],
                                'data' => $row['data']
                            );
                        }
                    }

                    foreach ($consolidatedData as $data) {
                        $invoice_klient_emri = $data['klient_emri'];
                        $invoice_klient_id = $data['klient_id'];
                        $invoice_date = $data['data'];
                        $invoice_fatura = $data['fatura'];
                        $shitje_totali = $data['shitje_totali'];
                        $pagesa_shuma = $data['pagesa_totali'];
                        $emertimi = $data['shitje_emertimi'];
                        $obligim = $shitje_totali - $pagesa_shuma;

                        $loan_query = "SELECT * FROM yinc WHERE kanali = '$invoice_klient_id'";
                        $loan_result = $conn->query($loan_query);

                        $total_loan = 0;

                        while ($loan = mysqli_fetch_assoc($loan_result)) {
                            $amount = $loan['shuma'] - $loan['pagoi'];
                            if ($amount > 0) {
                                $total_loan += $amount;
                            }
                        }
                    ?>
                        <div class="col-12 my-2 fade-in-element ">

                            <?php if ($total_loan > 0) { ?>
                                <div class="p-2 card rounded-5 shadow-sm border border-danger " style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 2rem; align-items: center;">
                                <?php } else { ?>
                                    <div class="p-2 card rounded-5 shadow-sm border" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2rem; align-items: center;">
                                    <?php } ?>
                                    <div class="flex align-items-center" id="emri">
                                        <p class="m-0 p-0 text-muted" style="font-size: 10px;">
                                            Emri
                                        </p>
                                        <div class="d-flex justify-content-between">
                                            <p class="m-0 p-0">
                                                <?php echo $invoice_klient_emri; ?>
                                            </p>
                                            <a href="#" class="toggle-emertimi-link text-dark">
                                                <i class="fas fa-chevron-down"></i> <!-- Use a down chevron icon initially -->
                                            </a>
                                        </div>
                                        <div class="emertimi-section" style="display: none;">
                                            <p class="m-0 ps-3 text-muted" style="font-size: 10px;">
                                                - <?php echo $emertimi; ?> <!-- Replace $emertimi with the actual variable containing the "emertimi" value -->
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Your styles and JavaScript code here (ensure Font Awesome and other dependencies are correctly loaded) -->


                                    <div class="flex align-items-center data">
                                        <p class="m-0 p-0 text-muted" style="font-size: 10px;">
                                            Data
                                        </p>
                                        <p class="m-0 p-0">
                                            <?php echo $invoice_date; ?>
                                        </p>
                                    </div>

                                    <div class="flex align-items-center" id="fatura">
                                        <p class="m-0 p-0 text-muted" style="font-size: 10px;">
                                            Fatura
                                        </p>
                                        <p class="m-0 p-0">
                                            <?php echo $invoice_fatura; ?>
                                        </p>

                                    </div>
                                    <div class="flex align-items-center" id="shitje_totali">
                                        <p class="m-0 p-0 text-muted" style="font-size: 10px;">
                                            Shuma
                                        </p>
                                        <p class="m-0 p-0">
                                            <?php echo $shitje_totali; ?>
                                        </p>
                                    </div>
                                    <div class="flex align-items-center">
                                        <?php
                                        if ($pagesa_shuma === null || $pagesa_shuma === '') {
                                            echo '<i class="fi fi-rr-clock m-0 p-0"></i>';
                                        } else { ?>
                                            <p class="text-muted m-0 p-0" style="font-size: 10px;">
                                                Shuma e paguar
                                            </p>
                                        <?php echo $pagesa_shuma;
                                        } ?>
                                    </div>
                                    <div class="flex align-items-center">
                                        <p class="m-0 p-0 text-muted" style="font-size: 10px;">
                                            Obligim
                                        </p>
                                        <p class="m-0 p-0">
                                            <?php echo $obligim; ?>
                                        </p>
                                    </div>
                                    <?php if ($total_loan > 0) { ?>
                                        <div class="flex align-items-center">
                                            <p class="m-0 p-0 text-muted" style="font-size: 10px;">
                                                Borgj
                                            </p>
                                            <p class="m-0 p-0">
                                                <?php echo $total_loan; ?>
                                            </p>
                                        </div>
                                    <?php } ?>

                                    <div style="display: flex; align-items: center;">

                                        <a class="button-custom-light open-modal" role="button" data-tooltip="Paguaj" data-invoice-fatura="<?php echo $invoice_fatura; ?>" data-shitje-totali="<?php echo $obligim; ?>">
                                            <i class="fi fi-rr-money-bill-wave"></i>
                                        </a>




                                        <a class="button-custom-light" role="button" data-tooltip="Print" href="fatura.php?invoice=<?php echo $invoice_fatura; ?>" target="_blank"><i class="fi fi-rr-print"></i></a>
                                        <a class="button-custom-light" role="button" data-tooltip="Ndrysho" href="shitje.php?fatura=<?php echo $invoice_fatura; ?>"><i class="fi fi-rr-edit"></i></a>
                                        <a class="button-custom-light delete" role="button" name="delete" data-tooltip="Fshi" id="<?php echo $invoice_fatura; ?>"><i class="fi fi-rr-trash"></i></a>
                                        <a class="button-custom-light" role="button" data-tooltip="Shih" href="fatura_details.php?invoice_fatura=<?php echo $invoice_fatura; ?>"><i class="fi fi-rr-square-plus"></i></a>
                                        <!-- <?php if ($total_loan > 0) : ?>
                                            <a class="button-custom-light" data-tooltip="<?php echo $total_loan; ?> ">
                                                <i class="fi fi-rr-money-check-edit-alt"></i>
                                            </a>
                                        <?php endif; ?> -->
                                    </div>

                                    </div>

                                </div>
                            <?php } // Save data to cache
                            ?>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pagesmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex">
                        <h5 class="modal-title" id="exampleModalLabel">Shto Pages&euml;</h5>
                        <p class="text-muted" style="font-size: 12px;">Plot&euml;soni formularin m&euml; posht&euml; p&euml;r t&euml; shtuar nj&euml;
                            pages&euml;.</p>

                    </div>
                    <button type="button" class="btn-close pe-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="user_form">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Fatura</label>
                                <input type="text" name="id_of_fatura" id="id_of_fatura" class="form-control input-custom-css" placeholder="Sh&euml;no numrin e fatur&euml;s">
                            </div>
                            <div class="col">
                                <label class="form-label">M&euml;nyra e pages&euml;s</label>
                                <select name="menyra" id="menyra" class="form-select input-custom-css" style="padding-top: 10px;padding-bottom: 10px;">
                                    <option value="BANK">BANK</option>
                                    <option value="CASH">CASH</option>
                                    <option value="PayPal">PayPal</option>
                                    <option value="Ria">Ria</option>
                                    <option value="MoneyGram">Money Gram</option>
                                    <option value="WesternUnion">Western Union</option>
                                </select>
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col">
                                <label class="form-label">Shuma</label>
                                <input type="text" name="shuma" id="shuma" class="form-control input-custom-css" placeholder="0" aria-label="Shuma">

                            </div>
                            <div class="col">
                                <label class="form-label">Data</label>
                                <input type="text" name="data" id="data" value="<?php echo date("Y-m-d"); ?>" class="form-control input-custom-css">
                            </div>
                        </div>

                        <div class="my-1">
                            <label class="form-label">P&euml;rshkrimi</label>
                            <textarea class="form-control shadow-sm rounded-5" name="pershkrimi" id="pershkrimi"></textarea>
                        </div>



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
                                    <td>Biznes</td>
                                    <td><input type="checkbox" name="kategorizimi[]" value="Biznes"></td>
                                </tr>
                                <tr>
                                    <td>Personal</td>
                                    <td><input type="checkbox" name="kategorizimi[]" value="Personal"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="mesg" style="color:red;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <input type="button" name="ruajp" id="btnruaj" class="save-button-custom-css" value="Ruaj">
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Fatur&euml; e re</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col">
                                <label for="emri" class="form-label">Emri & Mbiemri</label>
                                <input type="text" id="searchInputFirst" onkeyup="filterOptions()" class="form-control shadow-sm rounded-5 py-3" style="border:1" placeholder="Search for names..">
                                <br>
                                <select id="emriSelect" name="emri" class="form-select shadow-sm rounded-5 py-3">
                                    <?php
                                    // PHP: Fetching data from the "klientet" table where blocked='0'
                                    $gsta = $conn->query("SELECT * FROM klientet WHERE blocked='0'");
                                    while ($gst = mysqli_fetch_array($gsta)) {
                                        // PHP: Generating option elements with values from the database
                                        echo '<option value="' . $gst['id'] . '">' . $gst['emri'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <script>
                                function filterOptions() {
                                    var input, filter;
                                    input = document.getElementById('searchInputFirst');
                                    filter = input.value.toLowerCase();

                                    // Create an AJAX request
                                    var xmlhttp = new XMLHttpRequest();
                                    xmlhttp.onreadystatechange = function() {
                                        if (this.readyState == 4 && this.status == 200) {
                                            // Update the select dropdown with the response
                                            document.getElementById('emriSelect').innerHTML = this.responseText;
                                        }
                                    };

                                    // Send the request to the server-side script
                                    xmlhttp.open('GET', 'filter_names.php?filter=' + filter, true);
                                    xmlhttp.send();
                                }
                            </script>


                            <div class="col">
                                <!-- Input field for entering Data -->
                                <label for="datas" class="form-label">Data:</label>
                                <input type="text" name="data" class="form-control shadow-sm rounded-5 py-3" value="<?php echo date("Y-m-d"); ?>">

                            </div>
                        </div>

                        <div class="row my-3">
                            <div class="col">
                                <!-- Input field for displaying Fatura -->
                                <label for="imei" class="form-label">Fatura:</label>
                                <input type="text" name="fatura" class="form-control shadow-sm rounded-5 py-3" value="<?php echo date('dmYhis'); ?>" readonly>
                            </div>
                            <div class="col">
                                <?php
                                // Checking if the user's session is set to '1'
                                if ($_SESSION['acc'] == '1') {
                                ?>
                                    <!-- Select field for choosing gjendjaFatures -->
                                    <label for="gjendjaFatures" class="form-label">Zgjidhni gjendjen e fatur&euml;s:</label>
                                    <select name="gjendjaFatures" id="gjendjaFatures" class="form-select shadow-sm rounded-5 py-3">
                                        <option value="Rregullt">Rregullt</option>
                                        <option value="Pa rregullt">Pa rregullt</option>
                                    </select>
                                <?php
                                } else {
                                }
                                ?>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                    <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            const searchInput = $("#searchInput");
            const clearSearchInput = $("#clearSearchInput");
            const yearFilter = $("#yearFilter");
            const monthFilter = $("#monthFilter");
            const lengthMenu = $("#lengthMenu");
            const searchResults = $("#searchResults > div");
            const noResultsMessage = $("#noResultsMessage");
            const loanFilterCheckbox = $("#loanFilterCheckbox");
            searchInput.on("input", function() {
                const query = $(this).val().toLowerCase();
                clearSearchInput.toggle(!!query);
                applyFilters();
            });
            clearSearchInput.on("click", function() {
                searchInput.val("").trigger("input");
            });
            yearFilter.on("input", applyFilters);
            monthFilter.on("input", applyFilters);
            loanFilterCheckbox.on("change", applyFilters);
            lengthMenu.on("change", applyFilters);

            function applyFilters() {
                const query = searchInput.val().toLowerCase();
                const showLoansOnly = loanFilterCheckbox.is(":checked");
                const selectedYear = yearFilter.val();
                const selectedMonth = monthFilter.val();
                const itemsPerPage = parseInt(lengthMenu.val());
                let visibleCount = 0;
                searchResults.each(function() {
                    const invoiceKlientEmri = $(this).find("#emri").eq(0).text().toLowerCase();
                    const invoiceDate = $(this).find(".flex.align-items-center.data p:nth-child(2)").text().trim();
                    const hasLoan = parseFloat($(this).find(".button-custom-light[data-tooltip]").attr("data-tooltip")) > 0;
                    console.log("invoiceDate:", invoiceDate); // Debugging line

                    const invoiceYear = invoiceDate.substring(0, 4);
                    const invoiceMonth = invoiceDate.substring(5, 7);

                    const display = invoiceKlientEmri.includes(query) &&
                        (!showLoansOnly || (showLoansOnly && hasLoan)) &&
                        (selectedYear === "" || invoiceYear === selectedYear) &&
                        (selectedMonth === "" || invoiceMonth === selectedMonth);

                    if (display && (lengthMenu.val() === "all" || visibleCount < itemsPerPage)) {
                        $(this).css("display", "block").addClass("fade-in");
                        visibleCount++;
                    } else {
                        $(this).css("display", "none");
                    }
                });
                noResultsMessage.toggle(visibleCount === 0);
            }


            function handleDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "api/deletefat.php",
                            method: "POST",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                $("#alert_message").html(
                                    '<p class="border">' + data + "</p>"
                                );
                                if (data === "success") {
                                    $('#searchResults').load(location.href + ' #searchResults');
                                } else {
                                    // Handle error if needed
                                }
                            },
                            error: function(xhr, status, error) {
                                // Handle error if needed
                                $("#alert_message").html(
                                    '<p class="alert alert-danger">Error: ' + error + "</p>"
                                );
                            },
                        });
                    }
                });
            }

            $(document).on("click", ".delete", function() {
                var id = $(this).attr("id");
                handleDelete(id);
            });
        });
        $(document).on('click', '.open-modal', function() {
            const fatura = $(this).data("invoice-fatura");
            const shitje_totali = $(this).data("shitje-totali");
            $('#id_of_fatura').val(fatura);
            $('#shuma').val(shitje_totali);
            const myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
            myModal.show();
        });

        $(document).ready(function() {

            $('#btnruaj').click(function() {
                var data = $('#user_form').serialize() + '&btn_save=btn_save';
                $.ajax({
                    url: 'api/shto_pages.php',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: response,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 1500
                        }).then(() => {
                            location.reload();

                        });
                        setTimeout(function() {
                            $('#pagesmodal').modal('hide');
                        }, 1800);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error',
                            text: error,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            timer: 1500
                        });
                    }
                });
            });
        });

        $('#btnruaj').click(function() {
            $('#searchResults').load(location.href + ' #searchResults');
        });
    </script>

    <style>
        /* Style the toggle link */
        .toggle-emertimi-link {
            color: blue;
            /* Change the link color to blue */
            text-decoration: underline;
            /* Add underline to the link text */
            cursor: pointer;
        }
    </style>



    <script>
        $(document).ready(function() {
            // Add a click event handler to all elements with the class "toggle-emertimi-link"
            $(".toggle-emertimi-link").click(function(event) {
                event.preventDefault(); // Prevent the anchor link from navigating

                // Find the parent element (the container) and toggle the "emertimi-section" visibility
                var container = $(this).closest(".flex.align-items-center");
                var emertimiSection = container.find(".emertimi-section");

                // Toggle the visibility and update the chevron icon
                emertimiSection.toggle();
                var icon = $(this).find("i");
                icon.toggleClass("fa-chevron-down fa-chevron-up");
            });
        });
    </script>


    <?php include 'partials/footer.php'; ?>