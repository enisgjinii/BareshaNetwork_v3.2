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
            <?php if (!$hasTodayBackup): ?>
                <p class="bg-danger text-light rounded-5 shadow-sm p-3 mb-4" style="width:max-content;">
                    Nuk u gjet asnj&euml; rezerv&euml; p&euml;r sot. Klikoni butonin <b><i>Backup</b></i> p&euml;r t&euml; krijuar nj&euml; kopje rezerv&euml; t&euml;
                    re. 
                </p>
            <?php endif; ?>
            <div class="card shadow-sm rounded-5">
                <div class="card-body">
                    <h4 class="card-title" style="text-transform:none;">Lista e kopjeve rezerve</h4>

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
                                        <?php foreach ($backupFiles as $backupFile): ?>
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
                                                    <a class="btn btn-primary py-2 shadow rounded-5 text-white btn-sm"
                                                        href="<?php echo $backupFilePath; ?>" download
                                                        style="text-transform:none;"><i class="fi fi-rr-download"></i>
                                                        Shkarkoje</a>
                                                    <button style="text-transform:none;"
                                                        class="btn btn-danger py-2 shadow rounded-5 text-white btn-sm"
                                                        style="text-transform:none;"
                                                        onclick="deleteBackup('<?php echo $backupFile; ?>')"><i
                                                            class="fi fi-rr-trash"></i>Fshije</button>
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
        order: [[1, 'ASC']],
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
        },],
        initComplete: function () {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');

        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color']
    })


    function deleteBackup(backupFile) {
        // Send a request to the delete_backup.php script
        fetch('delete_backup.php?backupFile=' + backupFile)
            .then(response => response.text())
            .then(result => {
                // Display the SweetAlert 2 alert
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Deleted',
                    text: 'Backup ' + backupFile + ' has been deleted.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Refresh the page
                    location.reload();
                });
            })
            .catch(error => {
                console.error(error);
            });
    }
</script>