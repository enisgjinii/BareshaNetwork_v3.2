
<?php
include '../conn-d.php';
$merri = $conn->query("SELECT * FROM logs ORDER BY koha DESC");
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
