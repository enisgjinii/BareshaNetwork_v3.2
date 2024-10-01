<?php
include 'partials/header.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['oauth_uid'])) {
  echo '<script>
            Swal.fire({
                icon: "error",
                title: "Akses i Kufizuar",
                text: "Nuk keni akses në këtë sektor.",
                showConfirmButton: false,
                timer: 2000  
            }).then(() => window.location.href = "index.php");
          </script>';
  exit;
}
$user_id = $_SESSION['oauth_uid'];
// Handle Payment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paguaj'])) {
  $shpages = isset($_POST['pagoi']) ? floatval($_POST['pagoi']) : 0.00;
  $lloji = isset($_POST['lloji']) ? $_POST['lloji'] : '';
  $idof = isset($_POST['idp']) ? intval($_POST['idp']) : 0;
  if ($idof > 0) {
    $stmt = $conn->prepare("UPDATE yinc SET pagoi = pagoi + ?, lloji = ? WHERE id = ?");
    $stmt->bind_param("dsi", $shpages, $lloji, $idof);
    if ($stmt->execute()) {
      echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "Sukses",
                        text: "Pagesa u be me sukses",
                        showConfirmButton: false,
                        timer: 2000  
                    });
                  </script>';
    } else {
      error_log("Error in updating payment: " . $conn->error);
      echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Gabim",
                        text: "Gabim në procesimin e pagesës. Ju lutemi provoni përsëri më vonë.",
                        showConfirmButton: false,
                        timer: 2000  
                    });
                  </script>';
    }
    $stmt->close();
  }
}
?>
<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Financat</a></li>
          <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Borxhet</a></li>
        </ol>
      </nav>
      <!-- Action Buttons -->
      <div class="row mb-2">
        <div>
          <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="fi fi-rr-add"></i> Shto borxh
          </button>
        </div>
      </div>
      <!-- Add Debt Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Borxhet e klientëve</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="api/post_methods/post_new_debt.php">
                <div class="row">
                  <div class="col-md-6">
                    <label for="stafi" class="form-label">Zgjidh njërin nga klientët</label>
                    <select name="stafi" id="stafi" class="form-select shadow-sm rounded-5 border">
                      <?php
                      $gsta = $conn->query("SELECT * FROM klientet WHERE aktiv IS NULL");
                      while ($gst = $gsta->fetch_assoc()) :
                      ?>
                        <option value="<?= htmlspecialchars($gst['id']) ?>"><?= htmlspecialchars($gst['emri']) ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="shuma" class="form-label">Shuma</label>
                    <div class="input-group mb-2 rounded-5 border">
                      <span class="input-group-text rounded-5">&euro;</span>
                      <input type="text" name="shuma" class="form-control shadow-sm rounded-5 border" id="shuma" value="0.00">
                    </div>
                  </div>
                </div>
                <!-- Row 2 -->
                <div class="row">
                  <div class="col-md-6">
                    <label for="data" class="form-label">Data e pagesës</label>
                    <input type="text" name="data" class="form-control shadow-sm rounded-5 border" value="<?= date("d-m-Y") ?>">
                  </div>
                  <div class="col-md-6">
                    <label for="pershkrimi" class="form-label">Përshkrimi</label>
                    <textarea name="pershkrimi" class="form-control shadow-sm rounded-5 border" rows="3"></textarea>
                  </div>
                </div>
                <!-- YouTube Links -->
                <div class="row mt-3">
                  <div class="col-12">
                    <label for="youtubeLinks" class="form-label">Lidhjet e YouTube</label>
                    <p class="text-muted" style="font-size: 12px;">
                      Në këtë vend mund të shtoni lidhjet e këngëve nga platforma YouTube. Përdorni presje (",") për të ndarë lidhjet.
                      <span class="badge bg-primary rounded-5 px-2">Kujdes, lejohen vetëm 6 lidhje.</span>
                    </p>
                    <textarea name="youtubeLinks" id="youtubeLinks" class="form-control shadow-sm rounded-5 border" rows="6" style="height: 100px;"></textarea>
                  </div>
                </div>
                <!-- Video Details -->
                <div id="videoDetailsContainer" class="mt-3 d-none">
                  <p class="mb-3">Detajet e videos</p>
                  <div class="card">
                    <div class="card-body">
                      <p class="card-text"><strong>Titulli:</strong> <span id="videoTitle"></span></p>
                      <p class="card-text"><strong>Përshkrim:</strong> <span id="videoDescription"></span></p>
                      <p class="card-text"><strong>Publikuar në:</strong> <span id="publishedAt"></span></p>
                    </div>
                  </div>
                </div>
                <!-- Embedded Videos Grid -->
                <div class="row" id="embeddedVideosGrid"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
              <input type="submit" class="btn input-custom-css px-3 py-2" name="ruaj" value="Ruaj">
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Debts Table -->
      <div class="card shadow-sm rounded-5">
        <div class="card-body">
          <div class="table-responsive">
            <table id="example" class="table">
              <thead class="bg-light">
                <tr>
                  <th class="text-dark">Klienti</th>
                  <th class="text-dark">Shuma</th>
                  <th class="text-dark">Pagoi</th>
                  <th class="text-dark">Obligim</th>
                  <th class="text-dark">Forma</th>
                  <th class="text-dark">Përshkrimi</th>
                  <th class="text-dark">Data</th>
                  <th class="text-dark">Veprim</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $kueri = $conn->query("SELECT * FROM yinc ORDER BY id DESC");
                while ($k = $kueri->fetch_assoc()) :
                  $sid = $k['kanali'];
                  $gstaf = $conn->prepare("SELECT * FROM klientet WHERE id = ?");
                  $gstaf->bind_param("i", $sid);
                  $gstaf->execute();
                  $result = $gstaf->get_result();
                  $gstafi = $result->fetch_assoc();
                  $myNumber = (float)$k['shuma'];
                  $percentToGet = (float)$gstafi['perqindja'];
                  $percent = ($percentToGet / 100) * $myNumber;
                ?>
                  <tr>
                    <td><?= htmlspecialchars($gstafi['emri'] ?? '') ?></td>
                    <td><?= number_format($k['shuma'] ?? 0, 2) ?>&euro;</td>
                    <td><?= number_format($k['pagoi'] ?? 0, 2) ?>&euro;</td>
                    <td style="color:red;"><?= number_format(($k['shuma'] ?? 0) - ($k['pagoi'] ?? 0), 2) ?>&euro;</td>
                    <td><?= htmlspecialchars($k['lloji'] ?? '') ?></td>
                    <td>
                      <?php
                      $description = htmlspecialchars($k['pershkrimi'] ?? '');
                      $truncated = mb_substr($description, 0, 20);
                      $isLong = mb_strlen($description) > 20;
                      ?>
                      <span class="description-short"><?= $truncated ?></span>
                      <?php if ($isLong) : ?>
                        <span class="ellipsis">...</span>
                        <a href="javascript:void(0);" class="expand-description text-primary" style="text-decoration:none"> ▼</a>
                        <span class="description-full d-none"><?= $description ?></span>
                        <a href="javascript:void(0);" class="collapse-description text-primary d-none" style="text-decoration:none"> ▲</a>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($k['data'] ?? '') ?></td>
                    <td>
                      <?php
                      // Extract and sanitize links
                      $links = array_filter(array_map('trim', explode(',', $k['linku_i_kenges'] ?? '')));
                      ?>
                      <!-- Display Links or No Link Badge -->
                      <?php if (empty($links)): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-5">Nuk ka link</span>
                      <?php else: ?>
                        <?php foreach ($links as $link): ?>
                          <a
                            class="input-custom-css px-3 py-2 mx-1"
                            style="text-decoration:none;"
                            href="<?= htmlspecialchars($link) ?>"
                            target="_blank"
                            aria-label="External Link">
                            <i class="fi fi-rr-globe"></i>
                          </a>
                        <?php endforeach; ?>
                      <?php endif; ?>
                      <!-- Action Buttons -->
                      <a
                        href="#"
                        class="input-custom-css px-3 py-2 mx-1 delete-btn"
                        style="text-decoration:none;"
                        data-id="<?= htmlspecialchars($k['id']) ?>"
                        aria-label="Delete">
                        <i class="fi fi-rr-trash"></i>
                      </a>
                      <a
                        data-bs-toggle="modal"
                        data-bs-target="#pages<?= htmlspecialchars($k['id']) ?>"
                        class="input-custom-css px-3 py-2"
                        style="text-decoration:none;"
                        aria-label="Edit">
                        <i class="fi fi-rr-edit"></i>
                      </a>
                      <button
                        class="input-custom-css px-3 py-2 mx-1"
                        style="text-decoration:none;"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasRight<?= htmlspecialchars($k['id']) ?>"
                        aria-controls="offcanvasRight<?= htmlspecialchars($k['id']) ?>"
                        aria-label="View History">
                        <i class="fi fi-rr-time-past"></i>
                      </button>
                      <!-- Offcanvas for History -->
                      <div
                        class="offcanvas offcanvas-end"
                        tabindex="-1"
                        id="offcanvasRight<?= htmlspecialchars($k['id']) ?>"
                        aria-labelledby="offcanvasRightLabel<?= htmlspecialchars($k['id']) ?>">
                        <div class="offcanvas-header bg-primary text-white d-flex justify-content-between align-items-center">
                          <div>
                            <h5 class="offcanvas-title mb-0">Historia e borxheve për klientin</h5>
                            <div><?= htmlspecialchars($gstafi['emri'] ?? '') ?></div>
                          </div>
                          <button
                            type="button"
                            class="btn-close text-white"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                          <div class="timeline">
                            <?php
                            // Prepare and execute the statement to fetch history
                            $stmt = $conn->prepare("SELECT * FROM yinc WHERE kanali = ? ORDER BY id DESC");
                            $stmt->bind_param("i", $k['kanali']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                              <div class="timeline-item border p-3 mb-3 rounded shadow">
                                <div class="timeline-content">
                                  <h4 class="text-primary"><?= htmlspecialchars($row['data'] ?? '') ?></h4>
                                  <p><?= htmlspecialchars($row['pershkrimi'] ?? '') ?></p>
                                  <p><strong>Klienti:</strong> <?= htmlspecialchars($row['kanali'] ?? '') ?></p>
                                  <p><strong>Shuma:</strong> <?= htmlspecialchars(number_format($row['shuma'] ?? 0, 2)) ?>&euro;</p>
                                  <p><strong>Lloji:</strong> <?= htmlspecialchars($row['lloji'] ?? '') ?></p>
                                  <p><strong>Pagoi:</strong> <?= htmlspecialchars(number_format($row['pagoi'] ?? 0, 2)) ?>&euro;</p>
                                </div>
                              </div>
                            <?php endwhile; ?>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <!-- Payment Modal -->
                  <div class="modal fade" id="pages<?= $k['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="pagesLabel<?= $k['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="pagesLabel<?= $k['id'] ?>">Pagesë për klientin - <?= htmlspecialchars($gstafi['emri'] ?? '') ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <form method="POST" action="">
                            <input type="hidden" name="idp" value="<?= $k['id'] ?>">
                            <div class="mb-3">
                              <label for="pagoi" class="form-label">Shuma:</label>
                              <div class="input-group">
                                <span class="input-group-text">&euro;</span>
                                <input type="text" name="pagoi" class="form-control" id="pagoi" value="0.00">
                              </div>
                            </div>
                            <div class="mb-3">
                              <label for="lloji" class="form-label">Forma e pagesës:</label>
                              <select name="lloji" class="form-select" id="lloji">
                                <option value="Bank">Banka</option>
                                <option value="Cash">Cash</option>
                                <option value="custom">Personalizuar</option>
                              </select>
                              <div class="mt-2">
                                <input type="text" id="customOption<?= $k['id'] ?>" class="form-control rounded-5" placeholder="Shtoni opsionin e personalizuar">
                                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="shtoOpcioninPersonalizuar('lloji<?= $k['id'] ?>', 'customOption<?= $k['id'] ?>', 'mesazhi-gabimit<?= $k['id'] ?>')">Shto</button>
                                <div id="mesazhi-gabimit<?= $k['id'] ?>" class="text-danger mt-2 d-none"></div>
                              </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                          <button type="submit" class="btn input-custom-css px-3 py-2" name="paguaj">Paguaj</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<!-- JavaScript Enhancements -->
<script>
  $(document).ready(function() {
    const apiKey = 'AIzaSyCvc0tIeB58Sz0hpDFSEYxDXFT8tg0VGGQ';
    const maxDisplayedVideos = 6;
    const maxLinks = 6;
    // Initialize Selectr for better select elements
    new Selectr('#stafi', {
      searchable: true,
      width: 300
    });
    // Initialize DataTables for the main table
    const mainTable = $('#example').DataTable({
      searching: true,
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      order: [],
      buttons: [{
          extend: "pdfHtml5",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "copyHtml5",
          text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
          titleAttr: "Kopjo tabelen ne formatin Clipboard",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
          titleAttr: "Eksporto tabelen ne formatin Excel",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all",
            },
          },
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
          titleAttr: "Printo tabelën",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
      ],
      initComplete: function() {
        $(".dt-buttons").removeClass("dt-buttons btn-group").addClass("");
        $("div.dataTables_length select").addClass("form-select").css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem",
        });
      },
      fixedHeader: false,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color']
    });
    // Initialize DataTables for the deleted expenses table
    const deletedExpensesTable = $('#deletedExpensesTable').DataTable({
      searching: true,
      processing: true,
      serverSide: true,
      ajax: {
        url: "deleted_expenses.php",
        type: "POST"
      },
      dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
        "<'row'<'col-md-12'tr>>" +
        "<'row'<'col-md-6'><'col-md-6'p>>",
      buttons: [{
          extend: "pdfHtml5",
          text: '<i class="fi fi-rr-file-pdf fa-lg"></i> PDF',
          titleAttr: "Eksporto tabelen ne formatin PDF",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "copyHtml5",
          text: '<i class="fi fi-rr-copy fa-lg"></i> Kopjo',
          titleAttr: "Kopjo tabelen ne formatin Clipboard",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
        {
          extend: "excelHtml5",
          text: '<i class="fi fi-rr-file-excel fa-lg"></i> Excel',
          titleAttr: "Eksporto tabelen ne formatin Excel",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
          exportOptions: {
            modifier: {
              search: "applied",
              order: "applied",
              page: "all",
            },
          },
        },
        {
          extend: "print",
          text: '<i class="fi fi-rr-print fa-lg"></i> Printo',
          titleAttr: "Printo tabelën",
          className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
        },
      ],
      columns: [{
          data: "klienti"
        },
        {
          data: "shuma"
        },
        {
          data: "pagoi"
        },
        {
          data: null,
          render: function(data, type, row) {
            return (row.shuma - row.pagoi).toFixed(2) + '€';
          }
        },
        {
          data: "lloji"
        },
        {
          data: "pershkrimi"
        },
        {
          data: "data"
        },
        {
          data: "linku_i_kenges",
          render: function(data, type, row) {
            if (!data) return '<span class="badge bg-warning text-dark px-3 py-2 rounded-5">Nuk ka link</span>';
            const links = data.split(',').map(link => link.trim()).filter(link => link);
            return links.map(link => `<a class="btn btn-light btn-sm text-decoration-none mb-2" href="${link}" target="_blank"><i class="fi fi-rr-globe"></i> Linku</a>`).join('<br>');
          }
        }
      ],
      initComplete: function() {
        $(".dt-buttons").removeClass("dt-buttons btn-group").addClass("");
        $("div.dataTables_length select").addClass("form-select").css({
          width: "auto",
          margin: "0 8px",
          padding: "0.375rem 1.75rem 0.375rem 0.75rem",
          lineHeight: "1.5",
          border: "1px solid #ced4da",
          borderRadius: "0.25rem",
        });
      },
      fixedHeader: false,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
      },
      stripeClasses: ['stripe-color']
    });
    // Handle Delete Button Click
    $(document).on('click', '.delete-btn', function(e) {
      e.preventDefault();
      const id = $(this).data('id');
      const row = $(this).closest('tr');
      Swal.fire({
        title: 'A jeni i sigurt?',
        text: 'Nuk do të mund ta rikuperoni këtë rekord!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Po, fshijeni!',
        cancelButtonText: 'Anulo',
        reverseButtons: true,
        showCloseButton: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return fetch(`api/delete_methods/delete_record.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
              if (data.status === 'success') {
                return data;
              } else {
                throw new Error(data.message);
              }
            })
            .catch(error => {
              Swal.showValidationMessage(`Kërkesa dështoi: ${error}`);
            });
        }
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire(
            'Fshirë!',
            'Rekordi juaj është fshirë.',
            'success'
          ).then(() => {
            // Remove the row from the main DataTable
            mainTable.row(row).remove().draw();
            // Optionally, you can reload the deletedExpensesTable to reflect the change
            deletedExpensesTable.ajax.reload(null, false);
          });
        }
      });
    });
    // Custom Option Addition Function
    window.shtoOpcioninPersonalizuar = function(selectId, inputId, messageId) {
      const selectElement = document.getElementById(selectId);
      const inputElement = document.getElementById(inputId);
      const messageElement = document.getElementById(messageId);
      const value = inputElement.value.trim();
      messageElement.classList.add('d-none');
      messageElement.textContent = '';
      if (value) {
        const exists = Array.from(selectElement.options).some(option => option.value.toLowerCase() === value.toLowerCase());
        if (!exists) {
          const option = document.createElement("option");
          option.text = value;
          option.value = value;
          selectElement.appendChild(option);
          selectElement.value = value;
          inputElement.value = "";
        } else {
          messageElement.textContent = "Opsioni ekziston";
          messageElement.classList.remove('d-none');
        }
      } else {
        messageElement.textContent = "Ju lutem jepni një vlerë për opsionin personalizuar";
        messageElement.classList.remove('d-none');
      }
    };
    // Handle YouTube Links Input
    $('#youtubeLinks').on('input', function() {
      const youtubeLinks = $(this).val().split(',').map(link => link.trim()).filter(link => link);
      $('#embeddedVideosGrid').empty();
      clearVideoDetails();
      if (youtubeLinks.length > maxLinks) {
        Swal.fire({
          icon: 'warning',
          title: `Maximum ${maxLinks} links allowed.`,
          timer: 2000,
          showConfirmButton: false
        });
        return;
      }
      if (youtubeLinks.length === 0) {
        $('#videoDetailsContainer').hide();
        return;
      }
      $('#videoDetailsContainer').show();
      youtubeLinks.slice(0, maxDisplayedVideos).forEach(link => {
        const videoId = extractYouTubeVideoId(link);
        if (videoId) {
          $('#embeddedVideosGrid').append(`
                        <div class="col-md-4 mb-4">
                            <iframe width="100%" height="200" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>
                        </div>
                    `);
          fetchVideoDetails(videoId);
        }
      });
    });
    function extractYouTubeVideoId(link) {
      const regex = /(?:https?:\/\/)?(?:www\.)?youtu(?:be\.com\/watch\?v=|\.be\/)([^\s&]+)/;
      const match = link.match(regex);
      return match ? match[1].substring(0, 11) : null;
    }
    function fetchVideoDetails(videoId) {
      $.get(`https://www.googleapis.com/youtube/v3/videos?part=snippet&id=${videoId}&key=${apiKey}`, function(data) {
        if (data.items.length) {
          const details = data.items[0].snippet;
          $('#videoTitle').append(`<p>${details.title}</p>`);
          $('#videoDescription').append(`<p>${details.description}</p>`);
          $('#publishedAt').append(`<p>${details.publishedAt}</p>`);
        }
      }).fail(function() {
        console.error('Error fetching video details');
      });
    }
    function clearVideoDetails() {
      $('#videoTitle').empty();
      $('#videoDescription').empty();
      $('#publishedAt').empty();
    }
    // Handle Expand/Collapse Description
    $(document).on('click', '.expand-description', function(e) {
      e.preventDefault();
      const parent = $(this).closest('td');
      parent.find('.description-short').hide();
      parent.find('.ellipsis').hide();
      parent.find('.expand-description').hide();
      parent.find('.description-full').removeClass('d-none active').show();
      parent.find('.collapse-description').removeClass('d-none').show();
    });
    $(document).on('click', '.collapse-description', function(e) {
      e.preventDefault();
      const parent = $(this).closest('td');
      parent.find('.description-full').addClass('d-none active').hide();
      parent.find('.collapse-description').addClass('d-none').hide();
      parent.find('.description-short').show();
      parent.find('.ellipsis').show();
      parent.find('.expand-description').show();
    });
  });
</script>