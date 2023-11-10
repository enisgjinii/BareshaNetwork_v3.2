<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" /> -->
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet" />

    <!-- UIcons -->
    <link rel="stylesheet" href="assets/uicons-regular-rounded/css/uicons-regular-rounded.css">
</head>
<?php
include('fpdf/fpdf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonData = $_POST["data"];
    $data = json_decode($jsonData, true);

    // Remove the last two columns from each row
    foreach ($data as &$row) {
        array_splice($row, -2);
    }

    // Define the headers for the table
    $headers = array("Artisti", "Periudha raportuese", "Periudha kontabilitetit", "L&euml;shimi", "Emri i k&euml;ng&euml;s", "Shteti", "Totali", "Valuta");

    // Output the data as an HTML table
    // Output the sum
    // echo "The sum of Column6 is: {$sum}";

    $artistName = isset($data[0]["0"]) ? $data[0]["0"] : "";
    $words = explode(" ", $artistName);
    $firstLetter1 = isset($words[0][0]) ? $words[0][0] : "";
    $firstLetter2 = isset($words[1][0]) ? $words[1][0] : "";
    $randomNumber1 = rand(100, 999);
    $randomNumber2 = rand(100, 999);
    setlocale(LC_TIME, 'sq_AL.utf8');

    $date = new DateTime();
    $date_formatted = $date->format('Y');
    $invoiceNumber = "BN-{$randomNumber1}-{$randomNumber2}-{$firstLetter1}{$firstLetter2}-{$date_formatted}";

    // Create a new instance of the FPDF class
    $pdf = new FPDF();

    // Add a new page to the PDF document
    $pdf->AddPage();

    // Set the font and font size for the PDF document
    $pdf->SetFont('Arial', 'B', 16);

    // Set the cell width and height for the PDF document
    $cellWidth = 40;
    $cellHeight = 10;

    // Add the headers to the PDF document
    foreach ($headers as $header) {
        $pdf->Cell(40, 10, $header, 1);
    }

    // Add the data to the PDF document
    foreach ($data as $row) {
        $pdf->Ln();
        foreach ($row as $cell) {
            $pdf->Cell(40, 10, $cell, 1);
        }
    }

    // Save the PDF document to a local folder
    $pdf->Output('Faturat per platformat/' . $invoiceNumber  . " - " . $artistName . '.pdf', 'F');

    // Save the PDF document to the database
    $pdfData = $pdf->Output('', 'S');
    // Save $pdfData to the database
}
?>

