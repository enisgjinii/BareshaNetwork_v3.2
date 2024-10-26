<?php
include 'partials/header.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-3 shadow-sm rounded-5 mb-4 card">
                <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Financat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Pagesat Youtube</a>
                        </li>
                    </ol>
                </nav>
                <div class="row mb-2">
                    <div class="table-responsive">
                        <table id="activitiesTable" class="table table-striped table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Email</th>
                                    <th>Data</th>
                                    <th>Faqet e aksesuara</th>
                                    <th>Numri i aktiviteteve</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#activitiesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "api/get_methods/get_activities_data.php",
                "type": "POST"
            },
            "columns": [{
                    "data": "email"
                },
                {
                    "data": "activity_date"
                },
                {
                    "data": "pages"
                },
                {
                    "data": "activity_count"
                }
            ],
            "order": [
                [1, "desc"]
            ],
            "pageLength": 25,
            "responsive": true,
            "searchDelay": 500,
            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: "Kopjo tabelen ne formatin Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
                {
                    extend: "excelHtml5",
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                    titleAttr: "Eksporto tabelen ne formatin Excel",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    exportOptions: {
                        modifier: {
                            search: "applied",
                            order: "applied",
                            page: "all",
                        },
                    },
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
            ],
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
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
        });
    });
</script>