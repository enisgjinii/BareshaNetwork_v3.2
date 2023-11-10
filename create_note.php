<?php
  // Lidhja me databaze
  $conn = new mysqli("localhost", "root", "", "bareshao_f");

  // Kontrollo nese ekziston gabim ne lidhjen me databazen
  if ($conn->connect_error) {
    die("Lidhja me databazen deshtoi: " . $conn->connect_error);
  }

  // Kontrollo nese formulari eshte derguar me metoden POST
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // marre te dhenat e shenimit nga formulari
    $note = $_POST["note"];
    // krijimi i nje timestamp per te marre daten dhe kohen aktuale
    $date = date("Y-m-d H:i:s");

    // shtimi i shenimit ne databaze
    $sql = "INSERT INTO shenime (shenimi, data) VALUES ('$note', '$date')";
    if ($conn->query($sql) === TRUE) {
      echo "Shenimi u krijua me sukses";
    } else {
      echo "Gabim gjate krijimit te shenimit: " . $conn->error;
    }
  }

  // mbyllja e lidhjes me databaze
  $conn->close();
?>