<body>

    <div class="container p-5">
        <div class="float-start"><a href="faturat2.php" class='btn btn-light text-capitalize border border-1 shadow-2' id="backBtn" data-mdb-toggle="tooltip" title="Shko prapa"><i class="fas fa-arrow-left "></i> Back</a></div>
        <div class="float-end">
            <button data-mdb-toggle="tooltip" title="Printo fatur&euml;n" data-mdb-placement="top" class="btn btn-light text-capitalize border border-1 shadow-2" data-mdb-ripple-color="dark" id="printBtn" onclick="printData()"><i class="fas fa-print text-primary "></i> Print</button>
            <a class="btn btn-light text-capitalize border border-1 shadow-2" id="exportPdf" data-mdb-ripple-color="dark"><i class="far fa-file-pdf text-danger"></i> Export</a>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-light text-capitalize border border-1 shadow-2" data-mdb-toggle="modal" data-mdb-target="#exampleModal">
                <i class="fa-solid fa-circle-info text-warning"></i> Info
            </button>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">...</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="card border shadow-3">
            <div class="card-body">
                <div class="container mb-5 mt-3 ">
                    <div class="row d-flex align-items-baseline my-2 mx-1">
                        <div class="col-xl-9">
                            <p style="color: #7e8d9f;font-size: 20px;width:max-content" class="border border-1 shadow-2 px-2 rounded">Fatura : # <?php echo $invoiceNumber; ?></p>
                        </div>
                    </div>
                    <div class="container ">
                        <div class="col-md-12 my-5">
                            <div class="text-center">
                                <img src="images/favicon.png" height="110" alt="" loading="lazy" />
                                <p class="pt-3 fw-bold">Baresha Network</p>
                            </div>

                        </div>
                        <div class="row gap-3 my-2 mx-1 justify-content-center">
                            <div class="col border rounded-3 p-3 shadow-2">
                                <ul class="list-unstyled">
                                    <?php
                                    include('conn-d.php');
                                    if (!empty($artistName)) {
                                        // Get more information about the client
                                        $queryy = "SELECT * FROM klientet WHERE emri LIKE '%$artistName%'";
                                        $result = mysqli_query($conn, $queryy);

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Do something with the client information here
                                                $id_klienti = $row['id'];
                                                echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Fatura p&euml;r : <span style='color:#5d9fc5 ;'>$id_klienti</span></li>";
                                                echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Fatura p&euml;r : <span style='color:#5d9fc5 ;'>{$row['emri']}</span></li>";
                                                echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Adresa : <span style='color:#5d9fc5 ;'>{$row['adresa']}</span></li>";
                                                echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Monetizuar : <span style='color:#5d9fc5 ;'>{$row['monetizuar']}</span></li>";
                                                if (!empty($row['fb'])) {
                                                    echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Facebook : <a style='color:#5d9fc5 ;' href='{$row['fb']}' target='_blank'>{$row['fb']}</a></li>";
                                                } else {
                                                    echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Facebook : Nuk ka</li>";
                                                }
                                                if (!empty($row['ig'])) {
                                                    echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Instagram : <a style='color:#5d9fc5 ;' href='{$row['ig']}' target='_blank'>{$row['ig']}</a></li>";
                                                } else {
                                                    echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Instagram : Nuk ka</li>";
                                                }
                                            }
                                        } else {
                                            // No client information found
                                            echo "<li class='text-muted my-2'><i class='fas fa-circle fa-2xs' style='color:#84B0CA ;'></i> Fatura p&euml;r : <span style='color:#5d9fc5 ;'>{$artistName}</span></li>";
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-xl-4 border rounded-3 p-3 shadow-2">
                                <p class="text-muted">Fatura</p>
                                <ul class="list-unstyled">
                                    <li class="text-muted my-2"><i class="fas fa-circle fa-2xs" style="color:#84B0CA ;"></i> Numri identifikues : <span class="fw-bold"> # <?php echo $invoiceNumber; ?></li>
                                    <?php
                                    setlocale(LC_TIME, 'sq_AL.utf8');

                                    $date = new DateTime();
                                    $date_formatted = $date->format('d-m-Y');
                                    ?>
                                    <?php
                                    // Set up connection to database | Local
                                    $host = 'localhost';
                                    $dbname = 'bareshao_f';
                                    $username = 'root';
                                    $password = '123456';

                                    // Set up connection to database | Remote
                                    // $host = '198.38.83.75';
                                    // $dbname = 'bareshao_f';
                                    // $username = '6D2?19slm';
                                    // $password = 'bareshao_f';




                                    try {
                                        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                    } catch (PDOException $e) {
                                        die("Error connecting to database: " . $e->getMessage());
                                    }

                                    if (!empty($date_formatted) && !empty($invoiceNumber)) {
                                        $invoiceNumberInt = strval($invoiceNumber);


                                        // Check if user has already been inserted on the same date
                                        $stmt = $pdo->prepare("SELECT * FROM fatura_specifike WHERE id_e_artistit = :id AND data_e_fatures = :formatted_date");
                                        $stmt->bindParam(':id', $id_klienti);
                                        $stmt->bindParam(':formatted_date', $formatted_date);
                                        $stmt->execute();

                                        // Prepare SQL statement to insert data
                                        if ($stmt->rowCount() == 0) {
                                            // User has not been inserted on the same date, so insert data
                                            $stmt = $pdo->prepare("INSERT INTO fatura_specifike (emri_i_artistit,id_e_artistit,numri_identifikues_fatures,data_e_fatures,fatura) VALUES (:artistName,:id,:invoiceNumber,:formatted_date,:fatura)");
                                            $stmt->bindParam(':formatted_date', $formatted_date);
                                            $stmt->bindParam(':invoiceNumber', $invoiceNumberInt);
                                            $stmt->bindParam(':artistName', $artistName);
                                            $stmt->bindParam(':id', $id_klienti);
                                            $stmt->bindParam(':fatura', $pdfData);
                                            $stmt->execute();
                                        }

                                        // Convert date format to YYYY-MM-DD
                                        $formatted_date = DateTime::createFromFormat('d-m-Y', $date_formatted)->format('Y-m-d');

                                        // Bind parameter values to statement
                                        $stmt->bindParam(':formatted_date', $formatted_date);
                                        $stmt->bindParam(':invoiceNumber', $invoiceNumberInt);
                                        $stmt->bindParam(':artistName', $artistName);
                                        $stmt->bindParam(':id', $id_klienti);


                                        // Execute statement
                                        $stmt->execute();
                                    }

                                    // Close database connection
                                    // $pdo = null;
                                    ?>

                                    <li class="text-muted my-2"><i class="fas fa-circle fa-2xs" style="color:#84B0CA ;"></i> Data e krijimit: <span class="fw-bold"><?php echo $date_formatted; ?></span></li>
                                </ul>
                            </div>
                        </div>

                        <button style="text-transform:none;" class="toggle-button-platforms btn btn-primary shadow-sm rounded-3 my-3 " onclick="togglePlatformTable()"><i class="fi fi-rr-eye-crossed"></i> Fshih raportin e platformave</button>


                        <div class='table-responsive'>
                            <div class="platformTable">
                                <table class=' table table-bordered w-full'>
                                    <thead>
                                        <tr>
                                            <th>Icon of Partner</th>
                                            <th>Partner</th>
                                            <th>RevenueUSD</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        foreach ($data as $row) {
                                            foreach ($row as $key => $value) {
                                                if ($key == 1) {
                                                    $columnTwo = $value;
                                                }
                                                if ($key == 2) {
                                                    $columnThree = $value;
                                                }
                                            }
                                        }
                                        $artist = $artistName;
                                        $pyetsori = "SELECT Partner, SUM(`RevenueUSD`) as `sum`, AccountingPeriod FROM platformat WHERE Artist='$artist' AND AccountingPeriod='$columnThree' GROUP BY Partner";
                                        $rezultati = mysqli_query($conn, $pyetsori);
                                        $icons = array();
                                        $no_icons = array();
                                        // Create an associative array of partner names and icons
                                        $partnerIcons = array(
                                            'Spotify' => 'https://img.icons8.com/color/48/000000/spotify--v1.png',
                                            'Amazon Music' => 'https://img.icons8.com/color/48/000000/amazon-music.png',
                                            'Apple Music' => 'https://img.icons8.com/ios-filled/256/apple-music.png',
                                            'Deezer' => 'https://img.icons8.com/color/48/000000/deezer.png',
                                            'YouTube' => 'https://img.icons8.com/color/48/000000/youtube-play.png',
                                            'Tidal' => 'https://img.icons8.com/color/48/000000/tidal.png',
                                            'Pandora' => 'https://img.icons8.com/color/48/000000/pandora.png',
                                            'SoundCloud' => 'https://img.icons8.com/color/48/000000/soundcloud.png',
                                            'TikTok' => 'https://img.icons8.com/color/48/000000/tiktok.png',
                                            'Shazam' => 'https://img.icons8.com/color/48/000000/shazam.png',
                                            'Google Play' => 'https://img.icons8.com/color/48/000000/google-play.png',
                                            'Amazon' => 'https://img.icons8.com/color/48/000000/amazon.png',
                                            'YouTube Music' => 'https://img.icons8.com/color/48/000000/youtube-music.png',
                                            'iHeartRadio' => 'https://img.icons8.com/color/48/000000/iheartradio.png',
                                            'Napster' => 'https://img.icons8.com/color/48/000000/napster.png',
                                            'JOOX' => 'https://img.icons8.com/color/48/000000/joox.png',
                                            'Yandex Music' => 'https://img.icons8.com/color/48/000000/yandex-music.png',
                                            'Qobuz' => 'https://img.icons8.com/color/48/000000/qobuz.png',
                                            'Anghami' => 'https://img.icons8.com/color/48/000000/anghami.png',
                                            'KKBOX' => 'https://img.icons8.com/color/48/000000/kkbox.png',
                                            'Facebook' => 'https://img.icons8.com/color/48/000000/facebook.png',
                                            'Instagram' => 'https://img.icons8.com/color/48/000000/instagram-new.png',
                                            'Twitter' => 'https://img.icons8.com/color/48/000000/twitter.png',
                                            'Snapchat' => 'https://img.icons8.com/color/48/000000/snapchat.png',
                                            'Twitch' => 'https://img.icons8.com/color/48/000000/twitch.png',
                                            "VK" => "https://img.icons8.com/color/48/000000/vk-com.png",
                                            "MediaNet" => "https://img.icons8.com/color/48/000000/medianet.png"
                                        );
                                        while ($row = mysqli_fetch_assoc($rezultati)) {
                                            $partner = $row['Partner'];
                                            $sum = $row['sum'];
                                            $icon = '';
                                            // Check if the partner name is in the associative array
                                            if (array_key_exists($partner, $partnerIcons)) {
                                                $icon = $partnerIcons[$partner];
                                                $icons[] = array('partner' => $partner, 'sum' => $sum, 'icon' => $icon);
                                            } else {
                                                $no_icons[] = array('partner' => $partner, 'sum' => $sum);
                                            }
                                        }
                                        // Sort the arrays
                                        array_multisort(array_column($icons, 'partner'), SORT_ASC, $icons);
                                        array_multisort(array_column($no_icons, 'partner'), SORT_ASC, $no_icons);
                                        // Output the data
                                        $total_sum = 0;

                                        foreach ($icons as $partner) {
                                            echo "<tr>";
                                            echo "<td><img src='{$partner['icon']}' alt='{$partner['partner']}' width='30'></td>";
                                            echo "<td>{$partner['partner']}</td>";
                                            echo "<td>{$partner['sum']}</td>";
                                            echo "</tr>";
                                            $total_sum += $partner['sum']; // Add the current partner's sum to the total sum
                                        }

                                        foreach ($no_icons as $partner) {
                                            echo "<tr>";
                                            echo "<td>No icon available for {$partner['partner']}</td>";
                                            echo "<td>{$partner['partner']}</td>";
                                            echo "<td>{$partner['sum']}</td>";
                                            echo "</tr>";
                                            $total_sum += $partner['sum']; // Add the current partner's sum to the total sum
                                        }

                                        ?>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                            <th colspan="2">Total</th>
                                            <th><?php echo $total_sum; ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <br>

                        <button style="text-transform:none;" class="toggle-button btn btn-primary shadow-sm rounded-3" onclick="toggleTable()"><i class="fi fi-rr-eye-crossed"></i> Fshih tabel&euml;n e pergjitshme</button>


                        <div class="row my-5 mx-1 justify-content-center">
                            <div class="table-container">

                                <?php
                                echo "<div class='table-responsive'><table class='table border-bottom w-full'>";
                                echo "<thead><tr>";
                                foreach ($headers as $header) {
                                    echo "<th class='border-top'><i>{$header}</i></th>";
                                }
                                echo "</tr></thead>";
                                echo "<tbody>";
                                $sum = 0;
                                foreach ($data as $row) {
                                    echo "<tr>";
                                    foreach ($row as $key => $value) {
                                        echo "<td>{$value}</td>";
                                        if ($key == 1) {
                                            $columnTwo = $value;
                                        }
                                        if ($key == 2) {
                                            $columnThree = $value;
                                        }
                                        if ($key == 6) {
                                            $sum += $value;
                                            $sumDisplay = "$" . $sum;
                                            echo "<td>$</td>";
                                        }
                                    }
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table></div>";
                                ?>
                            </div>
                        </div>

                        <div class="row gap-2 my-5 mx-1 justify-content-center">
                            <div class="col border-bottom p-3">
                                <p>N&euml;nshkrimi</p>
                                <img src="" alt="" height="125">
                            </div>
                            <div class="col ">
                                <p class="text-black text-end p-3 rounded" style='width:fit-content;float:right'>
                                    <span class="text-black"><b>Shuma e p&euml;rgjithshme</b></span><br><span><?php echo "$sum" ?> &#36;</span>
                                </p>
                            </div>
                        </div>
                        <!-- <hr>
                        <div class="row">
                            <div class="col-xl-2">
                                <button type="button" class="btn btn-primary text-capitalize" style="background-color:#60bdf3 ;">Pay Now</button>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function printData() {
            var printBtn = document.getElementById("printBtn");
            var backBtn = document.getElementById("backBtn");
            var exportPdf = document.getElementById("exportPdf")
            backBtn.style.visibility = 'hidden';
            exportPdf.style.visibility = 'hidden';
            printBtn.style.visibility = 'hidden';
            window.print();
            exportPdf.style.visibility = 'visible';
            printBtn.style.visibility = 'visible';
            backBtn.style.visibility = 'visible';


        }

        function toggleTable() {
            var tableContainer = document.querySelector('.table-container');
            if (tableContainer.style.display === 'none') {
                tableContainer.style.display = 'block';
                document.querySelector('.toggle-button').innerHTML = '<i class="fi fi-rr-eye-crossed"></i> Fshih tabel&euml;n e pergjitshme';
                document.querySelector('.toggle-button').style.textTransform = 'none';
            } else {
                tableContainer.style.display = 'none';
                document.querySelector('.toggle-button').innerHTML = '<i class="fi fi-rr-eye"></i> Shfaq tabel&euml;n e pergjitshme';
                document.querySelector('.toggle-button').style.textTransform = 'none';
            }
        }

        function togglePlatformTable() {
            var tableContainer = document.querySelector('.platformTable');
            if (tableContainer.style.display === 'none') {
                tableContainer.style.display = 'block';
                document.querySelector('.toggle-button-platforms').innerHTML = '<i class="fi fi-rr-eye-crossed"></i> Fshih raportin e tabelave';
                document.querySelector('.toggle-button-platforms').style.textTransform = 'none';
            } else {
                tableContainer.style.display = 'none';
                document.querySelector('.toggle-button-platforms').innerHTML = '<i class="fi fi-rr-eye"></i> Shfaq raportin e tabelave';
                document.querySelector('.toggle-button-platforms').style.textTransform = 'none';
            }
        }
    </script>

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
</body>

</html>