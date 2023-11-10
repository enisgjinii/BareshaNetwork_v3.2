<?php
// Include the database connection
include 'conn-d.php';

// Fetch data from the database
$query = "SELECT ngarkimi.*, klientet.emri AS klienti_emri, users.name AS postuar_nga
    FROM ngarkimi
    LEFT JOIN klientet ON ngarkimi.klienti=klientet.id
    LEFT JOIN users ON ngarkimi.nga=users.id
    ORDER BY id DESC";
$result = $conn->query($query);

// Prepare an array to store the table data
$data = array();

// Loop through the result set and fetch the data
while ($row = mysqli_fetch_assoc($result)) {
    $deleteButton = '<a class="btn btn-danger text-white shadow-sm rounded-5" href="?del=' . $row['id'] . '" onclick="return confirm(\'A jeni i sigurt q&euml; d&euml;shironi ta fshini?\');"><i class="fi fi-rr-trash"></i></a>';

    $linkuColumn = '<a href="' . $row['linku'] . '" target="_blank">Hap Linkun</a>';
    $linkuplatColumn = '<a href="' . $row['linkuplat'] . '" target="_blank">Hap Linkun</a>';

    $data[] = array(
        'kengetari' => $row['kengetari'] . '<br><br>' . $deleteButton,
        'emri' => $row['emri'],
        'teksti' => $row['teksti'],
        'muzika' => $row['muzika'],
        'orkestra' => $row['orkestra'],
        'co' => $row['co'],
        'facebook' => $row['facebook'],
        'instagram' => $row['instagram'],
        'veper' => $row['veper'],
        'klienti_emri' => $row['klienti_emri'],
        'platformat' => $row['platformat'],
        'linku' => $linkuColumn,
        'linkuplat' => $linkuplatColumn,
        'data' => $row['data'],
        'gjuha' => $row['gjuha'],
        'infosh' => $row['infosh'],
        'postuar_nga' => $row['postuar_nga']
    );
}

// Return the data as JSON
echo json_encode(array('data' => $data));
?>