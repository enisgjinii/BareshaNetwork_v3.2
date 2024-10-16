<?php
ob_start();
include 'partials/header.php';
require_once 'conn-d.php';
function executePreparedStatement($conn, $query, $types = '', $params = [])
{
    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
    if ($types && $params) $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
    return $stmt;
}
function fetchExchangeRates($url)
{
    $json = @file_get_contents($url);
    if ($json === FALSE) throw new Exception("Failed to fetch exchange rates.");
    $data = json_decode($json);
    if (!$data) throw new Exception("Invalid exchange rate data.");
    return $data;
}
try {
    $exchangeRates = fetchExchangeRates('http://www.floatrates.com/daily/usd.json');
} catch (Exception $e) {
    $exchangeRates = null;
}
if (empty($_GET['fatura'])) {
    $errorMessage = "Nuk u gjet fatura!";
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gabim!',
            text: '$errorMessage',
            confirmButtonText: 'OK',
        }).then(() => {
            window.location.href = 'error.php?message=' + encodeURIComponent('$errorMessage');
        });
    </script>";
    exit();
}
$fatura = $_GET['fatura'];
try {
    $stmt = executePreparedStatement($conn, "SELECT * FROM faturafacebook WHERE fatura = ?", "s", [$fatura]);
    $invoiceResult = $stmt->get_result();
    $invoice = $invoiceResult->fetch_assoc();
    if (!$invoice) {
        $errorMessage = "Fatura nuk u gjet!";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gabim!',
                text: '$errorMessage',
                confirmButtonText: 'OK',
            }).then(() => {
                window.location.href = 'error.php?message=' + encodeURIComponent('$errorMessage');
            });
        </script>";
        exit();
    }
    $emriId = $invoice['emri'];
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gabim!',
            text: '$errorMessage',
            confirmButtonText: 'OK',
        }).then(() => {
            window.location.href = 'error.php?message=' + encodeURIComponent('$errorMessage');
        });
    </script>";
    exit();
}
function getUserDetails($conn, $userId)
{
    try {
        $stmt = executePreparedStatement($conn, "SELECT * FROM facebook WHERE id = ?", "i", [$userId]);
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (!$user) return null;
        return $user;
    } catch (Exception $e) {
        return null;
    }
}
$user = getUserDetails($conn, $emriId);
if (!$user) {
    $errorMessage = "Klienti nuk u gjet!";
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gabim!',
            text: '$errorMessage',
            confirmButtonText: 'OK',
        }).then(() => {
            window.location.href = 'error.php?message=' + encodeURIComponent('$errorMessage');
        });
    </script>";
    exit();
}
$perqindja = $user['perqindja'];
$pdc = $perqindja / 100;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ruaj'])) {
    $emertimi = trim($_POST['emertimi'] ?? '');
    $qmimiInput = floatval($_POST['qmimi'] ?? 0);
    $valuta = $_POST['valuta'] ?? 'euro';
    if (empty($emertimi) || $qmimiInput <= 0) {
        $action = "insert_error";
        $message = "Emërtimi dhe qmimi duhet të jenë të vlefshëm.";
    } else {
        if ($valuta === "euro") {
            $qmimi = $qmimiInput;
        } elseif ($exchangeRates && isset($exchangeRates->eur->rate)) {
            $qmimi = $qmimiInput * $exchangeRates->eur->rate;
        } else {
            $action = "insert_error";
            $message = "Nuk mund të konvertohet valuta.";
            goto redirect;
        }
        $shk = ($qmimi <= 0) ? 0.00 : $pdc * $qmimi;
        $shm = $qmimi - $shk;
        $datas = date("Y-m-d H:i:s");
        try {
            executePreparedStatement(
                $conn,
                "INSERT INTO shitjefacebook (emertimi, qmimi, perqindja, klientit, mbetja, totali, fatura, data) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                "sdidddsd",
                [$emertimi, $qmimi, $perqindja, $shm, $shk, $shm, $fatura, $datas]
            );
            $action = "insert_success";
            $message = "Fatura u shtua me sukses!";
        } catch (Exception $e) {
            $action = "insert_error";
            $message = "Ndodhi një gabim: " . $e->getMessage();
        }
    }
    redirect:
    header("Location: shitjeFacebook.php?fatura=" . urlencode($fatura) . "&action=" . urlencode($action) . "&message=" . urlencode($message));
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $updateId = intval($_POST['editid'] ?? 0);
    $eme = trim($_POST['emertimi'] ?? '');
    $qmimiInput = floatval($_POST['qmimi'] ?? 0);
    if ($updateId <= 0 || empty($eme) || $qmimiInput <= 0) {
        $action = "update_error";
        $message = "Të dhënat përditësuese janë të pavlefshme.";
    } else {
        $shk = ($qmimiInput <= 0) ? 0.00 : $pdc * $qmimiInput;
        $shm = $qmimiInput - $shk;
        try {
            executePreparedStatement(
                $conn,
                "UPDATE shitjefacebook SET emertimi = ?, qmimi = ?, klientit = ?, mbetja = ?, totali = ? WHERE id = ?",
                "sdiddi",
                [$eme, $qmimiInput, $shm, $shk, $shm, $updateId]
            );
            $action = "update_success";
            $message = "Fatura u përditësua me sukses!";
        } catch (Exception $e) {
            $action = "update_error";
            $message = "Ndodhi një gabim: " . $e->getMessage();
        }
    }
    header("Location: shitjeFacebook.php?fatura=" . urlencode($fatura) . "&action=" . urlencode($action) . "&message=" . urlencode($message));
    exit();
}
?>
<!-- Begin Page Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card p-5 rounded-5 shadow-sm mb-4">
                <?php
                try {
                    $stmt = executePreparedStatement($conn, "SELECT * FROM yinc WHERE kanali = ?", "s", [$emriId]);
                    $results = $stmt->get_result();
                    while ($obligation = $results->fetch_assoc()) {
                        if ($obligation['shuma'] > $obligation['pagoi']) {
                            $difference = number_format($obligation['shuma'] - $obligation['pagoi'], 2);
                            $pershkrimi = htmlspecialchars($obligation['pershkrimi']);
                            echo "<div class='alert alert-danger' role='alert'>
                                    Klienti ka një obligim me shumën: <b>{$difference}€</b>, <br><b>Përshtkrimi:</b> {$pershkrimi}.
                                  </div>";
                        }
                    }
                } catch (Exception $e) {
                    echo "<div class='alert alert-warning'>Nuk u mund të ngarkohen obligimet.</div>";
                }
                ?>
                <form method="POST" action="shitjeFacebook.php?fatura=<?= htmlspecialchars(urlencode($fatura)); ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="emertimi" class="form-label">Emërtimi</label>
                            <input type="text" name="emertimi" id="emertimi" class="form-control shadow-sm rounded-5" placeholder="Emërtimi" required>
                        </div>
                        <div class="col-md-4">
                            <label for="qmimi" class="form-label">Qmimi</label>
                            <input type="number" name="qmimi" id="qmimi" class="form-control shadow-sm rounded-5" placeholder="Qmimi" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="valuta" class="form-label">Valuta</label>
                            <select name="valuta" id="valuta" class="form-select shadow-sm rounded-5 p-2">
                                <option value="dollar">$ Dollar</option>
                                <option value="euro" selected>€ Euro</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" name="ruaj" class="btn btn-success px-4 py-2 rounded-5">
                            <i class="fi fi-rr-add-document fa-lg"></i> Shto
                        </button>
                    </div>
                </form>
                <hr>
                <table class="table table-bordered mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Emërtimi</th>
                            <th>Qmimi</th>
                            <th>Perqindja (%)</th>
                            <th>Shuma</th>
                            <th>Mbetja</th>
                            <th>Totali</th>
                            <th>Aksion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = executePreparedStatement($conn, "SELECT * FROM shitjefacebook WHERE fatura = ?", "s", [$fatura]);
                            $result = $stmt->get_result();
                            $rend = 0;
                            $totalSum = 0;
                            while ($row = $result->fetch_assoc()) {
                                $rend++;
                                $id = htmlspecialchars($row['id']);
                                $emertimi = htmlspecialchars($row['emertimi']);
                                $qmimi = number_format($row['qmimi'], 2);
                                $perqindja = number_format($row['perqindja'], 2);
                                $klientit = number_format($row['klientit'], 2);
                                $mbetja = number_format($row['mbetja'], 2);
                                $totali = number_format($row['totali'], 2);
                                $totalSum += $row['totali'];
                                echo "<tr>
                                        <th scope='row'>{$rend}</th>
                                        <td>{$emertimi}</td>
                                        <td>{$qmimi}€</td>
                                        <td>{$perqindja}%</td>
                                        <td>{$klientit}€</td>
                                        <td>{$mbetja}€</td>
                                        <td>{$totali}€</td>
                                        <td>
                                            <a href='delete.php?fshij={$id}&fatura=" . urlencode($fatura) . "' class='btn btn-danger btn-sm rounded-5 me-1 delete-btn' data-id='{$id}'><i class='fi fi-rr-trash'></i></a>
                                            <button type='button' class='btn btn-primary btn-sm rounded-5 edit-btn' data-bs-toggle='modal' data-bs-target='#editModal{$id}'>
                                                <i class='fi fi-rr-edit'></i>
                                            </button>
                                        </td>
                                      </tr>";
                                echo "<div class='modal fade' id='editModal{$id}' tabindex='-1' aria-labelledby='editModalLabel{$id}' aria-hidden='true'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <form method='POST' action='shitjeFacebook.php?fatura=" . htmlspecialchars(urlencode($fatura)) . "'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title' id='editModalLabel{$id}'>Edito Emërtimin</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <input type='hidden' name='editid' value='{$id}'>
                                                        <div class='mb-3'>
                                                            <label for='emertimi{$id}' class='form-label'>Emërtimi</label>
                                                            <input type='text' name='emertimi' id='emertimi{$id}' class='form-control shadow-sm rounded-5' value='{$emertimi}' required>
                                                        </div>
                                                        <div class='mb-3'>
                                                            <label for='qmimi{$id}' class='form-label'>Qmimi</label>
                                                            <input type='number' name='qmimi' id='qmimi{$id}' class='form-control shadow-sm rounded-5' value='{$qmimi}' step='0.01' min='0' required>
                                                        </div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Mbylle</button>
                                                        <button type='submit' name='update' class='btn btn-primary'>Ruaj</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                      </div>";
                            }
                            echo "<tr>
                                    <td colspan='6' class='text-end'><strong>Totali:</strong></td>
                                    <td colspan='2'><strong>" . number_format($totalSum, 2) . "€</strong></td>
                                  </tr>";
                        } catch (Exception $e) {
                            echo "<tr><td colspan='8' class='text-center text-danger'>Ndodhi një gabim: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    <a href="faturaFacebook.php" class="btn btn-light btn-sm me-2" target="_blank">
                        <i class="fi fi-rr-paper-plane"></i> Dergo
                    </a>
                    <a href="#" class="btn btn-danger btn-sm">
                        <i class="fi fi-rr-cross-circle"></i> Anulo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php';
ob_end_flush(); ?>
<!-- SweetAlert2 Integration -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }
        const action = getQueryParam('action');
        const message = getQueryParam('message');
        if (action && message) {
            let icon = 'info';
            let title = 'Info';
            switch (action) {
                case 'insert_success':
                    icon = 'success';
                    title = 'Sukses!';
                    break;
                case 'insert_error':
                    icon = 'error';
                    title = 'Gabim!';
                    break;
                case 'update_success':
                    icon = 'success';
                    title = 'Sukses!';
                    break;
                case 'update_error':
                    icon = 'error';
                    title = 'Gabim!';
                    break;
                case 'delete_success':
                    icon = 'success';
                    title = 'Fshirje Sukses!';
                    break;
                case 'delete_error':
                    icon = 'error';
                    title = 'Gabim!';
                    break;
                default:
                    icon = 'info';
                    title = 'Info';
            }
            Swal.fire({
                title: title,
                text: decodeURIComponent(message),
                icon: icon,
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true,
                willClose: () => {
                    const url = new URL(window.location);
                    url.searchParams.delete('action');
                    url.searchParams.delete('message');
                    window.history.replaceState({}, document.title, url.pathname + url.search);
                }
            });
        }
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const deleteUrl = this.getAttribute('href');
                Swal.fire({
                    title: 'A jeni i sigurt?',
                    text: "Ky veprim nuk mund të kthehet!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Po, fshije!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl + "&action=delete_success&message=Fatura u fshi me sukses.";
                    }
                });
            });
        });
    });
</script>