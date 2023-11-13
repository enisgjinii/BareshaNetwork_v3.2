<?php
include 'partials/header.php'; ?>

<?php

if (isset($_POST['ruaj'])) {
    // Validate and sanitize input
    $emri = isset($_POST['emri']) ? intval($_POST['emri']) : 0;
    $data = isset($_POST['data']) ? mysqli_real_escape_string($conn, $_POST['data']) : '';
    $fatura = isset($_POST['fatura']) ? mysqli_real_escape_string($conn, $_POST['fatura']) : '';
    $gjendjaFatures = isset($_POST['gjendjaFatures']) ? mysqli_real_escape_string($conn, $_POST['gjendjaFatures']) : '';

    // Validate and sanitize the array of links
    $linkuIKengesArray = array();
    for ($i = 1; $i <= 5; $i++) {
        $fieldName = "linkuIKenges_" . $i;
        if (isset($_POST[$fieldName])) {
            $linkuIKengesArray[] = mysqli_real_escape_string($conn, $_POST[$fieldName]);
        }
    }
    $kenga = implode(",", $linkuIKengesArray);

    $emri_i_kengetarit = isset($_POST['emri_i_kengetarit']) ? mysqli_real_escape_string($conn, $_POST['emri_i_kengetarit']) : '';

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO fatura (emri, emrifull, data, fatura, gjendja_e_fatures) VALUES (?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("issss", $emri, $emrifull, $data, $fatura, $gjendjaFatures);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect upon successful insertion
        header("Location: shitje.php?fatura=$fatura");
        exit;
    } else {
        // Handle errors
        echo "Gabim: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}



if (isset($_GET['fshij'])) {
    $fshijid = $_GET['fshij'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT emri, fatura, data FROM fatura WHERE fatura = ?");
    $stmt->bind_param("s", $fshijid);
    $stmt->execute();
    $stmt->store_result();

    // Bind result variables
    $stmt->bind_result($emr, $fatura2, $data2);

    // Fetch the result
    $stmt->fetch();

    // Close the statement
    $stmt->close();

    if ($conn->query("INSERT INTO draft (emri, data, fatura) VALUES (?, ?, ?)")) {
        $stmt2 = $conn->prepare("DELETE FROM fatura WHERE fatura = ?");
        $stmt2->bind_param("s", $fshijid);
        $stmt2->execute();
        $stmt2->close();

        $shdraft = $conn->query("SELECT * FROM shitje WHERE fatura = '$fshijid'");

        while ($draft = mysqli_fetch_array($shdraft)) {
            $shemertimi = $draft['emertimi'];
            $shqmimi = $draft['qmimi'];
            $shperqindja = $draft['perqindja'];
            $shklienti = $draft['klientit'];
            $shmbetja = $draft['mbetja'];
            $shtotali = $draft['totali'];
            $shfatura = $draft['fatura'];
            $shdata = $draft['data'];

            // Use prepared statement to prevent SQL injection
            $stmt3 = $conn->prepare("INSERT INTO shitjedraft (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt3->bind_param("ssssssss", $shemertimi, $shqmimi, $shperqindja, $shklienti, $shmbetja, $shtotali, $shfatura, $shdata);
            $stmt3->execute();
            $stmt3->close();

            $conn->query("DELETE FROM shitje WHERE fatura='$fshijid'");
        }
    } else {
        echo '<script>alert("' . $conn->error . '");</script>';
    }
}

?>


<div class="main-panel" id="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="faturat.php" class="text-reset" style="text-decoration: none;">
                                Pagesat Youtube ( Version i vjetër )
                                <span class="badge bg-warning text-dark rounded-5">v3.2 Punon</span>
                                <span class="badge bg-danger text-white rounded-5">v3.3 Zhvlersohet</span>

                            </a></li>
                </nav>
                <div id="alert_message"></div>
                <div class="row mb-4">
                    <div>
                        <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fi fi-rr-add-document fa-lg"></i>&nbsp; Fatur&euml; e
                            re</button>
                        <button style="text-transform: none;" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#pagesmodal"><i class="fi fi-rr-badge-dollar fa-lg"></i>&nbsp; Pages&euml; e
                            re</button>
                    </div>
                </div>
                <?php
                $query = "SELECT
                f.*,
                k.id AS klient_id,
                k.emri AS klient_emri,
                s.totali AS shitje_totali,
                COALESCE(SUM(p.shuma), 0) AS pagesa_totali,
                GROUP_CONCAT(DISTINCT s.emertimi) AS shitje_emertimi
            FROM
                fatura AS f
            LEFT JOIN
                klientet AS k ON f.emri = k.id
            LEFT JOIN
                shitje AS s ON f.fatura = s.fatura
            LEFT JOIN
                pagesat AS p ON f.fatura = p.fatura
            WHERE
                s.totali - COALESCE(p.shuma, 0) > 0
            GROUP BY
                f.id, k.id, k.emri
            ORDER BY
                f.id DESC;
            ";


                $result = $conn->query($query);
                ?>

                <?php if ($result->num_rows > 0) : ?>
                    <div class="card p-5 mt-3 rounded">
                        <table class="table table-bordered" id="faturat">
                            <thead>
                                <tr>
                                    <th>Emri</th>
                                    <th>Data</th>
                                    <th>Fatura</th>
                                    <th>Shuma</th>
                                    <th>Shuma e paguar</th>
                                    <th>Obligim</th>
                                    <th>Borgj</th>
                                    <th>Veprimet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <?php
                                    $invoice_fatura = $row['fatura'];
                                    $shitje_totali = $row['shitje_totali'];
                                    $pagesa_shuma = $row['pagesa_totali'];
                                    $emertimi = $row['shitje_emertimi'];
                                    $obligim = $shitje_totali - $pagesa_shuma;
                                    $invoice_klient_id = $row['klient_id'];
                                    $loan_query = "SELECT * FROM yinc WHERE kanali = '$invoice_klient_id'";
                                    $loan_result = $conn->query($loan_query);

                                    $total_loan = 0;

                                    while ($loan = mysqli_fetch_assoc($loan_result)) {
                                        $amount = $loan['shuma'] - $loan['pagoi'];
                                        if ($amount > 0) {
                                            $total_loan += $amount;
                                        }
                                    }
                                    // Only display the row if $obligim is greater than 0
                                    if ($obligim > 0) :
                                    ?>

                                        <tr>
                                            <td>
                                                <?php echo $row['klient_emri']; ?>
                                                <br><br>
                                                <a href="#" class="show-details btn btn-sm rounded py-2 border shadow-sm text-muted" style="text-transform: none;font-size: 12px;"><i class="fi fi-rr-eye"></i></a>
                                                <span class="text-muted details" style="display: none;"><?php echo $row['shitje_emertimi']; ?></span>
                                            </td>

                                            <td><?php echo $row['data']; ?></td>
                                            <td><?php echo $invoice_fatura; ?></td>
                                            <td><?php echo $shitje_totali; ?></td>
                                            <td>
                                                <?php
                                                if ($pagesa_shuma === null || $pagesa_shuma === '') {
                                                    echo '<i class="fi fi-rr-clock m-0 p-0"></i>';
                                                } else {
                                                    echo $pagesa_shuma;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $obligim; ?></td>
                                            <td><?php echo $total_loan; ?></td>
                                            <td>
                                                <a class="button-custom-light open-modal" role="button" data-tooltip="Paguaj" data-invoice-fatura="<?php echo $invoice_fatura; ?>" data-shitje-totali="<?php echo $obligim; ?>">
                                                    <i class="fi fi-rr-money-bill-wave"></i>
                                                </a>
                                                <a class="button-custom-light" role="button" data-tooltip="Print" href="fatura.php?invoice=<?php echo $invoice_fatura; ?>" target="_blank"><i class="fi fi-rr-print"></i></a>
                                                <a class="button-custom-light" role="button" data-tooltip="Ndrysho" href="shitje.php?fatura=<?php echo $invoice_fatura; ?>"><i class="fi fi-rr-edit"></i></a>
                                                <a class="button-custom-light delete" role="button" name="delete" data-tooltip="Fshi" id="<?php echo $invoice_fatura; ?>"><i class="fi fi-rr-trash"></i></a>
                                                <a class="button-custom-light" role="button" data-tooltip="Shih" href="fatura_details.php?invoice_fatura=<?php echo $invoice_fatura; ?>"><i class="fi fi-rr-square-plus"></i></a>
                                            </td>
                                        </tr><?php endif; ?>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php include 'pages_e_re.php' ?>
<?php include 'fature_e_re.php' ?>

<script>
    $(document).ready(function() {

        // Code for handling delete functionality
        function handleDelete(id) {
            Swal.fire({
                title: 'A je i sigurt ?',
                text: "Ky veprim nuk mund të zhbëhet.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Po, hiqeni!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "api/deletefat.php",
                        method: "POST",
                        data: {
                            id: id
                        },
                        success: function(data) {
                            // Refresh the page

                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error if needed
                            $("#alert_message").html(
                                '<p class="alert alert-danger">Error: ' + error + "</p>"
                            );
                        },
                    });
                }
            });
        }

        // Event listener for delete button
        $(document).on("click", ".delete", function() {
            var id = $(this).attr("id");
            handleDelete(id);
        });

        // Code for opening modal
        $(document).on('click', '.open-modal', function() {
            const fatura = $(this).data("invoice-fatura");
            const shitje_totali = $(this).data("shitje-totali");
            $('#id_of_fatura').val(fatura);
            $('#shuma').val(shitje_totali);
            const myModal = new bootstrap.Modal(document.getElementById('pagesmodal'));
            myModal.show();
        });

        // Code for saving data via AJAX
        $('#btnruaj').click(function() {
            var data = $('#user_form').serialize() + '&btn_save=btn_save';
            $.ajax({
                url: 'api/shto_pages.php',
                type: 'POST',
                data: data,
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: response,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                    setTimeout(function() {
                        $('#pagesmodal').modal('hide');
                    }, 1800);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: error,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        timer: 1500
                    });
                }
            });
        });

        // Code for showing/hiding details
        $('.show-details').on('click', function(event) {
            // Prevent the default link behavior
            event.preventDefault();

            // Find the next sibling element with class "details"
            var details = $(this).next('.details');

            // Toggle the display of the details span
            details.toggle();
        });

        // Code for initializing DataTable
        $('#faturat').DataTable({
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
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ["stripe-color"],
        });
    });
</script>


<?php include 'partials/footer.php'; ?>