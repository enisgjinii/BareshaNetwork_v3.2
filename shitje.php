<?php

include 'partials/header.php';
$json = file_get_contents('http://www.floatrates.com/daily/usd.json');
$obj = json_decode($json);
if (empty($_GET['fatura'])) {
  die("Nuk u gjet fatura!");
} else {
  $fatura = $_GET['fatura'];
}
// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM fatura WHERE fatura=?");
$stmt->bind_param("s", $fatura);
$stmt->execute();
$mfidi = $stmt->get_result()->fetch_assoc();
$midc = $mfidi['emri'];
$totali = $mfidi['totali_youtube'];



if (isset($_POST['ruaj'])) {

  $emertimi = mysqli_real_escape_string($conn, $_POST['emertimi']);
  $qmimi2 = $_POST['qmimi'] * $obj->eur->rate;
  if ($_POST['valuta'] == "euro") {
    $qmimi = $_POST['qmimi'];
  } else {
    $qmimi = $qmimi2;
  }
  // Use caching mechanism to improve performance
  $gstai = $conn->query("SELECT * FROM klientet WHERE id='$midc'");
  $gstai2 = mysqli_fetch_array($gstai);
  $perqindja = $gstai2['perqindja'];
  $pdc = $perqindja / 100;
  if ($qmimi <= "0") {
    $shk = "0.00";
  } else {
    $shk = $pdc * $qmimi;
  }
  $shm = $qmimi - $shk;
  $datas = date("Y-m-d H:i:s");

  $kenga = mysqli_real_escape_string($conn, $_POST['kenga']);
  $emri_artistit = mysqli_real_escape_string($conn, $_POST['emri_artistit']);



  if ($conn->query("INSERT INTO shitje (emertimi, qmimi, perqindja, klientit, mbetja,totali, fatura, data,linku_kenges,kengetari) VALUES ('$emertimi', '$qmimi', '$perqindja', '$shm', '$shk', '$shm', '$fatura', '$datas', '$kenga', '$emri_artistit')")) {
  } else {
    echo "Ndodhi nj&euml; gabim: " . $conn->error;
  }
}
if (isset($_POST['update'])) {
  $qmimi = $_POST['qmimi'];
  // Use caching mechanism to improve performance
  $gstai = $conn->query("SELECT * FROM klientet WHERE id='$midc'");
  $gstai2 = mysqli_fetch_array($gstai);
  $perqindja = $_POST['perqindja'];
  $pdc = $perqindja / 100;
  if ($qmimi <= "0") {
    $shk = "0.00";
  } else {
    $shk = $pdc * $qmimi;
  }
  $shm = $qmimi - $shk;
  $updateid = $_POST['editid'];
  $eme = $_POST['emertimi'];
  // Add error handling
  if ($conn->query("UPDATE shitje SET emertimi='$eme',perqindja='$perqindja' ,qmimi='$qmimi', klientit='$shm', mbetja='$shk', totali='$shm' WHERE id='$updateid'")) {
  } else {
    echo "Ndodhi nj&euml; gabim: " . $conn->error;
  }
}


?>



