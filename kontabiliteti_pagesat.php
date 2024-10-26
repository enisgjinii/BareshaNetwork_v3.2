<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'partials/header.php';
include 'conn-d.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="kontabiliteti_pagesat.php" class="text-reset" style="text-decoration: none;">
                            Pagesat e kryera
                        </a>
                    </li>
                </ol>
            </nav>

            <!-- Summary Section -->
            <div class="row my-3">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5>Total Pagesat Personale: <span id="totalPersonalPayments">0.00</span></h5>
                        <h5>Numri i Transaksioneve: <span id="totalPersonalTransactions">0</span></h5>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5>Total Pagesat Biznes: <span id="totalBiznesPayments">0.00</span></h5>
                        <h5>Numri i Transaksioneve: <span id="totalBiznesTransactions">0</span></h5>
                    </div>
                </div>
            </div>

            <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content;" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5 active" id="pills-pagesat_pers-tab" data-bs-toggle="pill" data-bs-target="#pills-pagesat_pers" type="button" role="tab" aria-controls="pills-pagesat_pers" aria-selected="true" style="text-decoration: none;text-transform: none;">Pagesat e kryera personale</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-pagesat_biz-tab" data-bs-toggle="pill" data-bs-target="#pills-pagesat_biz" type="button" role="tab" aria-controls="pills-pagesat_biz" aria-selected="false" style="text-decoration: none;text-transform: none;">Pagesat e kryera biznes</button>
                </li>
            </ul>
            <div class="tab-content text-dark" id="pills-tabContent">
                <!-- Personal Payments Tab -->
                <div class="tab-pane fade show active" id="pills-pagesat_pers" role="tabpanel" aria-labelledby="pills-pagesat_pers-tab" tabindex="0">
                    <div class="card p-5">
                        <div class="row">
                            <div class="col-12">
                                <!-- Advanced Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="minDate">Prej:</label>
                                        <input type="text" id="minDate" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="maxDate">Deri:</label>
                                        <input type="text" id="maxDate" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="clientName">Emri i Klientit:</label>
                                        <select id="clientName" class="form-select shadow-sm rounded-5">
                                            <option value="">Të gjithë</option>
                                            <!-- Options will be populated via AJAX -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="bankInfo">Banka:</label>
                                        <select id="bankInfo" class="form-select shadow-sm rounded-5">
                                            <option value="">Të gjithë</option>
                                            <!-- Options will be populated via AJAX -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="paymentType">Lloji i Pagesës:</label>
                                        <select id="paymentType" class="form-select shadow-sm rounded-5">
                                            <option value="">Të gjithë</option>
                                            <!-- Options will be populated via AJAX -->
                                        </select>
                                    </div>
                                </div>
                                <button id="filterDate" class="input-custom-css px-3 py-2 mb-3"> <i class="fi fi-rr-filter"></i> Filtro</button>
                                <!-- DataTable -->
                                <div class="table-responsive text-dark">
                                    <table id="paymentsTablePersonal" class="table table-bordered w-100 text-dark">
                                        <thead class="table-light text-dark">
                                            <tr class="text-dark">
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Emri i klientit</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">ID e faturës</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Vlera</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Data</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Banka</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Lloji</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Përshkrimi</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Shuma e përgjithshme pas %</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-dark">
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Chart -->
                                <canvas id="personalPaymentsChart" class="mt-5"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Business Payments Tab -->
                <div class="tab-pane fade" id="pills-pagesat_biz" role="tabpanel" aria-labelledby="pills-pagesat_biz-tab" tabindex="0">
                    <div class="card p-5 text-dark">
                        <div class="row">
                            <div class="col-12">
                                <!-- Advanced Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="minDateBiz">Prej:</label>
                                        <input type="text" id="minDateBiz" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="maxDateBiz">Deri:</label>
                                        <input type="text" id="maxDateBiz" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="clientNameBiz">Emri i Klientit:</label>
                                        <select id="clientNameBiz" class="form-select shadow-sm rounded-5">
                                            <option value="">Të gjithë</option>
                                            <!-- Options will be populated via AJAX -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="bankInfoBiz">Banka:</label>
                                        <select id="bankInfoBiz" class="form-select shadow-sm rounded-5">
                                            <option value="">Të gjithë</option>
                                            <!-- Options will be populated via AJAX -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="paymentTypeBiz">Lloji i Pagesës:</label>
                                        <select id="paymentTypeBiz" class="form-select shadow-sm rounded-5">
                                            <option value="">Të gjithë</option>
                                            <!-- Options will be populated via AJAX -->
                                        </select>
                                    </div>
                                </div>
                                <button id="filterDateBiz" class="input-custom-css px-3 py-2 mb-3"> <i class="fi fi-rr-filter"></i> Filtro</button>
                                <!-- DataTable -->
                                <div class="table-responsive">
                                    <table id="paymentsTableBiznes" class="table table-bordered w-100 text-dark">
                                        <thead class="table-light text-dark">
                                            <tr class="text-dark">
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Emri i klientit</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">ID e faturës</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Vlera</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Data</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Banka</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Lloji</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Përshkrimi</th>
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Shuma e përgjithshme pas %</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-dark">
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Chart -->
                                <canvas id="biznesPaymentsChart" class="mt-5"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tooltips -->
        <div class="d-none" id="tooltips">
            <div id="tooltipClientName">Zgjidhni emrin e klientit për të filtruar.</div>
            <div id="tooltipBankInfo">Zgjidhni bankën për të filtruar.</div>
            <div id="tooltipPaymentType">Zgjidhni llojin e pagesës për të filtruar.</div>
        </div>
    </div>
