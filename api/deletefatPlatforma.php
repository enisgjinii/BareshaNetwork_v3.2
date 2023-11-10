<?php
error_reporting(E_ALL);
include '../conn-d.php';
if(isset($_POST['id'])){
  $fshijid = $_POST['id'];

  if($conn->query("DELETE FROM faturaplatformes WHERE fatura='$fshijid'")){
    $kqyr = $conn->query("SELECT * FROM shitjeplatforma WHERE fatura='$fshijid'");
    $saka = mysqli_num_rows($kqyr);
    if($saka == 0){
      
    }else{
      $conn->query("DELETE FROM shitjeplatforma WHERE fatura='$fshijid'");
    }
 
  echo "Fatura me num&euml;r: ".$fshijid." &euml;sht&euml; fshir&euml; me sukses";
}else{
  echo "Gabim!!";
}
}
