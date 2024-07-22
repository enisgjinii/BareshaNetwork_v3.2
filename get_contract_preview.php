<?php
// get_contract_preview.php
// Sanitize and get form data
$emri = filter_input(INPUT_POST, 'emri', FILTER_SANITIZE_STRING);
$mbiemri = filter_input(INPUT_POST, 'mbiemri', FILTER_SANITIZE_STRING);
$numri_personal = filter_input(INPUT_POST, 'numri_personal', FILTER_SANITIZE_STRING);
$emriartistik = filter_input(INPUT_POST, 'emriartistik', FILTER_SANITIZE_STRING);
$vepra = filter_input(INPUT_POST, 'vepra', FILTER_SANITIZE_STRING);
$data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
$perqindja = filter_input(INPUT_POST, 'perqindja', FILTER_SANITIZE_STRING);
$shenime = filter_input(INPUT_POST, 'shenime', FILTER_SANITIZE_STRING);
// Format date
$formatted_date = !empty($data) ? date('d/m/Y', strtotime($data)) : '';
// Generate contract preview HTML
$preview = <<<HTML
<div class="card border-0 shadow-lg">
    <div class="card-header bg-primary text-white text-center py-4">
        <h3 class="mb-0">KONTRATË PËR TË DREJTËN E VEPRËS</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4" role="alert">
            <i class="fas fa-calendar-alt me-2"></i>
            Kjo kontratë u nënshkrua me datë <strong>{$formatted_date}</strong>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Artisti</h5>
                        <p class="card-text">
                            <strong>{$emri} {$mbiemri}</strong><br>
                            Emri artistik: "{$emriartistik}"<br>
                            Numri personal: <strong>{$numri_personal}</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Kompania</h5>
                        <p class="card-text">
                            <strong>Baresha Music SH.P.K.</strong><br>
                            Përfaqësuar nga: Baresha Music
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <h5 class="border-bottom pb-2">Detajet e Veprës</h5>
            <p>Artisti është autori dhe/apo pronari i regjistrimit të tingujve të kompozicionit muzikor të quajtur: <strong class="text-primary">{$vepra}</strong></p>
        </div>
        <div class="mb-4">
            <h5 class="border-bottom pb-2">Kushtet e Marrëveshjes</h5>
            <p>Baresha Music i takon e drejta ekskluzive e veprës (këngës), dhe kushtet e marrëveshjes janë të përcaktuara si në vijim:</p>
            <ol>
                <li class="mb-3">
                DHËNIA E TË DREJTAVE. Me nënshkrimin e kësaj kontrate artisti e jep te drejten e plote per perdorimin, botimin, riprodhimin, licesnimin, shperndarjen, performances, publikimin dhe shfaqejen e kenges, duke perfshire te gjitha rrjetet sociale, dhe platformat publikuese si Youtube, pa kufizuar shkarkimet digjitale,transmetimin dhe kopjet fizike për periudhen (vitet ose e perhershme) që fillon nga data e nënshkrimit të kësaj kontrate.
                </li>
                <li class="mb-3">
                LICENCA EKSKLUZIVE. Artisti pajtohet që Baresha Music SH.P.K ta ketë të drejtën ekskluzive për eksploatimin e këngës së cekur në këtë marrëveshje. Artisti nuk do t'i jepë asnjë të drejtë palës së tretë që konfliktojnë me licencën ekskluzive që i jepet Bareshës në këtë marrëveshje.
                </li>
                <li class="mb-3">
                KUFIZIMI I KANALEVE. Artisti pajtohet që kënga do të ngarkohet dhe lëshohet vetëm në platforma si Youtube, Spotify dhe platforma të tjera për transmetim të muzikës.
                </li>
                <li class="mb-3">
                PËRQINDJA. Palët pajtohen në ndarjen e përqindjes në vlerë prej {$perqindja}%  prej të të gjitha të ardhurave të gjeneruara nga eksploatimi i këngës pas nënshkrimit të kësaj kontrate dhe publikimit te vepres/kenges. Te ardhurat neto do të përcaktohen si të gjitha të ardhurat të marrura nga Baresha nga eksploatimi i këngës, të zbritura nga kostot direkte që Baresha ndërhyn në lidhje me këtë eksploatim.
                </li>
                <li class="mb-3">
                PREZANTIMET DHE GARANCITË. Artisti prezanton dhe garanton se (i) Artisti është pronari i vetëm dhe ekskluziv i regjistrimit. të tingujve të Vepres/Këngës, (i) asnjë pjesë e Këngës nuk do të shkelë të drejta të palëve të treta qe nuk jane pjese e kesaj marrveshje dhe Artisti nuk ka bërë marrëveshje të tjera për të drejta të Këngës që mund të pengojnë këtë Marrëveshje. Baresha Music Sh.p.k. ka per obligim qe ne afat prej 24 ore nga data e nenshkrimit te kesaj marrveshje te bej publikimin e kenges ne platformat dixhitale            
                </li>
                <li class="mb-3">
                    PËRMBUSHJA E KUSHTEVE. Artisti pranon që të respektojë rregullat dhe kushtet e kësaj
                    Marrëveshjeje dhe të ndjekë kërkesat dhe udhëzimet e Baresha Music lidhur me eksploatimin e Këngës. Nëse
                    Artisti shkel ndonjë kusht të kësaj Marrëveshjeje, Baresha ka të drejtë të ndalojë ose të ndërprejë
                    eksploatimin e Këngës dhe të kërkojë dëmshpërblim.
                </li>
            </ol>
        </div>
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-percentage me-2"></i>
            Përqindja e ndarjes së të ardhurave: <strong>{$perqindja}%</strong>
        </div>
        <div class="mb-4">
            <h5 class="border-bottom pb-2">Shënime Shtesë</h5>
            <p class="font-italic">{$shenime}</p>
        </div>
        <div class="row mt-5">
            <div class="col-md-6 text-center">
                <p>____________________________</p>
                <p><strong>{$emri} {$mbiemri}</strong></p>
                <p>Artisti</p>
            </div>
            <div class="col-md-6 text-center">
                <p>____________________________</p>
                <p><strong>Përfaqësuesi i Baresha Music</strong></p>
                <p>Për Baresha Music SH.P.K.</p>
            </div>
        </div>
    </div>
</div>
HTML;
echo $preview;
