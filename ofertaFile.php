<?php
// Start session and include necessary files
session_start();
include 'conn-d.php';

// Set headers to prevent caching
header('Cache-Control: no-cache');

// Function to sanitize input data
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Initialize variables
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$offer = null;
$errors = [];
$success = '';

// Fetch offer details using prepared statements
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM ofertat WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $offer = $result->fetch_assoc();
        } else {
            $errors[] = "Asnjë ofertë nuk u gjet me ID të dhënë.";
        }
        $stmt->close();
    } else {
        $errors[] = "Gabim në përgatitjen e kërkesës SQL.";
    }
} else {
    $errors[] = "ID nuk është caktuar ose është i pavlefshëm.";
}

// Handle signature submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signatureData'])) {
    $signatureData = $_POST['signatureData'];
    // Basic validation
    if (!empty($signatureData) && $id > 0) {
        // Optionally, you can validate the signature data format here
        $stmt = $conn->prepare("UPDATE ofertat SET nenshkrimi = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $signatureData, $id);
            if ($stmt->execute()) {
                $success = "Nënshkrimi u azhurnua me sukses!";
                // Refresh the offer data
                $stmt->close();
                $stmt = $conn->prepare("SELECT * FROM ofertat WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && $result->num_rows > 0) {
                        $offer = $result->fetch_assoc();
                    }
                    $stmt->close();
                }
            } else {
                $errors[] = "Gabim në azhurnimin e nënshkrimit: " . $conn->error;
            }
        } else {
            $errors[] = "Gabim në përgatitjen e kërkesës SQL.";
        }
    } else {
        $errors[] = "Nënshkrimi nuk mund të jetë bosh.";
    }
}
?>
<!doctype html>
<html lang="sq">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detajet e Ofertës</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <!-- Signature Pad CSS (Optional Custom Styling) -->
    <style>
        * {
            font-family: 'Montserrat', sans-serif;
        }

        .signature-pad {
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
        }

        .btn-custom {
            text-transform: none;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <!-- Back Button -->
        <a href="ofertat.php" class="btn btn-light border shadow-sm mb-4" data-bs-toggle="tooltip" title="Kthehu te lista e ofertave">
            <i class="fas fa-arrow-left me-2"></i> Kthehu Prapa
        </a>

        <!-- Display Success and Error Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Mbyll"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo $error; ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Mbyll"></button>
            </div>
        <?php endif; ?>

        <?php if ($offer): ?>
            <!-- Offer Details Card -->
            <div class="card shadow-sm border p-4 mb-4">
                <h3 class="fw-bold text-center mb-4">Kontrata e Kampanjës Marketing</h3>

                <!-- Contract Text -->
                <div class="mb-4">
                    <p class="fw-bold">1. Shërbimet</p>
                    <p>Agjencia pranon të furnizojë Klientit një kampanjë marketingu tre-mujore në Instagram, Facebook, Snapchat dhe TikTok. Kampanja do të jetë e projektuar për të rritur vizibilitetin dhe ndikimin e markës së Klientit, duke nxitur më shumë trafik dhe shitje në faqen e internetit të Klientit. Agjencia do të krijojë dhe do të menaxhojë kampanjat e reklamave në çdo platformë, si dhe do të sigurojë përditësime dhe raporte rregullisht mbi progresin e kampanjës.</p>

                    <p class="fw-bold">2. Kompensimi</p>
                    <p>Klienti pranon të paguajë Agjencisë <strong>[Shuma]</strong> për kampanjën marketingu tre-mujore. Pagesa do të jetë e detyrueshme për t'u paguar më <strong>[Data e Detyrueshme]</strong>, dhe mos pagimi mund të çojë në ndërprerjen e kampanjës.</p>

                    <p class="fw-bold">3. Afati</p>
                    <p>Afati i kësaj Kontrate do të jetë 3 muaj, fillon nga <strong>[Data e Fillimit]</strong> dhe përfundon në <strong>[Data e Mbarimit]</strong>.</p>

                    <p class="fw-bold">4. Përfundimi</p>
                    <p>Çdo palë mund të përfundojë këtë Kontratë me njoftim të shkruar për palën tjetër. Nëse Klienti përfundon Kontratën para se të përfundojë afati i 3 muajve, Klienti do të jetë i detyruar të paguajë pagesat e mbetura për shërbimet që janë ofruar deri në atë kohë.</p>

                    <p class="fw-bold">5. Konfidencialiteti</p>
                    <p>Të dyja palët pajtohen që të mbajnë konfidencialitetin e të gjitha informacioneve dhe materialeve të ndara midis tyre gjatë afatit të kësaj Kontrate.</p>

                    <p class="fw-bold">6. Ligji i Zbatueshëm</p>
                    <p>Kjo Kontratë do të nënshkruhet dhe do të kuptohet në përputhje me ligjet e shtetit të <strong>[Shteti]</strong>.</p>

                    <p class="fw-bold">7. Gjithë Kontrata</p>
                    <p>Kjo Kontratë përmban të gjitha marrëveshjet midis palëve dhe superson të gjitha negociatat dhe marrëveshjet të mëparshme ndërmjet palëve.</p>

                    <p class="fw-bold">8. Ligji i Zbatueshëm</p>
                    <p>Kjo Marrëveshje dhe të gjitha të drejtat dhe detyrimet e palëve në lidhje me këtë Marrëveshje do të nënshkohen dhe do të interpretohen në përputhje me ligjet dhe rregulloret e arbitrazhit të shtetit <strong>[Emri i Shtetit]</strong>. Të gjitha mosmarrëveshjet dhe mosmarrëveshjet në lidhje me këtë Marrëveshje do të zgjidhen në mënyrë miqësore midis palëve. Në rast se nuk ka zgjidhje miqësore, mosmarrëveshjet do të zgjidhen nëpërmjet arbitrazhit në përputhje me procedurat dhe rregulloret e arbitrazhit të shtetit <strong>[Emri i Shtetit]</strong>.</p>

                    <p>Te nënshkruarit e mëposhtëm pajtohen me kushtet dhe kushtet e kësaj Kontrate:</p>
                </div>

                <!-- Signatures Section -->
                <div class="row mt-5">
                    <!-- Client Signature -->
                    <div class="col-md-6 mb-4">
                        <h5>Klienti</h5>
                        <div class="border-bottom mb-3">
                            <?php
                            if (!empty($offer['nenshkrimi'])) {
                                echo '<img src="' . sanitizeInput($offer['nenshkrimi']) . '" alt="Nënshkrimi i Klientit" class="img-fluid" style="max-width: 200px;">';
                            } else {
                                echo '<p>Endroni nënshkrimin tuaj më poshtë.</p>';
                            }
                            ?>
                        </div>

                        <!-- Signature Pad Form -->
                        <form method="POST" enctype="multipart/form-data">
                            <label for="signature" class="form-label">Nënshkrimi:</label>
                            <canvas id="signature-pad" width="400" height="200" class="signature-pad mb-3"></canvas>
                            <input type="hidden" name="signatureData" id="signatureData">
                            <button type="button" class="btn btn-secondary btn-sm me-2" onclick="clearSignaturePad()">
                                <i class="fas fa-eraser me-1"></i> Fshij
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane me-1"></i> Dërgo
                            </button>
                        </form>
                    </div>

                    <!-- Agency Signature -->
                    <div class="col-md-6 mb-4 text-end">
                        <h5>Agjencia</h5>
                        <div class="border-bottom mb-3">
                            <?php
                            // Replace 'signatures/34.png' with dynamic data if available
                            echo '<img src="signatures/34.png" alt="Nënshkrimi i Agjencisë" class="img-fluid" style="max-width: 200px;">';
                            ?>
                        </div>
                        <p><em>Emri i Agjencisë</em></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <!-- Signature Pad JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>

    <!-- Initialize Tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

    <!-- Initialize Signature Pad -->
    <script>
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)', // Transparent background
            penColor: 'rgb(0, 0, 0)' // Black ink
        });

        function clearSignaturePad() {
            signaturePad.clear();
        }

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(event) {
            if (signaturePad.isEmpty()) {
                event.preventDefault();
                alert("Ju lutem vendosni një nënshkrim para se të dërgoni.");
            } else {
                var signatureData = signaturePad.toDataURL();
                document.getElementById('signatureData').value = signatureData;
            }
        });
    </script>
</body>

</html>