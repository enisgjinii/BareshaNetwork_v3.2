<?php
include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="ttatimi.php" class="text-reset" style="text-decoration: none;">
                            Tatimi
                        </a>
                    </li>
                </ol>
            </nav>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5 active" id="pills-Kontributet-tab" data-bs-toggle="pill" data-bs-target="#pills-Kontributet" type="button" role="tab" aria-controls="pills-Kontributet" aria-selected="true">Kontributet</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-tvsh-tab" data-bs-toggle="pill" data-bs-target="#pills-tvsh" type="button" role="tab" aria-controls="pills-tvsh" aria-selected="false">TVSH</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-5" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Tatim</button>
                </li>
                
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-Kontributet" role="tabpanel" aria-labelledby="pills-Kontributet-tab" tabindex="0">
                    <div class="card rounded-5 p-5">
                        <form action="add_contribution.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="" class="form-label">Data e kontributimit</label>
                                <input type="date" name="date" class="form-control rounded-5 border border-1">
                            </div>
                            <!-- Get actual date -->
                            <script>
                                document.getElementsByName('date')[0].value = new Date().toISOString().split('T')[0];
                            </script>
                            <div class="mb-3">
                                <label for="" class="form-label">Pershkrimi</label>
                                <input type="text" name="text" class="form-control rounded-5 border border-1">
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Ngarko dokumentin</label>
                                <input type="file" name="file" class="form-control rounded-5 border border-1">
                            </div>
                            <button type="submit" class="input-custom-css px-3 py-2">Krijo</button>
                        </form>
                    </div>
                    <hr>
                    <!-- Modal Structure -->
                    <div class="card rounded-5 p-5">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableOfContributions">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Data</th>
                                        <th>Pershkrimi</th>
                                        <th>Dokumenti</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM contributions";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php echo $row['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['date']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['description']; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#documentModal<?php echo $row['id']; ?>" data-docpath="contributions/<?php echo $row['document_path']; ?>">
                                                        <i class="fi fi-rr-document"></i>
                                                    </button>
                                                </td>
                                                <div class="modal fade" id="documentModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="documentModalLabel">Dokumenti i ngarkuar</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                                            </div>
                                                            <div class="modal-body">
                                                                <div id="documentContent<?php echo $row['id']; ?>"></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="input-custom-css px-3 py-2" data-dismiss="modal">Mbylle</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <script>
                                                    // JavaScript to load the document content dynamically when the modal is shown
                                                    $(document).ready(function() {
                                                        $('#documentModal<?php echo $row['id']; ?>').on('shown.bs.modal', function() {
                                                            var documentPath = "<?php echo $row['document_path']; ?>";
                                                            var fileExtension = documentPath.split('.').pop().toLowerCase();

                                                            if (fileExtension === 'pdf') {
                                                                $('#documentContent<?php echo $row['id']; ?>').html('<iframe id="documentFrame" src="' + documentPath + '" width="100%" height="600px"></iframe>');
                                                            } else if (fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png') {
                                                                $('#documentContent<?php echo $row['id']; ?>').html('<img id="documentImage" src="' + documentPath + '" width="100%" height="600px" />');
                                                            } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                                                                $('#documentContent<?php echo $row['id']; ?>').html('Unsupported file type.');
                                                            } else if (fileExtension === 'xls' || fileExtension === 'xlsx') {
                                                                $.ajax({
                                                                    url: 'load_excel.php',
                                                                    type: 'POST',
                                                                    data: {
                                                                        documentPath: documentPath
                                                                    },
                                                                    success: function(response) {
                                                                        $('#documentContent<?php echo $row['id']; ?>').html(response);
                                                                    },
                                                                    error: function(xhr, status, error) {
                                                                        $('#documentContent<?php echo $row['id']; ?>').html('Error loading spreadsheet: ' + error);
                                                                    }
                                                                });
                                                            } else {
                                                                $('#documentContent<?php echo $row['id']; ?>').html('Unsupported file type.');
                                                            }
                                                        });
                                                    });
                                                </script>

                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-tvsh" role="tabpanel" aria-labelledby="pills-tvsh-tab" tabindex="0">...</div>
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">...</div>
                <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">...</div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#tableOfContributions').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
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
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            buttons: [{
                    extend: "pdf",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
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
        });
    });
</script>
<?php include 'partials/footer.php' ?>