<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'conn-d.php';
function fetchContract($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM kontrata WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $contract = $result->fetch_assoc();
    $stmt->close();
    return $contract;
}
$contract = null;
$error = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $contract = fetchContract($conn, $id);
        if (!$contract) {
            $error = "Kontrata me ID të dhënë nuk u gjet.";
        }
    } catch (Exception $e) {
        $error = "Një gabim ka ndodhur gjatë marrjes së kontratës.";
        error_log("Error fetching contract: " . $e->getMessage());
    }
} else {
    $error = "ID e kontratës është e pavlefshme ose mungon.";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontrata me Baresha Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <style>
        /* Base Styles */
        * {
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            /* Further reduced font size */
            line-height: 1.3;
            /* Tighter line height */
        }
        .contract-container {
            background: #ffffff;
            padding: 20px;
            /* Further reduced padding */
            border: 1px solid #ccc;
            /* Simplified border */
            margin-bottom: 10px;
            /* Further reduced margin */
        }
        .contract-header {
            text-align: center;
            margin-bottom: 15px;
            /* Further reduced margin */
        }
        .contract-header img {
            width: 60px;
            /* Smaller logo */
            margin-bottom: 5px;
        }
        .contract-title {
            font-size: 18px;
            /* Further reduced title size */
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .contract-section {
            margin-bottom: 8px;
            /* Further reduced spacing between sections */
        }
        .contract-section p {
            color: #34495e;
            margin-bottom: 4px;
            /* Further reduced paragraph spacing */
        }
        .contract-section p strong {
            color: #2c3e50;
        }
        .signature-section {
            margin-top: 15px;
            /* Further reduced margin */
        }
        .signature-section img {
            width: 80px;
            /* Further reduced signatures */
            height: auto;
        }
        /* Hide non-essential elements on screen */
        .no-print {
            display: block;
        }
        /* Print Styles */
        @media print {
            @page {
                margin: 0;
                /* Remove default page margins */
            }
            body {
                margin: 0;
                /* Remove default body margins */
                padding: 0;
                font-size: 10px;
                /* Further reduced font size */
                color: #000;
                background: none;
            }
            .contract-container {
                padding: 15px;
                /* Further reduced padding */
                border: none;
                box-shadow: none;
                margin-bottom: 0;
            }
            .contract-title {
                font-size: 16px;
                /* Further reduced title size */
            }
            .contract-section {
                margin-bottom: 6px;
            }
            .contract-section p {
                margin-bottom: 3px;
            }
            .signature-section {
                margin-top: 10px;
            }
            .signature-section img {
                width: 60px;
            }
            /* Hide non-essential elements */
            .no-print {
                display: none;
            }
            /* Adjust page breaks */
            .contract-container {
                page-break-after: always;
            }
            /* Ensure images fit within the page */
            img {
                max-width: 100%;
                height: auto;
            }
        }
    </style>
    <script>
        window.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key.toLowerCase() === 'p') {
                event.preventDefault();
                window.print();
            }
        });
    </script>
