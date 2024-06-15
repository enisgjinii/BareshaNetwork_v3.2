<?php
include 'partials/header.php';
include 'conn-d.php';
// Fetch options for the dropdown
$sql = "SELECT * FROM googleauth";
$result = mysqli_query($conn, $sql);
$options = "";
while ($row = mysqli_fetch_array($result)) {
    $options .= "<option value='" . $row["id"] . "'>" . $row["firstName"] . " " . $row["last_name"] . "</option>";
}
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee'];
    $payment = $_POST['payment'];
    if ($_POST['action'] == 'edit') {
        $payment_id = $_POST['payment_id'];
        $update_sql = "UPDATE employee_payments SET payment_amount='$payment' WHERE id='$payment_id' ";
        if (mysqli_query($conn, $update_sql)) {
            $message = "success";
        } else {
            $message = "error";
            error_log(mysqli_error($conn));
        }
    } elseif ($_POST['action'] == 'delete') {
        $payment_id = $_POST['payment_id'];
        // Create backup table if not exists
        $create_backup_table_sql = "CREATE TABLE IF NOT EXISTS employee_payments_backup (
            id INT,
            employee_id INT NOT NULL,
            payment_amount DECIMAL(10, 2) NOT NULL,
            payment_date TIMESTAMP,
            backup_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($conn, $create_backup_table_sql);
        // Insert row into backup table before deleting
        $backup_sql = "INSERT INTO employee_payments_backup (id, employee_id, payment_amount, payment_date)
                       SELECT id, employee_id, payment_amount, payment_date
                       FROM employee_payments
                       WHERE id='$payment_id'";
        if (mysqli_query($conn, $backup_sql)) {
            $delete_sql = "DELETE FROM employee_payments WHERE id='$payment_id'";
            if (mysqli_query($conn, $delete_sql)) {
                $message = "success";
            } else {
                $message = "error";
                error_log(mysqli_error($conn));
            }
        } else {
            $message = "error";
            error_log(mysqli_error($conn));
        }
    } else {
        // Create new table if not exists
        $create_table_sql = "CREATE TABLE IF NOT EXISTS employee_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            employee_id INT NOT NULL,
            payment_amount DECIMAL(10, 2) NOT NULL,
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (employee_id) REFERENCES googleauth(id)
        )";
        if (mysqli_query($conn, $create_table_sql)) {
            $insert_sql = "INSERT INTO employee_payments (employee_id, payment_amount) VALUES ('$employee_id', '$payment')";
            if (mysqli_query($conn, $insert_sql)) {
                $message = "success";
            } else {
                $message = "error";
                error_log(mysqli_error($conn));
            }
        } else {
            $message = "error";
            error_log(mysqli_error($conn));
        }
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontabiliteti</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Pagesat e punëtoreve</a></li>
                </ol>
            </nav>
            <div class="row mb-2">
                <div>
                    <!-- Button trigger modal -->
                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#pagesmodal">
                        Pagesë e re
                    </button>
                    <!-- Button trigger modal -->
                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#deletedRows">
                        Shiko listen e pagesave te fshira
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="deletedRows" tabindex="-1" aria-labelledby="deletedRowsLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="deletedRowsLabel">Lista e pagesave të fshira</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered w-100" id="deletedTableData">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>ID e rreshtit</th>
                                                    <th>Puntori</th>
                                                    <th>Shuma e paguar</th>
                                                    <th>Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM employee_payments_backup ORDER by id DESC";
                                                $result = mysqli_query($conn, $sql);
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $sql_client = "SELECT * FROM googleauth WHERE id = " . $row["employee_id"];
                                                    $result_client = mysqli_query($conn, $sql_client);
                                                    $row_client = mysqli_fetch_assoc($result_client);

                                                    echo "<tr>";
                                                    echo "<td>" . $row["id"] . "</td>";
                                                    echo "<td>" . $row_client["email"] . "</td>";
                                                    echo "<td>" . $row["payment_amount"] . "</td>";
                                                    echo "<td>" . $row["payment_date"] . "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                            <tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Create Modal -->
                    <div class="modal fade" id="pagesmodal" tabindex="-1" aria-labelledby="pagesmodalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="pagesmodalLabel">Krijo pagesën</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="create">
                                        <div class="mb-3">
                                            <label for="employee" class="col-form-label">Puntori:</label>
                                            <select class="form-select" aria-label="Zgjedhi puntorin" id="employee" name="employee">
                                                <?php echo $options; ?>
                                            </select>
                                            <script>
                                                new Selectr('#employee', {
                                                    searchable: true
                                                });
                                            </script>
                                        </div>
                                        <div class="mb-3">
                                            <label for="recipient-name" class="col-form-label">Pagesa:</label>
                                            <input type="text" class="form-control rounded-5 border border-2" id="recipient-name" name="payment">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button><button type="submit" class="input-custom-css px-3 py-2">Ruaj</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3 shadow-sm rounded-5 mb-4 card">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead class="bg-light">
                            <tr>
                                <th>Puntori</th>
                                <th>Pagesa</th>
                                <th>Data</th>
                                <th>Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT ep.*, ga.firstName, ga.last_name FROM employee_payments ep INNER JOIN googleauth ga ON ep.employee_id = ga.id";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row["firstName"] . " " . $row["last_name"] . "</td>";
                                echo "<td>" . $row["payment_amount"] . "</td>";
                                echo "<td>" . $row["payment_date"] . "</td>";
                                echo '<td>
                <button class="input-custom-css px-3 py-2 edit-btn" 
                        data-id="' . $row["id"] . '" 
                        data-employee_id="' . $row["employee_id"] . '" 
                        data-employee_name="' . $row["firstName"] . ' ' . $row["last_name"] . '" 
                        data-payment_amount="' . $row["payment_amount"] . '" 
                        data-bs-toggle="modal" data-bs-target="#editModal"><i class="fi fi-rr-edit"></i></button>
                <button class="input-custom-css px-3 py-2 delete-btn" 
                        data-id="' . $row["id"] . '" 
                        data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fi fi-rr-trash"></i></button>
              </td>';
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div><!-- Edit Modal -->
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edito pagesën</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="payment_id" id="edit-payment-id">
                    <div class="mb-3">
                        <label for="edit-employee-id" class="col-form-label">Puntori ID:</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="edit-employee-id" name="employee_id" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-employee-name" class="col-form-label">Puntori:</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="edit-employee-name" name="employee_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-payment-amount" class="col-form-label">Pagesa:</label>
                        <input type="text" class="form-control rounded-5 border border-2" id="edit-payment-amount" name="payment">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                <button type="submit" class="input-custom-css px-3 py-2">Ruaj</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">Fshi pagesën</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                A jeni i sigurt që dëshironi të fshini këtë pagesë?
                <form method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="payment_id" id="delete-payment-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button> <button type="submit" class="input-custom-css px-3 py-2">Fshije</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let message = "<?php echo $message; ?>";
        if (message === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Aksioni përfundoi me sukses.'
            });
        } else if (message === "error") {
            Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: 'Pati një gabim gjatë përpunimit të kërkesës suaj. Ju lutemi provoni përsëri.'
            });
        }
        // Attach data to edit modal
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit-payment-id').value = this.getAttribute('data-id');
                document.getElementById('edit-employee-id').value = this.getAttribute('data-employee_id');
                document.getElementById('edit-employee-name').value = this.getAttribute('data-employee_name');
                document.getElementById('edit-payment-amount').value = this.getAttribute('data-payment_amount');
            });
        });
        // Attach data to delete modal
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete-payment-id').value = this.getAttribute('data-id');
            });
        });
    });
    // Add datatable 
    $(document).ready(function() {
        $('#table').DataTable({
            "searching": {
                "regex": true
            },

            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    type: ''
                }
            },
            "paging": true,
            "pageLength": 10,
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
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
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"],
            ],
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
            stripeClasses: ["stripe-color"],
        });
        $('#deletedTableData').DataTable({
            "searching": {
                "regex": true
            },
            responsive: true,
            "paging": true,
            "pageLength": 10,
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
            stripeClasses: ["stripe-color"],
        });
    });
</script>
<?php
include 'partials/footer.php';
?>