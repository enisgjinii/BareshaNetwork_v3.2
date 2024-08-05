<?php
include 'conn-d.php';

$query = "SELECT n.*, k.emri AS klienti_emri, u.name AS postuar_nga
    FROM ngarkimi n
    LEFT JOIN klientet k ON n.klienti = k.id
    LEFT JOIN users u ON n.nga = u.id
    ORDER BY n.id DESC";
$result = $conn->query($query);

$data = array_map(function ($row) {
    $linkuColumn = '<a class="input-custom-css px-3 py-2" href="' . $row['linku'] . '" target="_blank">Hap Linkun</a><br><br>';
    $linkuplatColumn = '<a class="input-custom-css px-3 py-2" href="' . $row['linkuplat'] . '" target="_blank">Hap Linkun</a>';

    return [
        'id' => $row['id'],
        'kengetari' => $row['kengetari'],
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
    ];
}, mysqli_fetch_all($result, MYSQLI_ASSOC));

echo json_encode(['data' => $data]);
