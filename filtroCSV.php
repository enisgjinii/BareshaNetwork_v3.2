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
                    <h4 class="font-weight-bold text-gray-800 mb-4">Filtro CSV</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Platformat</a>
                            <span>/</span>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Filtro CSV</u></a>
                            <br>
                        </h6>
                    </nav>
                </div>

                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <div class="form-group row">
                        <div class="col">
                            <form method="POST">
                                <label>Zgjedh klientin</label>
                                <select name="artistii" id="artistii" class="form-select border shadow-2  text-dark " data-live-search="true">
                                    <?php
                                    $merrarti = $conn->query("SELECT DISTINCT Emri FROM platformat_2");
                                    while ($merrart = mysqli_fetch_array($merrarti)) {
                                    ?>
                                        <option value="<?php echo $merrart['Emri']; ?>"><?php echo $merrart['Emri']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                        </div>
                        <div class="col">
                            <label>Zgjedh perioden</label>
                            <select name="perioda" id="perioda" class="form-select border shadow-2  text-dark w-100" data-live-search="true">
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-sm btn-primary text-white shadow-sm rounded-5"><i class="fi fi-rr-filter"></i></button>
                        </button>

                    </div>
                    </form>
                </div>





                <div class="card rounded-5 shadow-sm p-3">
                    <ul class="nav nav-pills nav-fill mb-3 border-bottom border-2" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-5" style="text-transform:none;" id="pills-raportiINxerrur-tab" data-bs-toggle="pill" data-bs-target="#pills-raportiINxerrur" type="button" role="tab" aria-controls="pills-raportiINxerrur" aria-selected="true">
                                <i class="fi fi-rr-ballot me-2"></i>
                                Raporti i nxerrur</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5" style="text-transform:none;" id="pills-totali-tab" data-bs-toggle="pill" data-bs-target="#pills-totali" type="button" role="tab" aria-controls="pills-totali" aria-selected="false"><i class="fi fi-rr-calculator me-2"></i>Raporte te detajuara</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5" style="text-transform:none;" id="pills-raportGrafik-tab" data-bs-toggle="pill" data-bs-target="#pills-raportGrafik" type="button" role="tab" aria-controls="pills-raportGrafik" aria-selected="false">raportGrafik</button>
                        </li>

                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-raportiINxerrur" role="tabpanel" aria-labelledby="pills-raportiINxerrur-tab">
                            <div class="card rounded-5 shadow-sm">
                                <div class="card-body">
                                    <h4 class="card-title" style="text-transform:none;">Platformat tjera</h4>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table id="example" data-ordering="false" class="table w-100 table-bordered">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Emri</th>
                                                            <th>Artist(s)</th>
                                                            <th>Reporting Period</th>
                                                            <th>Accounting Period</th>
                                                            <th>Release</th>
                                                            <th>Track</th>
                                                            <th>Country</th>
                                                            <th>Revenue (USD)</th>
                                                            <th>Revenue Share (%)</th>
                                                            <th>Split Pay Share (%)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if (isset($_POST['artistii']) && isset($_POST['perioda'])) {
                                                            $artistii = $_POST['artistii'];
                                                            $perioda = $_POST['perioda'];
                                                            $kueri = $conn->query("SELECT * FROM platformat_2 WHERE Emri='$artistii' AND AccountingPeriod='$perioda'");
                                                            $q4 = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat_2` WHERE Emri='$artistii' AND AccountingPeriod='$perioda'");
                                                            $qq4 = mysqli_fetch_array($q4);
                                                            if (mysqli_num_rows($kueri) > 0) {
                                                                while ($k = mysqli_fetch_array($kueri)) {

                                                        ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $k['Emri']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['Artist']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['ReportingPeriod']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['AccountingPeriod']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['Release']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['Track']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['Country']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['RevenueUSD']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['RevenueShare']; ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $k['SplitPayShare']; ?>
                                                                        </td>

                                                                    </tr>
                                                                <?php
                                                                }
                                                                ?>

                                                        <?php
                                                            }
                                                        } else {
                                                            // handle empty result set
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th>Emri</th>
                                                            <th>Artist(s)</th>
                                                            <th>Reporting Period</th>
                                                            <th>Accounting Period</th>
                                                            <th>Release</th>
                                                            <th>Track</th>
                                                            <th>Country</th>
                                                            <th>Revenue (USD)</th>
                                                            <th>Revenue Share (%)</th>
                                                            <th>Split Pay Share (%)</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-totali" role="tabpanel" aria-labelledby="pills-totali-tab">

                            <div class="d-flex align-items-start">
                                <div class="nav flex-column nav-pills me-3 w-25" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <button class="nav-link active border-end border-2 rounded-0" style="text-transform:none;" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">Raporti i totalit</button>
                                    <button class="nav-link  rounded-0 border-end border-2" style="text-transform:none;" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Raporti i totalit i pergjithshem ne baze te periudhes se kontabilitetit </button>
                                </div>
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                        <div class="card rounded-5 shadow-sm my-3">
                                            <div class="card-body">
                                                <h3 class="card-title" style="text-transform:none;"> </h3>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <?php

                                                            if (isset($_POST['artistii']) && isset($_POST['perioda'])) {
                                                                $artistii = $_POST['artistii'];
                                                                $perioda = $_POST['perioda'];
                                                                $kueri = $conn->query("SELECT * FROM platformat_2 WHERE Emri='$artistii' AND AccountingPeriod='$perioda'");
                                                                $q4 = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat_2` WHERE Emri='$artistii' AND AccountingPeriod='$perioda'");
                                                                $qq4 = mysqli_fetch_array($q4);
                                                                if (mysqli_num_rows($kueri) > 0) {


                                                            ?>
                                                                    <table data-ordering="false" class="table w-100 table-bordered">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th>Emri</th>
                                                                                <th>Periudha e kontabilitetit ( Accounting Period )</th>
                                                                                <th>Totali</th>
                                                                                <th></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                <?php
                                                                                if ($k = mysqli_fetch_array($kueri)) {
                                                                                    echo $k['Emri'];
                                                                                }
                                                                            } ?>
                                                                                </td>
                                                                                <td>
                                                                                    Ju keni zgjedhur perioden e kontabilitetit
                                                                                    <br><br>
                                                                                    <b><?php echo $perioda; ?></b>
                                                                                </td>
                                                                                <td id="<?php echo $qq4['sum'] ?>"><?php echo $qq4['sum'] ?> <br><br>
                                                                                </td>
                                                                                <td>
                                                                                    <button class="copy-button btn btn-light p-2 shadow-sm rounded-5" style="border:1px solid lightgrey; text-transform:none;" data-period="<?php echo $qq4['sum'] ?>"><i class="fi fi-rr-copy-alt align-middle me-2"></i><span class="align-middle">Kopjo vler&euml;n e totalit</span></button>

                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                        <div class="card rounded-5 shadow-sm my-3">
                                            <div class="card-body">
                                                <h3 class="card-title" style="text-transform:none;"></h3>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <table data-ordering="false" class="table w-100 table-bordered">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th>Periudha e kontabilitetit ( Accounting Period )</th>
                                                                        <th>Totali</th>
                                                                        <th>Copy</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    if (isset($_POST['artistii'])) {
                                                                        $artistii = $_POST['artistii'];

                                                                        // Retrieve a list of distinct accounting periods for the selected artist
                                                                        $periods_query = $conn->query("SELECT DISTINCT AccountingPeriod FROM platformat_2 WHERE Emri='$artistii'");

                                                                        // Create a table to display the results

                                                                        // Loop through each accounting period and retrieve the revenue sum
                                                                        while ($period_row = mysqli_fetch_array($periods_query)) {
                                                                            $perioda = $period_row['AccountingPeriod'];
                                                                            $revenue_query = $conn->query("SELECT SUM(`RevenueUSD`) as `sum` FROM `platformat_2` WHERE Emri='$artistii' AND AccountingPeriod='$perioda'");
                                                                            $revenue_row = mysqli_fetch_array($revenue_query);
                                                                            $revenue_sum = $revenue_row['sum'];
                                                                    ?>

                                                                            <tr>
                                                                                <td id='<?php echo $perioda ?>'><?php echo $perioda ?></td>
                                                                                <td id='revenue-<?php echo $perioda ?>'><?php echo $revenue_sum ?></td>
                                                                                <td><button class=" btn btn-light p-2 shadow-sm rounded-5" style="border:1px solid lightgrey; text-transform:none;" onclick='copyRevenue("<?php echo $perioda ?>")'><i class="fi fi-rr-copy-alt align-middle me-2"></i><span class="align-middle">Kopjo vler&euml;n e totalit</span></button></td>
                                                                            </tr>
                                                                <?php
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>































                        </div>

                        <div class="tab-pane fade" id="pills-raportGrafik" role="tabpanel" aria-labelledby="pills-raportGrafik-tab">
                            <div id="chart-container"></div>

                            <?php
                            // Get artist and accounting period from form submission
                            if (isset($_POST['artistii']) && isset($_POST['perioda'])) {
                                $artistii = $_POST['artistii'];
                                $perioda = $_POST['perioda'];

                                // Query the database for total revenue by accounting period
                                $query = "SELECT AccountingPeriod, SUM(RevenueUSD) AS TotalRevenue FROM platformat_2 WHERE Emri = '$artistii' GROUP BY AccountingPeriod";
                                $result = $conn->query($query);

                                // Create an array to store the data for the chart
                                $chartData = array();
                                while ($row = mysqli_fetch_array($result)) {
                                    $chartData[] = array($row['AccountingPeriod'], intval($row['TotalRevenue']));
                                }
                            }
                            ?>

                            <!-- Render the chart using Highcharts -->
                            <script src="https://code.highcharts.com/highcharts.js"></script>
                            <script>
                                // Create Highcharts chart
                                Highcharts.chart('chart-container', {
                                    tooltip: {
                                        enabled: false
                                    },
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: 'Total Revenue by Accounting Period'
                                    },
                                    xAxis: {
                                        type: 'category',
                                        title: {
                                            text: 'Accounting Period'
                                        }
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Total Revenue (USD)'
                                        }
                                    },
                                    series: [{
                                        name: 'Total Revenue',
                                        data: <?php echo json_encode($chartData); ?>,
                                        dataLabels: {
                                            enabled: true,
                                            format: '{point.y:.2f} USD',
                                            style: {
                                                fontSize: '14px',
                                                fontFamily: 'Arial, sans-serif'
                                            }
                                        }
                                    }]

                                });
                            </script>



                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


























<?php include 'partials/footer.php'; ?>
<script>
    function copyRevenue(period) {
        const revenue = document.querySelector(`#revenue-${period}`).textContent;
        navigator.clipboard.writeText(revenue);
    }
</script>
<script>
    var copyButtons = document.querySelectorAll('.copy-button');
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var period = button.getAttribute('data-period');
            var revenueSum = document.getElementById(period).textContent.trim();
            navigator.clipboard.writeText(revenueSum);
        });
    });
