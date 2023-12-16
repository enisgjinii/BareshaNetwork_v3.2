<?php include 'partials/header.php';

?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">

            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="klient-avanc.php" class="text-reset" style="text-decoration: none;">
                            Lista e avanceve te klienteve
                        </a>
                    </li>
            </nav>
            <div class="card rounded-5 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="example" class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Emri</th>
                                        <th>Data</th>
                                        <th>Klienti</th>
                                        <th>Shuma</th>
                                        <th>Borgji</th>
                                        <th>Niveli i urgjenc&euml;s</th>
                                        <th>Statusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $kueri = $conn->query("SELECT * FROM parapagimtable ORDER BY id DESC");


                                    while ($k = mysqli_fetch_assoc($kueri)) {
                                        $emri_id = $k['emri_id'];
                                        $klientQuery = $conn->query("SELECT * FROM klientet WHERE id = ' . $emri_id . '");

                                        if (!$klientQuery) {
                                            // Display the SQL error message
                                            die("Error in klientQuery: " . mysqli_error($conn));
                                        }

                                        $klientData = mysqli_fetch_array($klientQuery);
                                    ?>
                                        <tr>
                                            <td><?php echo $k['id']; ?></td>
                                            <td><?php echo $k['emri']; ?></td>
                                            <td><?php echo $k['data']; ?></td>
                                            <td><?php echo $klientData['emri']; ?></td>
                                            <td><?php echo $k['shuma']; ?></td>
                                            <td><?php echo $k['borgji']; ?></td>
                                            <td><?php echo $k['urgjenca']; ?></td>
                                            <td>
                                                <select name="statusi" id="statusi" class="form-select shadow-sm rounded-5">
                                                    <option value="1">E kryer</option>
                                                    <option value="0">E pa-kryer</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php } ?>

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






<?php include 'partials/footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            search: {
                return: true,
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Të gjitha"]
            ],
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select'); // add Bootstrap form-select class
                lengthSelect.css({
                    'width': 'auto', // adjust width to fit content
                    'margin': '0 8px', // add some margin around the element
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem', // adjust padding to match Bootstrap's styles
                    'line-height': '1.5', // adjust line-height to match Bootstrap's styles
                    'border': '1px solid #ced4da', // add border to match Bootstrap's styles
                    'border-radius': '0.25rem', // add border radius to match Bootstrap's styles
                }); // adjust width to fit content
            },

            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm border  me-2'
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'btn btn-light btn-sm border  me-2'
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm border  me-2'
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
            // Remove scroller
            scrollY: false,
            scrollX: false,
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5, 6, 7],
                "render": function(data, type, row) {
                    return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
                }
            }],
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
            "ordering": false
        })
    })
</script>