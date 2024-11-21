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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Georgia&family=Merriweather:wght@700&family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- MDB UI Kit -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <style>
        /* Base Styles */
        body {
            font-family: 'Georgia', serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        .contract-container {
            background: #ffffff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        /* Header */
        .contract-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .contract-header img {
            width: 100px;
            margin-bottom: 15px;
        }

        .contract-title {
            font-family: 'Merriweather', serif;
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .contract-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        /* Sections */
        .contract-section {
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .contract-section p {
            margin-bottom: 15px;
            font-size: 16px;
        }

        .contract-section p strong {
            color: #2c3e50;
        }

        /* Clauses */
        .contract-section ol {
            padding-left: 20px;
        }

        .contract-section ol li {
            margin-bottom: 15px;
            font-size: 16px;
        }

        /* Signatures */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #ccc;
            padding-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-box img {
            width: 200px;
            height: auto;
            margin-top: 10px;
        }

        .signature-box p {
            margin-top: 15px;
            font-weight: 500;
            font-size: 16px;
        }

        /* Footer */
        .contract-footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #7f8c8d;
        }

        /* Button */
        .btn-download {
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #2c3e50;
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background-color: #34495e;
            text-decoration: none;
            color: #ffffff;
        }

        .btn-download i {
            margin-right: 8px;
        }

        /* Alert Styles */
        .alert-custom {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
        }

        /* Print Styles */
        @media print {
            body {
                background: none;
                padding: 0;
            }

            .contract-container {
                box-shadow: none;
                border: none;
                padding: 20px;
                max-width: 100%;
                margin: 0;
            }

            .contract-header img {
                width: 80px;
                /* Reduce logo size */
                margin-bottom: 10px;
            }

            .contract-title {
                font-size: 24px;
                /* Reduce title size */
                letter-spacing: 1px;
            }

            .contract-subtitle {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .contract-section {
                margin-bottom: 15px;
                line-height: 1.4;
            }

            .contract-section p {
                margin-bottom: 10px;
                font-size: 14px;
            }

            .contract-section ol li {
                margin-bottom: 10px;
                font-size: 14px;
            }

            .signature-section {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #ccc;
            }

            .signature-box {
                width: 45%;
            }

            .signature-box img {
                width: 150px;
                /* Reduce signature size */
            }

            .signature-box p {
                margin-top: 10px;
                font-size: 14px;
            }

            .contract-footer {
                display: none;
                /* Hide footer */
            }

            .btn-download {
                display: none;
                /* Hide download button */
            }

            /* Adjust page breaks */
            .contract-container {
                page-break-after: always;
            }

            /* Remove unnecessary padding and margins */
            .contract-container,
            .contract-header,
            .contract-section,
            .signature-section {
                padding: 0;
                margin: 0;
            }

            /* Ensure images fit within the page */
            img {
                max-width: 100%;
                height: auto;
            }
        }
    </style>
    <script>
        // Prevent default print shortcut and trigger print
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
        <div class="alert alert-danger text-center alert-custom" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($contract): ?>
        <div class="contract-container">
            <!-- Header -->
            <div class="contract-header">
                <img src="images/brand-icon.png" alt="Brand Icon">
                <div class="contract-title">Kontratë për të Drejtën e Vepres</div>
                <div class="contract-subtitle">Baresha Music SH.P.K.</div>
            </div>
            <!-- Contract Details -->
            <div class="contract-section">
                <p>Kjo kontratë u nënshkrua më datë <strong><?php echo htmlspecialchars(date('d/m/Y', strtotime($contract['data']))); ?></strong> midis
                    <strong><?php echo htmlspecialchars($contract['emri'] . ' ' . $contract['mbiemri']); ?>, ("<?php echo htmlspecialchars($contract['emriartistik']); ?>")</strong>
                    dhe Baresha Music ("Baresha Music SH.P.K.").
                </p>
                <p>Numri personal: <strong><?php echo htmlspecialchars($contract['numri_personal']); ?></strong></p>
                <p>Artisti është autori dhe/apo pronari i regjistrimit të tingujve të kompozicionit muzikor të quajtur <strong><?php echo htmlspecialchars($contract['vepra']); ?></strong>.</p>
                <p>Baresha Music i takon e drejta ekskluzive e vepres (kenges), dhe kushtet e marrëveshjes janë të përcaktuara si në vijim:</p>
            </div>
            <!-- Clauses -->
            <div class="contract-section">
                <ol>
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
                        echo "<li>" . $law . "</li>";
                    }
                    ?>
                </ol>
            </div>
            <!-- Song Title -->
            <div class="contract-section">
                <p><strong>Titulli i këngës / veprës:</strong></p>
                <p class="border-bottom w-50"><?php echo htmlspecialchars($contract['vepra']); ?></p>
            </div>
            <!-- Signatures -->
            <div class="signature-section">
                <div class="signature-box">
                    <p><strong>Artisti/Pronar i kompozicionit të muzikës</strong></p>
                    <p><?php echo htmlspecialchars($contract['emri'] . ' ' . $contract['mbiemri']); ?></p>
                    <p><strong>Nënshkrimi:</strong></p>
                    <?php
                    $file_path = $contract['nenshkrimi'];
                    if ($file_path && file_exists($file_path)) {
                        echo '<img src="' . htmlspecialchars($file_path) . '" alt="Nënshkrimi">';
                    } else {
                        echo '<p>Nënshkrimi nuk është caktuar.</p>';
                    }
                    ?>
                </div>
                <div class="signature-box">
                    <p><strong>Pronar i të drejtave ekskluzive të eksploatimit të kompozicionit të muzikës</strong></p>
                    <p>Baresha Music Sh.p.k.</p>
                    <p><strong>Nënshkrimi:</strong></p>
                    <img src="signatures/34.png" alt="Baresha Signature">
                </div>
            </div>
            <!-- Additional Details -->
            <div class="contract-section mt-4">
                <p><strong>Data e nënshkrimit të marrëveshjes:</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($contract['data']))); ?></p>
                <?php if (!empty(trim($contract['shenim']))): ?>
                    <div class="border rounded p-4 mt-3 bg-light">
                        <h5>Shënime</h5>
                        <p><?php echo nl2br(htmlspecialchars($contract['shenim'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Download PDF -->
            <?php if ($contract['pdf_file']): ?>
                <div class="contract-section mt-3">
                    <a href="<?php echo htmlspecialchars($contract['pdf_file']); ?>" target="_blank" class="btn-download no-print">
                        <i class="fas fa-download"></i> Shkarko Kontratën (PDF)
                    </a>
                </div>
            <?php endif; ?>
            <!-- Footer -->
            <div class="contract-footer">
                &copy; <?php echo date('Y'); ?> Baresha Music SH.P.K. Të gjitha të drejtat e rezervuara.
            </div>
        </div>
    <?php endif; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- MDB UI Kit JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>