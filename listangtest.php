<?php
include 'partials/header.php';
if (isset($_GET['import'])) {
    $linkuof = $_GET['import'];
    $curl = curl_init('https://bareshamusic.sourceaudio.com/api/import/upload?token=6636-66f549fbe813b2087a8748f2b8243dbc&url=http://panel.bareshaoffice.com/' . $linkuof);
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true
    )
    );
    $cdata = json_decode(curl_exec($curl), true);
    curl_close($curl);
    if ($cdata['error']) {
        echo '<script>alert("' . $cdata['error'] . '");</script>';
    } else {
        echo '<script>alert("' . $cdata['status'] . '");</script>';
    }
}
if (isset($_GET['del'])) {
    $stmt = $conn->prepare("DELETE FROM ngarkimi WHERE id=?");
    $stmt->bind_param("s", $_GET['del']);
    if ($stmt->execute()) {
        echo '<script>alert("Eshte fshir me sukses")</script>';
    } else {
        echo "Pershkrimi i gabimit: " . $conn->error;
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">

                <div class="p-5 rounded-5 shadow-sm mb-4 card">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Lista e keng&euml;ve</h4> <!-- Breadcrumb -->
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Video - Ngarkimi</a>
                            <span>/</span>
                            <a href="klient.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Lista e keng&euml;ve</u></a>
                        </h6>
                    </nav>
                    <!-- Breadcrumb -->
                </div>
                <div class="card rounded-5 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="example" class="table w-100">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>K&euml;ng&euml;tari</th>
                                                <th>Emri</th>
                                                <th>T.Shkruesi</th>
                                                <th>Muzika</th>
                                                <th>Orkesetra</th>
                                                <th>C/O</th>
                                                <th>FB</th>
                                                <th>IG</th>
                                                <th>Veper nga Koha</th>
                                                <th>Klienti</th>
                                                <th>Platformat Tjera</th>
                                                <th style="color:green;">Linku</th>
                                                <th style="color:green;">Linku Plat.</th>
                                                <th>Data</th>
                                                <th>Gjuha</th>
                                                <th>Info Shtes</th>
                                                <th>Postuar Nga</th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Check if the id is set in the GET request
                                            if (isset($_GET['id'])) {
                                                // Store the id in a variable
                                                $kid = $_GET['id'];
                                                // Create the query with the WHERE clause
                                                $query = "SELECT ngarkimi.*, klientet.emri AS klienti_emri, users.name AS postuar_nga
    FROM ngarkimi
    LEFT JOIN klientet ON ngarkimi.klienti=klientet.id
    LEFT JOIN users ON ngarkimi.nga=users.id
    WHERE klienti='$kid'
    ORDER BY id DESC";
                                            } else {
                                                // Create the query without the WHERE clause
                                                $query = "SELECT ngarkimi.*, klientet.emri AS klienti_emri, users.name AS postuar_nga
    FROM ngarkimi
    LEFT JOIN klientet ON ngarkimi.klienti=klientet.id
    LEFT JOIN users ON ngarkimi.nga=users.id
    ORDER BY id DESC";
                                            }
                                            // Execute the query
                                            $result = $conn->query($query);
                                            // Loop through the result set
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $row['kengetari']; ?>
                                                            <br>
                                                            <br>
                                                            <a class="btn btn-danger text-white shadow-sm rounded-5" href="?del=<?php echo $row['id']; ?>"><i class="fi fi-rr-trash" onclick="return confirm('A jeni i sigurt qe deshironi ta fshini?');"></i></a>
                                                        </td>
                                                        <td><?php echo $row['emri']; ?></td>
                                                        <td><?php echo $row['teksti']; ?></td>
                                                        <td><?php echo $row['muzika']; ?></td>
                                                        <td> <?php echo $row['orkestra']; ?></td>
                                                        <td><?php echo $row['co']; ?></td>
                                                        <td><?php echo $row['facebook']; ?></td>
                                                        <td><?php echo $row['instagram']; ?></td>
                                                        <td><?php echo $row['veper']; ?></td>
                                                        <td><?php echo $row['klienti_emri']; ?></td>
                                                        <td>
                                                            <?php echo $row['platformat']; ?>
                                                        </td>
                                                        <td><a href="<?php echo $row['linku']; ?>" target="_blank">Hap Linkun</a></td>
                                                        <td><a href="<?php echo $row['linkuplat']; ?>" target="_blank">Hap Linkun</a></td>
                                                        <td><?php echo $row['data']; ?></td>
                                                        <td><?php echo $row['gjuha']; ?></td>
                                                        <td><?php echo $row['infosh']; ?></td>
                                                        <td><?php echo $row['postuar_nga']; ?></td>
                                                    </tr>
                                            <?php } ?>

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
</div>
<!-- End of Main Content -->
<!-- <script src="js/tooltips.js"></script>
<script src="js/popover.js"></script> -->

<?php include 'partials/footer.php'; ?>


<script>
    $('#example').DataTable({
        responsive: true,
        order: [
            [12, "desc"]
        ],
        search: {
            return: true,
        },
        dom: 'Bfrtip',
        buttons: [{
            extend: 'pdfHtml5',
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            titleAttr: 'Eksporto tabelen ne formatin PDF',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'copyHtml5',
            text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            titleAttr: 'Kopjo tabelen ne formatin Clipboard',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'excelHtml5',
            text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
            titleAttr: 'Eksporto tabelen ne formatin CSV',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: 'Printo tabel&euml;n',
            className: 'btn btn-light border shadow-2 me-2'
        }],
        initComplete: function() {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');

        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],

    })
</script>
