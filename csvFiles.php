<?php

ob_start();
include 'partials/header.php';


include('conn-d.php');
if (isset($_POST['submit_file'])) {
    $file = $_FILES["file"]["tmp_name"];
    $file_open = fopen($file, "r");
    $selected_option = mysqli_real_escape_string($conn, $_POST['my-select']);

    $counter = 0; // initialize counter variable

    while (($csv = fgetcsv($file_open, 0, ",")) !== false) {
        if ($counter >= 3) { // check if counter is >= 3
            $ReportingPeriod = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[0]) ? $csv[0] : ""));
            $AccountingPeriod = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[1]) ? $csv[1] : ""));
            $Artist = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[2]) ? $csv[2] : ""));
            $Release = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[3]) ? $csv[3] : ""));
            $Track = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[4]) ? $csv[4] : ""));
            $UPC = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[5]) ? $csv[5] : ""));
            $ISRC = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[6]) ? $csv[6] : ""));
            $Partner = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[7]) ? $csv[7] : ""));
            $Country = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[8]) ? $csv[8] : ""));
            $Type = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[9]) ? $csv[9] : ""));
            $Units = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[10]) ? $csv[10] : ""));
            $RevenueUSD = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[11]) ? $csv[11] : ""));
            $RevenueShare = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[12]) ? $csv[12] : ""));
            $SplitPayShare = mysqli_real_escape_string($conn, str_replace("'", "\'", isset($csv[13]) ? $csv[13] : ""));

            $query = "INSERT INTO platformat_2 (`ReportingPeriod`, `AccountingPeriod`, `Artist`, `Release`, `Track`, `UPC`, `ISRC`, `Partner`, `Country`, `Type`, `Units`, `RevenueUSD`, `RevenueShare`, `SplitPayShare`,`Emri`) VALUES ('$ReportingPeriod', '$AccountingPeriod', '$Artist', '$Release', '$Track', '$UPC', '$ISRC', '$Partner', '$Country', '$Type', '$Units', '$RevenueUSD', '$RevenueShare', '$SplitPayShare', '$selected_option')";
            $conn->query($query);
        }
        $counter++; // increment counter variable
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?status=success');
    exit;
}



// Check if the form was successfully submitted
if (isset($_GET['status']) && $_GET['status'] == 'success') {
}

ob_flush();
?>



<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 mb-4 card rounded-5 shadow-sm">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Inserto CSV</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Platformat</a>
                            <span>/</span>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Inserto CSV</u></a>
                            <br>
                        </h6>
                    </nav>
                </div>
                <div class="p-5 mb-4 card rounded-5 shadow-sm">
                    <form method="post" enctype="multipart/form-data">
                        <input type="text" class="form-control border shadow-sm rounded-5 text-dark" id="search-input" placeholder="K&euml;rko...">
                        <br>
                        <select id="my-select" name="my-select" class="form-select border shadow-sm rounded-5 text-dark" data-live-search="true" multiple>
                            <?php
                            $nxerrja_e_klientit = $conn->query("SELECT DISTINCT emri FROM klientet");
                            while ($nxerri = mysqli_fetch_array($nxerrja_e_klientit)) {
                            ?>
                                <option class="rounded-5 p-3 mt-1" value="<?php echo $nxerri['emri']; ?>"><?php echo $nxerri['emri']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <input type="hidden" name="selected_option" id="selected_option">
                        <div class="d-flex w-50 my-4">
                            <div class=" custom-file">
                                <input type="file" class="custom-file-input form-control shadow-sm rounded-5" id="file" name="file" placeholder="Zgjedh nj&euml; fajll">
                            </div>

                            &nbsp;&nbsp;&nbsp;

                            <input type="submit" name="submit_file" class="btn btn-primary shadow-sm rounded-5 text-white" value="Ngarko" />
                        </div>

                    </form>
                </div>
            </div>


            <?php include 'partials/footer.php'; ?>
            <script>
                var table = $('#example').DataTable({
                    responsive: true,
                    search: {
                        return: true,
                    },
                    dom: 'Bfrtip',
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
                        titleAttr: 'Eksporto tabelen ne formatin CSV',
                        className: 'btn btn-light border shadow-2 me-2',

                    }, {
                        extend: 'print',
                        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                        titleAttr: 'Printo tabel&euml;n',
                        className: 'btn btn-light border shadow-2 me-2'
                    }],
                    initComplete: function() {
                        var btns = $('.dt-buttons');
                        btns.addClass('');
                        btns.removeClass('dt-buttons btn-group');

                    },
                    fixedHeader: true,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
                    },

                });
            </script>

            <script>
                const searchInput = document.getElementById('search-input');
                const select = document.getElementById('my-select');
                const options = select.options;

                searchInput.addEventListener('keyup', function() {
                    const searchTerm = searchInput.value.toLowerCase();

                    for (let i = 0; i < options.length; i++) {
                        const optionText = options[i].textContent.toLowerCase();

                        if (optionText.includes(searchTerm)) {
                            options[i].style.display = 'block';
                        } else {
                            options[i].style.display = 'none';
                        }
                    }
                });
            </script>