<?php
include 'partials/header.php';

// Database connection (assuming $conn is defined in header.php)
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Sanitize and retrieve client ID
$kid = mysqli_real_escape_string($conn, $_GET['kid'] ?? '');

if (empty($kid)) {
  // Handle missing client ID
  echo "<script>
            Swal.fire({
                title: 'Gabim!',
                text: 'Nuk u gjet ID e klientit.',
                icon: 'error',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'client_list.php';
            });
          </script>";
  exit();
}

// Fetch client data
$clientQuery = $conn->prepare("SELECT * FROM klientet WHERE id = ?");
$clientQuery->bind_param("s", $kid);
$clientQuery->execute();
$guse2 = $clientQuery->get_result()->fetch_assoc();

if (!$guse2) {
  // Handle client not found
  echo "<script>
            Swal.fire({
                title: 'Gabim!',
                text: 'Klienti nuk u gjet.',
                icon: 'error',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'client_list.php';
            });
          </script>";
  exit();
}

// YouTube API configurations
$channel_id = $guse2['youtube'];
$api_key = "AIzaSyBrE0kFGTQJwn36FeR4NIyf4FEw2HqSSIQ";

// Function to fetch data from YouTube API
function fetchYouTubeData($part, $channel_id, $api_key)
{
  $url = "https://www.googleapis.com/youtube/v3/channels?part={$part}&id={$channel_id}&key={$api_key}";
  $response = file_get_contents($url);
  return json_decode($response, true);
}

$channelSnippet = fetchYouTubeData('snippet', $channel_id, $api_key);
$channelStatistics = fetchYouTubeData('statistics', $channel_id, $api_key);
$channelBanner = fetchYouTubeData('brandingSettings', $channel_id, $api_key);

// Log fetched data (for debugging)
echo "<script>console.log(" . json_encode($channelSnippet) . ");</script>";
echo "<script>console.log(" . json_encode($channelStatistics) . ");</script>";
echo "<script>console.log(" . json_encode($channelBanner) . ");</script>";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Update Private Info
  if (isset($_POST['infoprw'])) {
    $idup = mysqli_real_escape_string($conn, $_POST['idup']);
    $texti = mysqli_real_escape_string($conn, $_POST['infoprw']);

    $updateQuery = $conn->prepare("UPDATE klientet SET infoprw = ? WHERE id = ?");
    $updateQuery->bind_param("ss", $texti, $idup);

    if ($updateQuery->execute()) {
      echo "<script>
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Infot private u ndryshuan me sukses.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = 'kanal.php?kid=" . htmlspecialchars($kid, ENT_QUOTES, 'UTF-8') . "';
                    });
                  </script>";
      exit();
    } else {
      echo "<script>
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Dicka shkoi keq. Ju lutem provoni përsëri.',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000
                    });
                  </script>";
    }
  }

  // Add Strike
  if (isset($_POST['shto'])) {
    $strikeData = [
      'klienti' => mysqli_real_escape_string($conn, $_POST['klienti']),
      'dataf' => mysqli_real_escape_string($conn, $_POST['dataf']),
      'datas' => mysqli_real_escape_string($conn, $_POST['datas']),
      'url' => mysqli_real_escape_string($conn, $_POST['url']),
      'titulli' => mysqli_real_escape_string($conn, $_POST['titulli']),
      'pershkrimi' => mysqli_real_escape_string($conn, $_POST['pershkrimi']),
    ];

    $strikeQuery = $conn->prepare("INSERT INTO strike (dataf, datas, url, pershkrimi, klienti, titulli) VALUES (?, ?, ?, ?, ?, ?)");
    $strikeQuery->bind_param("ssssss", $strikeData['dataf'], $strikeData['datas'], $strikeData['url'], $strikeData['pershkrimi'], $strikeData['klienti'], $strikeData['titulli']);

    if ($strikeQuery->execute()) {
      echo "<script>
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Strike u shtua me sukses.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                  </script>";
    } else {
      echo "<script>
                    Swal.fire({
                        title: 'Gabim!',
                        text: '" . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . "',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000
                    });
                  </script>";
    }
  }
}

// Function to fetch data from generic API using cURL
function fetchDataFromAPI($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  if (curl_errno($ch)) {
    throw new Exception(curl_error($ch));
  }
  curl_close($ch);
  return json_decode($response, true);
}

// Fetch Social Media Data (Instagram and Facebook)
function getSocialMediaData($clientData)
{
  $socialMedia = [];

  // Define API endpoints (replace with actual URLs)
  $apiEndpoints = [
    'instagram' => 'https://api.example.com/instagram',
    'facebook' => 'https://api.example.com/facebook',
  ];

  foreach ($apiEndpoints as $platform => $url) {
    try {
      $data = fetchDataFromAPI($url);
      $socialMedia[$platform] = $data;
    } catch (Exception $e) {
      $socialMedia[$platform] = null;
    }
  }

  return $socialMedia;
}

