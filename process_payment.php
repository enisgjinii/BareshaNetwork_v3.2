<?php 


// process_payment.php
include 'conn-d.php';

// Handle payment submission
// if (isset($_POST['paguaj'])) {
//     $shpages = $_POST['pagoi'];
//     $lloji = $_POST['lloji'];
//     $idof = $_POST['idp'];

//     if ($conn->query("UPDATE yinc SET pagoi = pagoi + '$shpages', lloji = '$lloji' WHERE id = '$idof'")) {
//         echo "success";
//     } else {
//         echo "error";
//     }
// }
 
if (isset($_POST['paguaj'])) {
    $shpages = $_POST['pagoi'];
    $lloji = $_POST['lloji'];
    $idof = $_POST['idp'];
    if ($conn->query("UPDATE yinc SET pagoi=pagoi + '$shpages', lloji='$lloji' WHERE id='$idof'")) {
      
    } else {
      echo '<script>alert(' . $conn->error . ')</script>';
    }
  }
