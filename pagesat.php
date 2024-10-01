<?php
// Include the header partial
include 'partials/header.php';

// Determine if the current page is 'pagesat.php'
$is_pagesat_page = basename($_SERVER['PHP_SELF']) === 'pagesat.php';

/**
 * Fetch payment data from the database.
 *
 * @param mysqli $conn The database connection.
 * @return array An array of payment records.
 */
function fetchPaymentData($conn)
{
    $query = "
        SELECT 
            p.fatura, 
            p.kategoria, 
            SUM(p.shuma) AS total_shuma, 
            p.menyra, 
            p.data, 
            p.pershkrimi, 
            k.emri AS client_name 
        FROM pagesat p 
        JOIN fatura f ON p.fatura = f.fatura 
        JOIN klientet k ON f.emri = k.id 
        GROUP BY p.fatura, p.kategoria 
        ORDER BY p.id DESC
    ";

    $payments = [];

    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Handle category serialization
            $kategoria = !empty($row['kategoria']) ? unserialize($row['kategoria']) : [];
            $kategoria_str = is_array($kategoria)
                ? implode(", ", array_map(fn($v) => $v === 'null' ? 'Ska' : htmlspecialchars($v), $kategoria))
                : str_replace('null', 'Ska', htmlspecialchars($row['kategoria']));

            // Format the date
            $formatted_date = date("d-m-Y", strtotime($row['data']));

            // Append the processed row to payments array
            $payments[] = [
                'client_name'   => htmlspecialchars($row['client_name']),
                'fatura'        => htmlspecialchars($row['fatura']),
                'pershkrimi'    => htmlspecialchars($row['pershkrimi']),
                'total_shuma'   => htmlspecialchars($row['total_shuma']),
                'menyra'        => htmlspecialchars($row['menyra']),
                'data'          => $formatted_date,
                'kategoria'     => $kategoria_str,
                'fatura_pdf'    => htmlspecialchars($row['fatura'])
            ];
        }

        $stmt->close();
    } else {
        // Log the error for debugging
        error_log("Database Query Failed: " . $conn->error);
        // Optionally, handle the error gracefully in the UI
    }

    return $payments;
}

// Fetch the payment data
$payments = fetchPaymentData($conn);
?>

<!-- Your HTML code here -->

<?php if ($is_pagesat_page): ?>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Zgjedh versionin e faqes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Pagesat janë krijuar në dy versione. Ju lutem zgjidhni njërin prej tyre.</p>
                    <button class="input-custom-css px-3 py-2 text-decoration-none" data-bs-dismiss="modal">
                        Pagesa ( Versioni vjetër )
                    </button>
                    <a href="invoice.php" class="input-custom-css px-3 py-2 text-decoration-none">
                        Pagesa ( Versioni i ri )
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Bootstrap Modal -->

    <!-- JavaScript to Trigger the Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('exampleModal');
            if (modalElement) {
                const myModal = new bootstrap.Modal(modalElement);
                myModal.show();
            }
        });
    </script>
<?php endif; ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-2 rounded-5" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><span class="text-reset">Financat</span></li>
                    <li class="breadcrumb-item active">
                        <a href="pagesat.php" class="text-reset text-decoration-none">Pagesat e kryera</a>
                    </li>
                </ol>
            </nav>

            <!-- Card Container -->
            <div class="card shadow-sm rounded-5">
                <div class="container table-responsive p-3">
                    <!-- Date Range Filters -->
                    <div class="row mb-4">
                        <?php
                        $labels = ['min' => 'Prej', 'max' => 'Deri'];
                        foreach ($labels as $id => $label):
                        ?>
                            <div class="col-md-6 mb-3">
                                <label for="<?= $id ?>" class="form-label"><?= $label ?>:</label>
                                <p class="text-muted small">
                                    Zgjidhni një diapazon <?= $id === 'min' ? 'fillues' : 'mbarues' ?> të datës për të filtruar rezultatet.
                                </p>
                                <div class="input-group rounded-5">
                                    <span class="input-group-text border-0 bg-white">
                                        <i class="fi fi-rr-calendar"></i>
                                    </span>
                                    <input type="text" id="<?= $id ?>" name="<?= $id ?>" class="form-control rounded-5 flatpickr" placeholder="Zgjidhni datën">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Payments Table -->
                    <table id="paymentTable" class="table table-bordered w-100 text-dark">
                        <thead class="bg-light">
                            <tr>
                                <th>Klienti</th>
                                <th>Fatura</th>
                                <th>Pershkrimi</th>
                                <th>Shuma</th>
                                <th>Menyra</th>
                                <th>Data</th>
                                <th>Kategorizimi</th>
                                <th>Fatura PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment['client_name'] ?></td>
                                        <td><?= $payment['fatura'] ?></td>
                                        <td><?= $payment['pershkrimi'] ?></td>
                                        <td><?= $payment['total_shuma'] ?></td>
                                        <td><?= $payment['menyra'] ?></td>
                                        <td><?= $payment['data'] ?></td>
                                        <td><?= $payment['kategoria'] ?></td>
                                        <td>
                                            <a href="fatura.php?invoice=<?= $payment['fatura_pdf'] ?>" target="_blank" class="btn btn-light py-1 px-2 border border-1">
                                                <i class="fi fi-rr-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-danger">Nuk ka të dhëna për t'u shfaqur.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer partial
include 'partials/footer.php';
?>

<!-- External Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/sq.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>

<!-- Initialize Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Flatpickr
        flatpickr(".flatpickr", {
            locale: "sq",
            dateFormat: "d-m-Y",
            allowInput: true
        });

        // Initialize DataTable
        const table = $('#paymentTable').DataTable({
            dom: `
                <'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>
                <'row'<'col-12't>>
                <'row'<'col-md-6'><'col-md-6'p>>
            `,
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
                },
                {
                    targets: 5,
                    type: 'date-eu'
                }
            ],
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
            },
            initComplete: function() {
                // Customize DataTable components after initialization
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

        // Custom Date Range Filtering
        $.fn.dataTable.ext.search.push((settings, data) => {
            const min = $('#min').val();
            const max = $('#max').val();
            const date = moment(data[5], "DD-MM-YYYY");

            if ((!min && !max) ||
                (!min && date.isSameOrBefore(moment(max, "DD-MM-YYYY"))) ||
                (moment(min, "DD-MM-YYYY").isSameOrBefore(date) && !max) ||
                (moment(min, "DD-MM-YYYY").isSameOrBefore(date) && date.isSameOrBefore(moment(max, "DD-MM-YYYY")))) {
                return true;
            }
            return false;
        });

        // Redraw table on date change
        $('.flatpickr').on('change', () => table.draw());
    });
</script>

<!-- Custom Styles -->
<style>
    .wrap-text {
        white-space: normal !important;
    }
</style>