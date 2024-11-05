<?php

include('partials/header.php');

include('page_access_controller.php'); // Ensure this file handles user authentication and permissions

// Fetch contracts data
$kueri = $conn->query("SELECT * FROM kontrata ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="sq">

<head>
    <!-- Include Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        /* Custom Styles */

        /* Apply fixed table layout to respect column widths */
        table {
            table-layout: fixed;
            width: 100%;
            word-wrap: break-word;
        }

        /* Reduce padding and adjust font size for table cells */
        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
            /* Reduced from default */
            font-size: 13px;
            /* Slightly smaller font */
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            /* Prevent text from overflowing */
        }

        /* Allow text to wrap in specific columns if needed */
        .wrap-text {
            white-space: normal !important;
        }

        /* Adjust modal title font size */
        .modal-title {
            font-size: 1.1rem;
            /* Reduced from 1.25rem */
        }

        /* Adjust button sizes within the table */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 12px;
            border-radius: 0.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Additional adjustments for the DataTables buttons */
        .dt-buttons .btn {
            margin: 0 2px;
            padding: 0.3rem 0.6rem;
            font-size: 12px;
            white-space: nowrap;
        }

        /* Adjust input group elements */
        .input-group .form-control {
            padding: 0.3rem 0.5rem;
            font-size: 13px;
        }

        /* Reduce margins for form labels and small text */
        .form-label {
            margin-bottom: 0.2rem;
            font-size: 13px;
        }

        small.text-muted {
            font-size: 11px;
        }

        /* Adjust DataTables pagination and info text */
        .dataTables_paginate,
        .dataTables_info {
            font-size: 12px;
        }

        /* Adjust breadcrumb font size and spacing */
        .breadcrumb-item {
            font-size: 13px;
        }

        /* Optional: Remove or reduce card padding if necessary */
        .card-body {
            padding: 1rem;
            /* Adjust as needed */
        }

        /* Optional: Adjust modal padding */
        .modal-body {
            padding: 0.8rem;
            /* Reduced from default */
        }

        /* Optional: Adjust Toastr notifications font size */
        #toast-container>div {
            font-size: 13px;
        }

        /* Ensure actions buttons fit within their column */
        .actions-btn {
            display: flex;
            gap: 0.2rem;
            justify-content: center;
        }

        /* Tooltip styling for truncated text */
        th,
        td {
            position: relative;
        }

        th:hover::after,
        td:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 0;
            top: 100%;
            background: #333;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: normal;
            z-index: 10;
            width: max-content;
            max-width: 200px;
            display: block;
        }

        th:hover::after,
        td:hover::after {
            display: none;
        }

        th:hover::after,
        td:hover::after {
            display: block;
        }

        /* Adjust icon sizes within buttons */
        .btn i {
            margin: 0;
            font-size: 14px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {

            /* Adjust modal size for smaller screens */
            .modal-lg {
                max-width: 90%;
            }

            /* Reduce button sizes further if necessary */
            .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 11px;
            }
        }
    </style>
</head>

