<?php
//fetch.php
include '../conn-d.php';
$columns = array('emri', 'emriart', 'fatura', 'data', 'shuma', 'shpag', 'obligim');
$query = "SELECT * FROM fatura ";
if (isset($_POST["search"]["value"])) {
    $query .= 'WHERE emrifull LIKE "%' . $_POST["search"]["value"] . '%" OR data LIKE "%' . $_POST["search"]["value"] . '%" ';
}

if (isset($_POST["order"])) {
    $query .= 'ORDER BY ' . $columns[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
} else {
    $query .= 'ORDER BY id DESC ';
}
$query1 = '';

if ($_POST["length"] != -1) {
    $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$number_filter_row = mysqli_num_rows(mysqli_query($conn, $query));

$result = mysqli_query($conn, $query . $query1);

$data = array();

while ($row = mysqli_fetch_array($result)) {
    $dda = $row['data'];
    $date = date_create($dda);
    $dats = date_format($date, 'Y-m-d');
    $sid = $row['fatura'];
    $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$sid'");
    $qq4 = mysqli_fetch_array($q4);

    $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$sid'");
    $merrep = mysqli_fetch_array($merrpagesen);

    $klientiid = $row['emri'];
    if ($klientiid !== null) {
        $queryy = "SELECT * FROM klientet WHERE id=?";
        $stmt = $conn->prepare($queryy);
        $stmt->bind_param("i", $klientiid);
        $stmt->execute();
        $result_klientet = $stmt->get_result();
        $k4 = $result_klientet->fetch_assoc();
        $stmt->close();
    } else {
        $k4 = array(); // Set $k4 as an empty array if $klientiid is null
    }

    $obli = $qq4['sum'] - $merrep['sum'];
    if ($qq4['sum'] > $merrep['sum']) {
        $pagesaaa = '<b style="color:red;">' . $row["emrifull"] . '</b>';
    } else {
        $pagesaaa = '<b style="color:green;">' . $row["emrifull"] . '</b>';
    }
    $sub_array = array();
    $sub_array[] = $pagesaaa;
    $sub_array[] = $k4["emriart"] ?? ''; // Use ?? to provide a default value if $k4["emriart"] is null
    $sub_array[] = $row["fatura"];
    $sub_array[] = $dats;
    $sub_array[] = $qq4["sum"];
    $sub_array[] = $merrep['sum'];
    $sub_array[] = $obli;
    $sub_array[] = '<a class="btn btn-primary btn-sm" href="shitje.php?fatura=' . $sid . '" target="_blank"><i class="ti-pencil"></i></a>
                                                 <a class="btn btn-success btn-sm" target="_blank" href="fatura.php?invoice=' . $sid . '"><i class="ti-printer"></i></a> 
                                                 <a type="button" name="delete" class="btn btn-danger btn-xs delete" id="' . $sid . '"><i class="ti-trash"></i> </a> ';

    $data[] = $sub_array;
}

function get_all_data($conn)
{
    $query = "SELECT * FROM fatura";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result);
}

$output = array(
    "draw" => intval($_POST["draw"]),
    "recordsTotal" => get_all_data($conn),
    "recordsFiltered" => $number_filter_row,
    "data" => $data
);

echo json_encode($output);
?>
