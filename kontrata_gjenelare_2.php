<?php
include('partials/header.php');
?>
<style>
    /* Step indicator styling */
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .step {
        width: 30px;
        height: 30px;
        background-color: #ccc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 10px;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.3s ease;
        cursor: pointer;
    }

    .step.active {
        background-color: #007bff;
        color: #fff;
        transform: scale(1.1);
    }

    /* Step content styling */
    .step-content {
        display: none;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f8f8f8;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: opacity 0.3s ease;
        opacity: 0;
    }

    .step-content.active {
        display: block;
        opacity: 1;
    }

    /* Navigation buttons styling */
    .step-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .prev-btn,
    .next-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .prev-btn:hover,
    .next-btn:hover {
        background-color: #0056b3;
    }

    /* Form input styling (customize as needed) */
    .form-label {
        font-weight: bold;
    }

    .form-control {
        margin-top: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Additional styling for select dropdown */
    .form-select {
        appearance: none;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
        background-image: linear-gradient(45deg, transparent 50%, #007bff 50%),
            linear-gradient(135deg, #007bff 50%, transparent 50%);
        background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px);
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
    }

    /* Add more specific styling for your form elements as needed */
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Kontrata e re ( Gjenerale )</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Kontrata ( Gjenerale )</a>
                            <span>/</span>
                            <a href="kontrata_gjenelare_2.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Kontrata e re</u></a>
                        </h6>
                    </nav>
                </div>
                <div class="card p-5 shadow-sm rounded-5">
                    <h4 class="card-title">Kontrata gjenerale ne forme elektronike</h4>
                    <div class="step-indicator">
                        <div class="step"> 1</div>
                        <div class="step"> 2</div>
                        <div class="step"> 3</div>
                        <div class="step"> 4</div>
                        <!-- Add more steps as needed -->
                    </div>
                    <form id="stepForm" method="post" action="dorzoKontraten.php" enctype="multipart/form-data">
                        <div class="step-content">
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <label for="emri" class="form-label">Emri</label>
                                            <div class="input-group">
                                                <input type="text" name="emri" id="emri" class="form-control rounded-5 shadow-sm py-4" placeholder="Vendosni emrin">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <label for="mbiemri" class="form-label">Mbiemri</label>
                                    <div class="input-group">
                                        <input type="text" name="mbiemri" id="mbiemri" class="form-control rounded-5 shadow-sm py-4" placeholder="Vendosni mbiemrin">

                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="numri_tel" class="form-label">Numri i telefonit</label>
                                    <div class="input-group">
                                        <input type="tel" name="numri_tel" id="numri_tel" class="form-control rounded-5 shadow-sm py-4" placeholder="Vendosni numrin e telefonit">

                                    </div>
                                </div>
                                <div class="col">
                                    <label for="numri_personal" class="form-label">Numri personal</label>
                                    <input type="number" name="numri_personal" id="numri_personal" class="form-control rounded-5 shadow-sm py-3" placeholder="Vendosni numrin personal">
                                </div>
                            </div>


                        </div>
                        <div class="step-content">

                            <div class="row mb-3">


                                <div class="col">
                                    <label for="artisti" class="form-label">Klienti</label>
                                    <br>
                                    <span class="text-muted" style="font-size: 12px;">Në mungesë të klientit, kaloni te regjistrimi i klientit të ri.</span>
                                    <!-- Button trigger modal -->
                                    <a href="add_client.php" type="button" class="btn btn-primary btn-sm rounded-5 text-light">
                                        Klient i ri
                                    </a>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ...
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php

                                    // Prepare the SQL statement
                                    $sql = "SELECT emri,emailadd,emriart,youtube FROM klientet ORDER BY emri ASC";

                                    // Use a prepared statement to execute the query
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();

                                    // Bind result variables
                                    $stmt->bind_result($emri, $emailadd, $emriart, $youtube);

                                    // Generate the select dropdown
                                    echo "<select name='artisti' id='artisti' class='form-select mt-2 rounded-5 shadow-sm py-2' onchange='showEmail(this)'>";
                                    while ($stmt->fetch()) {
                                        echo "<option value='$emri|$emailadd|$emriart|$youtube'>$emri</option>";
                                    }
                                    echo "</select>";

                                    // Close the prepared statement and database connection
                                    $stmt->close();
                                    $conn->close();
                                    ?>
                                </div>

                                <div class="col">
                                    <label for="emailadresa" class="form-label">Adresa e email-it</label>
                                    <input type="text" name="email" id="email" class="form-control mt-2 rounded-5 shadow-sm">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="youtube_id" class="form-label">ID-ja e kanalit n&euml; YouTube</label>
                                    <input type="text" name="youtube_id" id="youtube_id" class="form-control mt-2 rounded-5 shadow-sm" readonly>
                                </div>

                                <div class="col">
                                    <label for="emriartistik" class="form-label">Emri artistik</label>
                                    <input type="text" name="emriartistik" id="emriartistik" class="form-control mt-2 rounded-5 shadow-sm">
                                </div>
                            </div>

                        </div>

                        <!-- Step 2 content -->
                        <div class="step-content">
                            <div class="row mb-3">

                                <div class="col-6">
                                    <label for="tvsh" class="form-label">P&euml;rqindja</label>
                                    <input type="number" name="tvsh" id="tvsh" class="form-control mt-2 rounded-5 shadow-sm" placeholder="Vendosni p&euml;rqindjen">
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="pronari_xhiroBanka" class="form-label">Pronari i xhirollogaris&euml;
                                        bankare</label>
                                    <input type="text" name="pronari_xhiroBanka" class="form-control rounded-5 shadow-sm" placeholder="Vendosni pronarin e xhirollogaris&euml;">
                                </div>
                                <div class="col">
                                    <label for="numri_xhiroBanka" class="form-label">Numri i xhirollogaris&euml; bankare</label>
                                    <input type="text" name="numri_xhiroBanka" class="form-control rounded-5 shadow-sm" placeholder="Vendosni numrin e xhirollogaris&euml; bankare">
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="kodi_swift" class="form-label">Kodi SWIFT</label>
                                    <input type="text" name="kodi_swift" class="form-control rounded-5 shadow-sm" placeholder="Vendosni kodin SWIFT">
                                </div>
                                <div class="col">
                                    <label for="iban" class="form-label">IBAN</label>
                                    <input type="text" name="iban" class="form-control rounded-5 shadow-sm" placeholder="Vendosni IBAN">
                                </div>
                            </div>
                        </div>
                        <div class="step-content">

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="emri_bankes" class="form-label">Emri i bank&euml;s</label>
                                    <input type="text" name="emri_bankes" class="form-control rounded-5 shadow-sm" placeholder="Vendosni emrin e bank&euml;s">
                                </div>
                                <div class="col">
                                    <label for="adresa_bankes" class="form-label">Adresa e bank&euml;s</label>
                                    <input type="text" name="adresa_bankes" class="form-control rounded-5 shadow-sm" placeholder="Vendosni adresen e bank&euml;s">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <?php
                                // Fetch country data from the REST Countries API
                                $url = "https://restcountries.com/v3.1/all";
                                $response = file_get_contents($url);
                                $countries = json_decode($response, true);

                                // Sort countries alphabetically by name
                                usort($countries, function ($a, $b) {
                                    return strcmp($a['name']['common'], $b['name']['common']);
                                });

                                // Generate the select dropdown menu
                                echo '<div class="col-6">';
                                echo '<label class="form-label" for="shteti">Shtet&euml;sia:</label>';
                                echo '<select class="form-select mt-2 rounded-5 shadow-sm" name="shteti" id="shteti">';

                                foreach ($countries as $country) {
                                    $countryName = $country['name']['common'];

                                    echo '<option value="' . $countryName . '">' . $countryName . '</option>';
                                }

                                echo '</select>';
                                echo '</div>';
                                ?>

                                <div class="col-6">
                                    <p class="form-label">Koh&euml;zgjatja n&euml; muaj<span style="font-size: 12px;"> (Vendos vetem nje numer)</span></p>
                                    <input type="number" name="kohezgjatja" class="form-control rounded-5 shadow-sm" placeholder="Vendosni koh&euml;zgjatjen">
                                </div>
                            </div>


                            <button type="submit" class="btn btn-light rounded-5 float-right shadow-5" style="text-transform:none;border:1px solid lightgrey;">
                                <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                                <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                            </button>
                        </div>

                        <!-- Navigation buttons -->
                        <div class="step-buttons">
                            <button type="button" class="prev-btn">E m&euml;parshme</button>
                            <button type="button" class="next-btn">Tjetra</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>

<?php include('partials/footer.php') ?>
<script>
    const steps = document.querySelectorAll('.step-content');
    const stepIndicators = document.querySelectorAll('.step');
    const prevButton = document.querySelector('.prev-btn');
    const nextButton = document.querySelector('.next-btn');
    let currentStep = 0; // Initialize currentStep to 0 for the first step

    // Initial setup
    updateStepIndicator();

    // Event listener for the "Next" button
    nextButton.addEventListener('click', () => {
        if (currentStep < steps.length - 1) {
            currentStep++;
            updateStepIndicator();
        }
        // Check if we're on the second or third step to show the "Previous" button
        updatePrevButtonVisibility();
    });

    // Event listener for the "Previous" button
    prevButton.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            updateStepIndicator();
        }
        // Check if we're on the first step to hide the "Previous" button
        updatePrevButtonVisibility();
    });

    function updateStepIndicator() {
        steps.forEach((step, index) => {
            if (index === currentStep) {
                step.classList.add('active');
                stepIndicators[index].classList.add('active');
            } else {
                step.classList.remove('active');
                stepIndicators[index].classList.remove('active');
            }
        });
    }

    function updatePrevButtonVisibility() {
        // Show the "Previous" button on the second and third steps, hide it on the first step
        if (currentStep === 0) {
            prevButton.style.display = 'none';
        } else {
            prevButton.style.display = 'block';
        }
    }

    function showEmail(select) {
        var selectedOption = select.options[select.selectedIndex];
        var nameAndEmail = selectedOption.value.split("|");
        if (nameAndEmail.length < 3 || nameAndEmail[2] === "" || nameAndEmail[0] === "") {
            document.getElementById("email").value = "Klienti qe keni zgjedhur nuk ka adres&euml; te emailit";
            document.getElementById("emriartistik").value = "";
            document.getElementById("youtube_id").value = "";
        } else {
            var email = nameAndEmail[1];
            var emriartistik = nameAndEmail[2];
            var youtube_id = nameAndEmail[3];
            document.getElementById("email").value = email;
            document.getElementById("emriartistik").value = emriartistik;
            document.getElementById("youtube_id").value = youtube_id;
        }
    }

    var canvas = document.getElementById('signature');
    var signaturePad = new SignaturePad(canvas);

    document.querySelector('form').addEventListener('submit', function(event) {
        var signatureData = signaturePad.toDataURL();
        document.getElementById('signatureData').value = signatureData;
    });
</script>