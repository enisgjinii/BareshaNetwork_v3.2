<?php
include 'partials/header.php';
// Connect to the database
include 'conn-d.php';
// Fetch total count from the database
$sql = "SELECT COUNT(*) AS count FROM employee_payments";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_count = $row['count'];
} else {
    echo "Error: " . $conn->error; // Echo any database errors
    $total_count = 0; // Set default count if there's an error
}
// Fetch sum of expenses from the database
$sqlExpenses = "SELECT SUM(payment_amount) as sum FROM employee_payments";
$resultExpenses = $conn->query($sqlExpenses);
if ($resultExpenses) {
    $rowExpenses = $resultExpenses->fetch_assoc();
    $total_sum = $rowExpenses['sum'];
} else {
    echo "Error: " . $conn->error; // Echo any database errors
    $total_sum = 0; // Set default sum if there's an error
}

$sqlForShpenzimet = "SELECT COUNT(*) AS count FROM expenses";
$resultForShpenzimet = $conn->query($sqlForShpenzimet);
if ($resultForShpenzimet) {
    $rowForShpenzimet = $resultForShpenzimet->fetch_assoc();
    $total_count_for_shpenzimet = $rowForShpenzimet['count'];
} else {
    echo "Error: " . $conn->error; // Echo any database errors
    $total_count_for_shpenzimet = 0; // Set default count if there's an error
}

$sqlForShpenzimet2 = "SELECT SUM(shuma) as sum FROM expenses";
$resultForShpenzimet2 = $conn->query($sqlForShpenzimet2);
if ($resultForShpenzimet2) {
    $rowForShpenzimet2 = $resultForShpenzimet2->fetch_assoc();
    $total_sum_for_shpenzimet = $rowForShpenzimet2['sum'];
} else {
    echo "Error: " . $conn->error; // Echo any database errors
    $total_sum_for_shpenzimet = 0; // Set default sum if there's an error
}


$sqlForIncomeInYoutubeInvoices = "
    SELECT 
        SUM(total_amount) AS total_cash,
        SUM(total_amount_in_eur) AS total_cash2,
        SUM(total_amount_in_eur_after_percentage) AS total_cash3,
        SUM(total_amount_after_percentage) AS total_cash4,
        SUM(paid_amount) AS total_cash5,
        (SUM(total_amount_after_percentage) - SUM(paid_amount)) AS total_cash_difference
    FROM 
        invoices AS i;
";



$resultForIncomeInYoutubeInvoices = $conn->query($sqlForIncomeInYoutubeInvoices);
if ($resultForIncomeInYoutubeInvoices) {
    $rowForIncomeInYoutubeInvoices = $resultForIncomeInYoutubeInvoices->fetch_assoc();
    $total_sum_for_income_in_youtube_invoices = $rowForIncomeInYoutubeInvoices['total_cash_difference'];
}

