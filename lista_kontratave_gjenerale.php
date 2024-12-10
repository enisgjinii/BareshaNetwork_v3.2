<?php
session_start();
include('conn-d.php');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

function sanitizeOutput($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

include('partials/header.php');
?>
<style>
    .table-sm td,
    .table-sm th {
        padding: 0.3rem;
        font-size: 0.9rem;
    }

    .input-custom-css {}

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    .document-dot {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        cursor: pointer;
    }

    .document-patente_shoferi {
        background-color: #007bff;
    }

    .document-leternjoftim {
        background-color: #28a745;
    }

    .document-pasaporte {
        background-color: #ffc107;
    }

    .document-default {
        background-color: #6c757d;
    }

    /* Offcanvas Content Styling */
    .offcanvas-header {
        border-bottom: 1px solid #dee2e6;
    }

    .offcanvas-body {
        padding: 0;
    }

    .document-viewer {
        width: 100%;
        height: 100%;
        border: none;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Lista e kontratatave (Gjenerale)</a></li>
                </ol>
            </nav>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo sanitizeOutput($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Mbyll"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo sanitizeOutput($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Mbyll"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <div class="card shadow-sm rounded-5">
                <div class="card-body">
                    <form method="POST" id="bulkDeleteForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <button type="submit" id="deleteButton" name="delete_selected" class="input-custom-css px-3 py-2 mb-4" disabled data-bs-toggle="tooltip" title="Fshij kontratat e zgjedhura">
                            <i class="fi fi-rr-trash me-1"></i> Fshij
                        </button>
                        <div class="table-responsive">
                            <table id="contractsTable" class="table table-bordered table-hover table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-dark">
                                            <input type="checkbox" id="selectAll" data-bs-toggle="tooltip" title="Zgjidh të gjitha kontratat">
                                        </th>
                                        <th class="text-dark">Emri dhe Mbiemri</th>
                                        <th class="text-dark">Data e Krijimit</th>
                                        <th class="text-dark">Data e Skadimit</th>
                                        <th class="text-dark">Përqindja</th>
                                        <th class="text-dark">Vepro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
                                        if (isset($_POST['selected_contracts']) && is_array($_POST['selected_contracts'])) {
                                            $selected = array_map('intval', $_POST['selected_contracts']);
                                            if (!empty($selected)) {
                                                $placeholders = implode(',', array_fill(0, count($selected), '?'));
                                                $stmt_delete = $conn->prepare("DELETE FROM kontrata_gjenerale WHERE id IN ($placeholders)");
                                                if ($stmt_delete) {
                                                    $types = str_repeat('i', count($selected));
                                                    $stmt_delete->bind_param($types, ...$selected);
                                                    if ($stmt_delete->execute()) {
                                                        $_SESSION['success'] = 'Kontratave të zgjedhura u fshijnë me sukses!';
                                                    } else {
                                                        $_SESSION['error'] = 'Gabim gjatë fshirjes së kontratave.';
                                                    }
                                                    $stmt_delete->close();
                                                } else {
                                                    $_SESSION['error'] = 'Gabim në përgatitjen e kërkesës SQL për fshirje.';
                                                }
                                            } else {
                                                $_SESSION['error'] = 'Nuk u zgjedh asnjë kontratë për fshirje.';
                                            }
                                        } else {
                                            $_SESSION['error'] = 'Nuk u zgjedh asnjë kontratë për fshirje.';
                                        }
                                        header("Location: " . $_SERVER['PHP_SELF']);
                                        exit();
                                    }
                                    $stmt = $conn->prepare("SELECT * FROM kontrata_gjenerale ORDER BY id DESC");
                                    if ($stmt) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while ($k = $result->fetch_assoc()) {
                                            $data_e_krijimit = $k['data_e_krijimit'];
                                            $kohezgjatja = $k['kohezgjatja'];
                                            $expiration_date = date('Y-m-d', strtotime($data_e_krijimit . ' + ' . $kohezgjatja . ' months'));
                                            $documentTypes = [
                                                'patente_shoferi' => 'Patentë shoferi',
                                                'leternjoftim'    => 'Letërnjoftim',
                                                'pasaporte'       => 'Pasaporte',
                                            ];
                                            $documentColorClasses = [
                                                'patente_shoferi' => 'document-patente_shoferi',
                                                'leternjoftim'    => 'document-leternjoftim',
                                                'pasaporte'       => 'document-pasaporte',
                                            ];
                                            $currentDocumentType = isset($k['lloji_dokumentit']) ? $k['lloji_dokumentit'] : '';
                                            $documentLabel = array_key_exists($currentDocumentType, $documentTypes) ? $documentTypes[$currentDocumentType] : 'I Panjohur';
                                            $documentColorClass = isset($documentColorClasses[$currentDocumentType]) ? $documentColorClasses[$currentDocumentType] : 'document-default';
                                            // Assuming 'document_path' contains the URL or path to the document
                                            $documentPath = isset($k['document_path']) ? sanitizeOutput($k['document_path']) : '';
                                    ?>
                                            <tr data-id="<?php echo sanitizeOutput($k['id']); ?>">
                                                <td>
                                                    <input type="checkbox" name="selected_contracts[]" value="<?php echo sanitizeOutput($k['id']); ?>" class="form-check-input contract-checkbox" data-bs-toggle="tooltip" title="Zgjidh kontratën">
                                                </td>
                                                <td>
                                                    <?php echo sanitizeOutput($k['emri'] . ' ' . $k['mbiemri']); ?>
                                                    <br><br>
                                                    <button type="button" class="input-custom-css px-3 py-2 show-modal-button" data-bs-toggle="modal" data-bs-target="#nenshkrimiModal<?php echo sanitizeOutput($k['id']); ?>" data-bs-toggle="tooltip" title="Shiko detajet e kontratës">
                                                        <i class="fi fi-rr-user"></i>
                                                    </button>
                                                    <span class="document-dot <?php echo sanitizeOutput($documentColorClass); ?>" data-bs-toggle="tooltip" title="<?php echo sanitizeOutput($documentLabel); ?>"></span>
                                                </td>
                                                <td>
                                                    <?php echo sanitizeOutput($k['data_e_krijimit']); ?>
                                                </td>
                                                <td>
                                                    <?php echo sanitizeOutput($expiration_date); ?>
                                                </td>
                                                <td>
                                                    <?php echo sanitizeOutput($k['tvsh']); ?>%
                                                </td>
                                                <td>
                                                    <div class="dropdown d-inline-block me-2">
                                                        <button class="btn btn-primary dropdown-toggle input-custom-css px-3 py-2 shadow-sm rounded-5 btn-sm" type="button" id="kontrataDropdown<?php echo sanitizeOutput($k['id']); ?>" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" title="Vepro me kontratën">
                                                            <i class="fi fi-rr-box-open"></i>
                                                        </button>
                                                        <ul class="dropdown-menu rounded-5 border" aria-labelledby="kontrataDropdown<?php echo sanitizeOutput($k['id']); ?>">
                                                            <li>
                                                                <a class="dropdown-item input-custom-css px-3 py-2 border rounded-5" href="kontrata_gjenerale_pdf.php?id=<?php echo sanitizeOutput($k['id']); ?>" data-bs-toggle="tooltip" title="Eksporto në PDF">
                                                                    PDF <i class="fi fi-rr-file-pdf ms-2"></i>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="modifiko-kontraten-gjenerale.php?id=<?php echo sanitizeOutput($k['id']); ?>" class="btn btn-primary input-custom-css px-3 py-2 rounded-5 me-2 btn-sm" data-bs-toggle="tooltip" title="Modifiko kontratën">
                                                        <i class="fi fi-rr-edit"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger input-custom-css px-3 py-2 rounded-5 me-2 btn-sm" onclick="confirmDelete(event, '<?php echo sanitizeOutput($k['id']); ?>')" data-bs-toggle="tooltip" title="Fshij kontratën">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-secondary input-custom-css px-3 py-2 rounded-5 me-2 btn-sm open-doc-button" data-document="<?php echo $documentPath; ?>" data-bs-toggle="tooltip" title="Hap Dokumentin">
                                                        <i class="fi fi-rr-file"></i>
                                                    </a>
                                                    <?php if (empty($k['nenshkrimi'])): ?>
                                                        <button type="button" class="btn btn-success input-custom-css px-3 py-2 rounded-5 btn-sm" data-bs-toggle="modal" data-bs-target="#sendEmailModal" data-id="<?php echo sanitizeOutput($k['id']); ?>" data-email="<?php echo sanitizeOutput($k['email']); ?>" data-link="https://panel.bareshaoffice.com/kontrataGjeneralePerKlient.php?id=" data-bs-toggle="tooltip" title="Dërgo kontratën via Email">
                                                            <i class="fi fi-rr-envelope"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="nenshkrimiModal<?php echo sanitizeOutput($k['id']); ?>" tabindex="-1" aria-labelledby="nenshkrimiModalLabel<?php echo sanitizeOutput($k['id']); ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="nenshkrimiModalLabel<?php echo sanitizeOutput($k['id']); ?>">Detajet e Kontratës - <?php echo sanitizeOutput($k['emri'] . ' ' . $k['mbiemri']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="container">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h5 class="mb-4">Informacioni i Kontratës</h5>
                                                                        <ul class="list-group">
                                                                            <?php if (!empty($k['id_kontrates'])): ?>
                                                                                <li class="list-group-item">
                                                                                    <strong>ID e Kontratës:</strong> <?php echo sanitizeOutput($k['id_kontrates']); ?>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                            <?php if (!empty($k['youtube_id'])): ?>
                                                                                <li class="list-group-item">
                                                                                    <strong>ID e YouTube:</strong> <?php echo sanitizeOutput($k['youtube_id']); ?>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                            <?php if (!empty($k['artisti'])): ?>
                                                                                <li class="list-group-item">
                                                                                    <strong>Artisti:</strong> <?php echo sanitizeOutput($k['artisti']); ?>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h5 class="mb-4">Informacioni i Bankës</h5>
                                                                        <ul class="list-group">
                                                                            <?php if (!empty($k['kodi_swift'])): ?>
                                                                                <li class="list-group-item">
                                                                                    <strong>Kodi i Swift:</strong> <?php echo sanitizeOutput($k['kodi_swift']); ?>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                            <?php if (!empty($k['iban'])): ?>
                                                                                <li class="list-group-item">
                                                                                    <strong>IBAN:</strong> <?php echo sanitizeOutput($k['iban']); ?>
                                                                                </li>
                                                                            <?php endif; ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbyll</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                        $stmt->close();
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-danger'>Gabim në përgatitjen e kërkesës SQL.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas Component for Document Viewing -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="documentOffcanvas" aria-labelledby="documentOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="documentOffcanvasLabel">Shiko Dokumentin</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Mbyll"></button>
    </div>
    <div class="offcanvas-body p-0">
        <iframe src="" class="document-viewer" id="documentViewer"></iframe>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendEmailModalLabel">Dërgo Kontraten via Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Mbyll"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="contract_id" id="contract_id">
                    <div class="mb-3">
                        <label for="email" class="form-label">Emaili Klientit</label>
                        <input type="email" name="email" id="email" class="form-control input-custom-css px-3 py-2" required data-bs-toggle="tooltip" title="Shkruani emailin e klientit">
                    </div>
                    <div class="mb-3">
                        <label for="linkuKontrates" class="form-label">Linku i Kontratës</label>
                        <input type="text" name="linkuKontrates" id="linkuKontrates" class="form-control input-custom-css px-3 py-2" readonly data-bs-toggle="tooltip" title="Linku do të gjenerohet automatikisht">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary input-custom-css px-3 py-2" data-bs-dismiss="modal">Anulo</button>
                    <button type="submit" name="submit" class="btn btn-light input-custom-css px-3 py-2 border">
                        <i class="fi fi-rr-paper-plane me-1"></i> Dërgo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $linkuKontrates = filter_var(trim($_POST['linkuKontrates']), FILTER_SANITIZE_URL);
    $contract_id = intval($_POST['contract_id']);
    if ($email && $linkuKontrates && $contract_id > 0) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bareshakontrata@gmail.com';
            $mail->Password   = 'ygxcwgkqyzmlmbcj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('bareshakontrata@gmail.com', 'Baresha Kontratë');
            $mail->addAddress($email);
            $mail->addStringEmbeddedImage(file_get_contents('images/brand-icon.png'), 'brand-icon', 'brand-icon.png');
            $mail->isHTML(true);
            $mail->Subject = 'Kontrata Gjenerale';
            $mail->Body    = '
                <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff; border: 1px solid #ced4da; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                            .btn { display: inline-block; padding: 10px 20px; margin: 10px 0; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; }
                            .btn:hover { background-color: #0056b3; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div style="text-align: center;">
                                <img src="cid:brand-icon" alt="Baresha Kontratë" width="100">
                            </div>
                            <h2>Përshendetje,</h2>
                            <p>Ju lutemi klikoni butonin më poshtë për të kaluar në faqen për të nënshkruar kontratën gjenerale.</p>
                            <p><a href="' . $linkuKontrates . '" class="btn">Kontrata</a></p>
                            <p>Ky link do të skadojë pas 24 orësh prej kësaj dërgeseje.</p>
                            <p>Ju faleminderit!</p>
                        </div>
                    </body>
                </html>
            ';
            $mail->CharSet = 'UTF-8';
            $mail->send();
            $_SESSION['success'] = 'Emaili u dërgua me sukses!';
        } catch (Exception $e) {
            $_SESSION['error'] = "Emaili nuk mund të dërgohet. Gabimi i Mailer: {$mail->ErrorInfo}";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error'] = 'Ju lutem sigurohuni që të gjitha fushat janë plotësuar saktë.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<?php include('partials/footer.php'); ?>

<script>
    function confirmDelete(event, id) {
        event.preventDefault();
        Swal.fire({
            title: 'Konfirmoni fshirjen',
            text: 'Jeni i sigurt që dëshironi ta fshini këtë rekord?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Po, fshijeni',
            cancelButtonText: 'Anulo',
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`api/delete_methods/delete_kontrata_gjenerale.php?id=${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'U fshi!',
                    text: 'Kontrata është fshirë.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    var table = $('#contractsTable').DataTable();
                    var row = $('tr[data-id="' + id + '"]');
                    table.row(row).remove().draw();
                });
            } else if (result.isConfirmed && result.value && !result.value.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    text: result.value.message || 'Nuk mund të fshihet kontrata.',
                });
            }
        });
    }

    // Offcanvas Document Viewer
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });

        var table = $('#contractsTable').DataTable({
            responsive: true,
            searching: true,
            ordering: true,
            order: [
                [2, 'desc']
            ],
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [{
                    extend: 'pdfHtml5',
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
                    titleAttr: 'Eksporto tabelën në formatin PDF',
                    className: 'btn btn-light btn-sm input-custom-css px-3 py-2 me-2 rounded-5'
                },
                {
                    extend: 'copyHtml5',
                    text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
                    titleAttr: 'Kopjo tabelën në Clipboard',
                    className: 'btn btn-light btn-sm input-custom-css px-3 py-2 me-2 rounded-5'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
                    titleAttr: 'Eksporto tabelën në formatin Excel',
                    className: 'btn btn-light btn-sm input-custom-css px-3 py-2 me-2 rounded-5'
                },
                {
                    extend: 'print',
                    text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
                    titleAttr: 'Printo tabelën',
                    className: 'btn btn-light btn-sm input-custom-css px-3 py-2 me-2 rounded-5'
                },
                {
                    text: '<i class="fi fi-rr-add-document fa-lg"></i> Shto Kontratë',
                    className: 'btn btn-light btn-sm input-custom-css px-3 py-2 me-2 rounded-5',
                    action: function(e, dt, node, config) {
                        window.location.href = 'kontrata_gjenerale_2.php';
                    },
                    titleAttr: 'Shto një kontratë të re'
                }
            ],
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.removeClass('dt-buttons btn-group').addClass('d-flex justify-content-center mb-3');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select');
                lengthSelect.css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem',
                });
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color']
        });

        // Open Document Button Click Handler
        $('.open-doc-button').on('click', function(e) {
            e.preventDefault();
            var documentPath = $(this).data('document');
            if (documentPath) {
                $('#documentViewer').attr('src', documentPath);
                var documentOffcanvas = new bootstrap.Offcanvas(document.getElementById('documentOffcanvas'));
                documentOffcanvas.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    text: 'Dokumenti nuk u gjet.',
                });
            }
        });
    });

    // Send Email Modal Script
    var sendEmailModal = document.getElementById('sendEmailModal');
    sendEmailModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var contractId = button.getAttribute('data-id');
        var email = button.getAttribute('data-email');
        var linkBase = button.getAttribute('data-link');
        var modal = this;
        modal.querySelector('#contract_id').value = contractId;
        modal.querySelector('#email').value = email;
        modal.querySelector('#linkuKontrates').value = linkBase + contractId;
    });

    // Toggle Select All Checkboxes
    document.getElementById('selectAll').addEventListener('change', function(event) {
        var checkboxes = document.querySelectorAll('.contract-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = event.target.checked;
        });
        toggleDeleteButton();
    });

    function toggleDeleteButton() {
        var checkboxes = document.querySelectorAll('.contract-checkbox:checked');
        var deleteButton = document.getElementById('deleteButton');
        deleteButton.disabled = checkboxes.length < 1;
    }

    var individualCheckboxes = document.querySelectorAll('.contract-checkbox');
    individualCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', toggleDeleteButton);
    });
</script>