<!-- Begin Page Content -->
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <div class="p-5 rounded-5 shadow-sm mb-4 card">
          <div>
            <button id="toggleTableBtn" class="btn btn-primary text-white rounded-5 shadow-sm my-3" style="text-transform:none;">Shiko tabelen e borgjit para se te krijosh
              fatur&euml;</button>
          </div>
          <div id="tableContent" style="display: none;">
            <?php
            $faturaQuery = "SELECT * FROM fatura WHERE emri='$midc' ORDER BY id DESC";
            $faturaResult = $conn->query($faturaQuery);
            if ($faturaRow = mysqli_fetch_array($faturaResult)) {
            ?>
              <p class="border p-2 w-25 rounded-5 shadow-sm">ID:
                <?php echo $faturaRow['fatura']; ?>
              </p>
              <p class="border p-2 w-25 rounded-5 shadow-sm">Data e fatures:
                <?php echo $faturaRow['data']; ?>
              </p>
            <?php
            }
            ?>
            <?php
            $stmt = $conn->prepare("SELECT * FROM yinc WHERE kanali=?");
            $stmt->bind_param('s', $midc);
            $stmt->execute();

            if ($result = $stmt->get_result()) {
            ?>
              <table class="table table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th>Klienti</th>
                    <th>Obligimi</th>
                    <th>P&euml;rshkrimi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $total = 0; // Variable to store the total

                  while ($listaob = mysqli_fetch_array($result)) {
                    if ($listaob['shuma'] > $listaob['pagoi']) {
                      $amount = $listaob['shuma'] - $listaob['pagoi'];
                      $total += $amount; // Add the amount to the total
                  ?>
                      <tr>
                        <td>
                          <?php echo $faturaRow['emrifull']; ?>
                        </td>
                        <td>
                          <?php echo $amount; ?>&euro;
                        </td>
                        <td>
                          <?php echo $listaob['pershkrimi']; ?>
                        </td>
                      </tr>
                  <?php
                    }
                  }
                  ?>
                  <tr>
                    <td colspan="1" style="text-align: right;"><b>Total:</b></td>
                    <td>
                      <b>
                        <?php echo $total; ?>&euro;
                      </b>
                    </td>
                  </tr>
                </tbody>
              </table>

            <?php
            }
            ?>
          </div>

          <script>
            var toggleBtn = document.getElementById("toggleTableBtn");
            var tableContent = document.getElementById("tableContent");

            toggleBtn.addEventListener("click", function() {
              if (tableContent.style.display === "none") {
                tableContent.style.display = "block";
                toggleBtn.textContent = "Fshihe tabelen";
              } else {
                tableContent.style.display = "none";
                toggleBtn.textContent = "Shfaqe tabelen";
              }
            });
          </script>




          <div class="p-3 my-3 card rounded-5">
            <form class="" method="POST" action="shitje.php?fatura=<?php echo $fatura; ?>">
              <div class="form-group row mb-3">
                <div class="col">
                  <label for="emertimi" class="form-label">Em&euml;rtimi</label>
                  <input type="text" name="emertimi" autocomplete="off" class="form-control shadow-sm rounded-5" placeholder="Em&euml;rtimi" style="border:1px solid lightgrey">
                </div>
                <div class="col">
                  <label for="emertimi" class="form-label">Qmimi</label>
                  <input type="text" name="qmimi" autocomplete="off" value="<?php echo $totali ?>" class="form-control shadow-sm rounded-5" placeholder="Qmimi" style="border:1px solid lightgrey">
                </div>



                <div class="col">
                  <label for="emri_artistit" class="form-label">Emri i artistit</label>
                  <input type="text" name="emri_artistit" autocomplete="off" class="form-control shadow-sm rounded-5" placeholder="Emri i artistit" style="border:1px solid lightgrey">
                </div>
                <div class="col-1">
                  <label for="valuta" class="form-label">Valuta
                  </label>
                  <select name="valuta" class="form-select shadow-sm rounded-5 p-2">
                    <option value="dollar">$</option>
                    <option value="euro" selected="">&euro;</option>
                  </select>
                </div>
              </div>
              <div class="form-group row mb-3">
                <div class="col">
                  <label for="kenga" id="youtube-label" class="form-label hidden">K&euml;nga</label>
                  <input type="text" id="youtube-link" name="kenga" autocomplete="off" class="form-control shadow-sm rounded-5 hidden" placeholder="K&euml;nga" style="border:1px solid lightgrey">
                </div>
                <div class="col">
                  <label for="kenga" id="title-label" class="form-label hidden">Titulli i k&euml;ng&euml;s</label>
                  <input type="text" id="title_of_music" name="title_of_music" autocomplete="off" class="form-control shadow-sm rounded-5 hidden" placeholder="Titulli i k&euml;ng&euml;s" style="border:1px solid lightgrey">
                </div>
              </div>
              <button type="button" id="expand-button" class="btn btn-primary rounded-5 shadow-sm text-white my-3" style="text-transform: none;">Shto k&euml;ng&euml; apo k&euml;ng&euml;tar</button>

              <script>
                document.getElementById('expand-button').addEventListener('click', function() {
                  var youtubeLink = document.getElementById('youtube-link');
                  var titleOfMusic = document.getElementById('title_of_music');
                  var youtubeLabel = document.getElementById('youtube-label');
                  var titleLabel = document.getElementById('title-label');

                  youtubeLink.classList.toggle('hidden');
                  titleOfMusic.classList.toggle('hidden');
                  youtubeLabel.classList.toggle('hidden');
                  titleLabel.classList.toggle('hidden');
                });
              </script>

              <style>
                .hidden {
                  display: none;
                }
              </style>


              <div>
                <button type="submit" name="ruaj" class="btn btn-sm btn-primary rounded-5 shadow-sm text-white" value="Shto">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </form>
            <br>
            <table class="table table-bordered">
              <thead class="bg-light">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Em&euml;rtimi</th>
                  <th scope="col">Qmimi</th>
                  <th scope="col">Perqindja</th>
                  <th scope="col">Shuma</th>
                  <th scope="col">Mbetja</th>
                  <th scope="col">Linku i youtubes</th>
                  <th scope="col">K&euml;ng&euml;tari</th>
                  <th scope="col">Totali</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $rend = 0;
                $i = mysqli_real_escape_string($conn, $_GET['fatura']);
                $stmt = $conn->prepare("SELECT * FROM shitje WHERE fatura=?");
                $stmt->bind_param("s", $i);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($r = mysqli_fetch_array($result)) {
                  $rend++;
                ?>
                  <tr>
                    <th scope="row">
                      <?php echo $rend; ?>
                    </th>
                    <td>
                      <?php echo $r['emertimi']; ?>
                    </td>
                    <td>
                      <?php echo $r['qmimi']; ?>
                    </td>
                    <td>
                      <?php echo $r['perqindja']; ?>
                    </td>
                    <td>
                      <?php echo $r['klientit']; ?>
                    </td>
                    <td>
                      <?php echo $r['mbetja']; ?>
                    </td>
                    <td>
                      <?php if (!empty($r['linku_kenges'])) { ?>
                        <a href="<?php echo $r['linku_kenges']; ?>" target="_blank" class="btn btn-danger text-white rounded-5 shadow-sm" style="text-transform:none;">
                          <i class="fab fa-youtube pe-2"></i>
                          Watch on YouTube
                        </a>
                      <?php } else { ?>
                        Nuk disponon keng&euml;
                      <?php } ?>
                    </td>


                    <td>
                      <?php echo !empty($r['kengetari']) ? $r['kengetari'] : 'Nuk disponon keng&euml'; ?>
                    </td>

                    <td>
                      <?php echo $r['totali']; ?>
                    </td>
                    <td>
                      <a class="btn btn-danger btn-sm text-white  rounded-5 " href="delete.php?fshij=<?php echo $r['id']; ?>&fatura=<?php echo $i; ?>"><i class="fi fi-rr-trash"></i></a>
                      <a class="btn btn-sm btn-primary text-white  rounded-5 " target="_blank" style="text-transform:none;" data-bs-toggle="modal" data-bs-target="#editrow<?php echo $r['id']; ?>"><i class="fi fi-rr-edit"></i></a>
                    </td>
                  </tr>
                  <div class="modal fade" id="editrow<?php echo $r['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">
                            <?php echo $r['emertimi']; ?>
                          </h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="editid" value="<?php echo $r['id']; ?>">
                            <div class="form-group row">
                              <div class="col">
                                <label for="emri">Em&euml;rtimi: </label>
                                <input type="text" name="emertimi" class="form-control shadow-sm rounded-5" style="border:1px solid lightgrey" value="<?php echo $r['emertimi']; ?>">
                              </div>
                              <div class="col">
                                <label for="nr">Qmimi</label>
                                <input type="text" name="qmimi" class="form-control shadow-sm rounded-5" style="border:1px solid lightgrey" value="<?php echo $r['qmimi']; ?>">
                              </div>
                            </div>



                            <div class="form-group row">
                              <div class="col">
                                <label for="perqindja">P&euml;rqindja: </label>
                                <input type="text" name="perqindja" class="form-control shadow-sm rounded-5" style="border:1px solid lightgrey" value="<?php echo $r['perqindja']; ?>">
                              </div>

                            </div>




                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary rounded-5 shadow-sm text-white" data-bs-dismiss="modal" style="text-transform:none;">Mbylle</button>
                              <button type="submit" class="btn btn-primary btn-sm rounded-5 shadow-sm text-white" name="update" value="Ruaj" style="text-transform:none;"> <i class="fi fi-rr-paper-plane"></i> P&euml;rditso</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>



                <?php } ?>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <?php
                  $stmt2 = $conn->prepare("SELECT SUM( totali ) as  sum  FROM  shitje  WHERE fatura=?");
                  $stmt2->bind_param("s", $i);
                  $stmt2->execute();
                  $result2 = $stmt2->get_result();
                  $qq4 = mysqli_fetch_array($result2);
                  ?>
                  <td><b>Totali:</b></td>
                  <td>
                    <?php echo $qq4['sum']; ?>€
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <br>
          <div>
            <a href="faturat.php" class="btn btn-sm btn-light rounded-5 float-right" target="_blank" style="border:1px solid lightgrey;text-transform:none;">
              <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
              <span style="display:inline-block;vertical-align:middle;">Dergo</span>
            </a>
            <a href="#" class="btn text-white shadow-0 btn-sm btn-danger rounded-5 border-1" style="background-color:#ce2029 ;text-transform:none;">
              <i class="fi fi-rr-cross-circle" style="display:inline-block;vertical-align:middle;"></i>
              <span style="display:inline-block;vertical-align:middle;">Anulo</span>
            </a>
          </div>

        </div>

      </div>
    </div>
  </div>
  <?php include 'partials/footer.php'; ?>
  <script>
    $(document).ready(function() {
      $('#youtube-link').on('input', function() {
        var link = $(this).val();
        var titleInput = $('#title_of_music');

        // Check if the first input field is empty
        if (link === '') {
          titleInput.val(''); // Clear the second input field
          return;
        }

        // Extract the video ID from the YouTube link
        var videoId = extractVideoId(link);

        // Make an API call to retrieve video information
        $.get('https://www.googleapis.com/youtube/v3/videos', {
          part: 'snippet',
          id: videoId,
          key: 'AIzaSyCjlRRPMTbGcM_QE081YCy4zHKI9sUaZTg'
        }, function(data) {
          var videoInfo = data.items[0].snippet;
          var videoTitle = videoInfo.title;

          titleInput.val(videoTitle); // Set the value of the second input field
        });
      });
    });

    // Function to extract the video ID from a YouTube link
    function extractVideoId(url) {
      var match = url.match(/(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:(?:&|\/|#|\?)(.+))?/);
      return match && match[1];
    }
  </script>