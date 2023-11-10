<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'conn-d.php';

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Get the signature data from the form input
//     $signatureData = $_POST['signatureData'];

//     // Check if the ID is set
//     if (isset($_GET['id'])) {
//         // Get the ID from the URL parameter
//         $id = $_GET['id'];

//         // Update the nenshkrimi column for the specific row with the given ID
//         $query = "UPDATE kontrata SET nenshkrimi = '$signatureData' WHERE id = $id";
//         $result = mysqli_query($conn, $query);

//         // Check if the query was successful
//         if ($result) {
//             // If the query was successful, display a success message
//             echo "<script>alert('Nenshkrimi u azhurnua me sukses!')</script>";
//         } else {
//             // If the query was not successful, display an error message
//             echo "Gabim n&euml; azhurnimin e nenshkrimit: " . mysqli_error($conn);
//         }
//     } else {
//         // If the ID was not set, display an error message
//         echo "ID nuk &euml;sht&euml; caktuar!";
//     }
// } else {
//     // If the request method is not POST, display an error message
//     // echo "K&euml;rkesa nuk &euml;sht&euml; e vlefshme!";
// }




// // Check if the ID is set
// if (isset($_GET['id'])) {
//     // Get the ID from the URL parameter
//     $id = $_GET['id'];

//     // Query the database to get the data for the specific row with the given ID
//     $query = "SELECT * FROM kontrata WHERE id = $id";
//     $result = mysqli_query($conn, $query);

//     // Check if the query was successful
//     if (mysqli_num_rows($result) > 0) {
//         // Fetch the data from the row
//         $row = mysqli_fetch_assoc($result);
//         // Display the data in HTML

//     } else {
//         // If no row was found with the given ID, display an error message
//         echo "Nuk u gjet asnj&euml; rresht me k&euml;t&euml; ID!";
//     }
// } else {
//     // If the ID was not set, display an error message
//     echo "ID nuk &euml;sht&euml; caktuar!";
// }
$token = $_GET['token']; // Assuming the token is passed as a URL parameter

// Execute the SQL query to check the token's validity and expiration time
$query = "SELECT * FROM tokens WHERE token = '$token' AND expiration_time >= " . time();
$result = mysqli_query($conn, $query);


// Token is valid and has not expired
// Proceed with the desired actions for the page

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontrata me Baresha Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <!-- Fav Icon ne formatin .png -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
        }

        #expire-message {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;

        }
    </style>
    <script>
        // Disable printing when Ctrl + P is pressed
        window.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.keyCode === 80) {
                event.preventDefault(); // Prevent default print behavior
                window.print(); // Open print dialog
            }
        });
    </script>
    <style type="text/css" media="print">
        body {
            visibility: hidden;
            display: none
        }
    </style>
</head>