<body>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb Navigation -->
                <nav class="bg-white px-2 rounded-5 mb-3" style="width:fit-content;" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Kontrata</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Lista e kontratave (Këngë)</a></li>
                    </ol>
                </nav>
                <!-- Contracts List Card -->
                <div class="card shadow-sm rounded-5">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Lista e kontratave</h4>
                        <div class="row mb-3">
                            <!-- Date Range Filters -->
                            <div class="col-md-6 mb-3">
                                <label for="min" class="form-label">Prej:</label>
                                <small class="text-muted">Zgjidhni një diapazon fillues të datës për të filtruar rezultatet.</small>
                                <div class="input-group mt-1">
                                    <span class="input-group-text bg-white border-0"><i class="fi fi-rr-calendar"></i></span>
                                    <input type="text" id="min" name="min" class="form-control rounded-5" placeholder="Zgjidhni datën e fillimit" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max" class="form-label">Deri:</label>
                                <small class="text-muted">Zgjidhni një diapazon mbarues të datës për të filtruar rezultatet.</small>
                                <div class="input-group mt-1">
                                    <span class="input-group-text bg-white border-0"><i class="fi fi-rr-calendar"></i></span>
                                    <input type="text" id="max" name="max" class="form-control rounded-5" placeholder="Zgjidhni datën e mbarimit" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- Contracts DataTable -->
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Emri dhe Mbiemri</th>
                                        <th>Perqindja (%)</th>
                                        <th>Klienti</th>
                                        <th>Vepra</th>
                                        <th>Data</th>
                                        <th>Kontrata PDF</th>
                                        <th>Kontrata e Vjetër</th>
                                        <th>Veprime</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($k = mysqli_fetch_array($kueri)) { ?>
                                        <tr>
                                            <td class="wrap-text" data-tooltip="<?php echo htmlspecialchars($k['emri'] . ' ' . $k['mbiemri']); ?>">
                                                <?php echo htmlspecialchars($k['emri'] . ' ' . $k['mbiemri']); ?>
                                            </td>
                                            <!-- Nënshkrimi Modal -->
                                            <div class="modal fade" id="nenshkrimiModal<?php echo $k['id']; ?>" tabindex="-1" aria-labelledby="nenshkrimiModalLabel<?php echo $k['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="nenshkrimiModalLabel<?php echo $k['id']; ?>">Nënshkrimi i <?php echo htmlspecialchars($k['emri'] . ' ' . $k['mbiemri']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <?php if (!empty($k['nenshkrimi'])) {
                                                                $file_path = htmlspecialchars($k['nenshkrimi']);
                                                                $download_filename = 'Nënshkrimi - ' . htmlspecialchars($k['emri'] . ' ' . $k['mbiemri']) . '.png';
                                                            ?>
                                                                <img src="<?php echo $file_path; ?>" alt="Nënshkrimi" class="img-fluid mb-2" style="max-width: 150px;">
                                                                <div>
                                                                    <a href="<?php echo $file_path; ?>" download="<?php echo $download_filename; ?>" class="btn btn-sm btn-success me-2" title="Shkarko">
                                                                        <i class="fi fi-rr-download"></i>
                                                                    </a>
                                                                    <a href="<?php echo $file_path; ?>" target="_blank" class="btn btn-sm btn-secondary" title="Shiko">
                                                                        <i class="fi fi-rr-resize"></i>
                                                                    </a>
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="badge bg-danger">Nuk është e nënshkruar</span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <td data-tooltip="<?php echo htmlspecialchars($k['perqindja']); ?>">
                                                <?php echo htmlspecialchars($k['perqindja']); ?>
                                            </td>
                                            <td class="wrap-text" data-tooltip="<?php echo htmlspecialchars(explode('|', $k['klienti'])[0]); ?>">
                                                <?php echo htmlspecialchars(explode('|', $k['klienti'])[0]); ?>
                                            </td>
                                            <td class="wrap-text" data-tooltip="<?php echo htmlspecialchars($k['vepra']); ?>">
                                                <?php echo htmlspecialchars($k['vepra']); ?>
                                            </td>
                                            <td data-tooltip="<?php echo date("d-m-Y", strtotime($k['data'])); ?>">
                                                <?php echo date("d-m-Y", strtotime($k['data'])); ?>
                                            </td>
                                            <td data-tooltip="<?php echo !empty($k['pdf_file']) ? 'Shiko PDF' : 'Nuk ka PDF'; ?>">
                                                <?php if (!empty($k['pdf_file'])) { ?>
                                                    <a href="<?php echo htmlspecialchars($k['pdf_file']); ?>" target="_blank" class="btn btn-sm btn-primary" title="Shiko PDF">
                                                        <i class="fi fi-rr-file-pdf"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="badge bg-warning text-dark rounded-pill">Nuk ka PDF</span>
                                                <?php } ?>
                                            </td>
                                            <td data-tooltip="<?php echo !empty($k['pdf_file_original']) ? 'Shiko e Vjetër' : 'Nuk ka e Vjetër'; ?>">
                                                <?php if (!empty($k['pdf_file_original'])) { ?>
                                                    <a href="<?php echo htmlspecialchars($k['pdf_file_original']); ?>" target="_blank" class="btn btn-sm btn-secondary" title="Shiko e Vjetër">
                                                        <i class="fi fi-rr-file-pdf"></i>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="badge bg-warning text-dark rounded-pill">Nuk ka e Vjetër</span>
                                                <?php } ?>
                                            </td>
                                            <td class="actions-btn" data-tooltip="Veprime">
                                                <!-- Edit and Delete Buttons -->
                                                <a href="modifiko_kontraten.php?id=<?php echo $k['id']; ?>" class="input-custom-css px-3 py-2" title="Modifiko Kontraten">
                                                    <i class="fi fi-rr-edit"></i>
                                                </a>
                                                <button class="input-custom-css px-3 py-2 delete-contract" data-id="<?php echo $k['id']; ?>" title="Fshije Kontraten">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                                <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#nenshkrimiModal<?php echo $k['id']; ?>" title="Shiko Nënshkrimin">
                                                    <i class="fi fi-rr-receipt"></i>
                                                </button>
                                                <?php if (empty($k['nenshkrimi'])) { ?>
                                                    <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal" data-id="<?php echo $k['id']; ?>" data-email="<?php echo htmlspecialchars($k['klient_email']); ?>" onclick="updateEmailInput(this)" data-link="https://panel.bareshaoffice.com/kontrataPerKlient.php?id=" title="Dërgo Kontraten">
                                                        <i class="fi fi-rr-envelope"></i>
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Send Contract Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="sendContractModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="POST" action="">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="sendContractModalLabel">Dërgo Kontraten</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Email Input -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Emaili Klientit</label>
                                            <input type="email" name="email" id="email" class="form-control rounded-5" required>
                                        </div>
                                        <!-- Contract Link Input -->
                                        <div class="mb-3">
                                            <label for="linkuKontrates" class="form-label">Linku i Kontratës</label>
                                            <div class="input-group">
                                                <input type="text" name="linkuKontrates" class="form-control rounded-5" id="linkuKontrates" readonly required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()" title="Kopjo Linkun">
                                                    <i class="fi fi-rr-copy"></i> Kopjo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbyll</button>
                                        <button type="submit" name="submit" class="btn btn-primary">Dërgo</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <!-- Include necessary scripts like jQuery, Bootstrap JS, DataTables JS, Flatpickr, etc. -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            // Initialize Toastr options
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000",
            };

            // Function to update email input in the send contract modal
            function updateEmailInput(button) {
                var id = button.getAttribute("data-id");
                var email = button.getAttribute("data-email");
                var link = button.getAttribute('data-link') + id + '&token=' + "<?php echo isset($token) ? $token : ''; ?>";
                document.getElementById('email').value = email;
                document.getElementById('linkuKontrates').value = link;
            }

            // Function to copy contract link to clipboard
            function copyToClipboard() {
                const linkInput = document.getElementById("linkuKontrates");
                linkInput.select();
                linkInput.setSelectionRange(0, 99999); // For mobile devices
                navigator.clipboard.writeText(linkInput.value).then(() => {
                    toastr.success('Linku është kopjuar në clipboard.');
                }).catch(() => {
                    toastr.error('Dështoi kopjimi i linkut.');
                });
            }

            // Initialize DataTables for Main Contracts List
            $(document).ready(function() {
                let table = $('#example').DataTable({
                    responsive: false, // Disabled responsive to prevent horizontal scroll
                    "ordering": false,
                    "searching": true,
                    "dom": "<'row mb-3'<'col-sm-2'l><'col-sm-6 text-center'B><'col-sm-4'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-6'i><'col-sm-6'p>>",
                    "buttons": [{
                            extend: 'pdfHtml5',
                            text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
                            titleAttr: 'Eksporto në PDF',
                            className: 'input-custom-css px-3 py-2 me-1'
                        },
                        {
                            extend: 'copyHtml5',
                            text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
                            titleAttr: 'Kopjo në clipboard',
                            className: 'input-custom-css px-3 py-2 me-1'
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
                            titleAttr: 'Eksporto në Excel',
                            className: 'input-custom-css px-3 py-2 me-1'
                        },
                    ],
                    "language": {
                        "url": "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
                    },
                    "stripeClasses": ['stripe-color'],
                    "columnDefs": [{
                            "width": "15%",
                            "targets": 0
                        },
                        {
                            "width": "5%",
                            "targets": 1
                        },
                        {
                            "width": "10%",
                            "targets": 2
                        },
                        {
                            "width": "10%",
                            "targets": 3
                        },
                        {
                            "width": "8%",
                            "targets": 4
                        },
                        {
                            "width": "10%",
                            "targets": 5
                        },
                        {
                            "width": "10%",
                            "targets": 6
                        },
                        {
                            "width": "22%",
                            "targets": 7
                        },
                        {
                            "targets": [0, 1, 2, 3, 4, 5, 6, 7],
                            "className": "text-center"
                        }
                    ],
                    initComplete: function() {
                        $(".dt-buttons").removeClass("dt-buttons btn-group");
                        $("div.dataTables_length select").addClass("form-select form-select-sm").css({
                            width: 'auto',
                            margin: '0 8px',
                            padding: '0.2rem 0.5rem',
                            lineHeight: '1.5',
                            border: '1px solid #ced4da',
                            borderRadius: '0.2rem',
                        });
                    },
                    "pagingType": "simple_numbers", // More compact pagination
                    "pageLength": 10, // Adjust as needed
                });

                // Initialize Flatpickr for date inputs
                flatpickr("#min", {
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    locale: "sq"
                });

                flatpickr("#max", {
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    locale: "sq"
                });

                // Custom date range filtering
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    let min = $('#min').val() ? new Date($('#min').val()) : null;
                    let max = $('#max').val() ? new Date($('#max').val()) : null;
                    let date = new Date(data[4]); // Assuming date is in the fifth column

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

                // Event listener for date range filtering
                $('#min, #max').on('change', function() {
                    table.draw();
                });
            });

            // Handle Delete Contract with Confirmation
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
                        reverseButtons: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to delete script
                            window.location.href = "api/delete_methods/delete_kontraten.php?id=" + contractId;
                        }
                    });
                });
            });

            // Handle Form Submission for Sending Contracts (if applicable)
            // Ensure you have appropriate handling on the server-side
        </script>
