<?php
error_reporting(0);
ini_set('display_errors', 0);
include 'partials/header.php';
include 'conn-d.php';
// $gsta = $conn->query("SELECT * FROM klientet WHERE youtube='$selectedChannel'");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['channel'])) {
    $selectedChannel = $_POST['channel'];
    $gsta = $conn->query("SELECT * FROM klientet WHERE youtube='$selectedChannel'");

    // Fetch the refresh token for the selected channel
    $sql = "SELECT refresh_token FROM youtube_refresh_tokens WHERE channel_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $selectedChannel);
    $stmt->execute();
    $stmt->bind_result($refreshToken);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    // Use the refresh token to fetch the metrics
    require_once __DIR__ . '/google-api/vendor/autoload.php';
    $client = new Google_Client();
    $client->setApplicationName('API code samples');
    $client->setScopes([
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/yt-analytics.readonly',
        'https://www.googleapis.com/auth/yt-analytics-monetary.readonly',
        'https://www.googleapis.com/auth/youtube.upload'

    ]);
    $client->setAuthConfig('client.json');
    $client->setAccessType('offline');
    $client->refreshToken($refreshToken);








    $service = new Google_Service_YouTubeAnalytics($client);
    $queryParams = [
        'currency' => 'EUR',
        'dimensions' => 'day',
        'endDate' => date('Y-m-d'),
        'ids' => 'channel==' . $selectedChannel,
        'metrics' => 'estimatedRevenue',
        'startDate' => '2006-01-01'
    ];
    $response = $service->reports->query($queryParams);
    $data = array();
    foreach ($response->rows as $row) {
        $data[] = array(
            'date' => $row[0],
            'revenue' => $row[1],
        );
    }
    $service = new Google_Service_YouTube($client);
    $channel = $service->channels->listChannels('snippet', array('id' => $selectedChannel));
    $channelThumbnail = $channel['items'][0]['snippet']['thumbnails']['default']['url'];

    foreach ($channel['items'] as $item) {
        $channelTitle = $item['snippet']['title'];
        $channels[] = $channelTitle;
    }

    // Display the channel names
    foreach ($channels as $channel) {
    }

    // Display the metrics data
    ?>
    <style>
        .selected-row {
            background-color: #e2e2e2;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        option.select-hr {
            border-bottom: 1px dotted #000;
        }
    </style>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="container">
                    <div class="p-5 mb-4 card rounded-5 shadow-sm">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-5 shadow-sm active border" style="text-transform: none;" id="pills-teArdhurat-tab" data-bs-toggle="pill" data-bs-target="#pills-teArdhurat" type="button" role="tab" aria-controls="pills-teArdhurat" aria-selected="true">T&euml;
                                    ardhurat</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-5 shadow-sm border" style="text-transform: none;" id="pills-Video-tab" data-bs-toggle="pill" data-bs-target="#pills-Video" type="button" role="tab" aria-controls="pills-Video" aria-selected="true">
                                    Ngarko video
                                </button>
                            </li>

                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-teArdhurat" role="tabpanel" aria-labelledby="pills-teArdhurat-tab" tabindex="0">
                                <div class="row">
                                    <div class="filter-container rounded-3 my-2 ">
                                        <div>
                                            <label for="filter" class="form-label">Filtro</label>
                                            <select id="filter" class="form-select shadow-sm rounded-5">
                                                <option value="7">7 ditet e fundit</option>
                                                <option value="28">28 ditet e fundit</option>
                                                <option value="90">90 ditet e fundit</option>
                                                <option value="365">365 ditet e fundit</option>
                                                <option value="lifetime">Lifetime</option>
                                                <option disabled>──────────</option>
                                                <option value="2023">2023</option>
                                                <option value="2022">2022</option>
                                            </select>
                                            <br>
                                            <!-- <span id="dateRange" style="color: grey; font-size: 14px;"></span> -->
                                            <p id="dateRangeDisplay"></p>

                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <button type="button" class="btn btn-primary shadow-sm rounded-5 text-white" style="text-transform: none;" data-bs-toggle="modal" data-bs-target="#customRangeModal">
                                                <i class="fi fi-rr-calendar-lines-pen"></i> Zgjedhje e personalizuar
                                            </button>
                                            <div class="modal fade" id="customRangeModal" tabindex="-1" aria-labelledby="customRangeModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="customRangeModalLabel">Zgjedhje e personalizuar
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form>
                                                                <div class="mb-3">
                                                                    <label for="startDateInput" class="form-label">
                                                                        Data e fillimit
                                                                    </label>
                                                                    <input type="date" class="form-control" id="startDateInput" pattern="\d{4}-\d{2}-\d{2}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="endDateInput" class="form-label">Data e mbarimit</label>
                                                                    <input type="date" class="form-control" id="endDateInput" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}">
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="button" class="btn btn-primary" id="applyCustomRangeBtn">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button id="exportButton" class="btn btn-primary shadow-sm rounded-5 text-white" style="text-transform: none;">
                                                <i class="fi fi-rr-file-excel"></i> Eksporto n&euml; Excel
                                            </button>
                                            <button id="exportButton" class="btn btn-primary shadow-sm rounded-5 text-white" style="text-transform: none;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                <i class="fi fi-rr-add-document"></i> Krijo fatur&euml;
                                            </button>
                                            <div class="modal fade fade-up" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Krijo fatur&euml;</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form method="POST" action="faturYoutube.php">
                                                                <label for="emri">Emri & Mbiemri</label>

                                                                <select name="emri" class="form-select shadow-sm rounded-5 my-2">
                                                                    <?php
                                                                    while ($gst = mysqli_fetch_array($gsta)) {
                                                                    ?>
                                                                        <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?></option>
                                                                    <?php } ?>

                                                                </select>



                                                                <label for="datas">Data:</label>
                                                                <input type="text" name="data" class="form-control shadow-sm rounded-5 my-2" value="<?php echo date("Y-m-d"); ?>">


                                                                <label for="imei">Fatura:</label>

                                                                <input type="text" name="fatura" class="form-control shadow-sm rounded-5 my-2" value="<?php echo date('dmYhis'); ?>" readonly>

                                                                <label for="gjendjaFatures">Zgjidhni gjendjen e
                                                                    fatur&euml;s:</label>
                                                                <select name="gjendjaFatures" id="gjendjaFatures" class="form-select shadow-sm rounded-5 my-2">
                                                                    <option value="Rregullt">Rregullt</option>
                                                                    <option value="Pa rregullt">Pa rregullt</option>
                                                                </select>

                                                                <label for="imei">Totali Youtube:</label>

                                                                <input type="text" name="totaliYoutube" id="totaliYoutube" class="form-control shadow-sm rounded-5 my-2" value="" readonly>


                                                                <label for="emertimi" class="form-label">Emertimi:</label>

                                                                <input type="text" name="emertimi" id="emertimi" class="form-control shadow-sm rounded-5 my-2" value="" readonly>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary rounded-5 shadow-sm" style="text-transform: none" data-bs-dismiss="modal">Mbylle</button>
                                                            <input type="submit" style="text-transform: none" class="btn btn-primary rounded-5 shadow-sm text-white" name="ruaj" value="Ruaj">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-lg-8 col-md-12 py-5 shadow-sm rounded-5" style="border:1px solid lightgrey;">
                                        <canvas id="myChart"></canvas>
                                    </div>
                                    <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
                                        <div class="table-responsive">
                                            <table class="table w-100 table-bordered" style="border:1px solid lightgrey;">
                                                <tbody>
                                                    <tr>
                                                        <th>Muaji</th>
                                                        <td>
                                                            <p id="muaji"></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Totali</th>
                                                        <td>
                                                            <p id="totalRevenue"></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Totali i zbritur nga perqindja 0.03</th>
                                                        <td>
                                                            <p id="totalRevenueWithPrecentage"></p>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <div class="table-responsive">
                                            <table class="table w-100 table-bordered" style="border:1px solid lightgrey;">
                                                <tbody>
                                                    <tr>
                                                        <th>Emri i kanalit</th>
                                                        <td>
                                                            <?php echo $channelTitle; ?>
                                                        </td>
                                                    </tr>


                                                    <tr>
                                                        <th>ID kanalit</th>
                                                        <td style="word-wrap: break-word;">
                                                            <?php echo $selectedChannel; ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade show" id="pills-Video" role="tabpanel" aria-labelledby="pills-Video-tab" tabindex="0">
                                <form action="https://www.youtube.com/upload" method="post" enctype="multipart/form-data">
                                    <div class="tab-pane fade show" id="pills-Video" role="tabpanel" aria-labelledby="pills-Video-tab" tabindex="0">
                                        <label for="video-file">Upload Video:</label>
                                        <input type="file" id="video-file" name="video" accept="video/*">
                                    </div>
                                    <button type="submit">Upload</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
