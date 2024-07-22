<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form data and generate the preview content
    $emri = $_POST['emri'] ?? '';
    $mbiemri = $_POST['mbiemri'] ?? '';
    $numri_tel = $_POST['numri_tel'] ?? '';
    $numri_personal = $_POST['numri_personal'] ?? '';
    $klienti = $_POST['klienti'] ?? '';
    $email = $_POST['email'] ?? '';
    $emriartistik = $_POST['emriartistik'] ?? '';
    $vepra = $_POST['vepra'] ?? '';
    $data = $_POST['data'] ?? '';
    $perqindja = $_POST['perqindja'] ?? '';
    $perqindja_other = $_POST['perqindja_other'] ?? '';
    $shenime = $_POST['shenime'] ?? '';
    // Generate the preview content
    // echo "<h3>Kontrata e re</h3>";
    // echo "<p><strong>Emri:</strong> $emri</p>";
    // echo "<p><strong>Mbiemri:</strong> $mbiemri</p>";
    // echo "<p><strong>Numri i telefonit:</strong> $numri_tel</p>";
    // echo "<p><strong>Numri personal:</strong> $numri_personal</p>";
    // echo "<p><strong>Klienti:</strong> $klienti</p>";
    // echo "<p><strong>Adresa e email-it:</strong> $email</p>";
    // echo "<p><strong>Emri artistik:</strong> $emriartistik</p>";
    // echo "<p><strong>Vepra:</strong> $vepra</p>";
    // echo "<p><strong>Data:</strong> $data</p>";
    // echo "<p><strong>Përqindja (Baresha):</strong> $perqindja%</p>";
    // echo "<p><strong>Përqindja (Klienti):</strong> $perqindja_other%</p>";
    // echo "<p><strong>Shënime:</strong> $shenime</p>";
}
?>
<div id="contractContent" class="container border ">
    <div class="">
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
        <p style="f" class='fw-bold my-0'> Kjo kontrat&euml; u n&euml;nshkrua me dat&euml;
            <?php echo date('d/m/Y', strtotime($data)); ?> midis
            <?php echo $emri ?>
            <?php echo $mbiemri ?>, ("
            <?php echo $emriartistik ?>") dhe Baresha Music ("Baresha Music SH.P.K.").
        </p>
        <p style="f" class="my-3">Numri personal : <b>
                <?php echo $numri_personal ?>
            </b> </p>
        <p style="f"> Artisti &euml;sht&euml; autori dhe/apo pronari i regjistrimit t&euml; tingujve t&euml; kompozicionit muzikor t&euml;
            quajtur</p>
        ( <b>
            <?php echo $vepra ?>
        </b> ) </p>
        <p style="f"> Baresha Music i takon e drejta ekskluzive e vepres (kenges), dhe kushtet e marrveshjes jane te
            percaktuara si ne vijim: </p>
        <p style="f" class="fw-bold my-3">N&euml; k&euml;t&euml; kontrat dy pal&euml;t pajtohen me nenet e sh&euml;nuara m&euml; posht&euml;</p>
        <p style="f">1.1. DHËNIA E TË DREJTAVE. Me n&euml;nshkrimin e k&euml;saj kontrate artisti e jep te drejten e plote per
            perdorimin, botimin, riprodhimin, licesnimin, shperndarjen, performances, publikimin dhe shfaqejen e
            kenges, duke perfshire te gjitha rrjetet sociale, dhe platformat publikuese si Youtube, pa kufizuar
            shkarkimet digjitale,transmetimin dhe kopjet fizike p&euml;r periudhen (vitet ose e perhershme) q&euml; fillon nga
            data e n&euml;nshkrimit t&euml; k&euml;saj kontrate.</p>
        <p style="f"> 2.1. LICENCA EKSKLUZIVE. Artisti pajtohet q&euml; Baresha Music SH.P.K ta ket&euml; t&euml; drejt&euml;n
            ekskluzive p&euml;r eksploatimin e k&euml;ng&euml;s s&euml; cekur n&euml; k&euml;t&euml; marr&euml;veshje. Artisti nuk do t'i jep&euml; asnj&euml; t&euml;
            drejt&euml; pal&euml;s s&euml; tret&euml; q&euml; konfliktojn&euml; me licenc&euml;n ekskluzive q&euml; i jepet Baresh&euml;s n&euml; k&euml;t&euml; marr&euml;veshje.
        </p>
        <p style="f"> 3.1. KUFIZIMI I KANALEVE. Artisti pajtohet q&euml; k&euml;nga do t&euml; ngarkohet dhe l&euml;shohet vet&euml;m n&euml;
            kanalin zyrtar 'Baresha Music' n&euml; platforma si YouTube, Spotify dhe platforma t&euml; tjera p&euml;r transmetim t&euml;
            muzik&euml;s. </p>
        <p style="f">4.1. PËRQINDJA. Pal&euml;t pajtohen n&euml; ndarjen e p&euml;rqindjes n&euml; vler&euml; prej <b>
                <?php echo $perqindja ?>%
            </b>prej t&euml; t&euml; gjitha t&euml; ardhurave t&euml; gjeneruara nga eksploatimi i k&euml;ng&euml;s pas n&euml;nshkrimit t&euml; k&euml;saj
            kontrate dhe publikimit te vepres/kenges. Te ardhurat neto do t&euml; p&euml;rcaktohen si t&euml; gjitha t&euml; ardhurat t&euml;
            marrura nga Baresha nga eksploatimi i k&euml;ng&euml;s, t&euml; zbritura nga kostot direkte q&euml; Baresha nd&euml;rhyn n&euml;
            lidhje me k&euml;t&euml; eksploatim. </p>
        <p style="f">5.1. PREZANTIMET DHE GARANCITË. Artisti prezanton dhe garanton se (i) Artisti &euml;sht&euml; pronari i
            vet&euml;m dhe ekskluziv i regjistrimit. t&euml; tingujve t&euml; Vepres/K&euml;ng&euml;s, (i) asnj&euml; pjes&euml; e K&euml;ng&euml;s nuk do t&euml;
            shkel&euml; t&euml; drejta t&euml; pal&euml;ve t&euml; treta qe nuk jane pjese e kesaj marrveshje dhe Artisti nuk ka b&euml;r&euml;
            marr&euml;veshje t&euml; tjera p&euml;r t&euml; drejta t&euml; K&euml;ng&euml;s q&euml; mund t&euml; pengojn&euml; k&euml;t&euml; Marr&euml;veshje. Baresha Music Sh.p.k.
            ka per obligim qe ne afat prej 24 ore nga data e nenshkrimit te kesaj marrveshje te bej publikimin e
            kenges ne platformat dixhitale. </p>
        <p style="f"> 6.1. PËRMBUSHJA E KUSHTEVE. Artisti pranon q&euml; t&euml; respektoj&euml; rregullat dhe kushtet e k&euml;saj
            Marr&euml;veshjeje dhe t&euml; ndjek&euml; k&euml;rkesat dhe udh&euml;zimet e Baresha Music lidhur me eksploatimin e K&euml;ng&euml;s. N&euml;se
            Artisti shkel ndonj&euml; kusht t&euml; k&euml;saj Marr&euml;veshjeje, Baresha ka t&euml; drejt&euml; t&euml; ndaloj&euml; ose t&euml; nd&euml;rprej&euml;
            eksploatimin e K&euml;ng&euml;s dhe t&euml; k&euml;rkoj&euml; d&euml;mshp&euml;rblim. </p>
        <!-- <div class="page-break"></div> -->
        <p style="f"> 7.1. KOHEZGJATJA DHE NDËRPRERJA. Kega\Vepra behet prone e perhershme e Baresha Music Sh.p.k.
            nga momenti i nenshkrimit te kesaj marrveshje, pervec ne rastet kur mes paleve arrihet nje marrveshje e
            perbashket me kushte te tjera. Palet kane t&euml; drejt&euml; t&euml; nd&euml;rprej&euml; k&euml;t&euml; Marr&euml;veshje pa shkaqe t&euml; arsyeshme
            me njoftim paraprak 7 dite kalendarike nga data fillestare e nenshkrimit dhe publikimit te kesaj
            marrveshje. Njoftimi duhet te behet me shkrim permes mjeteve te komunikimit (email). N&euml; rast nd&euml;rprerjeje
            nga ana e Baresha, t&euml; gjitha t&euml; drejtat kthehen te Artisti dhe Baresha nuk &euml;sht&euml; e detyruar t&euml; paguaj&euml;
            asnj&euml; pages&euml; ose d&euml;mshp&euml;rblim p&euml;r artistin. Palet nuk kane te drejte te kerkoje te drejtat e prones pasi
            te kaloj periudha prej 7 dite e bashkepunimit, nga data e marrveshjes. </p>
        <p style="f"> 8.1. LIGJI I ZBATUESHËM. Kjo Marr&euml;veshje dhe t&euml; gjitha t&euml; drejtat dhe detyrimet e pal&euml;ve n&euml;
            lidhje me k&euml;t&euml; Marr&euml;veshje do t&euml; n&euml;nshtrohen dhe do t&euml; interpretohen n&euml; p&euml;rputhje me ligjet dhe
            rregulloret e shtetit te Kosoves. </p>
        <!-- <div class="page-break"></div> -->
        <p style="f"> <b> Titulli i k&euml;ng&euml;s / vepr&euml;s </b> </p>
        <p style="f" class="border-bottom w-25">
            <?php echo $vepra ?>
        </p>
        <div class="row">
            <div class="col-6">
                <p style="f" class="fw-bold"> Artisti/Pronar i kompozicionit t&euml; muzik&euml;s <br> <span>Emri dhe Mbiemri
                    </span> </p>
                <p class="text-start">
                    <?php echo $emri ?>
                    <?php echo $mbiemri ?>
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
                <p style="f" class="fw-bold text-start"> Nenshkrimi <br> </p>
            </div>
            <div class="col-6">
                <p style="f" class="fw-bold text-end"> Nenshkrimi <br> </p>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                
            </div>
            <div class="col-4 text-center">
                <img src="images/vula.png" style="width: 150px; height: auto;margin-top:-45px">
            </div>
            <!-- <div class="col-4">
                <p style="f" class="border-bottom float-end w-50 text-end"> <img src="signatures/34.png" style="width: 150px; height: auto;"></p>
            </div> -->
        </div>
        <hr>
        <div class="row mt-5">
            <div class="col-12 float-end text-end">
                <p style="">Data e nenshkrimit te marrveshjes :
                    <?php echo $data ?>
                </p>
            </div>
            <div class="my-5 border rounded-5 py-3">
                <h6>Shenime</h6>
                <?php echo $shenime ?>
            </div>
        </div>
    </div>
</div>