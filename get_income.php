<?php
include 'conn-d.php';

// Fetch data from the database and return it as JSON
$data = array();
$kueri = $conn->query("SELECT * FROM yinc ORDER BY id DESC");
while ($k = mysqli_fetch_array($kueri)) {
    $sid = $k['kanali'];
    $gstaf = $conn->query("SELECT * FROM klientet WHERE id='$sid'");
    $gstafi = mysqli_fetch_array($gstaf);

    // Your existing code to calculate $percent and other values

    // Add the row data to the $data array
    $data[] = array(
        $gstafi['emri'],
        $k['shuma'] . '&euro;',
        $k['pagoi'] . '&euro;',
        $k['shuma'] - $k['pagoi'] . '&euro;',
        $k['lloji'], // The Forma column
        $k['pershkrimi'], // The P&euml;rshkrimi column
        $k['data'], // The Data column
    );
}

// Return the data as JSON
echo json_encode(array('data' => $data));
?>