</body>

</html>

<?php

require './vendor/autoload.php'; // Ensure Composer's autoload is correctly set up

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Token Generation
$dateComponent = date('Ymd');
$nameComponent = 'BAR';
$token = $dateComponent . $nameComponent;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $expirationTime = time() + (24 * 60 * 60); // 24 hours from now

    // Prepare the SQL statement using prepared statements for security
    $stmt = $conn->prepare("INSERT INTO tokens (token, expiration_time) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("si", $token, $expirationTime);
        if (!$stmt->execute()) {
            echo "<script>
                toastr.error('Gabim gjatë krijimit të tokenit: " . addslashes($stmt->error) . "');
            </script>";
            exit();
        }
        $stmt->close();
    } else {
        echo "<script>
            toastr.error('Gabim gjatë përgatitjes së query-t: " . addslashes($conn->error) . "');
        </script>";
        exit();
    }

    // Retrieve form inputs
    $linkuKontrates = trim($_POST['linkuKontrates'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo "<script>
            toastr.error('Emaili i dhënë është i pavlefshëm.');
        </script>";
        exit();
    }

    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bareshakontrata@gmail.com';
        $mail->Password   = 'ygxcwgkqyzmlmbcj'; // **Important:** Never expose credentials in code. Use environment variables.
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom('bareshakontrata@gmail.com', 'Baresha Kontratë');
        $mail->addAddress($email);

        // Embed Brand Icon
        $mail->addStringEmbeddedImage(file_get_contents('images/brand-icon.png'), 'brand-icon', 'brand-icon.png');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Kontrata për Këngë';
        $mail->Body    = '
            <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                            color: #333333;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #ffffff;
                            border: 1px solid #dddddd;
                            border-radius: 8px;
                        }
                        .btn {
                            display: inline-block;
                            padding: 10px 20px;
                            font-size: 16px;
                            color: #ffffff;
                            background-color: #007bff;
                            text-decoration: none;
                            border-radius: 5px;
                        }
                        .btn:hover {
                            background-color: #0056b3;
                        }
                        .footer {
                            margin-top: 20px;
                            font-size: 12px;
                            color: #777777;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div style="text-align: center;">
                            <img src="cid:brand-icon" alt="Baresha Kontratë" style="width: 80px; height: auto;">
                        </div>
                        <h2>Përshëndetje,</h2>
                        <p>Ju lutem klikoni butonin më poshtë për të nënshkruar kontratën për këngë.</p>
                        <p><a href="' . htmlspecialchars($linkuKontrates) . '" class="btn">Kontrata</a></p>
                        <p>Ju faleminderit!</p>
                        <div class="footer">
                            <p>Ky link skadon pas 24 ore nga dita e dërgimit.</p>
                        </div>
                    </div>
                </body>
            </html>
        ';

        // Send Email
        $mail->send();
        echo "<script>
            toastr.success('Email-i është dërguar me sukses.');
            setTimeout(function() { window.location.href = '" . $_SERVER['PHP_SELF'] . "'; }, 3000);
        </script>";
    } catch (Exception $e) {
        echo "<script>
            toastr.error('Email-i dështoi për shkak të: {$mail->ErrorInfo}');
        </script>";
    }
}
?>

<?php include 'partials/footer.php'; ?>