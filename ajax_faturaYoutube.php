<?php
include 'conn-d.php';
$columns = array('emri', 'emriart', 'fatura', 'data', 'shuma', 'shpag', 'obligim');

// Base query
$query = "SELECT f.id, f.emri,f.emrifull,f.fatura, f.data, 
                 COALESCE(SUM(s.totali), 0) AS totali_sum, 
                 COALESCE(SUM(p.shuma), 0) AS shuma_sum
          FROM fatura AS f
          LEFT JOIN shitje AS s ON f.fatura = s.fatura
          LEFT JOIN pagesat AS p ON f.fatura = p.fatura
          GROUP BY f.id
          HAVING totali_sum > shuma_sum";

// Apply search filter
if (!empty($_POST["search"]["value"])) {
    $search = $_POST["search"]["value"];
    $query .= " AND (emri LIKE '%$search%' OR emrifull LIKE '%$search%' OR fatura LIKE '%$search%')";
}

// Apply sorting
if (isset($_POST["order"])) {
    $orderColumn = $columns[$_POST['order']['0']['column']];
    $orderDir = $_POST['order']['0']['dir'];
    $query .= " ORDER BY $orderColumn $orderDir";
} else {
    $query .= " ORDER BY id DESC";
}

// Pagination
$start = $_POST['start'];
$length = $_POST['length'];
$query .= " LIMIT $start, $length";

$result = mysqli_query($conn, $query);

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $dda = $row['data'];
    $date = date_create($dda);
    $dats = date_format($date, 'Y-m-d');

    $sid = $row['fatura'];
    $q4 = mysqli_query($conn, "SELECT SUM(totali) as sum FROM shitje WHERE fatura='$sid'");
    $qq4 = mysqli_fetch_assoc($q4);

    $merrpagesen = mysqli_query($conn, "SELECT SUM(shuma) as sum FROM pagesat WHERE fatura='$sid'");
    $merrep = mysqli_fetch_assoc($merrpagesen);

    $id_of_client = $row['emri'];
    $query_for_client = "SELECT * FROM klientet WHERE id='$id_of_client'";
    $row_for_client = mysqli_fetch_assoc(mysqli_query($conn, $query_for_client));

    $query_to_kanal = "SELECT * FROM yinc WHERE kanali=?";
    $stmt_kanal = $conn->prepare($query_to_kanal);
    $stmt_kanal->bind_param('s', $id_of_client);
    $stmt_kanal->execute();
    $result_kanal = $stmt_kanal->get_result();
    $row_to_kanal = $result_kanal->fetch_array();

    $borgjeTotal = 0;
    $stmt1 = $conn->prepare("SELECT * FROM yinc WHERE kanali=?");
    $stmt1->bind_param('s', $id_of_client);
    $stmt1->execute();
    $nxerrjaEEmritKanalitResult = $stmt1->get_result();

    while ($rowe = $nxerrjaEEmritKanalitResult->fetch_array()) {
        $amount = $rowe['shuma'] - $rowe['pagoi'];
        if ($amount > 0) {
            $borgjeTotal += $amount;
        }
    }

    $obli = $qq4['sum'] - $merrep['sum'];

    $dotColor = $borgjeTotal > 0 ? 'red' : 'green';
    $dot = '<span class="dot dot-' . $dotColor . '" data-toggle="tooltip" title="Borgje: ' . $borgjeTotal . '"></span>';

    $pagesaaa = $dot . '<b>' . $row["emrifull"] . '</b>';
    $sub_array = array(
        $pagesaaa,
        $row_for_client['emriart'],
        $row["fatura"] . "<br><br><button class='btn btn-primary open-modal text-white rounded-5 shadow-sm'><i class='fi fi-rr-badge-dollar fa-lg'></i></button>",
        $dats,
        $qq4["sum"],
        $merrep['sum'],
        $obli,
        '<a class="btn btn-primary btn-sm rounded-5 shadow-sm text-white" href="shitje.php?fatura=' . $sid . '" target="_blank"><i class="fi fi-rr-edit"></i></a>
            <a class="btn btn-success btn-sm rounded-5 shadow-sm text-white" target="_blank" href="fatura.php?invoice=' . $sid . '"><i class="fi fi-rr-print"></i></a> 
            <a class="btn btn-danger btn-sm rounded-5 shadow-sm text-white delete" name="delete"  id="' . $sid . '"><i class="fi fi-rr-trash"></i> </a>'
    );
    $data[] = $sub_array;
}

$total_records_query = "SELECT COUNT(*) AS total_count FROM fatura";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records_row = mysqli_fetch_assoc($total_records_result);
$total_records = $total_records_row['total_count'];
$total_pages = ceil($total_records / $length);

$output = array(
    "draw" => intval($_POST["draw"]),
    "recordsTotal" => $total_records,
    "recordsFiltered" => $total_records,
    "data" => $data
);
echo json_encode($output);
