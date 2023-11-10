<?php

include 'partials/header.php';

$json = file_get_contents('http://www.floatrates.com/daily/usd.json');
$obj = json_decode($json);

if (empty($_GET['fatura'])) {
    die("Nuk u gjet fatura!");
} else {
    $fatura = $_GET['fatura'];
}

// Get the row from the database based on the provided invoice number
$result = $conn->query("SELECT * FROM faturaplatformes WHERE fatura='$fatura'");
$mfidi = mysqli_fetch_array($result);
$midc = $mfidi['emri'];

if (isset($_POST['ruaj'])) {
    $emertimi = mysqli_real_escape_string($conn, $_POST['emertimi']);
    $qmimi2 = $_POST['qmimi'] * $obj->eur->rate;

    if ($_POST['valuta'] == "euro") {
        $qmimi = $_POST['qmimi'];
    } else {
        $qmimi = $qmimi2;
    }

    // Get the percentage discount from the database
    $result = $conn->query("SELECT perqindjaendalurPlatforma FROM shitjeplatforma");
    $perqindjaAutomatike = mysqli_fetch_array($result);
    $perqindjaAutomatike2 = $perqindjaAutomatike['perqindjaendalurPlatforma'];

    // Get the discount percentage from the client table using a caching mechanism to improve performance
    $result = $conn->query("SELECT perqindja2 FROM klientet WHERE id='$midc'");
    $gstai2 = mysqli_fetch_array($result);
    $perqindja = $gstai2['perqindja2'];
    $perqindjaAutomatike2 = intval($perqindjaAutomatike2); // Convert to integer
    $perqindja = intval($perqindja); // Convert to integer

    $perqindjaTotale = $perqindjaAutomatike2 + $perqindja;

    $pdc = $perqindjaTotale / 100;

    if ($qmimi <= "0") {
        $mbetja = "0.00";
    } else {
        $mbetja = $pdc * $qmimi;
    }

    $totali = $qmimi - $mbetja;
    $datas = date("Y-m-d H:i:s");

    $perqindjaTotale = $perqindjaAutomatike2 + $perqindja;

    // Add error handling
    if (
        $conn->query("INSERT INTO shitjeplatforma (emertimi, qmimi, perqindja, klientit, mbetja,totali, fatura, data,perqindjaTotale) VALUES ('$emertimi', '$qmimi', '$perqindja', '$totali', '$mbetja', '$totali', '$fatura', '$datas',
    '$perqindjaTotale')")
    ) {
    } else {
        echo "Ndodhi nj&euml; gabim: " . $conn->error;
    }
}
if (isset($_POST['update'])) {
    $qmimi = $_POST['qmimi'];
    $gstai = $conn->query("SELECT * FROM klientet WHERE id='$midc'");
    $gstai2 = mysqli_fetch_array($gstai);
    $perqindja = $gstai2['perqindja2'];
    $pdc = $perqindja / 100;
    if ($qmimi <= "0") {
        $mbetja = "0.00";
    } else {
        $mbetja = $pdc * $qmimi;
    }
    $totali = $qmimi - $mbetja - 4;
    $updateid = $_POST['editid'];
    $eme = $_POST['emertimi'];
    $query = "UPDATE shitjeplatforma SET emertimi='$eme', qmimi='$qmimi', klientit='$totali', mbetja='$mbetja', totali='$totali' WHERE id='$updateid'";
    if (mysqli_query($conn, $query)) {
        // Success
    } else {
        echo "Ndodhi nj&euml; gabim: " . mysqli_error($conn);
    }
}
?>


