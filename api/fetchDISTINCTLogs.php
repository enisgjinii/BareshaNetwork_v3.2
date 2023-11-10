<?php
session_start(); // Initialize the session

include '../conn-d.php';
$emri = $_SESSION["emri"];
$merri = $conn->query("SELECT * FROM logs WHERE stafi='$emri' ORDER BY koha DESC");
$data = array();
while ($k = mysqli_fetch_array($merri)) {
    $data[] = array(
        $k['stafi'],
        $k['ndryshimi'],
        $k['koha']
    );
}

echo json_encode(array('data' => $data));
?>
