<?php
// config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
require_once 'conn-d.php';

// Constants
define('TOKEN_EXPIRATION_BUFFER', 60); // Buffer in seconds if needed
define('SENDGRID_API_KEY', 'YOUR_SENDGRID_API_KEY'); // Replace with your actual SendGrid API Key
define('SENDGRID_API_HOST', 'rapidprod-sendgrid-v1.p.rapidapi.com');

// Utility Functions
function validateToken($conn, $token)
{
    $sql = "SELECT * FROM tokens WHERE token = ? AND expiration_time >= ?";
    $stmt = $conn->prepare($sql);
    $currentTime = time();
    $stmt->bind_param("si", $token, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function fetchContract($conn, $id)
{
    $sql = "SELECT * FROM kontrata WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateSignature($conn, $id, $signatureData)
{
    $sql = "UPDATE kontrata SET nenshkrimi = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $signatureData, $id);
    return $stmt->execute();
}

function deleteToken($conn, $token)
{
    $sql = "DELETE FROM tokens WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    return $stmt->execute();
}

function sendSupportEmail($data)
{
    $stafiEmails = [
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
        ],
    ];

    $selectedStafi = $data['stafi'];
    if (!array_key_exists($selectedStafi, $stafiEmails)) {
        return false;
    }

    $emailDetails = [
        "personalizations" => [
            [
                "to" => [
                    ["email" => $stafiEmails[$selectedStafi]['email']]
                ],
                "subject" => "Form Submission"
            ]
        ],
        "from" => [
            "email" => "bot-teknik@bot.com"
        ],
        "content" => [
            [
                "type" => "text/plain",
                "value" => "Stafi: {$stafiEmails[$selectedStafi]['name']}\nEmail: {$data['emails']}\nEmri Juaj: {$data['emriJuaj']}\nData e problemit: {$data['dataProblemit']}\nMessage: {$data['message']}"
            ]
        ]
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://" . SENDGRID_API_HOST . "/mail/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($emailDetails),
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: " . SENDGRID_API_HOST,
            "X-RapidAPI-Key: " . SENDGRID_API_KEY,
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        // Log the error or handle accordingly
        return false;
    }

    return true;
}

// Handle POST Requests
$errors = [];
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['signatureData'])) {
        // Handle Signature Update
        $signatureData = $_POST['signatureData'] ?? '';
        $id = $_GET['id'] ?? null;

        if ($id) {
            if (updateSignature($conn, $id, $signatureData)) {
                $successMessage = "Nenshkrimi u azhurnua me sukses!";
            } else {
                $errors[] = "Gabim në azhurnimin e nenshkrimit: " . $conn->error;
            }
        } else {
            $errors[] = "ID nuk është caktuar!";
        }
    } elseif (isset($_POST['submit2'])) {
        // Handle Support Email
        $requiredFields = ['stafi', 'emails', 'emriJuaj', 'dataProblemit', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Fusha {$field} është e nevojshme.";
            }
        }

        if (empty($errors)) {
            if (sendSupportEmail($_POST)) {
                $successMessage = "Email sent successfully!";
                // Redirect to avoid form resubmission
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&token=" . $_GET['token']);
                exit();
            } else {
                $errors[] = "Gabim në dërgimin e email-it.";
            }
        }
    }
}

// Validate Token
$token = $_GET['token'] ?? '';
$tokenData = validateToken($conn, $token);

if (!$tokenData) {
    // Invalid or expired token, delete it
    if ($token) {
        deleteToken($conn, $token);
    }
    $tokenValid = false;
} else {
    $tokenValid = true;
    $contractId = $_GET['id'] ?? null;
    if ($contractId) {
        $contract = fetchContract($conn, $contractId);
        if (!$contract) {
            $errors[] = "Nuk u gjet asnjë rresht me këtë ID!";
        }
    } else {
        $errors[] = "ID nuk është caktuar!";
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

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <!-- Fav Icon -->
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

        /* Hide content when printing */
        @media print {
            body {
                visibility: hidden;
                display: none;
            }
        }
    </style>
    <script>
        // Disable printing when Ctrl + P is pressed
        window.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault(); // Prevent default print behavior
                window.print(); // Open print dialog
            }
        });
    </script>
</head>

