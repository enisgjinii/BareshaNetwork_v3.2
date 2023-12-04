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
            <div class="container">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Platformat</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="filtroCSV.php" class="text-reset" style="text-decoration: none;">
                                Lista e te ardhurave nga Platformat
                            </a></li>
                </nav>


                <div class="row mb-2">
                    <form id="filterForm">
                        <div class="row p-3 bg-white rounded-5 border shadow-sm">
                            <div class="col">
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
                            <div class="col">
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

                            <div>
                                <button class="input-custom-css btn-sm rounded-5 shadow-sm mt-3 px-3 py-2" type="submit" value="Filtro">
                                    <i class="fi fi-rr-filter"></i>
                                    Filtro
                                </button>
                            </div>
                        </div>
                    </form>
                    <style>
                        @media print {
                            body * {
                                visibility: hidden;
                                height: 100px;
                            }

                            #printable-content,
                            #printable-content * {
                                visibility: visible;
                               
                                /* border: 0; */
                            }

                            #printable-content {
                                position: absolute;
                                left: 0;
                                top: 0;
                            }
                            /* Dont print the button */
                            #printable-content .input-custom-css {
                                display: none;
                            }
                        }
                    </style>
                    <div class="row">
                        <div class="col bg-white bordered rounded-5 my-2 py-3" id="printable-content">
                            <!-- Display selected values in a table -->
                            <table class="table table-bordered">
                                <tr>
                                    <td>Klienti i zgjedhur:</td>
                                    <td><span id="displaySelectedClient"></span></td>
                                </tr>
                                <tr>
                                    <td>Periudha e zgjedhur e raportimit:</td>
                                    <td><span id="displaySelectedReportingPeriod"></span></td>
                                </tr>
                                <tr>
                                    <td>Të ardhurat e përgjithshme:</td>
                                    <td><span id="totalRevenue"></span></td>
                                </tr>
                            </table>
                            <br>
                            <!-- Button to trigger print -->
                            <div>
                                <button class="input-custom-css btn-sm rounded-5 shadow-sm px-3 py-2" onclick="printContent()"><i class="fi fi-rr-print"></i> Print</button>
                            </div>
                        </div>
                    </div>

                    <script>
                        function printContent() {
                            // Show only the content you want to print
                            document.body.style.visibility = 'hidden';
                            document.getElementById('printable-content').style.visibility = 'visible';

                            // Trigger the browser's print functionality
                            window.print();

                            // Reset visibility after printing
                            document.body.style.visibility = 'visible';
                        }
                    </script>
                    <script>
                        $(document).ready(function() {
                            // Initialize Selectr for the client select
                            var selectClient = new Selectr('#selectClient', {
                                searchable: true,
                                width: 300,
                                clearable: true,
                            });

                            // Initialize Selectr for the reporting period select
                            var selectReportingPeriod = new Selectr('#reportingPeriod', {
                                searchable: false,
                                width: 200,
                                clearable: true,
                            });

                            // Add an event listener for the form submission
                            $('#filterForm').submit(function(event) {
                                // Prevent the default form submission
                                event.preventDefault();

                                // Get the selected values
                                var selectedClient = selectClient.getValue();
                                var selectedReportingPeriod = selectReportingPeriod.getValue();

                                // Clear previous results and errors
                                $('#displaySelectedClient').empty();
                                $('#displaySelectedReportingPeriod').empty();
                                $('#totalRevenue').empty();
                                $('#errorContainer').empty();

                                // Update spans with selected values
                                $('#displaySelectedClient').text(selectedClient);
                                $('#displaySelectedReportingPeriod').text(selectedReportingPeriod);

                                // Reload the DataTable with the selected values
                                table.ajax.url('fetch_CSV.php?selectedClient=' + selectedClient + '&reportingPeriod=' + selectedReportingPeriod).load();

                                // Fetch total revenue from the server
                                $.ajax({
                                    url: 'fetch_total_revenue.php',
                                    method: 'POST',
                                    data: {
                                        selectedClient: selectedClient,
                                        reportingPeriod: selectedReportingPeriod
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


                    <div class="row">
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
    </div>






















    <script>
        var table = $('#example').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            search: {
                return: true,
            },
            ajax: {
                url: 'fetch_CSV.php',
                type: 'POST',
                data: function(d) {
                    d.selectedClient = $('#selectClient').val();
                    d.reportingPeriod = $('#reportingPeriod').val();
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

            searching: true,

            dom: 'Bfrtip',
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'input-custom-css text-dark px-3 py-2',
                title: 'Lista e ' + $('#selectClient').val() + ' për të ardhurat nga platformat gjatë periodes ' + $('#reportingPeriod').val(),
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'input-custom-css text-dark px-3 py-2',
                title: 'Lista e ' + $('#selectClient').val() + ' për të ardhurat nga platformat gjatë periodes ' + $('#reportingPeriod').val(),

            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'input-custom-css text-dark px-3 py-2',
                title: 'Lista e ' + $('#selectClient').val() + ' për të ardhurat nga platformat gjatë periodes ' + $('#reportingPeriod').val(),
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabel&euml;n',
                className: 'input-custom-css text-dark px-3 py-2',
                title: 'Lista e ' + $('#selectClient').val() + ' për të ardhurat nga platformat gjatë periodes ' + $('#reportingPeriod').val(),
            }],
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
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