</div>
<?php
include 'partials/footer.php';
?>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize date pickers
    flatpickr("#minDate", {
        dateFormat: "Y-m-d",
        locale: "sq"
    });
    flatpickr("#maxDate", {
        dateFormat: "Y-m-d",
        locale: "sq",
        maxDate: "today"
    });
    flatpickr("#minDateBiz", {
        dateFormat: "Y-m-d",
        locale: "sq"
    });
    flatpickr("#maxDateBiz", {
        dateFormat: "Y-m-d",
        locale: "sq",
        maxDate: "today"
    });

    $(document).ready(function() {
        var date = new Date();
        var today = date.getFullYear() + "-" + ('0' + (date.getMonth() + 1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2);

        // Fetch filter options for Personal Payments
        $.ajax({
            url: 'fetch_filter_options.php',
            method: 'GET',
            data: {
                type: 'personal'
            },
            dataType: 'json',
            success: function(data) {
                // Populate Client Name options
                $.each(data.client_names, function(key, value) {
                    $('#clientName').append('<option value="' + value + '">' + value + '</option>');
                });
                // Populate Bank Info options
                $.each(data.bank_infos, function(key, value) {
                    $('#bankInfo').append('<option value="' + value + '">' + value + '</option>');
                });
                // Populate Payment Type options
                $.each(data.payment_types, function(key, value) {
                    $('#paymentType').append('<option value="' + value + '">' + value + '</option>');
                });
            }
        });

        // Fetch filter options for Biznes Payments
        $.ajax({
            url: 'fetch_filter_options.php',
            method: 'GET',
            data: {
                type: 'biznes'
            },
            dataType: 'json',
            success: function(data) {
                // Populate Client Name options
                $.each(data.client_names, function(key, value) {
                    $('#clientNameBiz').append('<option value="' + value + '">' + value + '</option>');
                });
                // Populate Bank Info options
                $.each(data.bank_infos, function(key, value) {
                    $('#bankInfoBiz').append('<option value="' + value + '">' + value + '</option>');
                });
                // Populate Payment Type options
                $.each(data.payment_types, function(key, value) {
                    $('#paymentTypeBiz').append('<option value="' + value + '">' + value + '</option>');
                });
            }
        });

        // Initialize DataTable for Personal Payments
        var table = $('#paymentsTablePersonal').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "fetch_payments_personal.php",
                "type": "POST",
                "data": function(d) {
                    d.minDate = $('#minDate').val();
                    d.maxDate = $('#maxDate').val();
                    d.clientName = $('#clientName').val();
                    d.bankInfo = $('#bankInfo').val();
                    d.paymentType = $('#paymentType').val();
                },
                "dataSrc": function(json) {
                    // Update Summary Section
                    $('#totalPersonalPayments').text(parseFloat(json.totalPayments).toFixed(2));
                    $('#totalPersonalTransactions').text(json.totalTransactions);
                    // Update Chart
                    updatePersonalChart(json.chartData);
                    return json.data;
                },
                "error": function(xhr, error, thrown) {
                    console.log("Error:", error);
                    console.log("Response:", xhr.responseText);
                    alert("An error occurred while fetching data: " + thrown);
                }
            },
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                filename: "kontabilitet_personal_" + today
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                filename: "kontabilitet_personal_" + today
            }, {
                extend: "copyHtml5",
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: "Kopjo tabelen ne formatin Clipboard",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "kontabilitet_personal_" + today
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabelën',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                filename: "kontabilitet_personal_" + today
            }, ],
            initComplete: function() {
                var btns = $(".dt-buttons");
                btns.addClass("").removeClass("dt-buttons btn-group");
                var lengthSelect = $("div.dataTables_length select");
                lengthSelect.addClass("form-select");
                lengthSelect.css({
                    width: "auto",
                    margin: "0 8px",
                    padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                    lineHeight: "1.5",
                    border: "1px solid #ced4da",
                    borderRadius: "0.25rem",
                });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
            "columns": [{
                    "data": "client_name",
                    "orderable": true
                },
                {
                    "data": "invoice_id",
                    "orderable": true
                },
                {
                    "data": "payment_amount",
                    "orderable": true
                },
                {
                    "data": "payment_date",
                    "orderable": true
                },
                {
                    "data": "bank_info",
                    "orderable": true
                },
                {
                    "data": "type_of_pay",
                    "orderable": true
                },
                {
                    "data": "description",
                    "orderable": true
                },
                {
                    "data": "total",
                    "orderable": true
                }
            ],
            "pageLength": 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
            responsive: true
        });

        // Filter button click event
        $('#filterDate').on('click', function() {
            table.draw();
        });

        // Charts
        var personalChart;

        function updatePersonalChart(chartData) {
            if (personalChart) {
                personalChart.destroy();
            }
            var ctx = document.getElementById('personalPaymentsChart').getContext('2d');
            personalChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.dates,
                    datasets: [{
                        label: 'Total Pagesat Personale',
                        data: chartData.amounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Data'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Vlera'
                            }
                        }
                    }
                }
            });
        }

        // Initialize DataTable for Biznes Payments
        var table2 = $('#paymentsTableBiznes').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "fetch_payments_biznes.php",
                "type": "POST",
                "data": function(d) {
                    d.minDateBiz = $('#minDateBiz').val();
                    d.maxDateBiz = $('#maxDateBiz').val();
                    d.clientNameBiz = $('#clientNameBiz').val();
                    d.bankInfoBiz = $('#bankInfoBiz').val();
                    d.paymentTypeBiz = $('#paymentTypeBiz').val();
                },
                "dataSrc": function(json) {
                    // Update Summary Section
                    $('#totalBiznesPayments').text(parseFloat(json.totalPayments).toFixed(2));
                    $('#totalBiznesTransactions').text(json.totalTransactions);
                    // Update Chart
                    updateBiznesChart(json.chartData);
                    return json.data;
                },
                "error": function(xhr, error, thrown) {
                    console.log("Error:", error);
                    console.log("Response:", xhr.responseText);
                    alert("An error occurred while fetching data: " + thrown);
                }
            },
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                filename: "kontabilitet_biznes_" + today
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                filename: "kontabilitet_biznes_" + today
            }, {
                extend: "copyHtml5",
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: "Kopjo tabelen ne formatin Clipboard",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "kontabilitet_biznes_" + today
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabelën',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                filename: "kontabilitet_biznes_" + today
            }, ],
            initComplete: function() {
                var btns = $(".dt-buttons");
                btns.addClass("").removeClass("dt-buttons btn-group");
                var lengthSelect = $("div.dataTables_length select");
                lengthSelect.addClass("form-select");
                lengthSelect.css({
                    width: "auto",
                    margin: "0 8px",
                    padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                    lineHeight: "1.5",
                    border: "1px solid #ced4da",
                    borderRadius: "0.25rem",
                });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
            "columns": [{
                    "data": "client_name",
                    "orderable": true
                },
                {
                    "data": "invoice_id",
                    "orderable": true
                },
                {
                    "data": "payment_amount",
                    "orderable": true
                },
                {
                    "data": "payment_date",
                    "orderable": true
                },
                {
                    "data": "bank_info",
                    "orderable": true
                },
                {
                    "data": "type_of_pay",
                    "orderable": true
                },
                {
                    "data": "description",
                    "orderable": true
                },
                {
                    "data": "total",
                    "orderable": true
                }
            ],
            "pageLength": 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
            responsive: true
        });

        // Filter button click event
        $('#filterDateBiz').on('click', function() {
            table2.draw();
        });

        // Charts
        var biznesChart;

        function updateBiznesChart(chartData) {
            if (biznesChart) {
                biznesChart.destroy();
            }
            var ctx = document.getElementById('biznesPaymentsChart').getContext('2d');
            biznesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.dates,
                    datasets: [{
                        label: 'Total Pagesat Biznes',
                        data: chartData.amounts,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)', // Adjust color as needed
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Data'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Vlera'
                            }
                        }
                    }
                }
            });
        }

        // Tooltips
        $('#clientName').tooltip({
            title: $('#tooltipClientName').html(),
            placement: 'top'
        });
        $('#bankInfo').tooltip({
            title: $('#tooltipBankInfo').html(),
            placement: 'top'
        });
        $('#paymentType').tooltip({
            title: $('#tooltipPaymentType').html(),
            placement: 'top'
        });
    });
</script>