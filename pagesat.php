<?php
include 'partials/header.php';
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
            $kategoria = !empty($row['kategoria']) ? unserialize($row['kategoria']) : [];
            $kategoria_str = is_array($kategoria)
                ? implode(", ", array_map(fn($v) => $v === 'null' ? 'Ska' : htmlspecialchars($v), $kategoria))
                : str_replace('null', 'Ska', htmlspecialchars($row['kategoria']));
            $formatted_date = date("d-m-Y", strtotime($row['data']));
            $payments[] = [
                'client_name' => htmlspecialchars($row['client_name']),
                'fatura'      => htmlspecialchars($row['fatura']),
                'pershkrimi'  => htmlspecialchars($row['pershkrimi']),
                'total_shuma' => htmlspecialchars($row['total_shuma']),
                'menyra'      => htmlspecialchars($row['menyra']),
                'data'        => $formatted_date,
                'kategoria'   => $kategoria_str,
                'fatura_pdf'  => htmlspecialchars($row['fatura'])
            ];
        }
        $stmt->close();
    } else {
        error_log("Database Query Failed: " . $conn->error);
    }
    return $payments;
}
$payments = fetchPaymentData($conn);
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><span class="text-reset">Financat</span></li>
                    <li class="breadcrumb-item active" aria-current="page"><span class="text-reset">Pagesat Youtube</span></li>
                </ol>
            </nav>
            <div class="card shadow-none border  rounded-5">
                <div class="table-responsive p-3">
                    <div class="row mb-4">
                        <?php foreach (['min' => 'Prej', 'max' => 'Deri'] as $id => $label): ?>
                            <div class="col-md-6 mb-3">
                                <label for="<?= $id ?>" class="form-label"><?= $label ?>:</label>
                                <p class="text-muted small">Zgjidhni një diapazon <?= $id === 'min' ? 'fillues' : 'mbarues' ?> të datës për të filtruar rezultatet.</p>
                                <div class="input-group rounded-5">
                                    <span class="input-group-text border-0 bg-white"><i class="fi fi-rr-calendar"></i></span>
                                    <input type="text" id="<?= $id ?>" name="<?= $id ?>" class="form-control rounded-5 flatpickr" placeholder="Zgjidhni datën">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <table id="paymentTable" class="table w-100 text-dark compact-table">
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
                            <?php if ($payments): ?>
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
                                            <a href="fatura.php?invoice=<?= $payment['fatura_pdf'] ?>" target="_blank" class="btn btn-light py-1 px-2 border">
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
<?php include 'partials/footer.php'; ?>
<!-- Initialize Scripts -->
<script>
    $(document).ready(() => {
        flatpickr(".flatpickr", {
            locale: "sq",
            dateFormat: "d-m-Y",
            allowInput: true
        });
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
                    width: '12%',
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
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
            stripeClasses: ['stripe-color'],
            initComplete: function() {
                $('.dt-buttons').removeClass('dt-buttons btn-group');
                $('div.dataTables_length select').addClass('form-select').css({
                    width: 'auto',
                    margin: '0 8px',
                    padding: '0.375rem 1.75rem 0.375rem 0.75rem',
                    lineHeight: '1.5',
                    border: '1px solid #ced4da',
                    borderRadius: '0.25rem'
                });
            }
        });
        $.fn.dataTable.ext.search.push((settings, data) => {
            const min = $('#min').val();
            const max = $('#max').val();
            const date = moment(data[5], "DD-MM-YYYY");
            if (
                (!min && !max) ||
                (!min && date.isSameOrBefore(moment(max, "DD-MM-YYYY"))) ||
                (moment(min, "DD-MM-YYYY").isSameOrBefore(date) && !max) ||
                (moment(min, "DD-MM-YYYY").isSameOrBefore(date) && date.isSameOrBefore(moment(max, "DD-MM-YYYY")))
            ) {
                return true;
            }
            return false;
        });
        $('.flatpickr').on('change', () => table.draw());
    });
</script>
<!-- Custom Styles -->
<style>
    .compact-table th,
    .compact-table td {
        padding: 0.3rem;
        font-size: 0.9rem;
    }
    .compact-table th {
        white-space: nowrap;
    }
    .compact-table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .compact-table a.btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    /* Remove horizontal scroll */
    .table-responsive {
        overflow-x: hidden;
    }
    /* Adjust table layout */
    .compact-table {
        table-layout: fixed;
        width: 100%;
    }
</style>