</script>


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
    // Add event listener to the select element
    document.getElementById('my-select').addEventListener('change', function() {
        // Get the selected option value
        var selectedOption = this.options[this.selectedIndex].value;
        // Set the selected option value as the value of the hidden input field
        document.getElementById('selected_option').value = selectedOption;
    });
</script>

<script>
    // Get references to the two selects
    const artistiiSelect = document.getElementById('artistii');
    const periodaSelect = document.getElementById('perioda');

    // Define a function to fetch the options for the second select
    function updatePeriodaOptions() {
        // Get the value of the first select
        const artistii = artistiiSelect.value;

        // Fetch the options for the second select based on the value of the first select
        // You can use AJAX to make a request to the server and fetch the options dynamically
        // For simplicity, this example uses PHP to fetch the options from the server and embed them in the page
        const url = 'fetch-perioda-options.php?artistii=' + encodeURIComponent(artistii);
        fetch(url)
            .then(response => response.text())
            .then(optionsHtml => {
                periodaSelect.innerHTML = optionsHtml;
            })
            .catch(error => console.error(error));
    }

    // Attach the function to the change event of the first select
    artistiiSelect.addEventListener('change', updatePeriodaOptions);

    // Initialize the options for the second select based on the initial value of the first select
    updatePeriodaOptions();
</script>