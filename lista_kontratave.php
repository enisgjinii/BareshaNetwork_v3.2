<?php
// ob_start();
include('partials/header.php');
?>
<style>
    .wrap-text {
        white-space: normal !important;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontratat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                            Lista e kontratave ( Këngë )
                        </a>
                    </li>
            </nav>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2 my-2" data-bs-toggle="modal" data-bs-target="#deletedContractsOfSongs">
                Lista e kontratave ( Këngë ) të fshira
            </button>
            <!-- Modal -->
            <div class="modal fade" id="deletedContractsOfSongs" tabindex="-1" aria-labelledby="deletedContractsOfSongsLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deletedContractsOfSongsLabel">
                                Lista e kontratave ( Këngë ) të fshira
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table w-100" id="deleted_contracts_of_songs">
                                <thead class="table-light">
                                    <!-- Emri dhe mbiemri	Perqindja	Klienti	Vepra	Data	Kontrata PDF	Kontrata e vjeter	Modifiko -->
                                    <tr>
                                        <th>Emri dhe mbiemri</th>
                                        <th>Perqindja</th>
                                        <th>Klienti</th>
                                        <th>Vepra</th>
                                        <th>Data</th>
                                        <th>Kontrata PDF</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm rounded-5">
                <div class="card-body">
                    <h4 class="card-title">Lista e kontratave</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive p-1">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="min" class="form-label" style="font-size: 14px;">Prej:</label>
                                        <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon fillues të
                                            dates për të filtruar rezultatet.</p>
                                        <div class="input-group rounded-5">
                                            <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span>
                                            <input type="text" id="min" name="min" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" style="cursor: pointer;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="max" class="form-label" style="font-size: 14px;">Deri:</label>
                                        <p class="text-muted" style="font-size: 10px;">Zgjidhni një diapazon mbarues të
                                            dates për tëfiltruar rezultatet.</p>
                                        <div class="input-group rounded-5">
                                            <span class="input-group-text border-0" style="background-color: white;cursor: pointer;"><i class="fi fi-rr-calendar"></i></span><input type="text" id="max" name="max" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" style="cursor: pointer;">
                                        </div>
                                    </div>
                                </div>
                                <table id="example" class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="font-size: 14px;" style="font-size: 14px;">Emri dhe mbiemri</th>
                                            <th style="font-size: 14px;" style="font-size: 14px;">Perqindja</th>
                                            <th style="font-size: 14px;">Klienti</th>
                                            <th style="font-size: 14px;">Vepra</th>
                                            <th style="font-size: 14px;">Data</th>
                                            <th style="font-size: 14px;">Kontrata PDF</th>
                                            <th style="font-size: 14px;">Kontrata e vjeter</th>
                                            <th style="font-size: 14px;">Modifiko</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $kueri = $conn->query("SELECT * FROM kontrata ORDER BY id DESC");
                                        while ($k = mysqli_fetch_array($kueri)) {
                                        ?>
                                            <tr>
                                                <td style="font-size: 12px;" class="wrap-text" style="font-size: 12px;">
                                                    <?php echo $k['emri']; ?>
                                                    <?php echo $k['mbiemri']; ?>
                                                    <!-- Button trigger modal -->
                                                    <br><br>
                                                    <button type="button" class="btn btn-primary rounded-5 text-white m-0 px-3 show-modal-button" data-bs-toggle="modal" data-bs-target="#nenshkrimiModal<?php echo $k['id']; ?>" data-row-id="1">
                                                        <i class="fi fi-rr-receipt"></i>
                                                    </button>
                                                </td>
                                                <!-- Modal -->
                                                <div class="modal fade" id="nenshkrimiModal<?php echo $k['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Nënshkrimi i <?php echo $k['emri']; ?> <?php echo $k['mbiemri']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" id="modal-content">
                                                                <?php
                                                                if ($k['nenshkrimi'] == !null) {
                                                                    $file_path = $k['nenshkrimi'];
                                                                    $download_filename = 'Nënshkrimi - ' . ' ' . $k['emri'] . ' ' . $k['mbiemri'] . '.png'; // Create the download filename
                                                                    // Generate the download link with the dynamic filename
                                                                    $download_link = '<a class="btn btn-sm btn-primary shadow-sm rounded-5 text-white" href="' . $file_path . '" download="' . $download_filename . '">
                            ' . '<i class="fi fi-rr-download"></i>' . '
                         </a>';
                                                                ?>
                                                                    <img src="<?php echo $file_path; ?>" style="width: 100px; height: auto;">
                                                                    <div>
                                                                        <?php echo $download_link; ?>
                                                                        <a class="btn btn-sm btn-secondary shadow-sm rounded-5 text-white" href="<?php echo $file_path; ?>" target="_blank"><i class="fi fi-rr-resize"></i></a>
                                                                    </div>
                                                                <?php
                                                                } else {
                                                                    echo '<span class="badge rounded-pill text-bg-danger text-white w-100">Nuk është e nenshkruar</span>';
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <?php echo $k['perqindja'] ?>
                                                </td>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <?php
                                                    $nameAndEmail = explode("|", $k['klienti']);
                                                    echo $nameAndEmail[0];
                                                    ?>
                                                </td>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <?php echo $k['vepra']; ?>
                                                </td>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <?= date("d-m-Y", strtotime($k['data'])); ?>
                                                </td>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <div class="dropdown">
                                                        <button class="btn py-2 btn-primary dropdown-toggle shadow-sm rounded-5" type="button" id="kontrataDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fi fi-rr-box-open text-white"></i>
                                                        </button>
                                                        <ul class="dropdown-menu rounded-5 " style="border:1px solid lightgrey;" aria-labelledby="kontrataDropdown">
                                                            <li><a style="width:95%;" class="dropdown-item rounded-5 mx-auto px-1 my-1 border" href="kontrata_pdf.php?id=<?php echo $k['id']; ?>">Web
                                                                    <i class="fi fi-rr-browser"></i></a>
                                                            </li>
                                                            <li><a style="width:95%;" class="dropdown-item rounded-5 mx-auto px-1 my-1 border" href="kontrata_pdfOriginal.php?id=<?php echo $k['id']; ?>">PDF
                                                                    <i class="fi fi-rr-file-pdf"></i></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <?php
                                                    if (!empty($k['pdf_file'])) {
                                                        $pdf_file_path = $k['pdf_file'];
                                                    ?>
                                                        <a href="<?php echo $pdf_file_path; ?>" target="_blank" class="btn btn-primary rounded-5 text-white">
                                                            <i class="fi fi-rr-file-pdf"></i> Shiko PDF
                                                        </a>
                                                    <?php
                                                    } else {
                                                        echo 'Nuk ka PDF';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="font-size: 12px;" class="wrap-text">
                                                    <!-- Add edit and delete buttons -->
                                                    <a href="modifiko_kontraten.php?id=<?php echo $k['id']; ?>" class="btn btn-primary rounded-5 text-white"><i class="fi fi-rr-edit"></i></a>
                                                    <a href="#" class="btn btn-danger text-white rounded-5 delete-contract" data-id="<?php echo $k['id']; ?>"><i class="fi fi-rr-trash"></i></a>
                                                    <br><br>
                                                    <?php if ($k['nenshkrimi'] == !null) { ?>
                                                    <?php } else {
                                                    ?>
                                                        <button type="button" class="btn btn-success rounded-5 text-white" data-bs-toggle="modal" data-bs-target="#exampleModal" data-id="<?php echo $k['id']; ?>" data-email="<?php echo $k['klient_email']; ?>" onclick="updateEmailInput(this)" data-link="https://panel.bareshaoffice.com/kontrataPerKlient.php?id=">
                                                            <i class="fi fi-rr-envelope"></i>
                                                        </button>
                                                    <?php } ?>
                                                    <!-- Add a button with a Google Drive icon and to go redirect to file called add_manual_conttract.php -->
                                                    <!-- <br> -->
                                                    <a href="add_manual_contract.php?id=<?php echo $k['id']; ?>" class="btn btn-light rounded-5 border- border-2"><img style="width: 24px; height: 24px;" src="https://img.icons8.com/color/256/google-drive--v2.png" alt="google-drive--v2" /></a>
                                                </td>
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
<?php
require './vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$dateComponent = date('Ymd');
$nameComponent = 'BAR';
$token = $dateComponent . $nameComponent;
if (isset($_POST['submit'])) {
    $expirationTime = time() + (24 * 60 * 60);
    // Prepare the SQL statement
    $query = "INSERT INTO tokens (token, expiration_time) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        // Bind the values to the statement parameters
        mysqli_stmt_bind_param($stmt, "si", $token, $expirationTime);
        // Execute the statement
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            // Handle the query execution error
            echo "Error executing query: " . mysqli_stmt_error($stmt);
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Handle the statement preparation error
        echo "Error preparing statement: " . mysqli_error($conn);
    }
    // $deleteQuery = "DELETE FROM tokens WHERE expiration_time < " . time();
    // mysqli_query($conn, $deleteQuery);
    $linkuKontrates = $_POST['linkuKontrates'];
    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bareshakontrata@gmail.com'; // SMTP username
    $mail->Password   = 'ygxcwgkqyzmlmbcj'; // SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet = 'UTF-8';
    // Sender and recipient settings
    $mail->setFrom('bareshakontrata@gmail.com', 'Baresha Kontratë');
    $mail->addAddress($_POST['email']);
    // Attach image
    $mail->addStringEmbeddedImage(file_get_contents('images/brand-icon.png'), 'brand-icon', 'brand-icon.png');
    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Kontrata për këngë';
    $mail->Body    = '<html>
                            <head>
                                <style>
                                    /* Stilet tuaja CSS këtu */
                                </style>
                            </head>
                            <body>
                                <div style="max-width: 500px; margin: 0 auto; padding: 20px; background-color: #ffffff; border: 1px solid rgba(27, 31, 35, .15); border-radius: 6px; box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;">
                                    <div style="text-align: center;">
                                        <img src="cid:brand-icon" alt="" width="25%" style="display: inline-block;">
                                    </div>
                                    <br>
                                    <br>
                                    <h1 style="color: #333333; font-size: 24px; margin-bottom: 10px;">Përshendetje</h1>
                                    <p style="color: #555555; font-size: 16px; margin-bottom: 10px;">Klikoni butonin më poshtë për të kaluar në faqen për të nënshkruar kontratën për këngë.</p>
                                    <p><a href="' . $linkuKontrates . '" style="appearance: none; background-color: #ffffff; border: 1px solid rgba(27, 31, 35, .15); border-radius: 6px; box-shadow: rgba(27, 31, 35, .1) 0 1px 0; box-sizing: border-box; color: #000000; cursor: pointer; display: inline-block; font-size: 14px; font-weight: 600; line-height: 20px; padding: 6px 16px; position: relative; text-align: center; text-decoration: none; user-select: none; -webkit-user-select: none; touch-action: manipulation; vertical-align: middle; white-space: nowrap;" class="button">Kontrata</a></p>
                                    <p>Ju faleminderit</p>
                                    <i>Ky link skadon pas 24 ore prej ketij momenti</i>
                                </div>
                            </body>
                        </html>';
    try {
        // Send email
        $mail->send();
        echo "<script>
            Swal.fire({
                title: 'Sukses',
                text: 'Email-i juaj është dërguar.',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            }).then(function() {
                location.href = '" . $_SERVER['PHP_SELF'] . "';
            });
        </script>";
        exit();
    } catch (Exception $e) {
        echo "Email dështoi për shkak të: {$mail->ErrorInfo}";
    }
}
?>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Dërgo kontraten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="email" class="form-label">Emaili klientit</label>
                                <input type="email" name="email" id="email" class="form-control shadow-sm rounded-5">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="linkuKontrates" class="form-label">Linku kontrates</label>
                            <div class="input-group">
                                <input type="text" name="linkuKontrates" class="form-control shadow-sm rounded-5 " id="linkuKontrates" readonly>
                                <button class="btn btn-sm btn-light rounded-5 shadow-2 ms-3" style="border: 1px solid lightgrey;text-transform:none;" onclick="copyToClipboard()" type="button">
                                    <i class="fi fi-rr-copy" style="display:inline-block;vertical-align:middle;"></i>
                                    <span style="display:inline-block;vertical-align:middle;">Kopjo</span>
                                </button>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-light rounded-5 float-right border" style="text-transform:none;" name="submit">
                    <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                    <span style="display:inline-block;vertical-align:middle;">Dërgo</span>
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php
include('partials/footer.php');
ob_flush();
?>
<script>
    var token = "<?php echo isset($token) ? $token : ''; ?>";
    function updateEmailInput(button) {
        var id = button.getAttribute("data-id");
        var tableRow = button.closest("tr");
        var link = button.getAttribute('data-link');
        var linkWithToken = link + id + '&token=' + token;
        var linkInput = document.getElementById('linkuKontrates');
        linkInput.value = linkWithToken;
        var emailInput = document.getElementById('email');
        var email = button.getAttribute('data-email');
        emailInput.value = email;
    }
</script>
<script>
    function copyToClipboard() {
        const linkInput = document.getElementById("linkuKontrates");
        linkInput.select();
        document.execCommand("copy");
        Swal.fire({
            icon: 'success',
            title: 'Kopjuar!',
            text: 'Vlera është kopjuar në clipboard.',
            timer: 2000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            showConfirmButton: false
        });
    }
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#deleted_contracts_of_songs').DataTable({
            "processing": true,
            "serverSide": true,
            ordering: false,
            "ajax": {
                "url": "deleted_contracts_of_songs.php", // Specify the URL of your server-side script
                "type": "POST" // Use POST method to send data
            },
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }],
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
            stripeClasses: ['stripe-color'],
            "columns": [{
                    "data": null,
                    "render": function(data, type, row) {
                        // Concatenate "emri" and "mbiemri" fields
                        return data.emri + " " + data.mbiemri;
                    }
                },
                {
                    "data": "perqindja"
                },
                {
                    "data": "klienti",
                    "render": function(data, type, row) {
                        // Split the string by "|" and return the first part
                        return data.split('|')[0];
                    }
                },
                {
                    "data": "vepra"
                },
                {
                    "data": "data"
                },
                {
                    "data": "pdf_file"
                },
            ],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5],
                "render": function(data, type, row) {
                    return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
                }
            }]
        });
        // Initialize Flatpickr for date inputs with Albanian locale
        let minDate = flatpickr('#min', {
            dateFormat: "Y-m-d",
            locale: "sq"
        });
        let maxDate = flatpickr('#max', {
            dateFormat: "Y-m-d",
            locale: "sq"
        });
        let table = $('#example').DataTable({
            responsive: false,
            "ordering": false,
            "searching": true,
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin CSV',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
            }, {
                text: '<i class="fi fi-rr-add-document fa-lg"></i>&nbsp;&nbsp; Shto kontratë',
                className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
                action: function(e, node, config) {
                    window.location.href = 'kontrata_2.php';
                }
            }],
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
            stripeClasses: ['stripe-color'],
            columnDefs: [{
                    width: '2%',
                    targets: [0]
                }, // Adjust the width as needed
                {
                    width: '10%',
                    targets: [1, 2, 3]
                },
                {
                    width: '20%',
                    targets: [4, 6, 7]
                },
                {
                    width: '2%',
                    targets: [5]
                }, {
                    targets: 4, // Assuming the "Data" column is at index 4
                    type: 'date-range',
                    // Customize the date format if needed
                    render: function(data) {
                        return moment(data, 'DD-MM-YYYY').format('YYYY-MM-DD');
                    }
                }
            ],
        });
        // Custom filtering function which will search data in column four between two values
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let min = minDate.input.value ? new Date(minDate.input.value) : null;
            let max = maxDate.input.value ? new Date(maxDate.input.value) : null;
            let date = new Date(data[4]);
            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        });
        // Refilter the table
        $('#min, #max').on('change', function() {
            table.draw();
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".delete-contract").click(function(e) {
            e.preventDefault();
            var contractId = $(this).data("id");
            Swal.fire({
                icon: "warning",
                title: "Jeni të sigurt?",
                text: "Ky veprim nuk mund të zhbëhet!",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Po, fshije!",
                cancelButtonText: "Anulo",
                reverseButtons: true, // Reverses the positions of the confirm and cancel buttons
                allowOutsideClick: false, // Prevents users from closing the modal by clicking outside of it
                allowEscapeKey: false // Prevents users from closing the modal by pressing the Escape key
            }).then((result) => {
                if (result.isConfirmed) {
                    // Nëse është konfirmuar, ridrejtohuni te skenari i fshirjes me ID-në e kontratës
                    window.location.href = "fshij_kontraten.php?id=" + contractId;
                }
            });
        });
    });
</script>