<?php 
include 'conn-d.php';

if (isset($_POST['ruaj'])) {
    $stafi = mysqli_real_escape_string($conn, $_POST['stafi']);
    $gstai = $conn->query("SELECT * FROM klientet WHERE id='$stafi'");
    $gstai2 = mysqli_fetch_array($gstai);
  
  
  
    $shuma = $_POST['shuma'];
    $data = mysqli_real_escape_string($conn, $_POST['data']);
  
    $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);
    //Get the result.
  
    if ($conn->query("INSERT INTO yinc (kanali, shuma, pershkrimi, data) VALUES ('$stafi', '$shuma','$pershkrimi', '$data')")) {
      header("Location: yinc.php");
    } else {
      echo ("Gabim: " . $conn->error);
    }
  }
