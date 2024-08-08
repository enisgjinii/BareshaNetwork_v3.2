<?php include 'partials/header.php';
$is_pagesat_page = (basename($_SERVER['PHP_SELF']) === 'pagesat.php'); ?>
<!-- Your HTML code here -->
<?php if ($is_pagesat_page) : ?>
    <!-- Bootstrap modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Zgjedh versionin e faqes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Pagesat janë krijuar në dy versione. Ju lutem zgjidhni njërin prej tyre.</p>
                    <!-- <br> -->
                    <button style="text-decoration: none;" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Pagesa ( Versioni vjeter )</button>
                    <a href="invoice.php" style="text-decoration: none;" class="input-custom-css px-3 py-2">Pagesa ( Versioni i ri )</a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Bootstrap modal -->
    <!-- JavaScript to trigger the modal -->
    <script>
        // This script will execute only if the current page is pagesat.php
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            myModal.show();
        });
    </script>
<?php endif; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container">
            <nav class="bg-white px-2 rounded-5" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset text-decoration-none">Financat</a></li>
                    <li class="breadcrumb-item active"><a href="pagesat.php" class="text-reset text-decoration-none">Pagesat e kryera</a></li>
                </ol>
            </nav>
            <div class="card shadow-sm rounded-5">
                <div class="container table-responsive p-3">
                    <div class="row mb-4">
                        <?php foreach (['min' => 'Prej', 'max' => 'Deri'] as $id => $label) : ?>
                            <div class="col-md-6 text-dark text-dark">
                                <label for="<?= $id ?>" class="form-label"><?= $label ?>:</label>
                                <p class="text-muted small">Zgjidhni një diapazon <?= $id === 'min' ? 'fillues' : 'mbarues' ?> të datës për të filtruar rezultatet.</p>
                                <div class="input-group rounded-5  ">
                                    <span class="input-group-text border-0 bg-white "><i class="fi fi-rr-calendar"></i></span>
                                    <input type="text" id="<?= $id ?>" name="<?= $id ?>" class="form-control rounded-5 flatpickr " placeholder="Zgjidhni datën">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <table id="paymentTable" class="table table-bordered w-100 text-dark">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-dark">Klienti</th>
                                <th class="text-dark">Fatura</th>
                                <th class="text-dark">Pershkrimi</th>
                                <th class="text-dark">Shuma</th>
                                <th class="text-dark">Menyra</th>
                                <th class="text-dark">Data</th>
                                <th class="text-dark">Kategorizimi</th>
                                <th class="text-dark">Fatura PDF</th>
                            </tr>
                        </thead>
                        <tbody class="text-dark">
                            <?php
                            $stmt = $conn->prepare("SELECT p.fatura, p.kategoria, SUM(p.shuma) as total_shuma, p.menyra, p.data, p.pershkrimi, k.emri as client_name FROM pagesat p JOIN fatura f ON p.fatura = f.fatura JOIN klientet k ON f.emri = k.id GROUP BY p.fatura, p.kategoria ORDER BY p.id DESC");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) :
                                $kategoria = !empty($row['kategoria']) ? @unserialize($row['kategoria']) : [];
                                $kategoria_str = is_array($kategoria) ? implode(", ", array_map(fn ($v) => $v == 'null' ? 'Ska' : $v, $kategoria)) : str_replace('null', 'Ska', $row['kategoria']);
                            ?>
                                <tr>
                                    <td class="wrap-text"><?= htmlspecialchars($row['client_name']) ?></td>
                                    <td class="wrap-text"><?= htmlspecialchars($row['fatura']) ?></td>
                                    <td class="wrap-text"><?= htmlspecialchars($row['pershkrimi']) ?></td>
                                    <td class="wrap-text"><?= htmlspecialchars($row['total_shuma']) ?></td>
                                    <td class="wrap-text"><?= htmlspecialchars($row['menyra']) ?></td>
                                    <td class="wrap-text"><?= date("d-m-Y", strtotime($row['data'])) ?></td>
                                    <td class="wrap-text"><?= htmlspecialchars($kategoria_str) ?></td>
                                    <td>
                                        <a class="btn btn-light py-1 px-2 border border-1" target="_blank" href="fatura.php?invoice=<?= htmlspecialchars($row['fatura']) ?>">
                                            <i class="fi fi-rr-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile;
                            $stmt->close(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/sq.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".flatpickr", {
            locale: "sq",
            dateFormat: "d-m-Y",
            allowInput: true
        });
        let table = $('#paymentTable').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
                    titleAttr: "Eksporto në PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
                    titleAttr: "Kopjo në Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                },
                {
                    extend: "excelHtml5",
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
                    titleAttr: "Eksporto në Excel",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    exportOptions: {
                        modifier: {
                            search: "applied",
                            order: "applied",
                            page: "all"
                        }
                    }
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
                    titleAttr: "Printo tabelën",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
                }
            ],
            order: [],
            columnDefs: [{
                width: '10%',
                targets: '_all'
            }, {
                targets: 5,
                type: 'date-eu'
            }],
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            initComplete: function() {
                $('.dt-buttons').removeClass('dt-buttons btn-group');
                $('div.dataTables_length select').addClass('form-select').css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem'
                });
            }
        });
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            let min = $('#min').val(),
                max = $('#max').val(),
                date = moment(data[5], "DD-MM-YYYY");
            return (!min && !max) || (!min && date <= moment(max, "DD-MM-YYYY")) || (moment(min, "DD-MM-YYYY") <= date && !max) || (moment(min, "DD-MM-YYYY") <= date && date <= moment(max, "DD-MM-YYYY"));
        });
        $('.flatpickr').on('change', () => table.draw());
    });
</script>
<style>
    .wrap-text {
        white-space: normal !important;
    }
</style>