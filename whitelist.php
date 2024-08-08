<?php
include 'partials/header.php';

if (isset($_POST['channel_id'])) {
  $cid = $_POST['channel_id'];
  $note = $_POST['note'];

  $user_informations = $user_info['givenName'] . ' ' . $user_info['familyName'];
  $date_information = date('Y-m-d H:i:s');
  $log_description = $user_informations . " ka shtuar n&euml; Whitelist kanalin me id " . $cid . " me dat&euml;: " . $date_information;

  $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";

  if ($conn->query($query)) {
    // Success
    echo '<script>
            Swal.fire({
                icon: "success",
                title: "Sukses",
                text: "Kanali &euml;sht&euml; shtuar me sukses n&euml; whitelist."
            });
        </script>';
  } else {
    echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gabim",
                text: "Gabimi: ' . $conn->error . '"
            });
        </script>';
  }

  $cjson = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/whitelistChannel?token=6636-66f549fbe813b2087a8748f2b8243dbc&channel[0][channel_id]=' . $cid . '&channel[0][note]=' . $note);
  $cdata = json_decode($cjson, true);

  if ($cdata['error'] == true) {
    echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gabim",
                text: "Gabimi: ' . $cdata['error'] . '"
            });
        </script>';
  } else {
    echo '<script>
            Swal.fire({
                icon: "success",
                title: "Sukses",
                text: "Operacioni &euml;sht&euml; kryer me sukses."
            });
        </script>';
  }
}

if (isset($_GET['remove'])) {
  $removeid = $_GET['remove'];

  $user_informations = $user_info['givenName'] . ' ' . $user_info['familyName'];
  $date_information = date('Y-m-d H:i:s');
  $log_description = $user_informations . " ka fshir&euml; nga Whitelist kanalin me id " . $removeid . " me dat&euml;: " . $date_information;
  $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";

  if ($conn->query($query)) {
    // Success
    echo '<script>
            Swal.fire({
                icon: "success",
                title: "Sukses",
                text: "Kanali &euml;sht&euml; larguar me sukses nga whitelist."
            });
        </script>';
  } else {
    echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gabim",
                text: "Gabimi: ' . $conn->error . '"
            });
        </script>';
  }

  $crem = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/whitelistRemove?token=6636-66f549fbe813b2087a8748f2b8243dbc&channel[channel_id]=' . $removeid);
}
?>




<!-- Modal -->
<div class="modal fade" id="shtochannel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Shto nj&euml; kanal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

      </div>
      <div class="modal-body">
        <form method="POST" action="whitelist.php">
          <div class="form-group">
            <label>Channel ID:</label>
            <input type="text" name="channel_id" placeholder="Channel ID" class="form-control rounded-5 border border-2">
          </div>
          <div class="form-group">
            <label>P&euml;rshkrim:</label>
            <input type="text" name="note" placeholder="P&euml;rshkrimi" class="form-control rounded-5 border border-2">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
        <button type="submit" class="input-custom-css px-3 py-2">Shto</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <div class="container">
        <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Content ID</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                Whitelist
              </a>
            </li>
        </nav>
        <div class="row mb-2">
          <div>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#shtochannel">
              <i class="fi fi-rr-add"></i> &nbsp; Shto kanal
            </button>
          </div>
        </div>
        <div class="card shadow-sm rounded-5">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th class="text-dark">Kanali</th>
                        <th class="text-dark">Shenime</th>
                        <th></th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php

                      $json = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/whitelist?token=6636-66f549fbe813b2087a8748f2b8243dbc');

                      $data = json_decode($json, true);
                      foreach ($data['whitelist'] as $sku) {
                      ?>
                        <tr>
                          <td><a class="input-custom-css px-3 py-2" style="text-decoration: none;" href="https://www.youtube.com/channel/<?php echo $sku['channel_id']; ?>" target="_blank"><?php echo $sku['channel_name']; ?></a></td>
                          <td><?php echo $sku['data']['note'] ?></td>
                          <td><a class="input-custom-css px-3 py-2" style="text-decoration: none;" href="whitelist.php?remove=<?php echo $sku['channel_id']; ?>"><i class="fi fi-rr-trash"></i></a></td>
                        </tr>
                      <?php } ?>
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
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $('#example').DataTable({

    searching: true,
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
    stripeClasses: ['stripe-color'],
  })
</script>