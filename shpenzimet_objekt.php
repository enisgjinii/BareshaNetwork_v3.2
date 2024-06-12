<?php
include 'partials/header.php';
include 'conn-d.php';
// Fetch data
$sql = "SELECT DATE(created_at) AS date, SUM(shuma) AS total_shuma FROM expenses GROUP BY DATE(created_at)";
$result = $conn->query($sql);
$expenses = [];
$total = 0;
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
        $total += $row['total_shuma'];
    }
} else {
    echo "0 results";
}
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Shpenzimet e objektit</a></li>
                </ol>
            </nav>
            <div class="row mb-2">
                <div>
                    <!-- Button trigger modal -->
                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#pagesmodal">
                        Shto shpenzim
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="pagesmodal" tabindex="-1" aria-labelledby="pagesmodalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="pagesmodalLabel">Shto shpenzim</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="expense-form" method="post" action="save_expense.php" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="recipient-name" class="col-form-label">Emri i regjistruesit:</label>
                                            <input type="text" class="form-control rounded-5 border border-2" id="recipient-name" name="recipient-name" value="<?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="message-text" class="col-form-label">Pershkrimi:</label>
                                            <textarea class="form-control rounded-5 border border-2" id="message-text" name="message"></textarea>
                                        </div>
                                        <!-- Add input for amount -->
                                        <div class="mb-3">
                                            <label for="amount" class="col-form-label">Shuma e shpenzimit:</label>
                                            <input type="number" class="form-control rounded-5 border border-2" id="amount" name="amount">
                                        </div>
                                        <!-- Add input for adding a file -->
                                        <div class="mb-3">
                                            <label for="expense-file" class="col-form-label">Dokumenti i shpenzimit:</label>
                                            <input type="file" class="form-control rounded-5 border border-2" id="expense-file" name="file">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbyll</button>
                                            <button type="submit" class="input-custom-css px-3 py-2">Ruaj</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3 shadow-sm rounded-5 mb-4 card">
                <div class="table-responsive">
                    <table class="table table-bordered" id="expenses-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Registruesi</th>
                                <th>Pershkrimi</th>
                                <th>Shuma</th>
                                <th>Dokumenti</th>
                                <th>Data e krijuar</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch data from the database
                            $query = "SELECT * FROM expenses";
                            $result = $conn->query($query);
                            // Check if there are any rows returned
                            if ($result->num_rows > 0) {
                                // Iterate over the fetched data and display it in table rows
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['registruesi'] . "</td>";
                                    echo "<td>" . $row['pershkrimi'] . "</td>";
                                    echo "<td>" . $row['shuma'] . "</td>";
                                    echo "<td>" . $row['dokumenti'] . "</td>";
                                    echo "<td>" . $row['created_at'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="input-custom-css px-3 py-2 edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-id="' . $row['id'] . '" data-registruesi="' . $row['registruesi'] . '" data-pershkrimi="' . $row['pershkrimi'] . '" data-shuma="' . $row['shuma'] . '"><i class="fi fi-rr-edit"></i></button>';
                                    echo '<button type="button" class="input-custom-css px-3 py-2 ms-2 delete-btn" data-id="' . $row['id'] . '"><i class="fi fi-rr-trash"></i></button>';
                                    // Check if there is a document before displaying the download button
                                    if (!empty($row['dokumenti'])) {
                                        echo '<a style="text-decoration: none;" href="uploads/' . $row['dokumenti'] . '" download="' . $row['dokumenti'] . '" class="input-custom-css px-3 py-2 ms-2"><i class="fi fi-rr-download"></i></a>';
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                // If no rows are returned, display a message
                                echo "<tr><td colspan='6'>No expenses found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-3 text-center">
                <div class="col-12 my-3">
                    <div class="rounded-5 border border-1 bg-white px-3 d-inline-block">
                        <h2 class="display-4 text-primary">Shpenzimet totale: $<?php echo number_format($total, 2); ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card p-5 w-100 mb-2 rounded-5 shadow-sm">
                        <div class="card-body">
                            <div id="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Expense Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-expense-form" method="post" action="update_expense.php">
                    <input type="hidden" id="edit-expense-id" name="id">
                    <div class="mb-3">
                        <label for="edit-recipient-name" class="col-form-label">Emri i regjistruesit:</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="edit-recipient-name" name="recipient-name">
                    </div>
                    <div class="mb-3">
                        <label for="edit-message-text" class="col-form-label">Pershkrimi:</label>
                        <textarea class="form-control rounded-5 border border-2" id="edit-message-text" name="message"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-amount" class="col-form-label">Shuma e shpenzimit:</label>
                        <input type="number" class="form-control rounded-5 border border-2" id="edit-amount" name="amount">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                        <button type="submit" class="input-custom-css px-3 py-2">Ruaj</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 10,
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded',
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + " €";
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#fff"]
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: 'Shpenzimet',
            data: [
                <?php
                foreach ($expenses as $expense) {
                    echo $expense['total_shuma'] . ', ';
                }
                ?>
            ]
        }],
        xaxis: {
            categories: [
                <?php
                foreach ($expenses as $expense) {
                    echo "'" . $expense['date'] . "', ";
                }
                ?>
            ],
            title: {
                text: 'Data'
            }
        },
        yaxis: {
            title: {
                text: 'Shuma (€)'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return "€ " + val.toFixed(2);
                }
            }
        },
        title: {
            text: 'Përmbledhje e shpenzimeve sipas datës',
            align: 'center',
            style: {
                fontSize: '20px',
                fontWeight: 'bold',
                color: '#263238'
            }
        }
    }
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>
<?php include 'partials/footer.php'; ?>
<script>
    // data table
    $(document).ready(function() {
        $('#expenses-table').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            stripeClasses: ["stripe-color"],
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
    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const registruesi = this.dataset.registruesi;
                const pershkrimi = this.dataset.pershkrimi;
                const shuma = this.dataset.shuma;
                document.getElementById('edit-expense-id').value = id;
                document.getElementById('edit-recipient-name').value = registruesi;
                document.getElementById('edit-message-text').value = pershkrimi;
                document.getElementById('edit-amount').value = shuma;
            });
        });
        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'A jeni i sigurt?',
                    text: "Nuk mund ta ktheni atë që fshini!",
                    icon: 'Kujdes',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Anulo',
                    confirmButtonText: 'Po, fshije!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`delete_expense_k.php?id=${id}`, {
                                method: 'GET',
                            })
                            .then(response => response.text())
                            .then(data => {
                                if (data === 'success') {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'There was a problem deleting the expense.',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'There was a problem deleting the expense.',
                                    'error'
                                );
                            });
                    }
                });
            });
        });
    });
</script>