<!-- Modal -->
<div class="modal fade" id="listOfLoansModal" tabindex="-1" role="dialog" aria-labelledby="listOfLoansModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="listOfLoansModalLabel">Lista e borgjeve</h5>
                <button type="button" class="btn-close pe-5" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="example" class="table w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>Klienti</th>
                                <th>Shuma</th>
                                <th>Pagoi</th>
                                <th>Obligim</th>
                                <th>Forma</th>
                                <th>P&euml;rshkrimi</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kueri = $conn->query("SELECT * FROM yinc ORDER BY id DESC");
                            while ($k = mysqli_fetch_array($kueri)) {
                            ?>
                                <tr>
                                    <?php
                                    $sid = $k['kanali'];
                                    $gstaf = $conn->query("SELECT * FROM klientet WHERE id='$sid'");
                                    $gstafi = mysqli_fetch_array($gstaf);
                                    //My number is 928.
                                    $myNumber = $k['shuma'];
                                    //I want to get 25% of 928.
                                    $percentToGet = (float)$gstafi['perqindja'];
                                    //Convert our percentage value into a decimal.
                                    $percentInDecimal = $percentToGet / 100;
                                    //Get the result.
                                    $percent = $percentInDecimal * $myNumber;
                                    //Print it out - Result is 232.
                                    ?>
                                    <td><?php echo $gstafi['emri']; ?></td>
                                    <td><?php echo $k['shuma']; ?>&euro;</td>
                                    <td><?php echo $k['pagoi']; ?>&euro;</td>
                                    <td style="color:red;"><?php echo $k['shuma'] - $k['pagoi']; ?>&euro; </td>
                                    <td><?php echo $k['lloji']; ?></td>
                                    <td><?php echo $k['pershkrimi']; ?></td>
                                    <td><?php echo $k['data']; ?></td>
                                    <!-- Implement a button who fetch id and then go to another form submit and delete based from that id -->
                                </tr>
                                <div class="modal fade" id="pages<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Pages&euml;</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="idp" value="<?php echo $k['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="pagoi" class="form-label">Shuma:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">&euro;</span>
                                                            <input type="text" name="pagoi" class="form-control" id="pagoi" value="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="lloji" class="form-label">Forma e pages&euml;s:</label>
                                                        <select name="lloji" class="form-select" id="lloji">
                                                            <option value="Bank">Bank</option>
                                                            <option value="Cash">Cash</option>
                                                        </select>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hiqe</button>
                                                <button type="submit" name="paguaj" class="btn btn-primary">Paguaj</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th>Klienti</th>
                                <th>Shuma</th>
                                <th>Pagoi</th>
                                <th>Obligim</th>
                                <th>Forma</th>
                                <th>P&euml;rshkrimi</th>
                                <th>Data</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#example').DataTable({
        responsive: true,
        search: {
            return: true,
        },
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row'<'col-md-6'><'col-md-6'p>>",
        buttons: [{
                extend: "pdfHtml5",
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: "Eksporto tabelen ne formatin PDF",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for PDF
            },
            {
                extend: "copyHtml5",
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: "Kopjo tabelen ne formatin Clipboard",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for Copy
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
                filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for Excel
            },
            {
                extend: "print",
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: "Printo tabel&euml;n",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "faturat_e_fshira_" + getCurrentDate() + "" // Set custom filename for Print
            },
        ],
        order: [],
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
        stripeClasses: ['stripe-color']
    });
    function getCurrentDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        return yyyy + mm + dd;
    }
</script>