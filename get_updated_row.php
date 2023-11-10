<?php
include 'conn-d.php';

if (isset($_GET['faturaID'])) {
    $faturaID = $_GET['faturaID'];

    $query = "SELECT f.*, k.id AS klient_id, k.emri AS klient_emri, s.totali AS shitje_totali, p.shuma AS pagesa_shuma
              FROM fatura AS f 
              LEFT JOIN klientet AS k ON f.emri = k.id 
              LEFT JOIN (SELECT fatura, SUM(totali) AS totali FROM shitje GROUP BY fatura) AS s ON f.fatura = s.fatura
              LEFT JOIN pagesat AS p ON f.fatura = p.fatura
              WHERE f.fatura = $faturaID";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $updatedRowHTML = '
    <div class="row" data-fatura-id="' . $row['id'] . '">
        <td>' . $row['klient_emri'] . '</td>
        <td>' . $row['data'] . '</td>
        td>' . $row['fatura'] . '</td>
        <td>' . $row['shitje_totali'] . '</td>
        <td>' . $row['pagesa_shuma'] . '</td>
        td>' . $row['shitje_totali'] - $row['pagesa_shuma'] . '</td>
        
    </div>
';


        echo $updatedRowHTML;
    } else {
        echo "Row not found";
    }
} else {
    echo "Invalid fatura ID";
}

$conn->close();