<body>
    <?php if ($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($successMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="container my-3">
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($tokenValid && isset($contract)): ?>
        <div class="container my-5">
            <div class="py-5 px-5">
                <!-- SVG Header -->
                <svg width="100%" height="100%" viewBox="0 0 1440 390" xmlns="http://www.w3.org/2000/svg" class="transition duration-300 ease-in-out delay-150">
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="50%" x2="100%" y2="50%">
                            <stop offset="5%" stop-color="#ff0000"></stop>
                            <stop offset="95%" stop-color="#0693e3"></stop>
                        </linearGradient>
                    </defs>
                    <path d="M 0,400 C 0,400 0,133 0,133 C 72.9667,146.8077 145.9333,160.6154 218,165 C 290.0667,169.3846 361.2333,164.3462 450,154 C 538.7667,143.6538 645.1333,128 742,129 C 838.8667,130 926.2333,147.6538 994,150 C 1061.7667,152.3462 1109.9333,139.3846 1181,134 C 1252.0667,128.6154 1346.0333,130.8077 1440,133 C 1440,133 1440,400 1440,400 Z" fill="url(#gradient)" fill-opacity="0.53" transform="rotate(-180 720 200)">
                    </path>
                    <path d="M 0,400 C 0,400 0,266 0,266 C 106.6359,259.4462 213.2718,252.8923 284,263 C 354.7282,273.1077 389.5487,299.8769 468,288 C 546.4513,276.1231 668.5333,225.6 759,219 C 849.4667,212.4 908.318,249.7231 977,254 C 1045.6821,258.2769 1124.1949,229.5077 1203,226 C 1281.8051,222.4923 1360.9026,244.2462 1440,266 C 1440,266 1440,400 1440,400 Z" fill="url(#gradient)" fill-opacity="1" transform="rotate(-180 720 200)">
                    </path>

                    <foreignObject x="0" y="0" width="100%" height="100%">
                        <div class="text-center my-3">
                            <img class="bg-light p-3 rounded-5" src="images/brand-icon.png" alt="Brand Icon" style="width:10%;">
                            <div class="rounded-5 py-2 w-50 my-5 shadow-sm bg-light mx-auto">
                                <h4 class='fw-bold text-center'>KONTRATË PËR TË DREJTËN E VEPRËS</h4>
                            </div>
                        </div>
                    </foreignObject>
                </svg>

                <!-- Contract Details -->
                <p class='fw-bold my-3'>
                    Kjo kontratë u nënshkrua me datë
                    <?= htmlspecialchars(date('d/m/Y', strtotime($contract['data']))) ?> midis
                    <?= htmlspecialchars($contract['emri']) ?>
                    <?= htmlspecialchars($contract['mbiemri']) ?>, ("<?= htmlspecialchars($contract['emriartistik']) ?>") dhe Baresha Music ("Baresha Music SH.P.K.").
                </p>

                <p class="my-3">Numri personal:
                    <?= htmlspecialchars($contract['numri_personal']) ?>
                </p>
                <p>
                    Artisti është autori dhe/apo pronari i regjistrimit të tingujve të kompozicionit muzikor të quajtur
                </p>
                <p>
                    <b>
                        <?= htmlspecialchars($contract['vepra']) ?>
                    </b>
                </p>
                <p>
                    Baresha Music i takon e drejta ekskluzive e vepres (kenges), dhe kushtet e marrëveshjes janë të përcaktuara si në vijim:
                </p>

                <p class="fw-bold my-3">Në këtë kontratë dy palët pajtohen me nenet e shënuara më poshtë:</p>

                <!-- Contract Clauses -->
                <ol>
                    <li>
                        <strong>DHËNIA E TË DREJTAVE.</strong> Me nënshkrimin e kësaj kontrate artisti e jep të drejtën e plotë për përdorimin, botimin, riprodhimin, licesnimin, shpërndarjen, performances, publikimin dhe shfaqjen e këngës, duke përfshirë të gjitha rrjetet sociale dhe platformat publikuese si YouTube, pa kufizuar shkarkimet digjitale, transmetimin dhe kopjet fizike për periudhen (vitet ose e përhershme) që fillon nga data e nënshkrimit të kësaj kontrate.
                    </li>
                    <li>
                        <strong>LICENCA EKSKLUZIVE.</strong> Artisti pajtohet që Baresha Music SH.P.K ta ketë të drejtën ekskluzive për eksploatimin e këngës së caktuar në këtë marrëveshje. Artisti nuk do t'i japë asnjë të drejtë palës së tretë që konflikon me licencën ekskluzive që i jepet Baresha në këtë marrëveshje.
                    </li>
                    <li>
                        <strong>KUFIZIMI I KANALEVE.</strong> Artisti pajtohet që kënga do të ngarkohet dhe lëshohet vetëm në kanalin zyrtar 'Baresha Music' në platforma si YouTube, Spotify dhe platforma të tjera për transmetim të muzikës.
                    </li>
                    <li>
                        <strong>PËRQINDJA.</strong> Palët pajtohen në ndarjen e përqindjes në vlerë prej <b><?= htmlspecialchars($contract['perqindja']) ?>%</b> prej të gjitha të ardhurave të gjeneruara nga eksploatimi i këngës pas nënshkrimit të kësaj kontrate dhe publikimit të vepres/këngës. Të ardhurat neto do të përcaktohen si të gjitha të ardhurat e marra nga Baresha nga eksploatimi i këngës, të zbritura nga kostot direkte që Baresha ndërhyen në lidhje me këtë eksploatim.
                    </li>
                    <!-- Add additional clauses as needed -->
                </ol>

                <br>
                <p><b>Titulli i këngës / veprës</b></p>
                <p class="border-bottom w-25">
                    <?= htmlspecialchars($contract['vepra']) ?>
                </p>
                <br>

                <!-- Signatures -->
                <div class="row">
                    <div class="col-6">
                        <p class="fw-bold">Artisti/Pronar i kompozicionit të muzikës<br><span>Emri dhe Mbiemri</span></p>
                        <p><?= htmlspecialchars($contract['emri']) ?> <?= htmlspecialchars($contract['mbiemri']) ?></p>
                    </div>
                    <div class="col-6">
                        <p class="fw-bold text-end">Pronar i të drejtave ekskluzive të eksploatimit të kompozicionit të muzikës<br><span>Emri dhe Mbiemri</span></p>
                        <p class="text-end">Baresha Music Sh.p.k.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <p class="fw-bold">Nënshkrimi</p>
                        <p class="border-bottom w-50">
                            <img src="<?= htmlspecialchars($contract['nenshkrimi']) ?>" alt="Nënshkrimi i Artistit" style="width: 150px; height: auto;">
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="fw-bold text-end">Nënshkrimi</p>
                        <p class="border-bottom w-50 text-end">
                            <img src="signatures/34.png" alt="Nënshkrimi i Baresha" style="width: 150px; height: auto;">
                        </p>
                    </div>
                </div>
                <hr>

                <!-- Additional Details -->
                <div class="row mt-5">
                    <div class="col-12 text-end">
                        <p>Data e nënshkrimit të marrëveshjes: <?= htmlspecialchars($contract['data']); ?></p>
                    </div>
                </div>

                <?php if (!empty(trim($contract['shenim']))): ?>
                    <div class="my-5 border rounded-5 py-3">
                        <h6>Shënime</h6>
                        <p><?= nl2br(htmlspecialchars($contract['shenim'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Signature Form -->
                <div class="row">
                    <div class="col-12">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="signature" class="form-label">Nënshkrimi:</label>
                                <canvas id="signature" width="350" height="200" class="border mt-2 rounded-5 shadow-sm"></canvas>
                                <input type="hidden" name="signatureData" id="signatureData">
                            </div>
                            <button type="submit" class="btn btn-light border" style="text-transform:none;">
                                <i class="fas fa-paper-plane"></i> Dërgo
                            </button>
                            <button type="button" class="btn btn-light border ms-2" style="text-transform:none;" onclick="clearSignaturePad()">
                                <i class="fas fa-sync"></i> Fshij
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Signature Pad Script -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
                <script>
                    const canvas = document.getElementById('signature');
                    const signaturePad = new SignaturePad(canvas);

                    function clearSignaturePad() {
                        signaturePad.clear();
                    }

                    document.querySelector('form').addEventListener('submit', function(event) {
                        if (signaturePad.isEmpty()) {
                            alert("Ju lutem, nënshkruani kontratën para se ta dërgoni.");
                            event.preventDefault();
                        } else {
                            const signatureData = signaturePad.toDataURL();
                            document.getElementById('signatureData').value = signatureData;
                        }
                    });
                </script>
            </div>
        </div>
    <?php else: ?>
        <!-- Expiration Message -->
        <div class="container" id="expire-message">
            <div class="card p-5 text-center border">
                <div class="d-flex justify-content-center">
                    <img src="images/icons8-query-94.png" alt="Expired" width="94px">
                </div>
                <br>
                <p>Koha (tokeni) juaj për të nënshkruar kontratën me Baresha Network ka skaduar!</p>
                <p>Kërkoni një kohë të re duke ju shkruar stafit të Baresha Network.</p>
                <form action="" method="post">
                    <div class="row">
                        <div class="col my-3">
                            <div class="form-group">
                                <label for="stafi">Zgjidh stafin</label>
                                <select name="stafi" id="stafi" class="form-select" required>
                                    <option value="" disabled selected>Zgjidhni një stafi</option>
                                    <option value="afrim">Afrim Kolgeci (CEO)</option>
                                    <option value="enis">Enis Gjini (Zhvillues i uebit)</option>
                                    <option value="lyon">Lyon Cacaj (Dizajner)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col my-3">
                            <div class="form-group">
                                <label for="dataProblemit">Data e problemit</label>
                                <input type="date" class="form-control" id="dataProblemit" name="dataProblemit" value="<?= date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col my-3">
                            <div class="form-group">
                                <label for="emails">Email Address</label>
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
                        <label for="message">Mesazhi</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit2">Dërgo Email</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- MDB JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>

</html>