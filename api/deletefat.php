<?php
error_reporting(E_ALL);
include '../conn-d.php';

if (isset($_POST['id'])) {
  $fshijid = $_POST['id'];

  if ($conn->query("DELETE FROM fatura WHERE fatura='$fshijid'")) {
    $kqyr = $conn->query("SELECT * FROM shitje WHERE fatura='$fshijid'");
    $saka = mysqli_num_rows($kqyr);
    if ($saka == 0) {
    } else {
      $conn->query("DELETE FROM shitje WHERE fatura='$fshijid'");
    }

    echo "success"; // Sending success response
  } else {
    echo "error"; // Sending error response
  }
}