</head>
<body>
    <?php if ($error): ?>
        <div class="container my-3">
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
    <?php elseif ($contract): ?>
        <div class="container my-3">
            <div class="contract-container">
                <div class="contract-header">
                    <img src="images/brand-icon.png" alt="Brand Icon">
                    <div class="contract-title">Kontratë për të Drejtën e Vepres</div>
                </div>
                <div class="contract-section">
                    <p>Kjo kontratë u nënshkrua me datë <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($contract['data']))); ?></strong> midis
                        <strong><?php echo htmlspecialchars($contract['emri'] . ' ' . $contract['mbiemri']); ?>, ("<?php echo htmlspecialchars($contract['emriartistik']); ?>")</strong>
                        dhe Baresha Music ("Baresha Music SH.P.K.").
                    </p>
                    <p>Numri personal: <strong><?php echo htmlspecialchars($contract['numri_personal']); ?></strong></p>
                    <p>Artisti është autori dhe/apo pronari i regjistrimit të tingujve të kompozicionit muzikor të quajtur <strong><?php echo htmlspecialchars($contract['vepra']); ?></strong>.</p>
                    <p>Baresha Music i takon e drejta ekskluzive e vepres (kenges), dhe kushtet e marrëveshjes janë të përcaktuara si në vijim:</p>
                </div>
                <div class="contract-section">
                    <?php
                    $laws = [
                        "DHËNIA E TË DREJTAVE. Me nënshkrimin e kësaj kontrate artisti e jep të drejtën e plotë për përdorimin, botimin, riprodhimin, licesnimin, shpërndarjen, performancat, publikimin dhe shfaqjen e kenges, duke përfshirë të gjitha rrjetet sociale, dhe platformat publikuese si Youtube, pa kufizuar shkarkimet digjitale, transmetimin dhe kopjet fizike për periudhën (vitet ose e përhershme) që fillon nga data e nënshkrimit të kësaj kontrate.",
                        "LICENCA EKSKLUZIVE. Artisti pajtohet që Baresha Music SH.P.K ta ketë të drejtën ekskluzive për eksploatimin e këngës së caktuar në këtë marrëveshje. Artisti nuk do të japë asnjë të drejtë palës së tretë që konfliktojnë me licencën ekskluzive që i jepet Bareshës në këtë marrëveshje.",
                        "KUFIZIMI I KANALEVE. Artisti pajtohet që kënga do të ngarkohet dhe lëshohet vetëm në kanalin zyrtar 'Baresha Music' në platforma si YouTube, Spotify dhe platforma të tjera për transmetim të muzikës.",
                        "PËRQINDJA. Palët pajtohen në ndarjen e përqindjes në vlerë prej <strong>" . htmlspecialchars($contract['perqindja']) . "%</strong> prej të gjitha të ardhurave të gjeneruara nga eksploatimi i këngës pas nënshkrimit të kësaj kontrate dhe publikimit të vepres/kenges. Të ardhurat neto do të përcaktohen si të gjitha të ardhurat e marra nga Baresha nga eksploatimi i këngës, të zbritura nga kostot direkte që Baresha ndërhyjnë në lidhje me këtë eksploatim.",
                        "PREZANTIMET DHE GARANCITË. Artisti prezanton dhe garanton se (i) Artisti është pronari i vetëm dhe ekskluziv i regjistrimit të tingujve të Vepres/Këngës, (ii) asnjë pjesë e Këngës nuk do të shkelë të drejta të palëve të treta që nuk janë pjesë e kësaj marrëveshje dhe Artisti nuk ka bërë marrëveshje të tjera për të drejta të Këngës që mund të pengojnë këtë Marrëveshje. Baresha Music Sh.p.k. ka për obligim që në afat prej 24 ore nga data e nënshkrimit të kësaj marrëveshjeje të bëjë publikimin e këngës në platformat dixhitale.",
                        "PËRMBUSHJA E KUSHTEVE. Artisti pranon që të respektojë rregullat dhe kushtet e kësaj Marrëveshjeje dhe të ndjekë kërkesat dhe udhëzimet e Baresha Music lidhur me eksploatimin e Këngës. Nëse Artisti shkel ndonjë kusht të kësaj Marrëveshjeje, Baresha ka të drejtë të ndalojë ose të ndërpretë eksploatimin e Këngës dhe të kërkojë dëmshpërblim.",
                        "KOHEZGJATJA DHE NDËRPRERJA. Kënga/Vepra bëhet pronë e përhershme e Baresha Music Sh.p.k. nga momenti i nënshkrimit të kësaj marrëveshjeje, përveç në raste kur mes palëve arrin një marrëveshje e përbashkët me kushte të tjera. Palët kanë të drejtë të ndërpresin këtë Marrëveshje pa shkak të arsyeshëm me njoftim paraprak 7 ditë kalendarike nga data fillestare e nënshkrimit dhe publikimit të kësaj marrëveshjeje. Njoftimi duhet të bëhet me shkrim përmes mjeteve të komunikimit (email). Në rast ndërprerjeje nga ana e Baresha, të gjitha të drejtat kthehen te Artisti dhe Baresha nuk është e detyruar të paguajë asnjë pagesë ose dëmshpërblim për artistin. Palët nuk kanë të drejtë të kërkojnë të drejtat e pronës pasi të kalojë periudha prej 7 dite bashkëpunimi, nga data e marrëveshjes.",
                        "LIGJI I ZBATUESHËM. Kjo Marrëveshje dhe të gjitha të drejtat dhe detyrimet e palëve në lidhje me këtë Marrëveshje do të nënshkrohen dhe do të interpretohen në përputhje me ligjet dhe rregulloret e shtetit të Kosovës."
                    ];
                    foreach ($laws as $index => $law) {
                        echo "<p><strong>" . ($index + 1) . ".</strong> " . $law . "</p>";
                    }
                    ?>
                </div>
                <div class="contract-section">
                    <p><strong>Titulli i këngës / veprës:</strong></p>
                    <p class="border-bottom w-50"><?php echo htmlspecialchars($contract['vepra']); ?></p>
                </div>
                <div class="signature-section row">
                    <div class="col-6 text-center">
                        <p><strong>Artisti/Pronar i kompozicionit të muzikës</strong></p>
                        <p><?php echo htmlspecialchars($contract['emri'] . ' ' . $contract['mbiemri']); ?></p>
                        <p><strong>Nënshkrimi:</strong></p>
                        <p class="border-bottom">
                            <?php
                            $file_path = $contract['nenshkrimi'];
                            if ($file_path && file_exists($file_path)) {
                                echo '<img src="' . htmlspecialchars($file_path) . '" alt="Nënshkrimi" style="width: 80px; height: auto;">';
                            } else {
                                echo 'Nënshkrimi nuk është caktuar.';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-6 text-center">
                        <p><strong>Pronar i të drejtave ekskluzive të eksploatimit të kompozicionit të muzikës</strong></p>
                        <p>Baresha Music Sh.p.k.</p>
                        <p><strong>Nënshkrimi:</strong></p>
                        <p class="border-bottom">
                            <img src="signatures/34.png" alt="Baresha Signature" style="width: 80px; height: auto;">
                        </p>
                    </div>
                </div>
                <div class="contract-section mt-2">
                    <p><strong>Data e nënshkrimit të marrëveshjes:</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($contract['data']))); ?></p>
                    <?php if (!empty(trim($contract['shenim']))): ?>
                        <div class="border rounded p-2 mt-1 bg-light">
                            <h6>Shënime</h6>
                            <p><?php echo nl2br(htmlspecialchars($contract['shenim'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($contract['pdf_file']): ?>
                    <div class="contract-section">
                        <p><strong>Konkretizo PDF-në e Kontratës:</strong></p>
                        <a href="<?php echo htmlspecialchars($contract['pdf_file']); ?>" target="_blank" class="btn btn-primary no-print" style="padding: 2px 8px; font-size: 10px;">Shkarko Kontratën (PDF)</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>
</html>