<body>
    <?php
    if (mysqli_num_rows($result) > 0) {


        $id = $_GET['id'];

        $query = "SELECT * FROM kontrata WHERE id = $id";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

    ?>
            <div class="container my-5">
                <!-- <a href="lista_kontratave.php" class='btn btn-light text-capitalize border border-1 shadow-2 my-3' id="backBtn"
                data-mdb-toggle="tooltip" title="Shko prapa"><i class="fas fa-arrow-left "></i> Back</a> -->


                <div class="py-5 px-5">
                    <svg width="100%" height="100%" id="svg" viewBox="0 0 1440 390" xmlns="http://www.w3.org/2000/svg" class="transition duration-300 ease-in-out delay-150">
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="50%" x2="100%" y2="50%">
                                <stop offset="5%" stop-color="#ff0000"></stop>
                                <stop offset="95%" stop-color="#0693e3"></stop>
                            </linearGradient>
                        </defs>
                        <path d="M 0,400 C 0,400 0,133 0,133 C 72.96666666666667,146.80769230769232 145.93333333333334,160.6153846153846 218,165 C 290.06666666666666,169.3846153846154 361.23333333333335,164.34615384615387 450,154 C 538.7666666666667,143.65384615384613 645.1333333333334,128 742,129 C 838.8666666666666,130 926.2333333333331,147.65384615384616 994,150 C 1061.7666666666669,152.34615384615384 1109.9333333333334,139.3846153846154 1181,134 C 1252.0666666666666,128.6153846153846 1346.0333333333333,130.80769230769232 1440,133 C 1440,133 1440,400 1440,400 Z" stroke="none" stroke-width="0" fill="url(#gradient)" fill-opacity="0.53" class="transition-all duration-300 ease-in-out delay-150 path-0" transform="rotate(-180 720 200)">
                        </path>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="50%" x2="100%" y2="50%">
                                <stop offset="5%" stop-color="#ff0000"></stop>
                                <stop offset="95%" stop-color="#0693e3"></stop>
                            </linearGradient>
                        </defs>
                        <path d="M 0,400 C 0,400 0,266 0,266 C 106.63589743589745,259.44615384615383 213.2717948717949,252.89230769230772 284,263 C 354.7282051282051,273.1076923076923 389.5487179487179,299.87692307692305 468,288 C 546.4512820512821,276.12307692307695 668.5333333333333,225.60000000000005 759,219 C 849.4666666666667,212.39999999999995 908.3179487179486,249.72307692307692 977,254 C 1045.6820512820514,258.2769230769231 1124.194871794872,229.50769230769234 1203,226 C 1281.805128205128,222.49230769230766 1360.9025641025642,244.24615384615385 1440,266 C 1440,266 1440,400 1440,400 Z" stroke="none" stroke-width="0" fill="url(#gradient)" fill-opacity="1" class="transition-all duration-300 ease-in-out delay-150 path-1" transform="rotate(-180 720 200)">
                        </path>

                        <foreignObject x="0" y="0" width="100%" height="100%">
                            <div class="text-center my-3">
                                <img class="bg-light p-3 rounded-5" src="images/brand-icon.png" alt="" style="width:10%;">
                                <div class="rounded-5 py-2 w-50 my-5 shadow-sm bg-light mx-auto">
                                    <h4 class='fw-bold text-center'>KONTRATË PËR TË DREJTËN E VEPRËS</h4>
                                </div>
                            </div>
                        </foreignObject>
                    </svg>

                    <p class='fw-bold my-3'>
                        Kjo kontrat&euml; u n&euml;nshkrua me dat&euml;
                        <?php echo $row['data'] ?> midis
                        <?php echo $row['emri'] ?>
                        <?php echo $row['mbiemri'] ?>, ("
                        <?php echo $row['emriartistik'] ?>") dhe Baresha Music ("Baresha
                        Music SH.P.K.").
                    </p>

                    <p class="my-3">Numri personal :
                        <?php echo $row['numri_personal'] ?>
                    </p>
                    <p>
                        Artisti &euml;sht&euml; autori dhe/apo pronari i regjistrimit t&euml; tingujve t&euml; kompozicionit muzikor t&euml; quajtur</p>
                    <p>(
                        <b>
                            <?php echo $row['vepra'] ?>
                        </b> )
                    </p>
                    <p>
                        Baresha Music i takon e drejta ekskluzive e vepres (kenges), dhe kushtet e marrveshjes jane te
                        percaktuara si ne vijim:
                    </p>

                    <p class="fw-bold my-3">N&euml; k&euml;t&euml; kontrat dy pal&euml;t pajtohen me nenet e sh&euml;nuara m&euml; posht&euml;</p>

                    <p>1.1. DHËNIA E TË DREJTAVE. Me n&euml;nshkrimin e k&euml;saj kontrate artisti e jep te drejten e plote per
                        perdorimin, botimin, riprodhimin, licesnimin, shperndarjen, performances, publikimin dhe shfaqejen e
                        kenges, duke perfshire te gjitha rrjetet sociale, dhe platformat publikuese si Youtube, pa kufizuar
                        shkarkimet digjitale,transmetimin dhe kopjet fizike p&euml;r periudhen (vitet ose e perhershme) q&euml; fillon nga
                        data e n&euml;nshkrimit t&euml; k&euml;saj kontrate.</p>

                    <p>
                        2.1. LICENCA EKSKLUZIVE. Artisti pajtohet q&euml; Baresha Music SH.P.K ta ket&euml; t&euml; drejt&euml;n ekskluzive p&euml;r
                        eksploatimin e k&euml;ng&euml;s s&euml; cekur n&euml; k&euml;t&euml; marr&euml;veshje. Artisti nuk do t'i jep&euml; asnj&euml; t&euml; drejt&euml; pal&euml;s s&euml;
                        tret&euml; q&euml; konfliktojn&euml; me licenc&euml;n ekskluzive q&euml; i jepet Baresh&euml;s n&euml; k&euml;t&euml; marr&euml;veshje.
                    </p>

                    <p>
                        3.1. KUFIZIMI I KANALEVE. Artisti pajtohet q&euml; k&euml;nga do t&euml; ngarkohet dhe l&euml;shohet vet&euml;m n&euml; kanalin zyrtar
                        'Baresha Music' n&euml; platforma si YouTube, Spotify dhe platforma t&euml; tjera p&euml;r transmetim t&euml; muzik&euml;s.
                    </p>

                    <p>4.1. PËRQINDJA. Pal&euml;t pajtohen n&euml; ndarjen e p&euml;rqindjes n&euml; vler&euml; prej
                        <b>
                            <?php echo $row['perqindja'] ?>%
                        </b>prej t&euml; t&euml; gjitha t&euml;
                        ardhurave t&euml; gjeneruara nga eksploatimi i k&euml;ng&euml;s pas n&euml;nshkrimit t&euml; k&euml;saj kontrate dhe publikimit te
                        vepres/kenges. T&euml; ardhurat neto do t&euml; p&euml;rcaktohen si t&euml; gjitha t&euml; ardhurat t&euml; marrura nga Baresha
                        nga
                        eksploatimi i k&euml;ng&euml;s, t&euml; zbritura nga kostot direkte q&euml; Baresha nd&euml;rhyn n&euml; lidhje me k&euml;t&euml;
                        eksploatim.
                    </p>

                    <p>5.1. PREZANTIMET DHE GARANCITË. Artisti prezanton dhe garanton se (i) Artisti &euml;sht&euml; pronari i vet&euml;m dhe
                        ekskluziv i regjistrimit. t&euml; tingujve t&euml; Vepres/K&euml;ng&euml;s, (i) asnj&euml; pjes&euml; e K&euml;ng&euml;s nuk do t&euml; shkel&euml; t&euml;
                        drejta t&euml; pal&euml;ve t&euml; treta qe nuk jane pjese e kesaj marrveshje dhe Artisti nuk ka b&euml;r&euml; marr&euml;veshje t&euml;
                        tjera p&euml;r t&euml; drejta t&euml; K&euml;ng&euml;s q&euml; mund t&euml; pengojn&euml; k&euml;t&euml; Marr&euml;veshje.
                        Baresha Music Sh.p.k. ka per obligim qe ne afat prej 24 ore nga data e nenshkrimit te kesaj marrveshje
                        te bej publikimin e kenges ne platformat dixhitale.
                    </p>

                    <p>
                        6.1. PËRMBUSHJA E KUSHTEVE. Artisti pranon q&euml; t&euml; respektoj&euml; rregullat dhe kushtet e k&euml;saj Marr&euml;veshjeje
                        dhe t&euml; ndjek&euml; k&euml;rkesat dhe udh&euml;zimet e Baresha Music lidhur me eksploatimin e K&euml;ng&euml;s. N&euml;se Artisti shkel
                        ndonj&euml; kusht t&euml; k&euml;saj Marr&euml;veshjeje, Baresha ka t&euml; drejt&euml; t&euml; ndaloj&euml; ose t&euml; nd&euml;rprej&euml; eksploatimin e
                        K&euml;ng&euml;s dhe t&euml; k&euml;rkoj&euml; d&euml;mshp&euml;rblim.
                    </p>

                    <p>
                        7.1. KOHEZGJATJA DHE NDËRPRERJA.
                        Kega\Vepra behet prone e perhershme e Baresha Music Sh.p.k. nga momenti i nenshkrimit te kesaj
                        marrveshje, pervec ne rastet kur mes paleve arrihet nje marrveshje e perbashket me kushte te tjera.
                        Palet kane t&euml; drejt&euml; t&euml; nd&euml;rprej&euml; k&euml;t&euml; Marr&euml;veshje pa shkaqe t&euml; arsyeshme me njoftim paraprak 7 dite
                        kalendarike nga data fillestare e nenshkrimit dhe publikimit te kesaj marrveshje. Njoftimi duhet te
                        behet me shkrim permes mjeteve te komunikimit (email). N&euml; rast nd&euml;rprerjeje nga ana e Baresha, t&euml; gjitha
                        t&euml; drejtat kthehen te Artisti dhe Baresha nuk &euml;sht&euml; e detyruar t&euml; paguaj&euml; asnj&euml; pages&euml; ose d&euml;mshp&euml;rblim
                        p&euml;r artistin. Palet nuk kane te drejte te kerkoje te drejtat e prones pasi te kaloj periudha prej 7 dite
                        e bashkepunimit, nga data e marrveshjes.

                    </p>

                    <p>
                        8.1. LIGJI I ZBATUESHËM. Kjo Marr&euml;veshje dhe t&euml; gjitha t&euml; drejtat dhe detyrimet e pal&euml;ve n&euml; lidhje me
                        k&euml;t&euml; Marr&euml;veshje do t&euml; n&euml;nshtrohen dhe do t&euml; interpretohen n&euml; p&euml;rputhje me ligjet dhe rregulloret e
                        shtetit te Kosoves.
                    </p>
                    <br>
                    <p>
                        <b>
                            Titulli i k&euml;ng&euml;s / vepr&euml;s

                        </b>
                    </p>

                    <p class="border-bottom w-25">
                        <?php echo $row['vepra'] ?>
                    </p>
                    <br>

                    <div class="row">
                        <div class="col-6">
                            <p style="f" class="fw-bold"> Artisti/Pronar i kompozicionit t&euml; muzik&euml;s <br> <span>Emri dhe Mbiemri
                                </span> </p>
                            <p class="text-start">
                                <?php echo $row['emri'] ?>
                                <?php echo $row['mbiemri'] ?>
                            </p>
                        </div>
                        <div class="col-6">
                            <p style="f" class="fw-bold text-end"> Pronar i t&euml; drejtave ekskluzive t&euml; eksploatimit t&euml;
                                kompozicionit t&euml; muzik&euml;s <br> <span>Emri dhe Mbiemri <br>
                            </p>
                            <p style="f" class="text-end"> Baresha Music Sh.p.k. </p>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <p class="fw-bold text-start"> Nenshkrimi <br> </p>
                        </div>
                        <div class="col-6">
                            <p class="fw-bold text-end"> Nenshkrimi <br> </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <p style="f" class="border-bottom float-start w-50 text-start">
                                <?php $file_path = $row['nenshkrimi'];
                                echo '<img src="' . $file_path . '" style="width: 150px; height: auto;">'; ?>
                            </p>
                        </div>
                        <div class="col-4 text-center">
                            <img src="images/vula.png" style="width: 150px; height: auto;margin-top:-45px">
                        </div>
                        <div class="col-4">
                            <p style="f" class="border-bottom float-end w-50 text-end"> <img src="signatures/34.png" style="width: 150px; height: auto;"></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-5">
                        <div class="col-12 float-end text-end">
                            <p style="">Data e nenshkrimit te marrveshjes :
                                <?php echo $row['data']; ?>
                            </p>
                        </div>
                        <?php if (!($row['shenim'] == " ")) { ?>
                            <div class="my-5 border rounded-5 py-3">
                                <h6>Shenime</h6>
                                <?php echo $row['shenim'] ?>
                            </div>
                        <?php } ?>
                    </div>


                    <!-- <div class="row">
                    <div class="col-6">
                        <p>
                            <?php echo $row['emri'] ?>
                            <?php echo $row['mbiemri'] ?>
                        </p>
                    </div>

                    <div class="col-6">
                        <p class="border-bottom float-end w-50 text-end">
                            <?php
                            $file_path = $row['nenshkrimi'];
                            echo '<img src="' . $file_path . '" style="width: 150px; height: auto;">';
                            ?>
                            <br>
                        </p>
                    </div>

                </div> -->
                    <hr>

                    <!-- <div class="row">
                    <div class="col-6">
                        <p class="fw-bold">
                            Pronar i t&euml; drejtave ekskluzive t&euml; eksploatimit t&euml; kompozicionit t&euml; muzik&euml;s
                            <br>
                            <span>Emri dhe Mbiemri </span>

                        </p>
                    </div>
                    <div class="col-6">
                        <p class="fw-bold text-end">
                            Nenshkrimi
                            <br>
                        </p>
                    </div>
                </div> -->

                    <!-- <div class="row">
                    <div class="col-6">
                        <p>
                            Baresha Music Sh.p.k.
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="border-bottom float-end w-50 text-end">
                            <img src="signatures/34.png" style="width: 150px; height: auto;">

                            <br>
                        </p>
                    </div>
                </div> -->

                    <!-- <hr> -->

                    <div class="row mt-5">
                        <div class="col-6">
                            <div class="row">
                                <?php
                                include 'conn-d.php';

                                if (isset($_GET['id'])) {
                                    $id = $_GET['id'];

                                    $query = "SELECT * FROM kontrata WHERE id = $id";
                                    $result = mysqli_query($conn, $query);

                                    if (mysqli_num_rows($result) > 0) {
                                        $row = mysqli_fetch_assoc($result);

                                        // Display the data in HTML
                                ?>


                                        <form method="POST" enctype="multipart/form-data">
                                            <label for="signature">Nenshkrimi:</label>
                                            <br>
                                            <canvas id="signature" width="350" height="200" class="border mt-2 rounded-5 shadow-sm"></canvas>
                                            <input type="hidden" name="signatureData" id="signatureData">
                                            <br>
                                            <button type="submit" class="btn btn-light rounded-5 float-right border" style="text-transform:none;">
                                                <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                                                <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                            </button>
                                            <button type="button" class="btn btn-light rounded-5 float-right border mr-2" style="text-transform:none;" onclick="clearSignaturePad()">
                                                <i class="fi fi-rr-refresh" style="display:inline-block;vertical-align:middle;"></i>
                                                <span style="display:inline-block;vertical-align:middle;">Fshij</span>
                                            </button>
                                        </form>

                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
                                        <script>
                                            var canvas = document.getElementById('signature');
                                            var signaturePad = new SignaturePad(canvas);

                                            function clearSignaturePad() {
                                                signaturePad.clear();
                                            }
                                            document.querySelector('form').addEventListener('submit', function(event) {
                                                var signatureData = signaturePad.toDataURL();
                                                document.getElementById('signatureData').value = signatureData;
                                            });
                                        </script>
                                <?php
                                    } else {
                                        echo "Nuk u gjet asnj&euml; rresht me k&euml;t&euml; ID!";
                                    }
                                } else {
                                    echo "ID nuk &euml;sht&euml; caktuar!";
                                }
                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $signatureData = $_POST['signatureData'];
                                    if (isset($_GET['id'])) {
                                        $id = $_GET['id'];
                                        $query = "UPDATE kontrata SET nenshkrimi = '$signatureData' WHERE id = $id";
                                        $result = mysqli_query($conn, $query);

                                        if ($result) {
                                            echo "<script>alert('Nenshkrimi u azhurnua me sukses!')</script>";
                                        } else {
                                            echo "Gabim n&euml; azhurnimin e nenshkrimit: " . mysqli_error($conn);
                                        }
                                    } else {
                                        echo "ID nuk &euml;sht&euml; caktuar!";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                            <?php
                            echo $row['data'];
                            ?>
                        <?php
                        if (!($row['shenim'] == " ")) {
                        ?>
                            <div class="my-5 border rounded-5 py-3">
                                <h6>Shenime</h6>
                                <?php echo $row['shenim'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php
        include 'conn-d.php';
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "SELECT * FROM kontrata WHERE id = $id";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

        ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
                <script>
                    var canvas = document.getElementById('signature');
                    var signaturePad = new SignaturePad(canvas);

                    function clearSignaturePad() {
                        signaturePad.clear();
                    }
                    document.querySelector('form').addEventListener('submit', function(event) {
                        var signatureData = signaturePad.toDataURL();
                        document.getElementById('signatureData').value = signatureData;
                    });
                </script>
            <?php
            } else {
                echo "Nuk u gjet asnj&euml; rresht me k&euml;t&euml; ID!";
            }
        } else {
            echo "ID nuk &euml;sht&euml; caktuar!";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $signatureData = $_POST['signatureData'];
            if (isset($_GET['id'])) {
                $id = $_GET['id'];

                $query = "UPDATE kontrata SET nenshkrimi = '$signatureData' WHERE id = $id";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    echo "<script>alert('Nenshkrimi u azhurnua me sukses!')</script>";
                } else {
                    echo "Gabim n&euml; azhurnimin e nenshkrimit: " . mysqli_error($conn);
                }
            } else {
                echo "ID nuk &euml;sht&euml; caktuar!";
            }
        }
    } else {
        $deleteQuery = "DELETE FROM tokens WHERE token = '$token'";
        $deleteResult = mysqli_query($conn, $deleteQuery);
        if ($deleteResult) {
            ?>
            <?php
            if (isset($_POST['submit2'])) {
                $selectedStafi = $_POST['stafi'];
                $emailOfClient = $_POST['emails'];
                $stafiName = '';
                $emriJuaj = $_POST['emriJuaj'];
                $dataProblemit = $_POST['dataProblemit'];
                $message = $_POST['message'];
                if ($selectedStafi === 'afrim') {
                    $email = 'gjinienis148@gmail.com';
                    $stafiName = 'Afrim Kolgeci (CEO)';
                } elseif ($selectedStafi === 'enis') {
                    $email = 'egjini17@gmail.com';
                    $stafiName = 'Enis Gjini (Zhvillues i uebit)';
                } elseif ($selectedStafi === 'lyon') {
                    $email = 'enisgjini11@gmail.com';
                    $stafiName = 'Lyon Cacaj (Dizajner)';
                }
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://rapidprod-sendgrid-v1.p.rapidapi.com/mail/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode([
                        "personalizations" => [
                            [
                                "to" => [
                                    [
                                        'email' => $email
                                    ]
                                ],
                                "subject" => "Form Submission"
                            ]
                        ],
                        'from' => [

                            'email' => 'bot-teknik@bot.com'

                        ],
                        "content" => [
                            [
                                "type" => "text/plain",
                                "value" => "Stafi: $stafiName\nEmail: $emailOfClient\nEmri Juaj: $emriJuaj\nData e problemit: $dataProblemit\nMessage: $message",
                            ]
                        ]
                    ]),
                    CURLOPT_HTTPHEADER => [
                        "X-RapidAPI-Host: rapidprod-sendgrid-v1.p.rapidapi.com",
                        "X-RapidAPI-Key: 0c7b6f5beemsh8e58b659d2ef8dbp1f0360jsn34cb942c7f68",
                        "content-type: application/json"
                    ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    echo "Email sent successfully!";
                    $id = $_GET['id'];
                    $token = $_GET['token'];
                    header("Location: " . $_SERVER['PHP_SELF'] . "?id=$id&token=$token");
                    exit();
                }
            }
            ?>

            <div class="container" id="expire-message">
                <div class="row">
                    <div class="col mx-auto">
                        <div class="card p-5 text-center border">
                            <div class="d-flex justify-content-center">
                                <img src="images/icons8-query-94.png" alt="" width="94px">
                            </div>
                            <br>
                            <p>Koha (tokeni) juaj p&euml;r t&euml; n&euml;nshkruar kontrat&euml;n me Baresha Network ka skaduar!</p>
                            <p>K&euml;rkoni nj&euml; koh&euml; t&euml; re duke ju shkruar stafit t&euml; Baresha Network</p>
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col my-3">
                                        <div class="form-group">
                                            <label for="stafi">Zgjedh stafin</label>
                                            <select name="stafi" id="stafi" class="form-select">
                                                <option value="afrim">Afrim Kolgeci ( CEO )</option>
                                                <option value="enis">Enis Gjini ( Zhvillues i uebit )</option>
                                                <option value="lyon">Lyon Cacaj ( Dizajner )</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col my-3">
                                        <div class="form-group">
                                            <label for="dataProblemit">Data e problemit</label>
                                            <input type="date" class="form-control" id="dataProblemit" name="dataProblemit" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col my-3">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" class="form-control" id="emails" name="emails" required>
                                        </div>
                                    </div>
                                    <div class="col my-3">
                                        <div class="form-group">
                                            <label for="emriJuaj">Emri juaj</label>
                                            <input type="text" class="form-control" id="emriJuaj" name="emriJuaj" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group my-3">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" name="submit2">Send Email</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>











    <?php
        } else {
            echo "Error deleting token: " . mysqli_error($conn);
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script><!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>

</body>

</html>