<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'conn-d.php';

function sanitizeInput($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateToken($conn, $token)
{
    $currentTime = time();
    $stmt = $conn->prepare("SELECT * FROM tokens WHERE token = ? AND expiration_time >= ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("si", $token, $currentTime);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $isValid = $result->num_rows > 0;
    $stmt->close();
    return $isValid;
}

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

function updateSignature($conn, $id, $signatureData)
{
    if (!file_exists('signatures/')) {
        mkdir('signatures/', 0755, true);
    }
    $decodedData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
    if ($decodedData === false) {
        throw new Exception("Invalid signature data.");
    }
    $fileName = 'signatures/signature_' . $id . '_' . time() . '.png';
    if (file_put_contents($fileName, $decodedData) === false) {
        throw new Exception("Failed to save signature image.");
    }
    $stmt = $conn->prepare("UPDATE kontrata SET nenshkrimi = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("si", $fileName, $id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $stmt->close();
    return true;
}

function deleteToken($conn, $token)
{
    $stmt = $conn->prepare("DELETE FROM tokens WHERE token = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("s", $token);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $stmt->close();
    return true;
}

function sendEmail($selectedStafi, $emailOfClient, $emriJuaj, $dataProblemit, $message)
{
    $staff = [
        'afrim' => [
            'email' => 'gjinienis148@gmail.com',
            'name' => 'Afrim Kolgeci (CEO)'
        ],
        'enis' => [
            'email' => 'egjini17@gmail.com',
            'name' => 'Enis Gjini (Zhvillues i uebit)'
        ],
        'lyon' => [
            'email' => 'enisgjini11@gmail.com',
            'name' => 'Lyon Cacaj (Dizajner)'
        ]
    ];

    if (!array_key_exists($selectedStafi, $staff)) {
        throw new Exception("Invalid staff selection.");
    }

    $email = $staff[$selectedStafi]['email'];
    $stafiName = $staff[$selectedStafi]['name'];

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
            "X-RapidAPI-Key: YOUR_RAPIDAPI_KEY_HERE",
            "content-type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        throw new Exception("cURL Error #: " . $err);
    } else {
        return true;
    }
}

$token = isset($_GET['token']) ? sanitizeInput($_GET['token']) : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$contract = null;
$errors = [];
$successMessage = "";

try {
    if (!$token) {
        throw new Exception("Token nuk është caktuar!");
    }

    if (validateToken($conn, $token)) {
        if (!$id) {
            throw new Exception("ID nuk është caktuar!");
        }

        $contract = fetchContract($conn, $id);
        if (!$contract) {
            throw new Exception("Nuk u gjet asnjë rresht me këtë ID!");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signatureData'])) {
            $signatureData = sanitizeInput($_POST['signatureData']);
            if ($signatureData) {
                updateSignature($conn, $id, $signatureData);
                $successMessage = "Nënshkrimi u azhurnua me sukses!";
            } else {
                throw new Exception("Nënshkrimi është bosh!");
            }
        }
    } else {
        deleteToken($conn, $token);
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit2'])) {
    try {
        $selectedStafi = sanitizeInput($_POST['stafi']);
        $emailOfClient = filter_var($_POST['emails'], FILTER_VALIDATE_EMAIL);
        $emriJuaj = sanitizeInput($_POST['emriJuaj']);
        $dataProblemit = sanitizeInput($_POST['dataProblemit']);
        $message = sanitizeInput($_POST['message']);

        if (!$emailOfClient) {
            throw new Exception("Email-i i futur nuk është valid!");
        }

        sendEmail($selectedStafi, $emailOfClient, $emriJuaj, $dataProblemit, $message);
        $successMessage = "Email u dërgua me sukses!";
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
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
        * {
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }

        #expire-message {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        }

        .contract-container {
            background: #ffffff;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
            padding: 50px;
            border-radius: 25px;
            border: 2px solid #e0e0e0;
        }

        .contract-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .contract-header img {
            width: 120px;
            margin-bottom: 20px;
        }

        .contract-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .contract-section {
            margin-bottom: 30px;
        }

        .contract-section p {
            line-height: 1.8;
            color: #34495e;
        }

        .contract-section p strong {
            color: #2c3e50;
        }

        .signature-section {
            margin-top: 60px;
        }

        .signature-section img {
            width: 220px;
            height: auto;
        }

        .form-section {
            background: #f0f8ff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-section h5 {
            margin-bottom: 30px;
            color: #2c3e50;
            font-weight: bold;
            font-size: 20px;
        }

        @media print {
            body {
                visibility: hidden;
                display: none;
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
    <?php if ($successMessage): ?>
        <div class="alert alert-success text-center m-3" role="alert">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="container my-3">
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($contract && validateToken($conn, $token)): ?>
        <div class="container my-5">
            <div class="contract-container">
                <div class="contract-header">
                    <img src="images/brand-icon.png" alt="Brand Icon">
                    <div class="contract-title">KONTRATË PËR TË DREJTËN E VEPRËS</div>
                </div>
                <div class="contract-section">
                    <p>Kjo kontrat&euml; u n&euml;nshkrua me dat&euml;
                       <b> <?php echo date('d/m/Y', strtotime($contract['data'])); ?> </b> midis
                       <b> <?php echo htmlspecialchars($contract['emri'] . ' ' . $contract['mbiemri']); ?>, ("<?php echo htmlspecialchars($contract['emriartistik']); ?>")</b> dhe Baresha Music ("Baresha Music SH.P.K.").
                    </p>
                    <p>Numri personal: <b><?php echo htmlspecialchars($contract['numri_personal']); ?></b></p>
                    <p>Artisti &euml;sht&euml; autori dhe/apo pronari i regjistrimit t&euml; tingujve t&euml; kompozicionit muzikor t&euml; quajtur <strong><?php echo htmlspecialchars($contract['vepra']); ?></strong>.</p>
                    <p>Baresha Music i takon e drejta ekskluzive e vepres (kenges), dhe kushtet e marrveshjes jane te percaktuara si ne vijim:</p>
                </div>
                <div class="contract-section">
                    <?php
                    $laws = [
                        "DHËNIA E TË DREJTAVE. Me n&euml;nshkrimin e k&euml;saj kontrate artisti e jep te drejten e plote per perdorimin, botimin, riprodhimin, licesnimin, shperndarjen, performances, publikimin dhe shfaqejen e kenges, duke perfshire te gjitha rrjetet sociale, dhe platformat publikuese si Youtube, pa kufizuar shkarkimet digjitale, transmetimin dhe kopjet fizike p&euml;r periudhen (vitet ose e perhershme) q&euml; fillon nga data e n&euml;nshkrimit t&euml; k&euml;saj kontrate.",
                        "LICENCA EKSKLUZIVE. Artisti pajtohet q&euml; Baresha Music SH.P.K ta ket&euml; t&euml; drejt&euml;n ekskluzive p&euml;r eksploatimin e k&euml;ng&euml;s s&euml; cekur n&euml; k&euml;t&euml; marr&euml;veshje. Artisti nuk do t'i jep&euml; asnj&euml; t&euml; drejt&euml; pal&euml;s s&euml; tret&euml; q&euml; konfliktojn&euml; me licenc&euml;n ekskluzive q&euml; i jepet Baresh&euml;s n&euml; k&euml;t&euml; marr&euml;veshje.",
                        "KUFIZIMI I KANALEVE. Artisti pajtohet q&euml; k&euml;nga do t&euml; ngarkohet dhe l&euml;shohet vet&euml;m n&euml; kanalin zyrtar 'Baresha Music' n&euml; platforma si YouTube, Spotify dhe platforma t&euml; tjera p&euml;r transmetim t&euml; muzik&euml;s.",
                        "PËRQINDJA. Pal&euml;t pajtohen n&euml; ndarjen e p&euml;rqindjes n&euml; vler&euml; prej " . htmlspecialchars($contract['perqindja']) . "% prej t&euml; t&euml; gjitha t&euml; ardhurave t&euml; gjeneruara nga eksploatimi i k&euml;ng&euml;s pas n&euml;nshkrimit t&euml; k&euml;saj kontrate dhe publikimit te vepres/kenges. Te ardhurat neto do t&euml; p&euml;rcaktohen si t&euml; gjitha t&euml; ardhurat t&euml; marrura nga Baresha nga eksploatimi i k&euml;ng&euml;s, t&euml; zbritura nga kostot direkte q&euml; Baresha nd&euml;rhyn n&euml; lidhje me k&euml;t&euml; eksploatim.",
                        "PREZANTIMET DHE GARANCITË. Artisti prezanton dhe garanton se (i) Artisti &euml;sht&euml; pronari i vet&euml;m dhe ekskluziv i regjistrimit t&euml; tingujve t&euml; Vepres/K&euml;ng&euml;s, (i) asnj&euml; pjes&euml; e K&euml;ng&euml;s nuk do t&euml; shkel&euml; t&euml; drejta t&euml; pal&euml;ve t&euml; treta qe nuk jane pjese e kesaj marrveshje dhe Artisti nuk ka b&euml;r&euml; marr&euml;veshje t&euml; tjera p&euml;r t&euml; drejta t&euml; K&euml;ng&euml;s q&euml; mund t&euml; pengojn&euml; k&euml;t&euml; Marr&euml;veshje. Baresha Music Sh.p.k. ka per obligim qe ne afat prej 24 ore nga data e nenshkrimit te kesaj marrveshje te bej publikimin e kenges ne platformat dixhitale.",
                        "PËRMBUSHJA E KUSHTEVE. Artisti pranon q&euml; t&euml; respektoj&euml; rregullat dhe kushtet e k&euml;saj Marr&euml;veshjeje dhe t&euml; ndjek&euml; k&euml;rkesat dhe udh&euml;zimet e Baresha Music lidhur me eksploatimin e K&euml;ng&euml;s. N&euml;se Artisti shkel ndonj&euml; kusht t&euml; k&euml;saj Marr&euml;veshjeje, Baresha ka t&euml; drejt&euml; t&euml; ndaloj&euml; ose t&euml; nd&euml;rprej&euml; eksploatimin e K&euml;ng&euml;s dhe t&euml; k&euml;rkoj&euml; d&euml;mshp&euml;rblim.",
                        "KOHEZGJATJA DHE NDËRPRERJA. Kega\Vepra behet prone e perhershme e Baresha Music Sh.p.k. nga momenti i nenshkrimit te kesaj marrveshje, pervec ne rastet kur mes paleve arrihet nje marrveshje e perbashket me kushte te tjera. Palet kane t&euml; drejt&euml; t&euml; nd&euml;rprej&euml; k&euml;t&euml; Marr&euml;veshje pa shkaqe t&euml; arsyeshme me njoftim paraprak 7 dite kalendarike nga data fillestare e nenshkrimit dhe publikimit te kesaj marrveshje. Njoftimi duhet te behet me shkrim permes mjeteve te komunikimit (email). N&euml; rast nd&euml;rprerjeje nga ana e Baresha, t&euml; gjitha t&euml; drejtat kthehen te Artisti dhe Baresha nuk &euml;sht&euml; e detyruar t&euml; paguaj&euml; asnj&euml; pages&euml; ose d&euml;mshp&euml;rblim p&euml;r artistin. Palet nuk kane te drejte te kerkoje te drejtat e prones pasi te kaloj periudha prej 7 dite e bashkepunimit, nga data e marrveshjes.",
                        "LIGJI I ZBATUESHËM. Kjo Marr&euml;veshje dhe t&euml; gjitha t&euml; drejtat dhe detyrimet e pal&euml;ve n&euml; lidhje me k&euml;t&euml; Marr&euml;veshje do t&euml; n&euml;nshtrohen dhe do t&euml; interpretohen n&euml; p&euml;rputhje me ligjet dhe rregulloret e shtetit te Kosoves."
                    ];

                    foreach ($laws as $index => $law) {
                        echo "<p><strong>" . ($index + 1) . ".</strong> " . $law . "</p>";
                    }
                    ?>
                </div>
                <div class="contract-section">
                    <p><strong>Titulli i k&euml;ng&euml;s / vepr&euml;s:</strong></p>
                    <p class="border-bottom w-25"><?php echo htmlspecialchars($contract['vepra']); ?></p>
                </div>
                <div class="signature-section row">
                    <div class="col-md-6 text-center">
                        <p><strong>Artisti/Pronar i kompozicionit t&euml; muzik&euml;s</strong></p>
                        <p><?php echo htmlspecialchars($contract['emri'] . ' ' . $contract['mbiemri']); ?></p>
                        <p><strong>Nënshkrimi:</strong></p>
                        <p class="border-bottom">
                            <?php
                            $file_path = $contract['nenshkrimi'];
                            if ($file_path && file_exists($file_path)) {
                                echo '<img src="' . htmlspecialchars($file_path) . '" alt="Nënshkrimi" style="width: 220px; height: auto;">';
                            } else {
                                echo 'Nënshkrimi nuk është caktuar.';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-center">
                        <p><strong>Pronar i t&euml; drejtave ekskluzive t&euml; eksploatimit t&euml; kompozicionit t&euml; muzik&euml;s</strong></p>
                        <p>Baresha Music Sh.p.k.</p>
                        <p><strong>Nënshkrimi:</strong></p>
                        <p class="border-bottom">
                            <img src="signatures/34.png" alt="Baresha Signature" style="width: 220px; height: auto;">
                        </p>
                    </div>
                </div>
                <div class="contract-section mt-4">
                    <p><strong>Data e nënshkrimit të marrëveshjes:</strong> <?php echo htmlspecialchars($contract['data']); ?></p>
                    <?php if (!empty(trim($contract['shenim']))): ?>
                        <div class="border rounded p-4 mt-3 bg-light">
                            <h5>Shënime</h5>
                            <p><?php echo nl2br(htmlspecialchars($contract['shenim'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (empty($contract['nenshkrimi'])): ?>
                    <div class="form-section mt-4">
                        <h5>Vendosni Nënshkrimin</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <canvas id="signature" width="400" height="200" class="border rounded"></canvas>
                                <input type="hidden" name="signatureData" id="signatureData">
                            </div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-paper-plane"></i> Dërgo
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearSignaturePad()">
                                <i class="fas fa-sync-alt"></i> Fshij
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center mt-4" role="alert">
                        Kontrata është tashmë nënshkruar.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container" id="expire-message">
            <div class="card p-5 text-center">
                <img src="images/icons8-query-94.png" alt="Error" width="120px">
                <h3 class="mt-3">Tokeni ka skaduar!</h3>
                <p>Kërcenjë nj&euml; koh&euml; t&euml; re duke kontaktuar stafit të Baresha Network.</p>
                <div class="form-section mt-4">
                    <h5>Kontakt Stafin</h5>
                    <form action="" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="stafi" class="form-label">Zgjidh Stafin</label>
                                <select name="stafi" id="stafi" class="form-select" required>
                                    <option value="">Zgjidhni...</option>
                                    <option value="afrim">Afrim Kolgeci (CEO)</option>
                                    <option value="enis">Enis Gjini (Zhvillues i uebit)</option>
                                    <option value="lyon">Lyon Cacaj (Dizajner)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dataProblemit" class="form-label">Data e Problemit</label>
                                <input type="date" class="form-control" id="dataProblemit" name="dataProblemit" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emails" class="form-label">Email Adresa</label>
                                <input type="email" class="form-control" id="emails" name="emails" required>
                            </div>
                            <div class="col-md-6">
                                <label for="emriJuaj" class="form-label">Emri Juaj</label>
                                <input type="text" class="form-control" id="emriJuaj" name="emriJuaj" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Mesazhi</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit2">Dërgo Email</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>
        const canvas = document.getElementById('signature');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgba(0, 0, 0, 1)'
        });

        function clearSignaturePad() {
            signaturePad.clear();
        }

        document.querySelector('.contract-container form')?.addEventListener('submit', function(event) {
            const signatureInput = document.getElementById('signatureData');
            if (signaturePad.isEmpty()) {
                alert("Ju lutem nënshkruani kontratën para se ta dërgoni.");
                event.preventDefault();
            } else {
                const signatureData = signaturePad.toDataURL('image/png');
                signatureInput.value = signatureData;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>
