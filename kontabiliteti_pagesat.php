<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'partials/header.php';
include 'conn-d.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
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
            <ul class="nav nav-pills bg-white my-3 mx-0 rounded-5" style="width: fit-content;" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5 active" id="pills-pagesat_pers-tab" data-bs-toggle="pill" data-bs-target="#pills-pagesat_pers" type="button" role="tab" aria-controls="pills-pagesat_pers" aria-selected="true" style="text-decoration: none;text-transform: none;">Pagesat e kryera personale</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-pagesat_biz-tab" data-bs-toggle="pill" data-bs-target="#pills-pagesat_biz" type="button" role="tab" aria-controls="pills-pagesat_biz" aria-selected="false" style="text-decoration: none;text-transform: none;">Pagesat e kryera biznes</button>
                </li>
            </ul>
            <div class="tab-content text-dark" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-pagesat_pers" role="tabpanel" aria-labelledby="pills-pagesat_pers-tab" tabindex="0">
                    <div class="card p-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <div class="date-range-filter text-dark">
                                        <div class="row text-dark">
                                            <div class="col-6"> <input type="text" id="minDate" class="form-control shadow-sm rounded-5 w-100 mt-3" style="width: 230px;" placeholder="Prej: "></div>
                                            <div class="col-6"> <input type="text" id="maxDate" class="form-control shadow-sm rounded-5 w-100 mt-3" placeholder="Deri: "></div>
                                        </div>
                                        <br>
                                        <button id="filterDate" class="input-custom-css px-3 py-2"> <i class="fi fi-rr-filter"></i>Filtro</button>
                                    </div>
                                    <br>
                                    <table id="paymentsTablePersonal" class="table table-bordered w-100 text-dark">
                                        <thead class="table-light text-dark">
                                            <tr>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Emri i klientit</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">ID e faturës</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Vlera</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Data</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Banka</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Lloji</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Përshkrimi</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Shuma e përgjithshme pas %</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-pagesat_biz" role="tabpanel" aria-labelledby="pills-pagesat_biz-tab" tabindex="0">
                    <div class="card p-5 text-dark">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <div class="date-range-filter ">
                                        <div class="row">
                                            <div class="col-6"> <input type="text" id="minDateBiz" class="form-control shadow-sm rounded-5 w-100 mt-3" style="width: 230px;" placeholder="Prej: "></div>
                                            <div class="col-6"> <input type="text" id="maxDateBiz" class="form-control shadow-sm rounded-5 w-100 mt-3" placeholder="Deri: "></div>
                                        </div>
                                        <br>
                                        <button id="filterDateBiz" class="input-custom-css px-3 py-2"> <i class="fi fi-rr-filter"></i>Filtro</button>
                                    </div> <br>
                                    <table id="paymentsTableBiznes" class="table table-bordered w-100">
                                        <thead class="table-light text-dark">
                                            <tr >
                                                <th class="text-dark" style="white-space: normal;font-size: 12px;">Emri i klientit</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">ID e faturës</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Vlera</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Data</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Banka</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Lloji</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Përshkrimi</th>
                                                <th class="text-dark"  style="white-space: normal;font-size: 12px;">Shuma e përgjithshme pas %</th>
                                            </tr>
                                        </thead>
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
<?php
include 'partials/footer.php';
?>
<script>
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
        var today = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
        var table = $('#paymentsTablePersonal').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "fetch_payments_personal.php",
                "type": "POST",
                "data": function(d) {
                    d.minDate = $('#minDate').val();
                    d.maxDate = $('#maxDate').val();
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
                    "data": "client_name"
                },
                {
                    "data": "invoice_id"
                },
                {
                    "data": "payment_amount"
                },
                {
                    "data": "payment_date"
                },
                {
                    "data": "bank_info"
                },
                {
                    "data": "type_of_pay"
                },
                {
                    "data": "description"
                },
                {
                    "data": "total"
                }
            ],
            "pageLength": 10, // Default page length
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
        });
        $('#filterDate').on('click', function() {
            table.draw();
        });
        var table2 = $('#paymentsTableBiznes').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "fetch_payments_biznes.php",
                "type": "POST",
                "data": function(d) {
                    d.minDateBiz = $('#minDateBiz').val();
                    d.maxDateBiz = $('#maxDateBiz').val();
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
                    "data": "client_name"
                },
                {
                    "data": "invoice_id"
                },
                {
                    "data": "payment_amount"
                },
                {
                    "data": "payment_date"
                },
                {
                    "data": "bank_info"
                },
                {
                    "data": "type_of_pay"
                },
                {
                    "data": "description"
                },
                {
                    "data": "total"
                }
            ],
            "pageLength": 10, // Default page length
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
        });
        $('#filterDateBiz').on('click', function() {
            table2.draw();
        });
    });
</script>