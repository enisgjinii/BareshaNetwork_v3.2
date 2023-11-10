<?php include 'partials/header.php' ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">

                <?php
                include 'conn-d.php';

                // Define the year for which to calculate revenue totals
                $year = 2023;

                // Define an array of month names
                $months = array('Janar', 'Shkurt', 'Mars', 'Prill', 'Maj', 'Qershor', 'Korrik', 'Gusht', 'Shtator', 'Tetor', 'Nentor', 'Dhjetor');
                ?>
                <div class="p-5 mb-4 card rounded-5 shadow-sm">

                    <table class="table table-bordered" id="tabelaMuajve">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Muaji</th>
                                <th>Totali i t&euml; ardhurave</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i = 1; $i <= 12; $i++) { // Calculate the start and end dates of the month
                                $start_date = sprintf('%04d-%02d-01', $year, $i);
                                $end_date = sprintf(
                                    '%04d-%02d-%02d',
                                    $year,
                                    $i,
                                    date('t', strtotime($start_date))
                                ); // Prepare and execute the SQL query
                                $stmt = mysqli_prepare(
                                    $conn,
                                    "SELECT SUM(revenue) as total, MONTH(data) as month FROM monetizimi_youtube WHERE data BETWEEN ? AND ? GROUP BY month"
                                );
                                mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
                                mysqli_stmt_execute($stmt);
                                $results = mysqli_stmt_get_result($stmt); // Display the results as a table row
                                $row = mysqli_fetch_assoc($results);
                                $total = isset($row['total']) != null ? $row['total'] - 0.01
                                    : '<i class="fi fi-rr-time-quarter-to"></i>';
                                $month = isset($row['month']) ? $row['month'] : '-';
                                echo '<tr>
                                <td> ' . $i . '</td>
                                <td>' . $months[$i - 1] . '</td>
                                    <td>' . $total . '</td>
                                    </tr>';
                            } ?>

                        </tbody>
                    </table>
                </div>


                <div class="p-5 mb-4 card rounded-5 shadow-sm">
                    <table class="table table-bordered" id="tabelaRevenue">
                        <thead>
                            <tr>
                                <th>Emri i kanalit</th>
                                <th>ID Kanalit</th>
                                <th>Data</th>
                                <th>Te ardhurat</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php' ?>
<script type="text/javascript">

    $('#tabelaRevenue').DataTable({
        responsive: true,
        search: {
            return: true,
        }, dom: 'Bfrtip',
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
        }],
        initComplete: function () {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');
        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],
        ajax: {
            url: 'get_monetizimetYoutube.php',
            type: 'POST',
            data: function (d) {
                d.month = $('#month-select').val();
            }
        },
        columns: [
            { data: 'emri_kanalit' },
            { data: 'id_kanalit' },
            { data: 'data' },
            { data: 'revenue' },
        ]
    });
</script>


<script type="text/javascript">

    $('#tabelaMuajve').DataTable({
        responsive: true,
        search: {
            return: true,
        },
        lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "All"]],
        pageLength: -1, // add this line

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
        }],
        initComplete: function () {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');
        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],

    });
</script>