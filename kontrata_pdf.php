<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BareshaNetwork - <?php echo date("Y"); ?></title>

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

    $query = "SELECT * FROM kontrata WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

    ?>
        <div class="container my-5">
            <a href="lista_kontratave.php" class='btn btn-light text-capitalize border border-1 shadow-2 my-3' id="backBtn" data-mdb-toggle="tooltip" title="Shko prapa"><i class="fas fa-arrow-left "></i> Back</a>


            <div class="card  shadow-sm border py-5 px-5">
                <h3 class='fw-bold text-center'>KONTRATË PËR TË DREJTËN E VEPRËS</h3>

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
                    s
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
                    behet me shkrim perms mjeteve te komunikimit (email). N&euml; rast nd&euml;rprerjeje nga ana e Baresha, t&euml; gjitha
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
                        <p class="fw-bold">
                            Artisti/Pronar i kompozicionit t&euml; muzik&euml;s
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
                </div>



                <div class="row">
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
                </div>

                <hr>

                <div class="row">
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
                </div>



                <div class="row">
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
                </div>

                <hr>

                <div class="row mt-5">
                    <div class="col-12 float-end text-end">
                        <p>Data e nenshkrimit te marrveshjes</p>
                        <div class="border-bottom w-25 float-end text-end">
                            <?php
                            echo $row['data'];

                            ?>
                        </div>
                    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script><!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>