<?php
require_once 'partials/header.php';
// Define the directory where the backup files are stored
$backupDirectory = 'backups';
// Get the list of backup files
$backupFiles = scandir($backupDirectory);
// Remove the "." and ".." entries from the list
$backupFiles = array_diff($backupFiles, array('.', '..'));
// Sort the backup files in descending order based on file creation time
usort($backupFiles, function ($a, $b) use ($backupDirectory) {
    $fileA = $backupDirectory . '/' . $a;
    $fileB = $backupDirectory . '/' . $b;
    return filemtime($fileB) - filemtime($fileA);
});
// Check if there is no backup for the current day
$hasTodayBackup = false;
$today = date('d-m-Y');
foreach ($backupFiles as $backupFile) {
    $backupFilePath = $backupDirectory . '/' . $backupFile;
    $creationTime = date('d-m-Y', filemtime($backupFilePath));
    if ($creationTime === $today) {
        $hasTodayBackup = true;
        break;
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <?php if (!$hasTodayBackup) : ?>
                <p class="bg-danger text-light rounded-5 shadow-sm p-3 mb-4" style="width:max-content;">
                    Nuk u gjet asnj&euml; rezerv&euml; p&euml;r sot. Klikoni butonin <b><i>Backup</b></i> p&euml;r t&euml; krijuar nj&euml; kopje rezerv&euml; t&euml;
                    re.
                </p>
            <?php endif; ?>
            <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="lista_kopjeve_rezerve.php" class="text-reset" style="text-decoration: none;">
                            Lista e kopjeve rezerve
                        </a>
                    </li>
            </nav>
            <div class="card shadow-sm rounded-5">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table" id="listaKopjeve">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Skedari rezerv&euml;</th>
                                            <th>Ora e krijimit</th>
                                            <th>Veprimet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($backupFiles as $backupFile) : ?>
                                            <?php
                                            $backupFilePath = $backupDirectory . '/' . $backupFile;
                                            $creationTime = date('d-m-Y H:i:s', filemtime($backupFilePath));
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $backupFile; ?>
                                                </td>
                                                <td>
                                                    <?php echo $creationTime; ?>
                                                </td>
                                                <td>
                                                    <a class="input-custom-css px-3 py-2" style="text-transform:none;text-decoration:none" href="<?php echo $backupFilePath; ?>" download style="text-transform:none;"><i class="fi fi-rr-download"></i>
                                                        Shkarkoje</a>
                                                    <button class="input-custom-css px-3 py-2" style="text-transform:none;text-decoration:none" onclick="deleteBackup('<?php echo $backupFile; ?>')"><i class="fi fi-rr-trash"></i>Fshije</button>
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
<script>
    $('#listaKopjeve').DataTable({
        responsive: false,
        search: {
            return: true,
        },
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row'<'col-md-6'><'col-md-6'p>>",
        order: [
            [0, 'DESC']
        ],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'Te gjitha']
        ],
        buttons: [{
            extend: 'pdfHtml5',
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            titleAttr: 'Eksporto tabelen ne formatin PDF',
            className: 'btn btn-sm btn-light border rounded-5 me-2'
        }, {
            extend: 'copyHtml5',
            text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            titleAttr: 'Kopjo tabelen ne formatin Clipboard',
            className: 'btn btn-sm btn-light border rounded-5 me-2'
        }, {
            extend: 'excelHtml5',
            text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
            titleAttr: 'Eksporto tabelen ne formatin CSV',
            className: 'btn btn-sm btn-light border rounded-5 me-2'
        }, {
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: 'Printo tabel&euml;n',
            className: 'btn btn-sm btn-light border rounded-5 me-2'
        }, ],
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

    function deleteBackup(backupFile) {
        Swal.fire({
            title: 'A jeni të sigurt?',
            text: 'Ky veprim nuk mund të kthehet mbrapa!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Po, fshije!',
            cancelButtonText: 'Anulo'
        }).then((result) => {
            if (result.isConfirmed) {
                // Përdoruesi ka konfirmuar, dërgo kërkesën për fshirjen
                fetch('delete_backup.php?backupFile=' + backupFile)
                    .then(response => response.text())
                    .then(result => {
                        // Shfaq një mesazh suksesi
                        Swal.fire({
                            icon: 'success',
                            title: 'Kopja rezervë është fshirë',
                            text: 'Kopja ' + backupFile + ' është fshirë.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Rifresko faqen
                            location.reload();
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        // Shfaq një mesazh gabimi
                        Swal.fire({
                            icon: 'error',
                            title: 'Gabim',
                            text: 'Dështoi në fshirjen e kopjes së rezervës.',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }
</script>