$sql_getting_cinc = "SELECT SUM(total_amount) AS total_cash4, SUM(total_amount_after_percentage) AS total_cash5, (SUM(total_amount) - SUM(total_amount_after_percentage)) AS fitimi FROM invoices";
$result_getting_citimi = $conn->query($sql_getting_cinc);
if ($result_getting_citimi) {
    $row_getting_citimi = $result_getting_citimi->fetch_assoc();
    $fitimi = $row_getting_citimi['fitimi'];
}
// Encode data to JSON format
$jsonTotalCount = json_encode((int)$total_count);
$jsonTotalSum = json_encode((int)$total_sum);
$jsonTotalCountForShpenzimet = json_encode((int)$total_count_for_shpenzimet);
$jsonTotalSumForShpenzimet = json_encode((int)$total_sum_for_shpenzimet);
$jsonTotalSumForIncome = json_encode((int)$total_sum_for_income_in_youtube_invoices);
$jsonTotalSumForSell = json_encode((int)$fitimi);
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="pasqyrat.php" class="text-reset" style="text-decoration: none;">Pasqyrat</a>
                    </li>
                </ol>
            </nav>
            <ul class="nav nav-underline" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pagesat_e_punetoreve-tab" data-bs-toggle="tab" data-bs-target="#pagesat_e_punetoreve-tab-pane" type="button" role="tab" aria-controls="pagesat_e_punetoreve-tab-pane" aria-selected="true">
                        Pagesat e punetoreve</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Shpenzimet e objektit</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Tatimet</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pagesatYoutube-tab" data-bs-toggle="tab" data-bs-target="#pagesatYoutube-tab-pane" type="button" role="tab" aria-controls="pagesatYoutube-tab-pane" aria-selected="false">Pagesat e Youtubes</button>
                </li>
            </ul>
            <br /><br />
            <div class="tab-content bg-white p-3 border border-1 rounded-5" id="myTabContent">
                <div class="tab-pane fade show active" id="pagesat_e_punetoreve-tab-pane" role="tabpanel" aria-labelledby="pagesat_e_punetoreve-tab" tabindex="0">
                    <div id="chart" class="w-100"></div>
                    <br /><br />
                    <hr />
                    <div class="text-center">
                        <a href="pagesat_punetor.php" style="text-decoration: none;" class="input-custom-css px-3 py-2">Kalo tek pagesat e puntorve</a>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div id="chart2" class="w-100"></div>
                    <br /><br />
                    <hr />
                    <div class="text-center">
                        <a href="shpenzimet_objekt.php" style="text-decoration: none;" class="input-custom-css px-3 py-2">Kalo tek shpenzimet e objektit</a>
                    </div>
                </div>
                <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">...</div>
                <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">...</div>
                <div class="tab-pane fade" id="pagesatYoutube-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                    <div id="chart3" class="w-100"></div>
                    <br /><br />
                    <div id="chart4" class="w-100"></div>
                    <hr />
                    
                    <div class="text-center">
                        <a href="invoice.php" style="text-decoration: none;" class="input-custom-css px-3 py-2">Kalo tek faturat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php' ?><script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pass PHP data to JavaScript
        var total_count = <?php echo $jsonTotalCount; ?>;
        var total_sum = <?php echo $jsonTotalSum; ?>;
        var total_count_for_shpenzimet = <?php echo $jsonTotalCountForShpenzimet; ?>;
        var total_sum_for_shpenzimet = <?php echo $jsonTotalSumForShpenzimet; ?>;
        var total_sum_for_income = <?php echo $jsonTotalSumForIncome; ?>;
        var total_sum_for_sell = <?php echo $jsonTotalSumForSell; ?>;
        // Highcharts options
        Highcharts.chart('chart', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Përmbledhje e shpenzimeve per pagesat e punetoreve'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)'
                    }
                }
            },
            series: [{
                name: 'Shpenzimet',
                colorByPoint: true,
                data: [{
                    name: 'Numërimi i përgjithshëm',
                    y: total_count
                }, {
                    name: 'Shuma totale',
                    // add euro symbol
                    y: total_sum
                }]
            }]
        });

        Highcharts.chart('chart2', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Përmbledhje e shpenzimeve te objektit'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)'
                    }
                }
            },
            series: [{
                name: 'Shpenzimet',
                colorByPoint: true,
                data: [{
                    name: 'Numërimi i përgjithshëm',
                    y: total_count_for_shpenzimet
                }, {
                    name: 'Shuma totale',
                    // add euro symbol
                    y: total_sum_for_shpenzimet
                }]
            }]
        });

        Highcharts.chart('chart3', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Totali i cili duhet te paguhet ne te gjitha faturat pa valuten EUR'
            },
            tooltip: {
                pointFormat: '{series.name}'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} €'
                    }
                }
            },
            series: [{
                name: 'Shuma e cila duhet te realizohet ne kuader te faturave pa valuten EUR',
                colorByPoint: true,
                data: [{
                    name: 'Shuma totale',
                    // add euro symbol
                    y: total_sum_for_income
                }]
            }]
        });

        Highcharts.chart('chart4', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Totali i fitimit nga faturat pa valuten EUR'
            },
            tooltip: {
                pointFormat: '{series.name}'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} €'
                    }
                }
            },
            series: [{
                name: 'Shuma e fitimit nga faturat pa valuten EUR',
                colorByPoint: true,
                data: [{
                    name: 'Shuma totale',
                    // add euro symbol
                    y: total_sum_for_sell
                }]
            }]
        });
    });
</script>