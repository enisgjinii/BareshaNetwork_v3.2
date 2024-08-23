<?php
include 'partials/header.php';
function handleAction($conn, $user_info, $action, $channel_id, $note = '')
{
  $user_name = $user_info['givenName'] . ' ' . $user_info['familyName'];
  $date = date('Y-m-d H:i:s');
  $log_description = "$user_name ka $action Whitelist kanalin me id $channel_id me datë: $date";
  $stmt = $conn->prepare("INSERT INTO logs (stafi, ndryshimi, koha) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $user_name, $log_description, $date);
  $result = $stmt->execute() ? ['success', 'Sukses', 'Veprimi u krye me sukses.'] : ['error', 'Gabim', 'Gabimi: ' . $conn->error];
  $stmt->close();
  $api_url = $action == 'shtuar në'
    ? "https://bareshamusic.sourceaudio.com/api/contentid/whitelistChannel?token=6636-66f549fbe813b2087a8748f2b8243dbc&channel[0][channel_id]=$channel_id&channel[0][note]=$note"
    : "https://bareshamusic.sourceaudio.com/api/contentid/whitelistRemove?token=6636-66f549fbe813b2087a8748f2b8243dbc&channel[channel_id]=$channel_id";
  $api_response = json_decode(file_get_contents($api_url), true);
  $api_result = isset($api_response['error']) && $api_response['error']
    ? ['error', 'Gabim', 'Gabimi: ' . $api_response['error']]
    : ['success', 'Sukses', 'Operacioni është kryer me sukses.'];
  echo "<script>Swal.fire({icon:'{$result[0]}',title:'{$result[1]}',text:'{$result[2]}'});</script>";
  echo "<script>Swal.fire({icon:'{$api_result[0]}',title:'{$api_result[1]}',text:'{$api_result[2]}'});</script>";
}
if (isset($_POST['channel_id'])) handleAction($conn, $user_info, 'shtuar në', $_POST['channel_id'], $_POST['note']);
if (isset($_GET['remove'])) handleAction($conn, $user_info, 'fshirë nga', $_GET['remove']);
$whitelist_data = json_decode(file_get_contents('https://bareshamusic.sourceaudio.com/api/contentid/whitelist?token=6636-66f549fbe813b2087a8748f2b8243dbc'), true);
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <nav class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Content ID</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Whitelist</a>
          </li>
        </ol>
      </nav>
      <div class="row mb-2">
        <div>
          <button type="button" class="input-custom-css px-3 py-2" data-bs-toggle="modal" data-bs-target="#shtochannel">
            <i class="fi fi-rr-add"></i> &nbsp; Shto kanal
          </button>
        </div>
      </div>
      <div class="card shadow-sm rounded-5">
        <div class="card-body">
          <div class="table-responsive">
            <table id="example" class="table w-100 table-bordered">
              <thead class="bg-light">
                <tr>
                  <th class="text-dark">Kanali</th>
                  <th class="text-dark">Shënime</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($whitelist_data['whitelist'] as $channel): ?>
                  <tr>
                    <td><a class="input-custom-css px-3 py-2" style="text-decoration: none;" href="https://www.youtube.com/channel/<?php echo $channel['channel_id']; ?>" target="_blank"><?php echo $channel['channel_name']; ?></a></td>
                    <td><?php echo $channel['data']['note'] ?></td>
                    <td><a class="input-custom-css px-3 py-2" style="text-decoration: none;" href="whitelist.php?remove=<?php echo $channel['channel_id']; ?>"><i class="fi fi-rr-trash"></i></a></td>
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
<div class="modal fade" id="shtochannel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Shto një kanal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="whitelist.php">
          <div class="form-group">
            <label>Channel ID:</label>
            <input type="text" name="channel_id" placeholder="Channel ID" class="form-control rounded-5 border border-2">
          </div>
          <div class="form-group">
            <label>Përshkrim:</label>
            <input type="text" name="note" placeholder="Përshkrimi" class="form-control rounded-5 border border-2">
          </div>
          <div class="modal-footer">
            <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
            <button type="submit" class="input-custom-css px-3 py-2">Shto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
  $('#example').DataTable({
    searching: true,
    dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>><'row'<'col-md-12'tr>><'row'<'col-md-6'><'col-md-6'p>>",
    buttons: [{
        extend: "pdfHtml5",
        text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
        titleAttr: "Eksporto tabelen ne formatin PDF",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
      },
      {
        extend: "copyHtml5",
        text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
        titleAttr: "Kopjo tabelen ne formatin Clipboard",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
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
            page: "all"
          }
        }
      },
      {
        extend: "print",
        text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
        titleAttr: "Printo tabelën",
        className: "btn btn-light btn-sm bg-light border me-2 rounded-5"
      }
    ],
    initComplete: function() {
      var btns = $(".dt-buttons");
      btns.addClass("").removeClass("dt-buttons btn-group");
      var lengthSelect = $("div.dataTables_length select");
      lengthSelect.addClass("form-select").css({
        width: "auto",
        margin: "0 8px",
        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
        lineHeight: "1.5",
        border: "1px solid #ced4da",
        borderRadius: "0.25rem"
      });
    },
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
    },
    stripeClasses: ['stripe-color']
  });
</script>