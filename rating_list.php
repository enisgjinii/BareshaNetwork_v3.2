<?php
include 'partials/header.php';
// Define the Rating class
class Rating
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    // Fetch ratings from the database
    public function fetchRatings()
    {
        $ratings = [];
        $sql = "SELECT * FROM rating ORDER BY id DESC"; // Order by ID descending for latest first
        $result = $this->conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $ratings[] = $row;
            }
        }
        return $ratings;
    }
}
// Instantiate the Rating class and fetch data
$rating = new Rating($conn);
$ratings = $rating->fetchRatings();
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumb Navigation -->
            <nav class="bg-white px-3 py-2 rounded-5 mb-4" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#" class="text-reset text-decoration-none">Klientët</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="invoice.php" class="text-reset text-decoration-none">Lista e vlersimeve</a>
                    </li>
                </ol>
            </nav>
            <!-- Card Container -->
            <div class="card rounded-5 shadow-sm">
                <div class="card-body">
                    <!-- Table View for Tablet and Desktop -->
                    <div class="d-none d-lg-block d-xl-block d-md-none">
                        <div class="table-responsive">
                            <table id="ratings" class="table table-bordered table-hover w-100">
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
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['rating']); ?></td>
                                            <td><?php echo htmlspecialchars($row['comment']); ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- List View for Mobile -->
                    <div class="d-md-block d-lg-none d-xl-none">
                        <?php foreach ($ratings as $row) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <!-- ID -->
                                    <div class="row mb-2">
                                        <div class="col-4 font-weight-bold">ID:</div>
                                        <div class="col-8"><?php echo htmlspecialchars($row['id']); ?></div>
                                    </div>
                                    <!-- Emri -->
                                    <div class="row mb-2">
                                        <div class="col-4 font-weight-bold">Emri:</div>
                                        <div class="col-8"><?php echo htmlspecialchars($row['username']); ?></div>
                                    </div>
                                    <!-- Vlersimi -->
                                    <div class="row mb-2">
                                        <div class="col-4 font-weight-bold">Vlersimi:</div>
                                        <div class="col-8"><?php echo htmlspecialchars($row['rating']); ?></div>
                                    </div>
                                    <!-- Komenti -->
                                    <div class="row mb-2">
                                        <div class="col-4 font-weight-bold">Komenti:</div>
                                        <div class="col-8"><?php echo htmlspecialchars($row['comment']); ?></div>
                                    </div>
                                    <!-- Data -->
                                    <div class="row">
                                        <div class="col-4 font-weight-bold">Data:</div>
                                        <div class="col-8"><?php echo htmlspecialchars($row['created_at']); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<!-- Ensure jQuery and DataTables JS are included before this script -->
<script>
    $(document).ready(function() {
        // Function to initialize DataTable
        function initializeDataTable() {
            $('#ratings').DataTable({
                "order": [
                    [0, "desc"] // Order by the first column (ID) descending
                ],
                dom: "<'row'<'col-md-3 col-sm-6'l><'col-md-6 col-sm-12 text-center'B><'col-md-3 col-sm-6'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row'<'col-md-6 col-sm-12'i><'col-md-6 col-sm-12'p>>",
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
                    // Customize button group
                    var btns = $(".dt-buttons");
                    btns.removeClass("dt-buttons btn-group").addClass("mb-2");
                    // Customize length select
                    var lengthSelect = $("div.dataTables_length select");
                    lengthSelect.addClass("form-select");
                },
                fixedHeader: true, // Keeps the header fixed while scrolling
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json", // Albanian language
                },
                stripeClasses: ['stripe-color'] // Custom stripe classes if defined
            });
        }
        // Function to destroy DataTable
        function destroyDataTable() {
            if ($.fn.DataTable.isDataTable('#ratings')) {
                $('#ratings').DataTable().destroy();
                $('#ratings tbody').empty(); // Optional: Clear the table body if needed
            }
        }
        // Function to check screen size and initialize/destroy DataTable accordingly
        function handleResponsiveDataTable() {
            if ($(window).width() >= 768) { // md breakpoint is 768px
                if (!$.fn.DataTable.isDataTable('#ratings')) {
                    initializeDataTable();
                }
            } else {
                destroyDataTable();
            }
        }
        // Initial check on document ready
        handleResponsiveDataTable();
        // Handle window resize events
        $(window).resize(function() {
            handleResponsiveDataTable();
        });
    });
</script>