<?php
include 'partials/header.php';
include 'conn-d.php';
// Grab the value of client and reporting period from the POST parameters
$selectedClient = $_POST['selectedClient'] ?? null;
$reportingPeriod = $_POST['reportingPeriod'] ?? null;
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Platformat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="filtroCSV.php" class="text-reset" style="text-decoration: none;">
                            Lista e te ardhurave nga Platformat
                        </a></li>
            </nav>
            <div class="row mb-3">
                <style>
                    .offcanvas-backdrop.show {
                        opacity: 0.1;
                    }
                </style>
                <div>
                    <a style="text-decoration:none;" href="csvFiles.php" class="input-custom-css px-3 py-2" type="button"><i class="fi fi-rr-file"></i> Ngarko CSV</a>
                    <a style="text-decoration:none;" type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Shiko Guiden per filtrimin e CSV-së
                    </a>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Guida per filtrimin e CSV-së</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="https://www.iorad.com/player/2354784/Guida-per-filtrimin-e-tabeles-se-CSV-se-se-klientit?src=iframe&oembed=1" width="100%" height="500px" style="width: 100%; height: 500px; border-bottom: 1px solid #ccc;" referrerpolicy="strict-origin-when-cross-origin" frameborder="0" webkitallowfullscreen="webkitallowfullscreen" mozallowfullscreen="mozallowfullscreen" allowfullscreen="allowfullscreen" allow="camera; microphone; clipboard-write"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="input-custom-css px-3 py-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filteringoffcanvas" aria-controls="filteringoffcanvas"><i class="fi fi-rr-filter"></i> Filtro</button>
                    <div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="filteringoffcanvas" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="offcanvasRightLabel">Opsionet e filtrimit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form id="filterForm">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="selectClient" class="form-label"> Emri i klientit</label>
                                        <select name="selectClient" id="selectClient" class="form-select shadow-sm rounded-5" style="border: 1px solid #ced4da">
                                            <?php
                                            $get_NameOfClient = "SELECT DISTINCT Emri FROM platformat_2";
                                            $result = $conn->query($get_NameOfClient);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row['Emri'] . "'>" . $row['Emri'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="reportingPeriod" class="form-label">Periudha e raportimit ( Reporting Period )</label>
                                        <select name="reportingPeriod" id="reportingPeriod" class="form-select shadow-sm rounded-5" style="border: 1px solid #ced4da">
                                            <?php
                                            $get_Period = "SELECT DISTINCT ReportingPeriod FROM platformat_2";
                                            $result = $conn->query($get_Period);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row['ReportingPeriod'] . "'>" . $row['ReportingPeriod'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="country" class="form-label">Shteti</label>
                                        <select name="country" id="country" class="form-select shadow-sm rounded-5" style="border: 1px solid #ced4da">
                                            <?php
                                            $get_Period = "SELECT DISTINCT Country FROM platformat_2";
                                            $result = $conn->query($get_Period);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row['Country'] . "'>" . $row['Country'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="splitpayshare" class="form-label">Ndarja e pagesës në pjesën më të madhe</label>
                                        <select name="splitpayshare" id="splitpayshare" class="form-select shadow-sm rounded-5" style="border: 1px solid #ced4da">
                                            <?php
                                            $get_Period = "SELECT DISTINCT SplitPayShare FROM platformat_2";
                                            $result = $conn->query($get_Period);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='" . $row['SplitPayShare'] . "'>" . $row['SplitPayShare'] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div>
                                        <button class="input-custom-css btn-sm rounded-5 shadow-sm mt-3 px-3 py-2" type="submit" value="Filtro">
                                            <i class="fi fi-rr-filter"></i>
                                            Filtro
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="row mt-2">
                    <div class="col-12 card p-5 rounded-5">
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
    <script>
        $(document).ready(function() {
            // Initialize Selectr for the client select
            var selectClient = new Selectr('#selectClient', {
                searchable: true,
                width: 300,
                clearable: false,
            });
            // Initialize Selectr for the reporting period select
            var selectReportingPeriod = new Selectr('#reportingPeriod', {
                searchable: false,
                width: 200,
                clearable: true,
            });
            // Initialize Selectr for the reporting period select
            var selectCountry = new Selectr('#country', {
                searchable: false,
                width: 200,
                clearable: true,
            });
            var selectSplitPayShare = new Selectr('#splitpayshare', {
                searchable: false,
                width: 200,
                clearable: true,
            })
            // Add an event listener for the form submission
            $('#filterForm').submit(function(event) {
                // Prevent the default form submission
                event.preventDefault();
                // Get the selected values
                var selectedClient = selectClient.getValue();
                var selectedReportingPeriod = selectReportingPeriod.getValue();
                var selectedCountry = selectCountry.getValue();
                var selectedSplitPayShare = selectSplitPayShare.getValue();
                // Clear previous results and errors
                $('#displaySelectedClient').empty();
                $('#displaySelectedReportingPeriod').empty();
                $('#totalRevenue').empty();
                $('#errorContainer').empty();
                // Update spans with selected values
                $('#displaySelectedClient').text(selectedClient);
                $('#displaySelectedReportingPeriod').text(selectedReportingPeriod);
                // Reload the DataTable with the selected values
                table.ajax.url('fetch_CSV.php?selectedClient=' + selectedClient + '&reportingPeriod=' + selectedReportingPeriod + '&selectedCountry=' + selectedCountry + '&selectedSplitPayShare=' + selectedSplitPayShare).load();
                // Fetch total revenue from the server
                $.ajax({
                    url: 'fetch_total_revenue.php',
                    method: 'POST',
                    data: {
                        selectedClient: selectedClient,
                        reportingPeriod: selectedReportingPeriod,
                        selectedCountry: selectedCountry,
                        selectedSplitPayShare: selectedSplitPayShare
                    },
                    success: function(response) {
                        // Parse the response as a number
                        var totalRevenue = parseFloat(response);
                        // Check if the parsing was successful
                        if (!isNaN(totalRevenue)) {
                            // Format the number with commas and decimal places
                            var formattedTotalRevenue = totalRevenue.toLocaleString(undefined, {
                                maximumFractionDigits: 2
                            });
                            // Update the total revenue
                            $('#totalRevenue').text(formattedTotalRevenue + ' USD');
                        } else {
                            // Handle the case where parsing fails
                            console.error('Failed to parse total revenue:', response);
                            $('#errorContainer').text('Error: Failed to parse total revenue.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle errors
                        console.error('Error fetching total revenue:', textStatus, errorThrown);
                        $('#errorContainer').text('Error: Failed to fetch total revenue.');
                    }
                });
            });
        });
    </script>
    <script>
        var table = $('#example').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: 'fetch_CSV.php',
                type: 'POST',
                data: function(d) {
                    d.selectedClient = $('#selectClient').val();
                    d.reportingPeriod = $('#reportingPeriod').val();
                    d.selectedCountry = $('#country').val();
                    d.selectedSplitPayShare = $('#splitpayshare').val();
                    d.search.value = $('#example_filter input').val(); // Include search value
                }
            },
            columns: [{
                    data: 'Emri'
                },
                {
                    data: 'Artist'
                },
                {
                    data: 'ReportingPeriod'
                },
                {
                    data: 'AccountingPeriod'
                },
                {
                    data: 'Release'
                },
                {
                    data: 'Track'
                },
                {
                    data: 'Country'
                },
                {
                    data: 'RevenueUSD'
                },
                {
                    data: 'RevenueShare'
                },
                {
                    data: 'SplitPayShare'
                }
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, 1000],
                [10, 25, 50, 100, 500, 1000]
            ],
            searching: true,
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: "copyHtml5",
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: "Kopjo tabelen ne formatin Clipboard",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabelën',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
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
                // Apply date range filter on input change
                $('#reportingPeriodStart, #reportingPeriodEnd').on('change', function() {
                    table.draw();
                });
            },
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color']
        });
    </script>
    <?php include 'partials/footer.php'; ?>