<!-- Begin Page Content -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 rounded-5 shadow-sm mb-4 card">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM yinc WHERE kanali=?");
                    $stmt->bind_param('s', $midc);
                    $stmt->execute();
                    if ($result = $stmt->get_result()) {
                        while ($listaob = mysqli_fetch_array($result)) {
                            if ($listaob['shuma'] > $listaob['pagoi']) {
                                ?>
                                <div class="alert alert-danger" id="alert" role="alert">
                                    Klienti ka nj&euml; obligim me shum&euml;n: <b>
                                        <?php echo $listaob['shuma'] - $listaob['pagoi']; ?>&euro;
                                    </b>, <br><b>P&euml;rshkrimi:</b>
                                    <?php echo $listaob['pershkrimi']; ?>.
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>



                    <form method="POST" action="shitjePlatforma.php?fatura=<?php echo $fatura; ?>">
                        <div class="form-group row mb-3">
                            <div class="col-4">
                                <label for="emertimi">Emertimi</label>
                                <input type="text" name="emertimi" autocomplete="off"
                                    class="form-control shadow-sm rounded-5" placeholder="Em&euml;rtimi">
                            </div>
                            <div class="col-4">
                                <label for="emertimi">Qmimi</label>
                                <input type="text" name="qmimi" autocomplete="off"
                                    class="form-control shadow-sm rounded-5" placeholder="Qmimi">
                            </div>
                            <div class="col-4">
                                <label for="valuta">Zgjidhni
                                    valut&euml;
                                </label>
                                <select name="valuta" class="form-select shadow-sm rounded-5 p-2">
                                    <option value="dollar">$</option>
                                    <option value="euro" selected="">&euro;</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="submit" name="ruaj"
                                class="btn btn-sm btn-primary rounded-5 shadow-sm text-white" value="Shto">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </form>
                    <br>
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Em&euml;rtimi</th>
                                <th scope="col">Qmimi</th>
                                <th scope="col">Perqindja</th>
                                <th scope="col">Perqindja e ndalur ne platforma</th>
                                <th scope="col">Perqindja totale</th>
                                <th scope="col">Shuma</th>
                                <th scope="col">Mbetja</th>
                                <th scope="col">Totali</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rend = 0;
                            $i = mysqli_real_escape_string($conn, $_GET['fatura']);
                            $stmt = $conn->prepare("SELECT * FROM shitjeplatforma WHERE fatura=?");
                            $stmt->bind_param("s", $i);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($r = mysqli_fetch_array($result)) {
                                $rend++;
                                ?>
                                <tr>
                                    <th scope="row">
                                        <?php echo $rend; ?>
                                    </th>
                                    <td>
                                        <?php echo $r['emertimi']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['qmimi']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['perqindja']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['perqindjaendalurPlatforma'] ?>
                                    </td>
                                    <td>
                                        <?php echo $r['perqindjaTotale']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['klientit']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['mbetja']; ?>
                                    </td>
                                    <td>
                                        <?php echo $r['totali']; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-sm text-white  rounded-5 "
                                            href="delete.php?fshij=<?php echo $r['id']; ?>&faturaPlatforma=<?php echo $i; ?>"><i
                                                class="fi fi-rr-trash"></i></a>
                                        <a class="btn btn-sm btn-primary text-white  rounded-5 " target="_blank"
                                            style="text-transform:none;" data-bs-toggle="modal"
                                            data-bs-target="#editrow<?php echo $r['id']; ?>"><i
                                                class="fi fi-rr-edit"></i></a>
                                    </td>
                                </tr>
                                <div class="modal fade" id="editrow<?php echo $r['id']; ?>" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">
                                                    <?php echo $r['emertimi']; ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="" enctype="multipart/form-data">
                                                    <input type="hidden" name="editid" value="<?php echo $r['id']; ?>">
                                                    <div class="form-group row">
                                                        <div class="col">
                                                            <label for="emri">Em&euml;rtimi: </label>
                                                            <input type="text" name="emertimi"
                                                                class="form-control shadow-sm rounded-5"
                                                                value="<?php echo $r['emertimi']; ?>">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="nr">Qmimi</label>
                                                            <input type="text" name="qmimi"
                                                                class="form-control shadow-sm rounded-5"
                                                                value="<?php echo $r['qmimi']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Mbylle</button>
                                                        <button type="submit" class="btn btn-primary" name="update"
                                                            value="Ruaj"> <i class="fi fi-rr-paper-plane"></i> </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?php
                                $stmt2 = $conn->prepare("SELECT SUM( totali ) as  sum  FROM  shitjeplatforma  WHERE fatura=?");
                                $stmt2->bind_param("s", $i);
                                $stmt2->execute();
                                $result2 = $stmt2->get_result();
                                $qq4 = mysqli_fetch_array($result2);
                                ?>
                                <td><b>Totali:</b></td>
                                <td>
                                    <?php echo $qq4['sum']; ?>€
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <br>
                    <div>
                        <a href="listaEFaturaveTePlatformave.php" class="btn btn-sm btn-light rounded-5 float-right"
                            target="_blank" style="border:1px solid lightgrey;text-transform:none;">
                            <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                            <span style="display:inline-block;vertical-align:middle;">Dergo</span>
                        </a>
                        <a href="#" class="btn text-white shadow-0 btn-sm btn-danger rounded-5 border-1"
                            style="background-color:#ce2029 ;text-transform:none;">
                            <i class="fi fi-rr-cross-circle" style="display:inline-block;vertical-align:middle;"></i>
                            <span style="display:inline-block;vertical-align:middle;">Anulo</span>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <?php include 'partials/footer.php'; ?>