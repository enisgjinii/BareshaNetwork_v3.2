<?php

ob_start();
include 'partials/header.php';
include('conn-d.php');

if (isset($_POST['submit_file'])) {
    $file = $_FILES["file"]["tmp_name"];
    $file_open = fopen($file, "r");
    $selected_option = mysqli_real_escape_string($conn, $_POST['my-select']);

    $counter = 0; // initialize counter variable
    $batchSize = 100; // Adjust the batch size as needed
    $batchValues = array();

    while (($csv = fgetcsv($file_open, 0, ",")) !== false) {
        if ($counter >= 3) {
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

            // Build values for batch insertion
            $batchValues[] = "('$ReportingPeriod', '$AccountingPeriod', '$Artist', '$Release', '$Track', '$UPC', '$ISRC', '$Partner', '$Country', '$Type', '$Units', '$RevenueUSD', '$RevenueShare', '$SplitPayShare', '$selected_option')";

            // Check if a batch is ready to be inserted
            if (count($batchValues) >= $batchSize) {
                $query = "INSERT INTO platformat_2 (`ReportingPeriod`, `AccountingPeriod`, `Artist`, `Release`, `Track`, `UPC`, `ISRC`, `Partner`, `Country`, `Type`, `Units`, `RevenueUSD`, `RevenueShare`, `SplitPayShare`, `Emri`) VALUES " . implode(",", $batchValues);
                $conn->query($query);
                $batchValues = array(); // Reset batch array
            }
        }
        $counter++; // increment counter variable
    }

    // Insert any remaining rows in the batch
    if (count($batchValues) > 0) {
        $query = "INSERT INTO platformat_2 (`ReportingPeriod`, `AccountingPeriod`, `Artist`, `Release`, `Track`, `UPC`, `ISRC`, `Partner`, `Country`, `Type`, `Units`, `RevenueUSD`, `RevenueShare`, `SplitPayShare`, `Emri`) VALUES " . implode(",", $batchValues);
        $conn->query($query);
    }

    // Close file
    fclose($file_open);

    // Redirect to the same page with a success status
    header('Location: ' . $_SERVER['PHP_SELF'] . '?status=success');
    exit;
}

// Check if the form was successfully submitted
if (isset($_GET['status']) && $_GET['status'] == 'success') {
}

// Commit any open transaction
$conn->commit();

// Flush the output buffer
ob_flush();
?>




<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Platformat</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                                Inserto CSV
                            </a>
                        </li>
                </nav>
                <div class="p-5 mb-4 card rounded-5 shadow-sm" id="upload-container">
                    <form method="post" enctype="multipart/form-data" onsubmit="handleFormSubmission()">
                        <select id="my-select" name="my-select" class="form-select border shadow-sm rounded-5 text-dark">
                            <?php
                            $nxerrja_e_klientit = $conn->query("SELECT DISTINCT emri FROM klientet");
                            while ($nxerri = mysqli_fetch_array($nxerrja_e_klientit)) {
                            ?>
                                <option class="rounded-5 p-3 mt-1" value="<?php echo $nxerri['emri']; ?>"><?php echo $nxerri['emri']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <script>
                            new Selectr('#my-select', {
                                searchable: true,
                                width: 300
                            });
                        </script>
                        <input type="hidden" name="selected_option" id="selected_option">
                        <div class="d-flex w-50 my-4">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input form-control shadow-sm rounded-5" id="file" name="file" placeholder="Zgjedh nj&euml; fajll">
                            </div>
                            &nbsp;&nbsp;&nbsp;
                            <input type="submit" name="submit_file" class="btn btn-primary shadow-sm rounded-5 text-white" value="Ngarko" />
                    </form>

                    <script>
                        function handleFormSubmission() {
                            // Show loading spinner
                            Swal.fire({
                                title: 'Duke u ngarkuar...',
                                text: 'Ju lutemi prisni derisa skedari të jetë duke u ngarkuar.',
                                icon: 'info',
                                allowOutsideClick: false,
                                showCancelButton: false,
                                showConfirmButton: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                },
                            });

                            // Perform the form submission
                            return true; // Continue with form submission
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            <?php if (isset($_GET['status']) && $_GET['status'] == 'success') { ?>
                                Swal.fire({
                                    title: 'Sukses!',
                                    text: 'Skedari është ngarkuar me sukses.',
                                    icon: 'success',
                                    showConfirmButton: true,
                                }).then(function() {
                                    // Redirect to filtroCSV.php
                                    window.location.href = 'filtroCSV.php';
                                });
                            <?php } ?>
                        });
                    </script>
                </div>
            </div>



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
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>