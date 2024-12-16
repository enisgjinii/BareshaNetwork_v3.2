<?php
require __DIR__ . '/vendor/autoload.php';
session_start();
include('partials/header.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'track') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $trackingFile = __DIR__ . '/tracking_data.json';
            $fp = fopen($trackingFile, 'c+');
            if ($fp && flock($fp, LOCK_EX)) {
                $existingData = filesize($trackingFile) > 0 ? fread($fp, filesize($trackingFile)) : '';
                $trackingData = json_decode($existingData, true) ?: [];
                $trackingData[] = $data;
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, json_encode($trackingData, JSON_PRETTY_PRINT));
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            http_response_code(200);
            echo 'Tracking data recorded';
        } else {
            http_response_code(400);
            echo 'Invalid JSON';
        }
        exit();
    }
    if ($action === 'changeData') {
        $password = $_POST['password'] ?? '';
        $newData = $_POST['newData'] ?? '';
        if ($password === $adminPassword) {
            $dataFile = __DIR__ . '/data_changes.json';
            $fp = fopen($dataFile, 'c+');
            if ($fp && flock($fp, LOCK_EX)) {
                $existingData = filesize($dataFile) > 0 ? fread($fp, filesize($dataFile)) : '';
                $dataChanges = json_decode($existingData, true) ?: [];
                $dataChanges[] = ['newData' => $newData, 'timestamp' => date('c'), 'admin' => 'Authenticated'];
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, json_encode($dataChanges, JSON_PRETTY_PRINT));
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            $_SESSION['admin_authenticated'] = true;
            http_response_code(200);
            echo 'Data change successful';
        } else {
            http_response_code(403);
            echo 'Invalid administrator password';
        }
        exit();
    }
    if ($action === 'copyCell') {
        $cellData = $_POST['cellData'] ?? '';
        $row = $_POST['row'] ?? '';
        $column = $_POST['column'] ?? '';
        if ($cellData) {
            $copyFile = __DIR__ . '/copy_actions.json';
            $fp = fopen($copyFile, 'c+');
            if ($fp && flock($fp, LOCK_EX)) {
                $existingData = filesize($copyFile) > 0 ? fread($fp, filesize($copyFile)) : '';
                $copyData = json_decode($existingData, true) ?: [];
                $copyData[] = [
                    'cellData' => $cellData,
                    'row' => $row,
                    'column' => $column,
                    'timestamp' => date('c'),
                    'url' => $_SERVER['HTTP_REFERER'] ?? 'Unknown',
                    'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                ];
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, json_encode($copyData, JSON_PRETTY_PRINT));
                flock($fp, LOCK_UN);
                fclose($fp);
            }
            http_response_code(200);
            echo 'Copy action recorded';
        } else {
            http_response_code(400);
            echo 'No cell data provided';
        }
        exit();
    }
}

use Google_Client;
use Google_Service_Sheets;