$socialMediaData = getSocialMediaData($guse2);

// Fetch Ads Data
$adsid = mysqli_real_escape_string($conn, $guse2['ads']);
$adsQuery = $conn->prepare("SELECT * FROM ads WHERE id = ?");
$adsQuery->bind_param("s", $adsid);
$ads = $adsQuery->execute() ? $adsQuery->get_result()->fetch_assoc() : [];

// Calculate Financials
function calculateFinancials($conn, $clientId, $clientName)
{
  $totals = [
    'totali' => 0.00,
    'totaliMbetur' => 0.00,
    'totaliYoutube' => 0.00,
    'totaliObligimitYoutube' => 0.00
  ];

  $faturaQuery = $conn->prepare("SELECT fatura FROM fatura WHERE emri = ?");
  $faturaQuery->bind_param("s", $clientId);
  $faturaQuery->execute();
  $faturaResult = $faturaQuery->get_result();

  while ($row = $faturaResult->fetch_assoc()) {
    $fatura = $row['fatura'];

    // Prepare and execute sum queries
    $sumQueries = [
      'shumaKlientit' => $conn->prepare("SELECT SUM(klientit) as total FROM shitje WHERE fatura = ?"),
      'shumaMbetur' => $conn->prepare("SELECT SUM(mbetja) as total FROM shitje WHERE fatura = ?"),
      'shumaYoutube' => $conn->prepare("SELECT SUM(totali) as sum FROM shitje WHERE fatura = ?"),
      'shumaObligimitYoutube' => $conn->prepare("SELECT SUM(shuma) as sum FROM pagesat WHERE fatura = ?")
    ];

    foreach ($sumQueries as $key => $stmt) {
      $stmt->bind_param("s", $fatura);
      $stmt->execute();
      ${$key} = $stmt->get_result()->fetch_assoc()['sum'] ?? 0;
      $stmt->close();
    }

    $obligimi = ($shumaObligimitYoutube - $shumaYoutube) ?: 0;
    $totals['totali'] += $shumaKlientit;
    $totals['totaliMbetur'] += $shumaMbetur;
    $totals['totaliYoutube'] += $shumaYoutube;
    $totals['totaliObligimitYoutube'] += $obligimi;
  }

  // Handle empty totals
  foreach ($totals as $key => $value) {
    $totals[$key] = $value ?: 0.00;
  }

  // Fetch total Revenue from other platforms
  $revenueQuery = $conn->prepare("SELECT SUM(RevenueUSD) as sum FROM platformat WHERE Artist = ?");
  $revenueQuery->bind_param("s", $clientName);
  $revenueQuery->execute();
  $revenueResult = $revenueQuery->get_result()->fetch_assoc();
  $totals['revenueOtherPlatforms'] = $revenueResult['sum'] ?? 0.00;

  return $totals;
}

$financials = calculateFinancials($conn, $kid, $guse2['emri']);

?>
<!-- TinyMCE Integration -->
<script src="https://cdn.tiny.cloud/1/v1lt364np68v98q2hye277yd2kz3szp65wttpsgbe8g4z6iv/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#editor',
    menubar: false,
    plugins: 'lists link image table',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image',
    height: 300
  });
</script>

