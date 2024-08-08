<?php
include 'partials/header.php';
class Rating
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    public function fetchRatings()
    {
        $ratings = [];
        $sql = "SELECT * FROM rating";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ratings[] = $row;
            }
        }
        return $ratings;
    }
}
$rating = new Rating($conn);
$ratings = $rating->fetchRatings();
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Klientët</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="invoice.php" class="text-reset" style="text-decoration: none;">
                            Lista e vlersimeve
                        </a>
                    </li>
                </ol>
            </nav>
            <div class="card rounded-5 shadow-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="ratings" class="table table-bordered w-100">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-dark">ID</th>
                                            <th class="text-dark">Emri</th>
                                            <th class="text-dark">Vlersimi</th>
                                            <th class="text-dark">Komenti</th>
                                            <th class="text-dark">Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ratings as $row) : ?>
                                            <tr>
                                                <td>
                                                    <?php echo $row['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['username']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['rating']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['comment']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['created_at']; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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
        $('#ratings').DataTable({
            "order": [
                [0, "desc"]
            ],
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
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
            stripeClasses: ['stripe-color']
        })
    })
</script>