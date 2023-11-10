<?php header('Cache-Control: no-cache');
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body>
    <?php
    include 'conn-d.php';


    $id = $_GET['id'];

    $query = "SELECT * FROM ofertat WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        ?>
        <div class="container my-5">
            <a href="ofertat.php" class='btn btn-light text-capitalize border border-1 shadow-2 my-3' id="backBtn"
                data-mdb-toggle="tooltip" title="Shko prapa"><i class="fas fa-arrow-left "></i> Back</a>


            <div class="card  shadow-sm border py-5 px-5">
                <h3 class='fw-bold text-center'>KONTRATA E KAMPANJËS MARKETING</h3>

                <p class='fw-bold my-3'>Kjo Kontrat&euml; e Kampanj&euml;s Marketing (“Kontrat&euml;”) &euml;sht&euml; b&euml;r&euml; dhe hyri n&euml; fuqi midis
                    [Emri i Klientit], me seli n&euml; [Adresa e Klientit] ("Klienti") dhe [Emri i Agjencis&euml; s&euml; Marketingut], me
                    seli n&euml; [Adresa e Agjencis&euml;] ("Agjencia").
                </p>

                <p class="fw-bold my-3">N&euml; k&euml;t&euml; kontrat dy pal&euml;t pajtohen me nenet e sh&euml;nuara m&euml; posht&euml;</p>

                <p>1. Sh&euml;rbimet. Agjencia pranon t&euml; furnizoj&euml; Klientit nj&euml; kampanj&euml; marketingu tre-mujore n&euml; Instagram,
                    Facebook, Snapchat dhe TikTok. Kampanja do t&euml; jet&euml; e projektuar p&euml;r t&euml; rritur vizibilitetin dhe ndikimin
                    e mark&euml;s s&euml; Klientit, duke nxitur m&euml; shum&euml; trafik dhe shitje n&euml; faqen e internetit t&euml; Klientit. Agjencia
                    do t&euml; krijoj&euml; dhe do t&euml; menaxhoj&euml; kampanjat e reklamave n&euml; çdo platform&euml;, si dhe do t&euml; siguroj&euml;
                    p&euml;rdit&euml;sime dhe raporte rregullisht mbi progresin e kampanj&euml;s.</p>

                <p>
                    2. Kompensimi. Klienti pranon t&euml; paguaj&euml; Agjencis&euml; [Shuma] p&euml;r kampanj&euml;n marketingu tre-mujore. Pagesa
                    do t&euml; jen&euml; t&euml; detyrueshme p&euml;r t'u paguar m&euml; [Data e Detyrueshme], dhe mos pagimi mund t&euml; çoj&euml; n&euml;
                    nd&euml;rprerjen e kampanj&euml;s.
                </p>

                <p>
                    3. Afati. Afati i k&euml;saj Kontrate do t&euml; jet&euml; 3 muaj, fillon nga [Data e Fillimit] dhe p&euml;rfundon n&euml; [Data
                    e Mbarimit].
                </p>

                <p>4. P&euml;rfundimi. Çdo pal&euml; mund t&euml; p&euml;rfundoj&euml; k&euml;t&euml; Kontrat&euml; me njoftim t&euml; shkruar p&euml;r pal&euml;n tjet&euml;r. N&euml;se
                    Klienti p&euml;rfundon Kontrat&euml;n para se t&euml; p&euml;rfundoj&euml; afati i 3 muajve, Klienti do t&euml; jet&euml; i detyruar t&euml;
                    paguaj&euml; pagesat e mbetura p&euml;r sh&euml;rbimet q&euml; jan&euml; ofruar deri n&euml; at&euml; koh&euml;.</p>

                <p>5. Konfidencialiteti. T&euml; dyja pal&euml;t pajtohen q&euml; t&euml; mbajn&euml; konfidencialitetin e t&euml; gjitha informacioneve
                    dhe materialeve t&euml; ndar&euml; midis tyre gjat&euml; afatit t&euml; k&euml;saj Kontrate.</p>

                <p>
                    6. Ligji i Zbatuesh&euml;m. Kjo Kontrat&euml; do t&euml; n&euml;nshkruhet dhe do t&euml; kuptohet n&euml; p&euml;rputhje me ligjet e
                    shtetit t&euml; [Shteti].
                </p>

                <p>
                    7. Gjith&euml; Kontrata. Kjo Kontrat&euml; p&euml;rmban t&euml; gjitha marr&euml;veshjet midis pal&euml;ve dhe superson t&euml; gjitha
                    negociatat dhe marr&euml;veshjet t&euml; m&euml;parshme nd&euml;rmjet pal&euml;ve.
                </p>

                <p>
                    8.1. LIGJI I ZBATUESHËM. Kjo Marr&euml;veshje dhe t&euml; gjitha t&euml; drejtat dhe detyrimet e pal&euml;ve n&euml; lidhje me
                    k&euml;t&euml; Marr&euml;veshje do t&euml; n&euml;nshtrohen dhe do t&euml; interpretohen n&euml; p&euml;rputhje me ligjet dhe rregulloret e
                    shtetit [emri i shtetit]. T&euml; gjitha mosmarr&euml;veshjet dhe mosmarr&euml;veshjet n&euml; lidhje me k&euml;t&euml; Marr&euml;veshje do
                    t&euml; zgjidhen n&euml; m&euml;nyr&euml; miq&euml;sore midis pal&euml;ve. N&euml; rast se nuk ka zgjidhje miq&euml;sore, mosmarr&euml;veshjet dhe
                    mosmarr&euml;veshjet do t&euml; zgjidhen n&euml;p&euml;rmjet arbitrazhit n&euml; p&euml;rputhje me procedurat dhe rregulloret e
                    arbitrazhit t&euml; shtetit [emri i shtetit].
                </p>

                <p>T&euml; n&euml;nshkruarit e m&euml;posht&euml;m pajtohen me kushtet dhe kushtet e k&euml;saj Kontrate:
                </p>


                <div class="row mt-5">
                    <div class="col-6">
                        <p>ARTISTI</p>
                        <div class="border-bottom w-25">
                            <?php
                            $file_path = $row['nenshkrimi'];
                            echo '<img src="' . $file_path . '" style="width: 150px; height: auto;">';
                            ?>
                        </div>
                        <?php
                        include 'conn-d.php';

                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];

                            $query = "SELECT * FROM ofertat WHERE id = $id";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);

                                // Display the data in HTML
                                ?>


                                <form method="POST" enctype="multipart/form-data">
                                    <label for="signature">Nenshkrimi:</label>
                                    <br>
                                    <canvas id="signature" width="400" height="200"
                                        class="border mt-2 rounded-5 shadow-sm"></canvas>
                                    <input type="hidden" name="signatureData" id="signatureData">
                                    <br>
                                    <button type="submit" class="btn btn-light rounded-5 float-right border"
                                        style="text-transform:none;">
                                        <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                                        <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                    </button>
                                    <button type="button" class="btn btn-light rounded-5 float-right border mr-2"
                                        style="text-transform:none;" onclick="clearSignaturePad()">
                                        <i class="fi fi-rr-refresh" style="display:inline-block;vertical-align:middle;"></i>
                                        <span style="display:inline-block;vertical-align:middle;">Fshij</span>
                                    </button>
                                </form>

                                <script
                                    src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
                                <script>
                                    var canvas = document.getElementById('signature');
                                    var signaturePad = new SignaturePad(canvas);
                                    function clearSignaturePad() {
                                        signaturePad.clear();
                                    }
                                    document.querySelector('form').addEventListener('submit', function (event) {
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

                                $query = "UPDATE ofertat SET nenshkrimi = '$signatureData' WHERE id = $id";
                                $result = mysqli_query($conn, $query);

                                if ($result) {
                                    echo "<script>alert('Nenshkrimi u azhurnua me sukses!')</script>";
                                    echo "<script>var isReloaded = false; setTimeout(function() { if (!isReloaded) { isReloaded = true; window.location.reload(true); } }, 1000);</script>";
                                } else {
                                    echo "Gabim n&euml; azhurnimin e nenshkrimit: " . mysqli_error($conn);
                                }
                            } else {
                                echo "ID nuk &euml;sht&euml; caktuar!";
                            }
                        }
                        ?>

                    </div>
                    <div class="col-6 float-end text-end">
                        <p>BARESHA</p>
                        <div class="border-bottom w-25 float-end text-end">
                            <img src='signatures/34.png' style="width: 150px; height: auto;">
                        </div>
                    </div>

                </div>
            </div>

        </div>

    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script><!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>