<?php
} else {
    // Display the channel selection form page
    header('Location: channel_selection.php');
    exit;
}
?>

<?php
include 'partials/footer.php'; ?>
<script>
    // Merrim t&euml; dh&euml;nat nga PHP dhe i formatojm&euml; si nj&euml; varg JavaScript
    var data = <?php echo json_encode($data); ?>;

    // Ekstraktojme vlerat 'date' dhe 'revenue' nga vargu data
    var dates = data.map(function(item) {
        return item.date;
    });

    var revenues = data.map(function(item) {
        return item.revenue;
    });

    // Variablat p&euml;r t&euml; dh&euml;nat e filtruara
    var filteredDates = [];
    var filteredRevenues = [];

    // Krijo nj&euml; grafik t&euml; ri duke p&euml;rdorur Chart.js
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart;

    function filterData(value) {
        filteredDates = [];
        filteredRevenues = [];
        var totalRevenue = 0; // Variab&euml;l p&euml;r t&euml; ruajtur t&euml; ardhurat totale

        if (value === 'all') {
            filteredDates = dates;
            filteredRevenues = revenues;
        } else if (value === 'lifetime') {
            filteredDates = dates;
            filteredRevenues = revenues;
        } else if (value === 'year' || value === '2023' || value === '2022' || value === '2021' || value === '2020' || value === '2019' || value === '2018' || value === '2017' || value === '2016' || value === '2015') {
            var selectedYear = value;

            filteredDates = [];
            filteredRevenues = [];

            for (var i = 0; i < dates.length; i++) {
                var currentYearFromData = dates[i].split('-')[0]; // Ekstrakto vitin nga data n&euml; formatin "YYYY-MM-DD"

                if (currentYearFromData === selectedYear) {
                    filteredDates.push(dates[i]);
                    filteredRevenues.push(revenues[i]);
                }
            }
        } else if (value === 'custom') {
            var startDateInput = document.getElementById('startDateInput');
            var endDateInput = document.getElementById('endDateInput');
            var startDate = startDateInput.value;
            var endDate = endDateInput.value;

            if (startDate && endDate) {
                for (var i = 0; i < dates.length; i++) {
                    var currentDate = dates[i];
                    if (currentDate >= startDate && currentDate <= endDate) {
                        filteredDates.push(dates[i]);
                        filteredRevenues.push(revenues[i]);
                        totalRevenue += parseFloat(revenues[i]); // Kumulo t&euml; ardhurat
                    }
                }
            }
        } else {
            var selectedValue = value.split(' ')[0];
            var selectedRange = parseInt(selectedValue);

            if (!isNaN(selectedRange)) {
                var startDate = new Date();
                startDate.setDate(startDate.getDate() - selectedRange);

                for (var i = 0; i < dates.length; i++) {
                    var currentDate = new Date(dates[i]);
                    if (currentDate >= startDate) {
                        filteredDates.push(dates[i]);
                        filteredRevenues.push(revenues[i]);
                        totalRevenue += parseFloat(revenues[i]); // Kumulo t&euml; ardhurat
                    }
                }
            } else {
                var selectedMonth = selectedValue;
                var selectedYear = value.split(' ')[1];

                var currentMonth = new Date().getMonth();
                var currentYear = new Date().getFullYear();

                if (
                    parseInt(selectedYear) > currentYear ||
                    (parseInt(selectedYear) === currentYear && months.indexOf(selectedMonth) > currentMonth)
                ) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'P&euml;rzgjedhje e pavlefshme',
                        text: 'Ju keni zgjedhur nj&euml; muaj t&euml; ardhsh&euml;m.',
                    });
                } else {
                    for (var i = 0; i < dates.length; i++) {
                        var currentDate = new Date(dates[i]);
                        var currentMonthName = months[currentDate.getMonth()];
                        var currentYearName = currentDate.getFullYear().toString();

                        if (currentMonthName === selectedMonth && currentYearName === selectedYear) {
                            filteredDates.push(dates[i]);
                            filteredRevenues.push(revenues[i]);
                            totalRevenue += parseFloat(revenues[i]); // Kumulo t&euml; ardhurat
                        }
                    }
                }
            }
        }

        if (chart) {
            chart.data.labels = filteredDates;
            chart.data.datasets[0].data = filteredRevenues;
            chart.update();
        } else {
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: filteredDates,
                    datasets: [{
                        label: 'T&euml; ardhurat',
                        data: filteredRevenues,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // P&euml;rdit&euml;so treguesin e t&euml; ardhurave totale
        var totalRevenueElement = document.getElementById('totalRevenue');
        var selectedMonth = filterSelect.value.split(' ')[0];
        var selectedYear = filterSelect.value.split(' ')[1];
        var monthName = months[months.indexOf(selectedMonth)];
        totalRevenueElement.textContent = totalRevenue.toFixed(2); // Formatizo t&euml; ardhurat totale me 2 shifra dhjetore

        var muajiElement = document.getElementById('muaji');
        muajiElement.textContent = monthName; // Vendos vler&euml;n e elementit "muaji" me emrin e muajit

        var totalRevenueWithPercentageElement = document.getElementById('totalRevenueWithPrecentage');
        var totalRevenueWithPercentage = totalRevenue - (totalRevenue * 0.02);
        totalRevenueWithPercentageElement.textContent = totalRevenueWithPercentage.toFixed(2); // Vendos vler&euml;n e elementit "totalRevenueWithPrecentage" me vler&euml;n e llogaritur

        var totaliYoutubeInput = document.getElementById('totaliYoutube');
        totaliYoutubeInput.value = totalRevenue.toFixed(2); // Vendos vler&euml;n e fush&euml;s s&euml; hyrjes "totaliYoutube" me t&euml; ardhurat totale

        var emertimiInput = document.getElementById('emertimi');
        emertimiInput.value = monthName; // Vendos vler&euml;n e fush&euml;s s&euml; hyrjes "emertimi" me emrin e muajit
    }

    var filterSelect = document.getElementById('filter');
    filterSelect.addEventListener('change', function() {
        var selectedValue = this.value;
        filterData(selectedValue);
    });

    // Krijimi i ngjarjes p&euml;r butonin "Zbato" n&euml; modal
    var applyCustomRangeBtn = document.getElementById('applyCustomRangeBtn');
    applyCustomRangeBtn.addEventListener('click', function() {
        var startDateInput = document.getElementById('startDateInput');
        var endDateInput = document.getElementById('endDateInput');
        var startDate = startDateInput.value;
        var endDate = endDateInput.value;

        if (startDate && endDate) {
            for (var i = 0; i < dates.length; i++) {
                var currentDate = dates[i];
                if (currentDate >= startDate && currentDate <= endDate) {
                    filteredDates.push(dates[i]);
                    filteredRevenues.push(revenues[i]);
                    totalRevenue += parseFloat(revenues[i]); // Kumulo t&euml; ardhurat
                }
            }

            // Thirrja e funksionit filterData p&euml;r t&euml; p&euml;rdit&euml;suar grafikun dhe elementet e tjera
            filterData('custom');
        }

        // Mbyllja e modalit
        var customRangeModal = new bootstrap.Modal(document.getElementById('customRangeModal'));
        customRangeModal.hide();
    });

    // Shto opsione p&euml;r t&euml; gjith&euml; muajt
    var monthsSelect = document.getElementById('filter');
    var currentYear = new Date().getFullYear();
    var currentMonth = new Date().getMonth();
    var months = [
        'Janar', 'Shkurt', 'Mars', 'Prill', 'Maj', 'Qershor',
        'Korrik', 'Gusht', 'Shtator', 'Tetor', 'N&euml;ntor', 'Dhjetor'
    ];

    var monthIndex = currentMonth; // Set the month index to the current month
    if (currentMonth === 0) { // If current month is January
        previousMonth = 11; // Set previous month to December
        previousYear = currentYear - 1; // Previous year is the current year minus 1
    } else {
        previousMonth = currentMonth - 1; // Subtract 1 from current month to get previous month
        previousYear = currentYear; // Previous year is the same as current year 1200 metra katror
    }

    for (var i = 0; i < months.length; i++) {
        var monthOption = document.createElement('option');
        monthOption.value = months[i] + ' ' + currentYear;

        // Kontrollojm&euml; n&euml;se muaji &euml;sht&euml; n&euml; t&euml; ardhmen
        if (currentYear === currentYear && i > currentMonth) {
            monthOption.textContent = '\u26A0 ' + months[i] + ' ' + currentYear; // Karakteri Unicode p&euml;r simbolin e paralajm&euml;rimit
            monthOption.style.color = 'red'; // Stilo opsionin me ngjyr&euml; t&euml; kuqe
        } else {
            monthOption.textContent = months[i] + ' ' + currentYear;
        }

        monthsSelect.appendChild(monthOption);
    }

    var totalRevenue = filteredRevenues.reduce(function(accumulator, currentValue) {
        return accumulator + parseFloat(currentValue);
    }, 0);

    var totalRevenueElement = document.getElementById('totalRevenue');
    totalRevenueElement.textContent = 'T&euml; ardhurat totale: ' + totalRevenue.toFixed(2) + ' EUR';
    monthsSelect.appendChild(monthOption);


    filterData(months[previousMonth] + ' ' + currentYear);

    var monthNames = [
        'Jan', 'Shk', 'Mar', 'Pri', 'Maj', 'Qer',
        'Kor', 'Gus', 'Sht', 'Tet', 'N&euml;n', 'Dhj'
    ];

    function formatDate(date) {
        var day = String(date.getDate()).padStart(2, '0');
        var month = monthNames[date.getMonth()];
        var year = date.getFullYear();
        return day + '/' + month + '/' + year;
    }

    var applyCustomRangeBtn = document.getElementById('applyCustomRangeBtn');
    applyCustomRangeBtn.addEventListener('click', function() {
        var startDateInput = document.getElementById('startDateInput');
        var endDateInput = document.getElementById('endDateInput');
        var startDate = startDateInput.value;
        var endDate = endDateInput.value;

        if (startDate && endDate) {
            // Clear the filtered data before applying the custom range
            filteredDates = [];
            filteredRevenues = [];
            totalRevenue = 0;

            for (var i = 0; i < dates.length; i++) {
                var currentDate = dates[i];
                if (currentDate >= startDate && currentDate <= endDate) {
                    filteredDates.push(dates[i]);
                    filteredRevenues.push(revenues[i]);
                    totalRevenue += parseFloat(revenues[i]); // Accumulate the revenue
                }
            }

            // Update the select element to "custom"
            filterSelect.value = 'custom';

            // Call the filterData function to update the chart and other elements
            filterData('custom');

            // Display the selected date range as a paragraph
            var dateRangeDisplay = document.getElementById('muaji');
            dateRangeDisplay.textContent = startDate + ' - ' + endDate;

            // Close the custom date range modal after 1 second
            var customRangeModal = new bootstrap.Modal(document.getElementById('customRangeModal'));
            customRangeModal.hide();

        }
    });

    // Add the hide.bs.modal event listener to reset the form fields when the modal is closed
    var customRangeModal = document.getElementById('customRangeModal');
    customRangeModal.addEventListener('hide.bs.modal', function() {
        var startDateInput = document.getElementById('startDateInput');
        var endDateInput = document.getElementById('endDateInput');
        startDateInput.value = '';
        endDateInput.value = '';
    });
</script>




<script>
    $(document).ready(function() {
        var table = $('#example').DataTable({
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
                className: 'btn btn-light border shadow-2 me-2'
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabel&euml;n',
                className: 'btn btn-light border shadow-2 me-2'
            }, ],
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
                this.api()
                    .columns()
                    .every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color']
        });
    });
</script>