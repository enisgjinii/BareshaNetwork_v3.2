<?php

include 'partials/header.php';
if (isset($_GET['claim'])) {
  $cid = $_GET['claim'];
  $cdata = date("Y-m-d H:i:s");
  $cname = $_SESSION['emri'];
  $cnd = $cname . " ka ber Release Claim k&euml;ng&euml;n me Claim ID " . $cid;
  $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
  if ($conn->query($query)) {
  } else {
    echo '<script>alert("' . $conn->error . '")</script>';
  }
  $cjson = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/releaseclaim?token=6636-66f549fbe813b2087a8748f2b8243dbc&release[0][type]=claim&release[0][id]=' . $cid);

  $cdata = json_decode($cjson, true);
  if (isset($cdata['error']) && $cdata['error'] == true) {
    echo '<script>alert("' . $cdata['error'] . '");</script>';
  } else {
    echo '<script>Sukses.</script>';
  }
}
?>
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
                Release Claim
              </a>
            </li>
        </nav>
        <div class="card rounded-5">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table id="example" class="table w-100 table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th>Track ID</th>
                        <th>Video</th>
                        <th>Kanali</th>
                        <th>Claim ID</th>
                        <th></th>



                      </tr>
                    </thead>

                    <tbody>
                      <?php

                      $json = file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/claims?token=6636-66f549fbe813b2087a8748f2b8243dbc&show=5000');

                      $data = json_decode($json, true);
                      foreach ($data['claim'] as $sku) {
                      ?>
                        <tr>
                          <td><a href="track.php?id=<?php echo $sku['track_id']; ?>" target="_blank"><?php
                                                                                                      echo $sku['track_id'];

                                                                                                      ?></a></td>
                          <td><a href="https://www.youtube.com/watch?v=<?php echo $sku['video_id']; ?>" target="_blank"><?php echo $sku['video_title']; ?></a></td>

                          <td><a href="https://www.youtube.com/channel/<?php echo $sku['channel_id']; ?>" target="_blank"><?php echo $sku['video_author']; ?></a></td>
                          <td><?php echo $sku['claim_id']; ?></td>
                          <td><?php
                              if ($sku['released'] == '1') {
                                echo $sku['released_by'];
                              } else {
                                echo "<a class='btn btn-primary' href='claim.php?claim=" . $sku['claim_id'] . "'>Release</a>";
                              }
                              ?></td>
                        </tr>


                      <?php } ?>

                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th>Track ID</th>
                        <th>Video</th>
                        <th>Kanali</th>
                        <th>Claim ID</th>
                        <th></th>

                      </tr>
                    </tfoot>
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
    responsive: true,
    search: {
      return: true,
    },
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
    }],
    initComplete: function() {
      var btns = $('.dt-buttons');
      btns.addClass('');
      btns.removeClass('dt-buttons btn-group');

    },
    fixedHeader: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
    },
    stripeClasses: ['stripe-color'],
  })
</script>