try {
    $credentialsPath = __DIR__ . '/kinetic-horizon-357319-7be6ed2f0d17.json';
    $client = new Google_Client();
    $client->setAuthConfig($credentialsPath);
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAccessType('offline');
    $service = new Google_Service_Sheets($client);
    $spreadsheetId = '1_LjDu_JQcdiNTQqS5kXRd6i3Qn_zPyCTb2zv1dcv1o8';
    $spreadsheet = $service->spreadsheets->get($spreadsheetId);
    $sheets = $spreadsheet->getSheets();
    $selectedSheet = $_GET['sheet'] ?? $sheets[0]->getProperties()->getTitle();
    $range = $selectedSheet . '!A1:Z';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();
} catch (Exception $e) {
    $error = htmlspecialchars($e->getMessage());
}
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Google Sheets Viewer</h1>
        <button id="themeToggle" class="btn btn-primary">Toggle Theme</button>
    </div>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php else: ?>
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <label for="sheet" class="form-label me-2 align-self-center">Select Sheet:</label>
                    <select name="sheet" id="sheet" class="form-select me-2" onchange="this.form.submit()">
                        <?php foreach ($sheets as $sheet): ?>
                            <?php $title = htmlspecialchars($sheet->getProperties()->getTitle()); ?>
                            <option value="<?= $title ?>" <?= $title === $selectedSheet ? 'selected' : '' ?>><?= $title ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" type="submit">Refresh</button>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <div class="input-group">
                    <span class="input-group-text">Auto-Refresh (seconds)</span>
                    <input type="number" id="refreshInterval" class="form-control" value="60" min="10">
                    <button id="setRefresh" class="btn btn-outline-secondary">Set</button>
                </div>
            </div>
        </div>
        <?php if (!empty($values)): ?>
            <div class="table-responsive">
                <table id="sheetsTable" class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <?php $maxColumns = max(array_map('count', $values)); ?>
                            <?php for ($c = 0; $c < $maxColumns; $c++): ?>
                                <th><?= isset($values[0][$c]) ? htmlspecialchars($values[0][$c]) : 'Column ' . ($c + 1) ?></th>
                            <?php endfor; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i < count($values); $i++): ?>
                            <tr>
                                <?php for ($c = 0; $c < $maxColumns; $c++): ?>
                                    <td>
                                        <?= htmlspecialchars($values[$i][$c] ?? '') ?>
                                        <button class="btn btn-sm btn-link copy-cell" data-cell="<?= htmlspecialchars($values[$i][$c] ?? '') ?>" data-row="<?= $i + 1 ?>" data-column="<?= $c + 1 ?>" title="Copy">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </td>
                                <?php endfor; ?>
                                <td>
                                    <span class="read-more-toggle" data-row="<?= $i + 1 ?>">Read More</span>
                                    <div class="read-more-content" id="read-more-<?= $i + 1 ?>" style="display: none;">
                                        <p>Additional details for row <?= $i + 1 ?>.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <h3>Change Data</h3>
                <div class="mb-3">
                    <textarea id="newData" class="form-control" rows="3" placeholder="Enter new data here..."></textarea>
                </div>
                <button id="changeDataBtn" class="btn btn-primary">Change Data</button>
            </div>
        <?php else: ?>
            <p class="text-muted">No data found in the selected sheet.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    body.dark-mode {
        background-color: #2e2e2e;
        color: #ffffff;
    }

    .dark-mode table {
        background-color: #3e3e3e;
        color: #ffffff;
    }

    .read-more-toggle {
        cursor: pointer;
        color: #007bff;
    }

    .read-more-toggle:hover {
        text-decoration: underline;
    }

    table.dataTable thead th {
        background-color: #f1f1f1;
    }

    body.dark-mode table.dataTable thead th {
        background-color: #4e4e4e;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#sheetsTable').DataTable({
            paging: true,
            searching: false,
            ordering: true,
            info: false,
            responsive: true,
            lengthChange: false,
            pageLength: 10,
            columnDefs: [{
                orderable: false,
                targets: -1
            }],
            language: {
                paginate: {
                    previous: "Prev",
                    next: "Next"
                }
            }
        });
        $('#themeToggle').on('click', function() {
            $('body').toggleClass('dark-mode');
            localStorage.setItem('theme', $('body').hasClass('dark-mode') ? 'dark' : 'light');
        });
        if (localStorage.getItem('theme') === 'dark') $('body').addClass('dark-mode');

        function refreshData() {
            location.reload();
        }
        var refreshTimer = setInterval(refreshData, $('#refreshInterval').val() * 1000);
        $('#setRefresh').on('click', function() {
            clearInterval(refreshTimer);
            var interval = parseInt($('#refreshInterval').val()) * 1000;
            if (isNaN(interval) || interval < 10000) {
                alert('Enter a valid number (min 10 seconds).');
                interval = 60000;
            }
            refreshTimer = setInterval(refreshData, interval);
        });

        function sendTracking(eventType, details) {
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'track',
                    event: eventType,
                    details: details,
                    timestamp: new Date().toISOString(),
                    url: window.location.href,
                    userAgent: navigator.userAgent
                })
            }).catch(err => console.error('Tracking error:', err));
        }
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.copy-cell').length) {
                sendTracking('click', {
                    tag: e.target.tagName,
                    id: e.target.id,
                    classes: e.target.className,
                    text: e.target.innerText.substring(0, 100)
                });
            }
        });
        $(document).on('keydown', function(e) {
            sendTracking('keypress', {
                key: e.key,
                code: e.code,
                keyCode: e.keyCode
            });
        });
        $(document).on('copy', function(e) {
            sendTracking('copy', {
                selectedText: window.getSelection().toString()
            });
        });
        $('.read-more-toggle').on('click', function() {
            var rowId = $(this).data('row');
            $('#read-more-' + rowId).slideToggle();
        });
        $('#changeDataBtn').on('click', function() {
            var newData = $('#newData').val().trim();
            if (!newData) {
                alert('Enter data to change.');
                return;
            }
            var password = prompt("Enter Administrator Password:");
            if (password === null) return;
            $.post(window.location.href, {
                action: 'changeData',
                password: password,
                newData: newData
            }, function(response) {
                alert(response);
                setTimeout(refreshData, 2000);
            }).fail(function(xhr) {
                alert(xhr.responseText);
            });
        });
        $('.copy-cell').on('click', function(e) {
            e.preventDefault();
            var cellData = $(this).data('cell');
            var row = $(this).data('row');
            var column = $(this).data('column');
            navigator.clipboard.writeText(cellData).then(function() {
                alert('Copied: ' + cellData);
            }, function(err) {
                console.error('Copy failed:', err);
            });
            $.post(window.location.href, {
                action: 'copyCell',
                cellData: cellData,
                row: row,
                column: column
            }, function(response) {
                console.log(response);
            }).fail(function(xhr) {
                console.error('Copy record error:', xhr.responseText);
            });
        });
    });
</script>
<?php include 'partials/footer.php'; ?>
</body>

</html>