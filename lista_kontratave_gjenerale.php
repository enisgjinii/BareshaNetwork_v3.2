<?php
include('partials/header.php');
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontratat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                            Lista e kontratave ( Gjenerale )
                        </a>
                    </li>
                </ol>
            </nav>
            <div class="card shadow-sm rounded-5">
                <div class="card-body">
                    <form method="POST" action="api/delete_methods/delete_contracts.php">
                        <button type="submit" id="deleteButton" name="delete_selected" class="btn btn-danger rounded-5 mb-4 text-white btn-sm" disabled>
                            <i class="fi fi-rr-trash"></i> Fshij
                        </button>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-dark">#</th>
                                                <th class="text-dark">Emri dhe mbiemri</th>
                                                <th class="text-dark">Data e krijimit</th>
                                                <th class="text-dark">Data e skadimit</th>
                                                <th class="text-dark">Përqindja</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $kueri = $conn->query("SELECT * FROM kontrata_gjenerale ORDER BY id DESC");
                                            while ($k = mysqli_fetch_array($kueri)) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="selected_contracts[]" value="<?php echo $k['id']; ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo $k['emri']; ?>
                                                        <?php echo $k['mbiemri']; ?>
                                                        <br><br>
                                                        <button type="button" class="btn btn-primary rounded-5 text-white m-0 px-3 show-modal-button" data-bs-toggle="modal" data-bs-target="#nenshkrimiModal<?php echo $k['id']; ?>" data-row-id="1">
                                                            <i class="fi fi-rr-user"></i>
                                                        </button>
                                                    </td>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="nenshkrimiModal<?php echo $k['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">Detajet e
                                                                        kontrates
                                                                        <?php echo $k['emri']; ?>
                                                                        <?php echo $k['mbiemri']; ?>
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body" id="modal-content">
                                                                    <div class="container">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <h5 class="mb-4">Informacioni i Kontratës</h5>
                                                                                <ul class="list-group">
                                                                                    <?php if (!empty($k['id_kontrates'])) { ?>
                                                                                        <li class="list-group-item">
                                                                                            <p class="text-muted p-0 m-0" style="font-size: 10px;">ID e kontrates</p>
                                                                                            <p><?php echo $k['id_kontrates']; ?></p>
                                                                                        </li>
                                                                                    <?php } ?>
                                                                                    <?php if (!empty($k['youtube_id'])) { ?>
                                                                                        <li class="list-group-item">
                                                                                            <p class="text-muted p-0 m-0" style="font-size: 10px;">ID e youtubes</p>
                                                                                            <p><?php echo $k['youtube_id']; ?></p>
                                                                                        </li>
                                                                                    <?php } ?>
                                                                                    <?php if (!empty($k['artisti'])) { ?>
                                                                                        <li class="list-group-item">
                                                                                            <p class="text-muted p-0 m-0" style="font-size: 10px;">Artisti</p>
                                                                                            <p>
                                                                                                <?php
                                                                                                $nameAndEmail = explode("|", $k['artisti']);
                                                                                                echo $nameAndEmail[0];
                                                                                                ?>
                                                                                            </p>
                                                                                        </li>
                                                                                    <?php } ?>
                                                                                    <!-- Add similar checks for other fields -->
                                                                                </ul>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <h5 class="mb-4">Informacioni i Bankës</h5>
                                                                                <ul class="list-group">
                                                                                    <?php if (!empty($k['kodi_swift'])) { ?>
                                                                                        <li class="list-group-item">
                                                                                            <p class="text-muted p-0 m-0" style="font-size: 10px;">Kodi i swifteve</p>
                                                                                            <p><?php echo $k['kodi_swift']; ?></p>
                                                                                        </li>
                                                                                    <?php } ?>
                                                                                    <?php if (!empty($k['iban'])) { ?>
                                                                                        <li class="list-group-item">
                                                                                            <p class="text-muted p-0 m-0" style="font-size: 10px;">IBAN</p>
                                                                                            <p><?php echo $k['iban']; ?></p>
                                                                                        </li>
                                                                                    <?php } ?>
                                                                                    <!-- Add similar checks for other fields -->
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <td>
                                                        <?php echo $k['data_e_krijimit']; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        // Assuming $k is your array containing data_e_krijimit and kohezgjatja
                                                        $data_e_krijimit = $k['data_e_krijimit'];
                                                        $kohezgjatja = $k['kohezgjatja'];
                                                        // Calculate expiration date
                                                        $expiration_date = date('Y-m-d', strtotime($data_e_krijimit . ' + ' . $kohezgjatja . ' months'));
                                                        echo $expiration_date;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $k['tvsh']; ?>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn py-2 btn-primary dropdown-toggle shadow-sm rounded-5" type="button" id="kontrataDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fi fi-rr-box-open text-white"></i>
                                                            </button>
                                                            <ul class="dropdown-menu rounded-5 " style="border:1px solid lightgrey;" aria-labelledby="kontrataDropdown">
                                                                <li><a style="width:95%;" class="dropdown-item rounded-5 mx-auto px-1 my-1 border" href="kontrata_gjenerale_pdf.php?id=<?php echo $k['id']; ?>">PDF
                                                                        <i class="fi fi-rr-file-pdf"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <!-- Add edit and delete buttons -->
                                                        <a href="modifiko-kontraten-gjenerale.php?id=<?php echo $k['id']; ?>" class="btn btn-primary rounded-5 py-1 text-white"><i class="fi fi-rr-edit"></i></a>
                                                        <a href="api/delete_methods/delete_fshij_kontraten_gjenerale.php?id=<?php echo $k['id']; ?>" class="btn btn-danger rounded-5 py-1 text-white" onclick="confirmDelete(event, '<?php echo $k['id']; ?>')">
                                                            <i class="fi fi-rr-trash"></i>
                                                        </a>
                                                        <?php if ($k['nenshkrimi'] == !null) { ?>
                                                        <?php } else {
                                                        ?>
                                                            <button type="button" class="btn btn-success rounded-5 py-1 text-white" data-bs-toggle="modal" data-bs-target="#exampleModal" data-id="<?php echo $k['id']; ?>" data-email="<?php echo $k['email']; ?>" onclick="updateEmailInput(this)" data-link="https://panel.bareshaoffice.com/kontrataGjeneralePerKlient.php?id=">
                                                                <i class="fi fi-rr-envelope"></i>
                                                            </button>
                                                        <?php } ?>
                                                        <br><br>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
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

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $linkuKontrates = $_POST['linkuKontrates'];
    // Inicializimi i objektit PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Cilësimet e serverit
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Serveri SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bareshakontrata@gmail.com'; // Emri i përdoruesit SMTP
        $mail->Password   = 'ygxcwgkqyzmlmbcj'; // Fjalëkalimi SMTP
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        // Cilësimet e dërguesit dhe të pranuesit
        $mail->setFrom('bareshakontrata@gmail.com', 'Baresha Kontratë');
        $mail->addAddress($email);
        // Bashkangjit imazhin
        $mail->addStringEmbeddedImage(file_get_contents('images/brand-icon.png'), 'brand-icon', 'brand-icon.png');
        // Përmbajtja e emailit
        $mail->isHTML(true);
        $mail->Subject = 'Kontrata Gjenerale';
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
                                    <p style="color: #555555; font-size: 16px; margin-bottom: 10px;">Klikoni butonin më poshtë për të kaluar në faqen për të nënshkruar kontratën gjenerale.</p>
                                    <p><a href="' . $linkuKontrates . '" style="appearance: none; background-color: #ffffff; border: 1px solid rgba(27, 31, 35, .15); border-radius: 6px; box-shadow: rgba(27, 31, 35, .1) 0 1px 0; box-sizing: border-box; color: #000000; cursor: pointer; display: inline-block; font-size: 14px; font-weight: 600; line-height: 20px; padding: 6px 16px; position: relative; text-align: center; text-decoration: none; user-select: none; -webkit-user-select: none; touch-action: manipulation; vertical-align: middle; white-space: nowrap;" class="button">Kontrata</a></p>
                                    <p>Ju faleminderit</p>
                                    <i>Ky link skadon pas 24 ore prej ketij momenti</i>
                                </div>
                            </body>
                        </html>';

        $mail->CharSet = 'UTF-8';
        // Dërgoni emailin
        $mail->send();
        echo 'Emaili është dërguar me sukses!';
    } catch (Exception $e) {
        echo "Mesazhi nuk mund të dërgohet. Gabimi i Mailer: {$mail->ErrorInfo}";
    }
}
?>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Dërgo
                    kontraten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label for="email" class="form-label">Emaili
                                    klientit</label>
                                <input type="email" name="email" id="email" class="form-control shadow-sm rounded-5">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="linkuKontrates" class="form-label">Linku
                                kontrates</label>
                            <input type="text" name="linkuKontrates" class="form-control shadow-sm rounded-5" id="linkuKontrates">
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
</div>
<?php
include('partials/footer.php');
?>
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
                return fetch(`api/delete_methods/delete_kontrata_gjenerale.php?id=${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
            },
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Duke fshirë...',
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'U fshi!',
                        text: 'Kontrata është fshirë.',
                        timer: 3000
                    });
                    window.location.href = 'lista_kontratave_gjenerale.php';
                }, 2000);
            }
        });
    }
</script>
<script>
    function updateEmailInput(button) {
        var id = button.getAttribute("data-id");
        var tableRow = button.closest("tr");
        var link = button.getAttribute('data-link');
        var linkInput = document.getElementById('linkuKontrates');
        linkInput.value = link + id;
        // var email = tableRow.cells[4].textContent.trim();
        // document.getElementById("email").value = email;
        // document.getElementById("id").value = id;
        var emailInput = document.getElementById('email');
        var email = button.getAttribute('data-email');
        emailInput.value = email;
    }
</script>
<script>
    // Function to enable/disable the Delete button based on the number of checkboxes checked
    function toggleDeleteButton() {
        var checkboxes = document.querySelectorAll('input[name="selected_contracts[]"]:checked');
        var deleteButton = document.getElementById('deleteButton');
        // If two or more checkboxes are checked, enable the button; otherwise, disable it
        if (checkboxes.length >= 2) {
            deleteButton.disabled = false;
        } else {
            deleteButton.disabled = true;
        }
    }
    // Listen for changes in checkbox state
    var checkboxes = document.querySelectorAll('input[name="selected_contracts[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', toggleDeleteButton);
    });
</script>
<script>
    $('#example').DataTable({
        responsive: false,
        searching: true,
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
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: 'Printo tabelën',
            className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
        }, {
            text: '<i class="fi fi-rr-add-document fa-lg"></i>&nbsp;&nbsp; Shto kontratë',
            className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
            action: function(e, node, config) {
                window.location.href = 'kontrata_gjenelare_2.php';
            }
        }, ],
        initComplete: function() {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');
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
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color']
    })
</script>