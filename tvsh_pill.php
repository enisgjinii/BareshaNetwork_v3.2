<div class="tab-pane fade show" id="pills-tvsh" role="tabpanel" aria-labelledby="pills-tvsh-tab" tabindex="0">
    <div class="card rounded-5 p-5">

        <form action="add_tvsh.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col mb-3">
                    <label for="" class="form-label">Data e pagesës së tvsh-së</label>
                    <input type="date" name="datetvsh" id="datetvsh" class="form-control rounded-5 border border-1">
                </div>
                
                <!-- Get actual date -->
                <script>
                    document.getElementsByName('datetvsh')[0].value = new Date().toISOString().split('T')[0];
                </script>
                <div class="col  mb-3">
                    <label for="" class="form-label">Pershkrimi</label>
                    <input type="text" name="text" class="form-control rounded-5 border border-1">
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label for="" class="form-label">Zgjedh perioden</label>
                    <select name="periodtvsh" id="periodtvsh" class="form-select rounded-5 border border-1">
                        <option value="TM1">TM1</option>
                        <option value="TM2">TM2</option>
                        <option value="TM3">TM3</option>
                        <option value="TM4">TM4</option>
                    </select>
                </div>
                <script>
                    new Selectr('#periodtvsh', {
                        searchable: true,
                        width: 300
                    })
                </script>
                <div class="col mb-3">
                    <label for="" class="form-label">Shëno vlerën</label>
                    <input type="number" name="value" id="value" class="form-control rounded-5 border border-1" required>
                </div>
            </div>
            <div class="row">
                <div class="mb-3">
                    <label for="" class="form-label">Forma e pagesës</label>
                    <select name="formatvsh" id="formatvsh" class="form-select rounded-5 border border-1">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                        
                    </select>
                </div>
            </div>
            <div class="w-25 mb-3">
                <label for="" class="form-label">Ngarko dokumentin</label>
                <input type="file" name="file" class="form-control rounded-5 border border-1">
            </div>
            <button type="submit" class="input-custom-css px-3 py-2">Krijo</button>
        </form>
    </div>
    <hr>
    <!-- Modal Structure -->
    <div class="card rounded-5 p-5">
    <div class="row">
                <div class="col">
                    <label for="periodFilterTvsh" class="form-label">Filtro sipas periodes:</label>
                    <select id="periodFilterTvsh" class="form-select">
                        <option value="">All</option>
                        <?php
                        $periods = [];
                        $sql = "SELECT DISTINCT period FROM tvsh";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $periods[] = $row['period'];
                            }
                        }
                        foreach ($periods as $period) {
                            echo "<option value=\"$period\">$period</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <div class="filter-container mb-3">
                        <label for="yearFilterTvsh" class="form-label">Filtro :</label>
                        <select id="yearFilterTvsh" class="form-select">
                            <option value="">All</option>
                            <?php
                            $years = [];
                            $sql = "SELECT DISTINCT YEAR(date) AS year FROM tvsh";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $years[] = $row['year'];
                                }
                            }
                            foreach ($years as $year) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="tableOfTVSH">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Pershkrimi</th>
                        <th>Perioda</th>
                        <th>Vlera</th>
                        <th>Forma e pagesës</th>
                        <th>Dokumenti</th>

                </thead>
                <tbody>
                    <?php
                    $selectedPeriod = isset($_GET['period']) ? $_GET['period'] : '';
                    $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
                    $sql = "SELECT * FROM tvsh WHERE 1";
                    if (!empty($selectedPeriod)) {
                        $sql .= " AND period = '$selectedPeriod'";
                    }
                    if (!empty($selectedYear)) {
                        $sql .= " AND YEAR(date) = '$selectedYear'";
                    }
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
                                    <?php echo $row['period']; ?>
                                </td>
                                <td>
                                    <?php echo $row['value']; ?>
                                </td>
                                <td>
                                    <?php echo $row['payment_method']; ?>
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
        <div class="mt-3">
            <strong>Total Value:</strong> <span id="totalValueTvsh">0</span>
        </div>
    </div>
</div>

<script>
        $(document).ready(function() {
            // Initialize the DataTable
            var table2 = $('#tableOfTVSH').DataTable({
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
                    // Calculate and display the initial sum
                    updateTotalValue2();
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
                        titleAttr: "Printo tabelën",
                        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    },
                ],
                stripeClasses: ["stripe-color"],
            });
            // Custom filtering function for year
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var selectedYear = $('#yearFilterTvsh').val();
                    var date = data[1] || ""; // Use data for the date column
                    console.log("Selected Year:", selectedYear);
                    console.log("Date in Row:", date);
                    if (selectedYear === "" || date.startsWith(selectedYear)) {
                        return true;
                    }
                    return false;
                }
            );
            $('#periodFilterTvsh').on('change', function() {
                table2.column(3).search(this.value).draw();
                updateTotalValue2(); // Update the total value when the period filter changes
            });
            $('#yearFilterTvsh').on('change', function() {
                table2.draw(); // Redraw the table with the custom filter applied
                updateTotalValue2(); // Update the total value when the year filter changes
            });
            function updateTotalValue2() {
                var total = 0;
                // Calculate the sum of the values in column 4
                table2.column(4, {
                    search: 'applied'
                }).data().each(function(value) {
                    total += parseFloat(value) || 0;
                });
                // Display the total value
                $('#totalValueTvsh').text(total.toFixed(2));
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#datetvsh", {
                dateFormat: "Y-m-d", // You can customize the date format as needed
                maxDate: "today",
                locale: "sq" // Set locale to Albanian
            });
        });
        document.getElementsByName('datetvsh')[0].value = new Date().toISOString().split('T')[0];
    </script>