<!-- Main Content -->
<div class="container-fluid mt-4">
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb bg-white p-3 rounded-3 border" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="clients.php" class="text-decoration-none">Klientet</a></li>
      <li class="breadcrumb-item"><a href="client_list.php" class="text-decoration-none">Lista e Klientëve</a></li>
      <li class="breadcrumb-item active" aria-current="page">Kanali i Klientit <?php echo htmlspecialchars($guse2['emri'], ENT_QUOTES, 'UTF-8'); ?></li>
    </ol>
  </nav>

  <div class="row mt-4">
    <!-- Left Column: YouTube Channel Info -->
    <div class="col-lg-6 mb-4">
      <div class="card shadow-sm rounded-5">
        <div class="card-body text-center">
          <img src="images/youtube.png" width="100" class="mb-3" alt="YouTube Logo">
          <img src="<?php echo htmlspecialchars($channelSnippet['items'][0]['snippet']['thumbnails']['high']['url'] ?? 'images/default-avatar.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Logo i Kanalit" class="rounded-circle mb-4" width="150">
          <h4 class="card-title mb-3"><?php echo htmlspecialchars($channelSnippet['items'][0]['snippet']['title'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></h4>
          <p class="card-text text-muted"><?php echo htmlspecialchars($channelSnippet['items'][0]['snippet']['description'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>

          <!-- Channel Statistics -->
          <div class="row justify-content-center">
            <?php
            $stats = [
              'Total Abonues' => number_format($channelStatistics['items'][0]['statistics']['subscriberCount'] ?? 0, 0, '.', ','),
              'Total Shikime' => number_format($channelStatistics['items'][0]['statistics']['viewCount'] ?? 0, 0, '.', ','),
              'Total Video' => htmlspecialchars($channelStatistics['items'][0]['statistics']['videoCount'] ?? '0', ENT_QUOTES, 'UTF-8')
            ];
            foreach ($stats as $label => $value) {
              echo '<div class="col-auto">
                                    <div class="text-secondary"><i class="ti-user"></i> ' . $label . ':</div>
                                    <div class="text-primary">' . $value . '</div>
                                  </div>';
            }
            ?>
          </div>

          <hr class="my-4">

          <!-- Action Buttons -->
          <div class="d-flex justify-content-center gap-2">
            <a href="listang.php?id=<?php echo htmlspecialchars($kid, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary"><i class="ti-flag-alt"></i> Raporti</a>
            <a href="https://www.youtube.com/channel/<?php echo htmlspecialchars($channel_id, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn-outline-success">Shfleto kanalin</a>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#strike"><i class="ti-info-alt"></i> Strike</button>
          </div>
        </div>
      </div>

      <!-- Social Media Card -->
      <div class="card mt-4 shadow-sm rounded-5">
        <div class="card-header bg-dark text-white">
          <h5 class="mb-0">Social Media</h5>
        </div>
        <ul class="list-group list-group-flush">
          <?php
          $socialPlatforms = [
            'Instagram' => [
              'icon' => 'fi fi-brands-instagram',
              'url' => htmlspecialchars($guse2['ig'], ENT_QUOTES, 'UTF-8')
            ],
            'Facebook' => [
              'icon' => 'fi fi-brands-facebook',
              'url' => htmlspecialchars($guse2['fb'], ENT_QUOTES, 'UTF-8')
            ]
          ];

          foreach ($socialPlatforms as $platform => $details) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><i class="' . $details['icon'] . '"></i> ' . $platform . '</h6>
                                </div>
                                <span class="text-secondary">' . ($details['url'] ?: 'N/A') . '</span>
                                <a href="' . $details['url'] . '" target="_blank" class="btn btn-sm btn-primary">Visit</a>
                              </li>';
          }
          ?>
        </ul>
      </div>
    </div>

    <!-- Right Column: Client Details and Financials -->
    <div class="col-lg-6">
      <div class="card shadow-sm rounded-5 mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th scope="col">Fusha</th>
                  <th scope="col">Vlera</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $clientDetails = [
                  'Emri i plotë' => ucfirst($guse2['emri'] ?? 'N/A'),
                  'Emri Artistik' => ucfirst($guse2['emriart'] ?? 'N/A'),
                  'Monetizuar' => ($guse2['monetizuar'] === "PO") ? '<span class="text-success">PO</span>' : '<span class="text-danger">JO</span>',
                  'Email' => htmlspecialchars($ads['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'ADS ID' => htmlspecialchars($ads['adsid'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Shteti' => htmlspecialchars($ads['shteti'] ?? 'N/A', ENT_QUOTES, 'UTF-8') .
                    '<br><a href="https://www.google.com/maps/place/' . urlencode($ads['shteti']) . '" target="_blank" class="btn btn-light btn-sm shadow-sm border">
                                            <img src="https://img.icons8.com/emoji/36/' . strtolower($ads['shteti']) . '-emoji.png" alt="Shteti">
                                         </a>',
                  'Perqindja' => htmlspecialchars($guse2['perqindja'], ENT_QUOTES, 'UTF-8') . '%',
                  'Shuma totale e pagesave' => number_format($financials['totali'], 2, '.', ',') . ' &euro;',
                  'Shuma totale e pagesave ne platforma tjera' => number_format($financials['revenueOtherPlatforms'], 2, '.', ',') . ' &euro;',
                  'Shuma totale e pagesave ne platformen Youtube' => number_format($financials['totaliYoutube'], 2, '.', ',') . ' &euro;',
                  'Shuma totale e obligimit ne platformen Youtube' => ($financials['totaliObligimitYoutube'] == 0) ?
                    '<span class="text-success">Ky klient nuk ka obligim</span>' :
                    number_format($financials['totaliObligimitYoutube'], 2, '.', ',') . ' &euro;',
                  'Fitimi total nga klienti' => number_format($financials['totaliMbetur'], 2, '.', ',') . ' &euro;',
                  'Data e kontratës' => htmlspecialchars($guse2['dk'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Data e Skadimit (Kontratës)' => htmlspecialchars($guse2['dks'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Adresa' => htmlspecialchars($guse2['adresa'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Kategoria' => htmlspecialchars($guse2['kategoria'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Email' => htmlspecialchars($guse2['emailadd'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Email per platforma' => htmlspecialchars($guse2['emailp'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Nr.Tel' => htmlspecialchars($guse2['nrtel'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Email qe kan akses' => htmlspecialchars($guse2['emails'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                  'Info Shtesë' => htmlspecialchars($guse2['info'] ?? 'N/A', ENT_QUOTES, 'UTF-8'),
                ];

                foreach ($clientDetails as $field => $value) {
                  echo '<tr>
                                            <th scope="row">' . $field . '</th>
                                            <td>' . $value . '</td>
                                          </tr>';
                }

                // Info Private with TinyMCE Editor
                echo '<tr>
                                        <th scope="row">Info Private</th>
                                        <td>
                                            <form id="updateForm" method="POST" action="">
                                                <input type="hidden" name="idup" value="' . htmlspecialchars($guse2['id'], ENT_QUOTES, 'UTF-8') . '">
                                                <textarea id="editor" name="infoprw" placeholder="Info Shtesë">' . htmlspecialchars($guse2['infoprw'] ?? '', ENT_QUOTES, 'UTF-8') . '</textarea>
                                                <button type="submit" class="btn btn-primary btn-sm mt-2">Përditso të dhënat personale</button>
                                            </form>
                                        </td>
                                      </tr>';
                ?>
                <tr>
                  <td colspan="2" class="text-end">
                    <a href="editk.php?id=<?php echo htmlspecialchars($kid, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary btn-sm me-2">Ndrysho</a>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="konfirmoDeaktivizimin('<?php echo htmlspecialchars($kid, ENT_QUOTES, 'UTF-8'); ?>')">Fshij</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Strike Modal -->
  <div class="modal fade" id="strike" tabindex="-1" aria-labelledby="strikeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" action="">
          <div class="modal-header">
            <h5 class="modal-title" id="strikeModalLabel">Shto Strike</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="klienti" value="<?php echo htmlspecialchars($kid, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-3">
              <label for="titulli" class="form-label">Titulli:</label>
              <input type="text" id="titulli" name="titulli" class="form-control" placeholder="Titulli!" required>
            </div>
            <div class="mb-3">
              <label for="dataf" class="form-label">Data e Strike:</label>
              <input type="date" id="dataf" name="dataf" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="datas" class="form-label">Data e Skadimit:</label>
              <input type="date" id="datas" name="datas" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="url" class="form-label">URL:</label>
              <input type="url" id="url" name="url" class="form-control" placeholder="Linku i kenges!" required>
            </div>
            <div class="mb-3">
              <label for="pershkrimi" class="form-label">Përshkrimi:</label>
              <textarea id="pershkrimi" name="pershkrimi" class="form-control" placeholder="Përshkrimi" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulo</button>
            <button type="submit" name="shto" class="btn btn-danger">Shto</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Additional Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

  <script>
    // Initialize DataTables for existing tables if any
    $(document).ready(function() {
      // Example initialization for a table with ID 'example'
      $('#example').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'pdfHtml5',
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            titleAttr: 'Eksporto tabelen ne formatin PDF',
            className: 'btn btn-light border shadow-2 me-2'
          },
          {
            extend: 'copyHtml5',
            text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            titleAttr: 'Kopjo tabelen ne formatin Clipboard',
            className: 'btn btn-light border shadow-2 me-2'
          },
          {
            extend: 'excelHtml5',
            text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
            titleAttr: 'Eksporto tabelen ne formatin Excel',
            className: 'btn btn-light border shadow-2 me-2'
          },
          {
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: 'Printo tabelën',
            className: 'btn btn-light border shadow-2 me-2'
          }
        ],
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        fixedHeader: true,
        responsive: true
      });
    });

    // Function to confirm client deactivation
    function konfirmoDeaktivizimin(clientId) {
      Swal.fire({
        title: 'A jeni i sigurt?',
        text: 'Jeni duke u përgatitur për të deaktivizuar këtë klient!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Po, deaktivizoje!',
        cancelButtonText: 'Anulo',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return new Promise((resolve) => {
            setTimeout(() => {
              resolve();
            }, 2000);
          });
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Deaktivizuar!',
            text: 'Klienti është deaktivizuar me sukses.',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.href = 'passive_client.php?id=' + clientId;
          });
        }
      });
    }
  </script>
</div>

<?php include 'partials/footer.php'; ?>