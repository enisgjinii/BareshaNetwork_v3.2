<?php
include 'partials/header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
if (!isset($_SESSION['oauth_uid'])) {
  echo '<script>
            Swal.fire({
              icon: "error",
              title: "Akses i Kufizuar",
              text: "Nuk keni akses në këtë sektor.",
              showConfirmButton: false,
              timer: 2000  
            }).then(function() {
              window.location.href = "index.php";
            });
          </script>';
  exit;
}
$user_id = $_SESSION['oauth_uid'];

?>

<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Financat</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
              Shpenzimet
            </a>
          </li>
      </nav>
      <div class="row mb-2">
        <div>
          <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#tabelaShpenzimeveModal">
            <i class="fi fi-rr-add"></i> &nbsp; Shto shpenzim
          </button>
          <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#deletedExpenses">
            <i class="fi fi-rr-trash"></i> &nbsp; Shpenzimet e fshira
          </button>
        </div>
      </div>
      <div class="modal fade" id="deletedExpenses" tabindex="-1" aria-labelledby="deletedExpenses" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deletedExpensesLabel">Shpenzimet e fshira</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table id="deletedExpensesTable" class="table table-bordered w-100">
                <thead class="bg-light">
                  <tr>
                    <th>Klienti</th>
                    <th>Shuma</th>
                    <th>Pagoi</th>
                    <th>Obligim</th>
                    <th>Forma</th>
                    <th>P&euml;rshkrimi</th>
                    <th>Data</th>
                    <th>Link</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="tabelaShpenzimeveModal" tabindex="-1" role="dialog" aria-labelledby="tabelaShpenzimeveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="tabelaShpenzimeveModalLabel">Shpenzimet e klient&euml;ve</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="process_form.php">
                <div class="row">
                  <div class="col-md-6">
                    <label for="emri" class="form-label">Zgjidh nj&euml;rin nga klient&euml;t</label>
                    <select name="stafi" id="stafi" class="form-select shadow-sm rounded-5" style="border: 1px solid #ced4da">
                      <?php
                      $gsta = $conn->query("SELECT * FROM klientet");
                      while ($gst = mysqli_fetch_array($gsta)) {
                      ?>
                        <option value="<?php echo $gst['id']; ?>"><?php echo $gst['emri']; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="datab" class="form-label">Shuma</label>
                    <div class="input-group mb-2 rounded-5 me-2">
                      <div class="input-group-prepend rounded-5 me-2" style="border: 1px solid #ced4da">
                        <div class="input-group-text rounded-5">&euro;</div>
                      </div>
                      <input type="text" name="shuma" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da" id="inlineFormInputGroup" value="0.00">
                    </div>
                  </div>
                </div>
                <!-- Row 2 -->
                <div class="row">
                  <div class="col-md-6">
                    <label for="datas" class="form-label">Data e pages&euml;s</label>
                    <input type="text" name="data" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da" value="<?php echo date("d-m-Y"); ?>">
                  </div>
                  <div class="col-md-6">
                    <label for="pershkrimi" class="form-label">P&euml;rshkrimi</label>
                    <textarea name="pershkrimi" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da"></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label for="youtubeLinks" class="form-label">Lidhjet e YouTube</label>
                    <p class="text-muted" style="font-size: 12px;">Në këtë vend mund të shtoni lidhjet e këngëve nga platforma YouTube. Kur vendosni një lidhje dhe dëshironi të shtoni më shumë, duhet të shtoni një presje (",") në fund të çdo lidhjeje. <span class="badge bg-primary rounded-5 px-2">Kujdes, lejohen vetëm 6 lidhje.</span></p>
                    <textarea style="height: 100px" rows="6" name="youtubeLinks" id="youtubeLinks" class="form-control shadow-sm rounded-5" style="border: 1px solid #ced4da"></textarea>
                  </div>
                </div>
                <!-- Container for video details -->
                <div id="videoDetailsContainer" class="mt-3" style="display: none;">
                  <p class="mb-3">Detajet e videos</p>
                  <div class="card">
                    <div class="card-body">
                      <p class="card-text"><strong>Titulli:</strong> <span id="videoTitle"></span></p>
                      <p class="card-text"><strong>Përshkrim:</strong> <span id="videoDescription"></span></p>
                      <p class="card-text"><strong>Publikuar në:</strong> <span id="publishedAt"></span></p>
                    </div>
                  </div>
                </div>
                <!-- Grid layout for embedded videos -->
                <div class="row" id="embeddedVideosGrid"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
              <input type="submit" class="input-custom-css px-3 py-2" name="ruaj" value="Ruaj">
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="card shadow-sm rounded-5">
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
                <table id="tabelaShpenzimevesss" class="table " hidden>
                  <thead class="bg-light">
                    <tr>
                      <th></th>
                      <th width="2%">Klienti</th>
                      <th>Shuma</th>
                      <th>Pagoi</th>
                      <th>Obligim</th>
                      <th>Forma</th>
                      <th>P&euml;rshkrimi</th>
                      <th>Data</th>
                      <th>Link</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $kueri = $conn->query("SELECT * FROM yinc ORDER BY id DESC");
                    while ($k = mysqli_fetch_array($kueri)) {
                    ?>
                      <tr>
                        <?php
                        $sid = $k['kanali'];
                        $gstaf = $conn->query("SELECT * FROM klientet WHERE id='$sid'");
                        $gstafi = mysqli_fetch_array($gstaf);
                        $myNumber = $k['shuma'];
                        $percentToGet = (float)$gstafi['perqindja'];
                        $percentInDecimal = $percentToGet / 100;
                        $percent = $percentInDecimal * $myNumber;
                        ?>
                        <td style="white-space: normal;">
                          <!-- <a href="#" class="btn btn-danger px-2 m-2 btn-sm text-white rounded-5 shadow-sm delete-btn" data-id="<?php echo $k['id']; ?>"><i class="fi fi-rr-trash py-3"></i></a> -->
                          <a data-bs-toggle="modal" data-bs-target="#pages<?php echo $k['id']; ?>" class="btn btn-primary btn-sm px-2 m-2 text-white rounded-5 shadow-sm" style="text-transform: none;"><i class="fi fi-rr-edit py-3"></i></a>
                          <button class="btn btn-success px-2 m-2 btn-sm text-white rounded-5 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight<?php echo $k['id']; ?>" aria-controls="offcanvasRight<?php echo $k['id']; ?>"><i class="fi fi-rr-time-past py-3"></i></button>
                        </td>
                        <td style="white-space: normal;"><?php echo $gstafi['emri']; ?></td>
                        <td style="white-space: normal;"><?php echo $k['shuma']; ?>&euro;</td>
                        <td style="white-space: normal;"><?php echo $k['pagoi']; ?>&euro;</td>
                        <td style="color:red;"><?php echo $k['shuma'] - $k['pagoi']; ?>&euro; </td>
                        <td style="white-space: normal;"><?php echo $k['lloji']; ?></td>
                        <td style="white-space: normal;"><?php echo $k['pershkrimi']; ?></td>
                        <td style="white-space: normal;"><?php echo $k['data']; ?></td>
                        <td style="white-space: normal;">
                          <?php
                          $links = explode(',', $k['linku_i_kenges']);
                          if (empty($links[0])) { ?>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-5">Nuk ka link</span>
                            <?php } else {
                            foreach ($links as $link) { ?>
                              <a class="input-custom-css px-3 py-2" style="text-transform: none; text-decoration: none;" href="<?php echo $link; ?>" target="_blank"><i class="fi fi-rr-globe"></i> Linku</a><br><br><br>
                          <?php }
                          }
                          ?>
                        </td>
                      </tr>
                      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight<?php echo $k['id']; ?>" aria-labelledby="offcanvasRightLabel<?php echo $k['id']; ?>">
                        <div class="offcanvas-header bg-primary text-white d-flex justify-content-between align-items-center">
                          <div>
                            <h5 class="offcanvas-title mb-0"><?php echo "Historia e shpenzime për klientin"; ?></h5>
                            <div><?php echo $gstafi['emri']; ?></div>
                          </div>
                          <button type="button" class="btn-close text-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                          <div class="timeline">
                            <?php
                            // Fetch data from yinc for the corresponding kanali ID
                            $yinc_query = "SELECT * FROM yinc WHERE kanali = " . $k['kanali'] . " ORDER BY id DESC";
                            $yinc_result = mysqli_query($conn, $yinc_query);
                            while ($row = mysqli_fetch_assoc($yinc_result)) {
                            ?>
                              <div class="timeline-item border p-3 mb-3 rounded shadow">
                                <div class="timeline-content">
                                  <h4 class="text-primary"><?php echo $row['data']; ?></h4>
                                  <p><?php echo $row['pershkrimi']; ?></p>
                                  <p><strong>Klienti:</strong> <?php echo $row['kanali']; ?></p>
                                  <p><strong>Shuma:</strong> <?php echo $row['shuma']; ?></p>
                                  <p><strong>Lloji:</strong> <?php echo $row['lloji']; ?></p>
                                  <p><strong>Pagoi:</strong> <?php echo $row['pagoi']; ?></p>
                                </div>
                              </div>
                            <?php
                            }
                            ?>
                          </div>
                        </div>
                      </div>
                      <form method="POST" id="myForm">
                        <div class="modal fade" id="pages<?php echo $k['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="tabelaShpenzimeveModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="tabelaShpenzimeveModalLabel">Pagesë për klientin - <?php echo $gstafi['emri']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <input type="hidden" name="idp" value="<?php echo $k['id']; ?>">
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
                                  </select>
                                  <br>
                                  <input type="text" id="customOption" class="form-control rounded-5" placeholder="Shtoni opsionin e personalizuar">
                                  <br>
                                  <button onclick="shtoOpcioninPersonalizuar()" class="input-custom-css px-3 py-2" type="button">Shto</button>
                                  <br>
                                  <div id="mesazhi-gabimit" class="text-danger mt-2" style="display: none;"></div>
                                </div>
                                <script>
                                  function shtoOpcioninPersonalizuar() {
                                    var elementiSelektuar = document.getElementById("lloji");
                                    var inputiOpsionitPersonalizuar = document.getElementById("customOption");
                                    var mesazhiGabimit = document.getElementById("mesazhi-gabimit");
                                    var vleraOpsionitPersonalizuar = inputiOpsionitPersonalizuar.value.trim();
                                    // Fshijeni mesazhin e gabimit e mëparshëm
                                    mesazhiGabimit.style.display = "none";
                                    if (vleraOpsionitPersonalizuar !== "") {
                                      // Kontrollo nese opsioni personalizuar tashme ekziston
                                      var ekziston = Array.from(elementiSelektuar.options).some(option => option.value === vleraOpsionitPersonalizuar);
                                      if (!ekziston) {
                                        var opsioni = document.createElement("option");
                                        opsioni.text = vleraOpsionitPersonalizuar;
                                        opsioni.value = vleraOpsionitPersonalizuar;
                                        elementiSelektuar.appendChild(opsioni);
                                        inputiOpsionitPersonalizuar.value = ""; // Pastrojeni fushën pas shtimit të opsionit personalizuar
                                      } else {
                                        mesazhiGabimit.textContent = "Opsioni ekziston";
                                        mesazhiGabimit.style.display = "block";
                                      }
                                    } else {
                                      mesazhiGabimit.textContent = "Ju lutem jepni një vlerë për opsionin personalizuar";
                                      mesazhiGabimit.style.display = "block";
                                    }
                                  }
                                </script>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                                <button type="submit" class="input-custom-css px-3 py-2" name="paguaj" id="paguaj">Paguaj</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    <?php } ?>
                  </tbody>
                  <tfoot class="bg-light">
                    <tr>
                      <th>#</th>
                      <th>Klienti</th>
                      <th>Shuma</th>
                      <th>Pagoi</th>
                      <th>Obligim</th>
                      <th>Forma</th>
                      <th>P&euml;rshkrimi</th>
                      <th>Data</th>
                      <th>Link</th>
                    </tr>
                  </tfoot>
                </table>
                <table id="tabelaShpenzimeve" class="display table">
                  <thead class="bg-light">
                    <tr>
                      <th></th>
                      <th>Klienti</th>
                      <th>Shuma</th>
                      <th>Pagoi</th>
                      <th>Obligim</th>
                      <th>Forma</th>
                      <th>Përkshkrimi</th>
                      <th>Data</th>
                      <th>Link</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  // const apiKey = 'AIzaSyCvc0tIeB58Sz0hpDFSEYxDXFT8tg0VGGQ';
  $(document).ready(function() {
    const $youtubeLinksInput = $('#youtubeLinks');
    const $videoTitle = $('#videoTitle');
    const $videoDescription = $('#videoDescription');
    const $publishedAt = $('#publishedAt');
    const $embeddedVideosGrid = $('#embeddedVideosGrid');
    const $videoDetailsContainer = $('#videoDetailsContainer');
    const $feedbackMessage = $('#feedbackMessage');
    const apiKey = 'AIzaSyCvc0tIeB58Sz0hpDFSEYxDXFT8tg0VGGQ'; // Replace 'YOUR_API_KEY' with your actual API key
    const maxDisplayedVideos = 6;
    const maxDisplayedDetails = 6;
    const maxLinks = 6; // Maximum number of links allowed
    $youtubeLinksInput.on('input', function() {
      const youtubeLinks = $youtubeLinksInput.val().trim().split(',');
      $embeddedVideosGrid.empty();
      clearVideoDetails();
      if (youtubeLinks.length > maxLinks) {
        $feedbackMessage.text(`Maximum ${maxLinks} links allowed.`);
        $feedbackMessage.show();
        $videoDetailsContainer.hide();
        return;
      } else {
        $feedbackMessage.hide();
      }
      // Check if textarea is empty or contains only whitespace
      if (!youtubeLinks.some(link => link.trim() !== '')) {
        $videoDetailsContainer.hide();
        return;
      }
      $videoDetailsContainer.show();
      youtubeLinks.slice(0, maxDisplayedVideos).forEach(link => {
        const videoId = extractYouTubeVideoId(link);
        if (videoId) {
          // Create a grid item for each embedded video
          const embedCode = `<div class="col-md-4 mb-4"><iframe width="100%" height="200" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe></div>`;
          $embeddedVideosGrid.append(embedCode);
          // Get video details using the YouTube Data API
          fetch(`https://www.googleapis.com/youtube/v3/videos?part=snippet&id=${videoId}&key=${apiKey}`)
            .then(response => response.json())
            .then(data => {
              const videoDetails = data.items[0].snippet;
              displayVideoDetails(videoDetails);
            })
            .catch(error => console.error('Error fetching video details:', error));
        }
      });
    });
    // Clear button functionality
    $('#clearButton').click(function() {
      $youtubeLinksInput.val('');
      $embeddedVideosGrid.empty();
      clearVideoDetails();
      $videoDetailsContainer.hide();
      $feedbackMessage.hide();
    });

    function extractYouTubeVideoId(link) {
      const regex = /[?&]v=([^&]+)/;
      const match = link.match(regex) || link.match(/(?:\/|%3D|v=|vi=)([^"&\?\/\s]{11})/);
      return match && match[1] ? match[1] : null;
    }

    function displayVideoDetails(details) {
      if ($videoTitle.children().length < maxDisplayedDetails) {
        $videoTitle.append(`<p>${details.title}</p>`);
        $videoDescription.append(`<p>${details.description}</p>`);
        $publishedAt.append(`<p>${details.publishedAt}</p>`);
      }
    }

    function clearVideoDetails() {
      $videoTitle.empty();
      $videoDescription.empty();
      $publishedAt.empty();
    }
  });
  new Selectr('#stafi', {
    searchable: true,
    width: 300
  });
  $(document).ready(function() {
    var table = $('#tabelaShpenzimeve').DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": "fetch_shpenzime.php", // Path to your server-side script
      "columns": [{
          "data": null,
          "render": function(data, type, row) {
            return '<a href="#" class="btn btn-danger px-2 m-2 btn-sm text-white rounded-5 shadow-sm delete-btn" data-id="' + row.id + '"><i class="fi fi-rr-trash py-3"></i></a>' +
              '<a data-bs-toggle="modal" data-bs-target="#pages' + row.id + '" class="btn btn-primary btn-sm px-2 m-2 text-white rounded-5 shadow-sm" style="text-transform: none;"><i class="fi fi-rr-edit py-3"></i></a>' +
              '<button class="btn btn-success px-2 m-2 btn-sm text-white rounded-5 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight' + row.id + '" aria-controls="offcanvasRight' + row.id + '"><i class="fi fi-rr-time-past py-3"></i></button>';
          }
        },
        {
          "data": "klienti_emri"
        },
        {
          "data": "shuma"
        },
        {
          "data": "pagoi"
        },
        {
          "data": function(row) {
            var remainingBalance = row.shuma - row.pagoi;
            // Check if the remaining balance is 0
            if (remainingBalance === 0) {
              // Return the value with green color
              return '<span style="color: green;">' + remainingBalance + '</span>';
            } else if (remainingBalance > 0) {
              // Return the value with red color
              return '<span style="color: red;">' + remainingBalance + '</span>';
            } else {
              // Return the value as is
              return remainingBalance;
            }
          }
        },
        {
          "data": "lloji"
        },
        {
          "data": "pershkrimi"
        },
        {
          "data": "data"
        },
        {
          "data": "linku_i_kenges",
          "render": function(data) {
            var links = data.split(',');
            var output = '';
            if (links[0]) {
              for (var i = 0; i < links.length; i++) {
                output += '<a class="input-custom-css px-3 py-2" style="text-transform: none; text-decoration: none;" href="' + links[i] + '" target="_blank"><i class="fi fi-rr-globe"></i> Linku</a><br><br><br>';
              }
            } else {
              output = '<span class="badge bg-warning text-dark px-3 py-2 rounded-5">Nuk ka link</span>';
            }
            return output;
          }
        },
      ],
      stripeClasses: ["stripe-color"],
      columnDefs: [{
        "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8],
        "render": function(data, type, row) {
          return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
        }
      }],
      "lengthMenu": [5, 10, 25, 50], // Set the number of records per page
      "pageLength": 10, // Initial records per page
      "paging": true, // Enable pagination
      "searching": true, // Enable search functionality
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
        titleAttr: 'Eksporto tabelen ne formatin Excel',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5',
        exportOptions: {
          modifier: {
            search: 'applied',
            order: 'applied',
            page: 'all'
          }
        }
      }, {
        extend: 'print',
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: 'Printo tabel&euml;n',
        className: 'btn btn-light btn-sm bg-light border me-2 rounded-5'
      }, ],
      "drawCallback": function() {
        // Add click event listener to delete buttons inside DataTable
        $('.delete-btn').off('click').on('click', function(event) {
          event.preventDefault(); // Prevent the default action of the link
          const id = $(this).data('id');
          // Show confirmation dialog with additional features
          Swal.fire({
            title: 'A jeni i sigurt?',
            text: 'Nuk do të mund ta rikuperoni këtë rekord!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Po, fshijeni!',
            cancelButtonText: 'Anulo', // Adding cancel button text
            reverseButtons: true, // Swapping the buttons position
            showCloseButton: true, // Display close button
            showLoaderOnConfirm: true, // Display loader on confirm button
            preConfirm: () => { // Pre-confirm function
              return fetch(`delete_record.php?id=${id}`)
                .then(response => {
                  if (!response.ok) {
                    throw new Error(response.statusText)
                  }
                  return response.text(); // Change to text() to see the response
                })
                .then(responseText => {
                  console.log(responseText); // Log the response
                  return responseText; // Return response text
                })
                .catch(error => {
                  Swal.showValidationMessage(
                    `Kërkesa dështoi: ${error}`
                  );
                });
            }
          }).then((result) => {
            if (result.isConfirmed) {
              // If confirmed, show success message
              Swal.fire(
                'Fshirë!',
                'Rekordi juaj është fshirë.',
                'success'
              );
              const currentPage = table.page.info().page;
              // Reload table data
              table.ajax.reload(function() {
                // After reload, set the table to the saved current page
                table.page(currentPage).draw('page');
              });
            }
          });
        });
      }
    });
  });
  $('#deletedExpensesTable').DataTable({
    searching: true,
    "processing": true,
    "serverSide": true,
    "ajax": {
      url: "deleted_expenses.php",
      type: "POST"
    },
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
      "<'row'<'col-md-12'tr>>" +
      "<'row'<'col-md-6'><'col-md-6'p>>",
    buttons: [{
        extend: "pdfHtml5",
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: "Eksporto tabelen ne formatin PDF",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      },
      {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      },
      {
        extend: "excelHtml5",
        text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
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
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: "Printo tabel&euml;n",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
      },
    ],
    "columns": [{
        "data": "klienti"
      },
      {
        "data": "shuma"
      },
      {
        "data": "pagoi"
      },
      {
        "data": function(row) {
          return row.shuma - row.pagoi;
        }
      },
      {
        "data": "data"
      },
      {
        "data": "lloji"
      },
      {
        "data": "pershkrimi"
      },
      {
        "data": "linku_i_kenges"
      }
    ],
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
    fixedHeader: false,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color']
  });
  document.getElementById('lloji').addEventListener('change', function() {
    var selectedOption = this.value;
    if (selectedOption === 'custom') {
      Swal.fire({
        title: 'Shëno emrin e personalizuar të bankës:',
        input: 'text',
        showCancelButton: true,
        confirmButtonText: 'Shto',
        cancelButtonText: 'Anulo',
        inputValidator: (value) => {
          if (!value) {
            return 'Ju duhet të shënoni diçka!';
          }
        }
      }).then((result) => {
        if (result.isConfirmed) {
          var customBankName = result.value;
          // Add the custom bank name as an option
          var selectElement = document.getElementById('lloji');
          var customOption = document.createElement('option');
          customOption.value = customBankName;
          customOption.textContent = customBankName;
          selectElement.appendChild(customOption);
          // Select the newly added custom bank name
          selectElement.value = customBankName;
        }
      });
    }
  });
</script>

<script src="form_script.js"></script>