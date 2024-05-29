<?php
// Përfshij header-in e faqes
include 'partials/header.php';
include 'page_access_controller.php'
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Kontratat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">
                            Kontrata e re ( Gjenerale )
                        </a>
                    </li>
                </ol>
            </nav>
            <form method="post" action="dorzoKontraten.php" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 ">
                        <div class="accordion mb-sm-3 mb-md-3" id="accordionExample">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header">
                                    <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fi fi-rr-user me-2"></i> Të dhënat e klientit
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <label for="emri_klientit" class="form-label">Emri</label>
                                        <input type="text" name="emri" id="emri" class="form-control rounded-5 border border-2 " placeholder="Shëno emrin e klientit" required oninvalid="this.setCustomValidity('Emri i klientit duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                        <br>
                                        <label for="mbiemri_klientit" class="form-label">Mbiemri</label>
                                        <input type="text" name="mbiemri" id="mbiemri" class="form-control rounded-5 border border-2" placeholder="Shëno mbiemrin e klientit" required oninvalid="this.setCustomValidity('Mbiemri i klientit duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                        <br>
                                        <label for="numri_i_telefonit" class="form-label">Numri i telefonit</label>
                                        <input type="tel" name="numri_tel" id="numri_tel" class="form-control rounded-5 border border-2" placeholder="Shëno numrin e telefonit" required oninvalid="this.setCustomValidity('Numri i telefonit duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                        <br>
                                        <label for="numri_personal" class="form-label">Numri personal</label>
                                        <input type="text" name="numri_personal" id="numri_personal" class="form-control rounded-5 border border-2" placeholder="Shëno numrin personal" required oninvalid="this.setCustomValidity('Numri personal duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="accordion" id="client-infos-youtube-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#ciya" aria-expanded="true" aria-controls="ciya">
                                        <i class="fi fi-rr-file-user me-2"></i> Të dhënat e klientit ( Të gatshme )
                                    </button>
                                </h2>
                                <div id="ciya" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#client-infos-youtube-accordion">
                                    <div class="accordion-body">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="artisti" class="form-label">Klienti</label>
                                                    <?php
                                                    // Prepare the SQL statement with a parameterized query
                                                    $sql = "SELECT emri,emailadd,emriart,youtube,nrllog FROM klientet ORDER BY emri ASC";
                                                    // Use a prepared statement to execute the query
                                                    $stmt = $conn->prepare($sql);
                                                    // Check if the prepared statement was successful
                                                    if ($stmt) {
                                                        // Execute the prepared statement
                                                        $stmt->execute();
                                                        // Bind result variables
                                                        $stmt->bind_result($emri, $emailadd, $emriart, $youtube, $nrllog);
                                                        // Generate the select dropdown
                                                        echo "<select name='artisti' id='artisti' class='form-select mt-2 rounded-5 shadow-sm py-2' onchange='showEmail(this)'>";
                                                        // Add default option
                                                        echo "<option value='' selected>Zgjidhni një klient</option>";
                                                        // Fetch results and generate options
                                                        while ($stmt->fetch()) {
                                                            // Output sanitized values to prevent XSS attacks
                                                            $emri_sanitized = htmlspecialchars($emri);
                                                            echo "<option value='$emri_sanitized|$emailadd|$emriart|$youtube|$nrllog'>$emri_sanitized</option>";
                                                        }
                                                        echo "</select>";
                                                        // Close the prepared statement
                                                        $stmt->close();
                                                    } else {
                                                        // Handle error if the prepared statement failed
                                                        echo "Gabim në përgatitjen e deklaratës SQL";
                                                    }
                                                    ?>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="text-muted d-block mt-2 mb-3" style="font-size: 12px;">Në mungesë të klientit, kaloni te
                                                        regjistrimi i klientit të ri.</span>
                                                    <a href="add_client.php" class="input-custom-css px-3 py-2" style="text-decoration: none;">
                                                        <i class="fi fi-rr-user-add me-2"></i>
                                                        Krijo klientë
                                                    </a>
                                                </div>
                                            </div>
                                            <script>
                                                new Selectr('#artisti', {
                                                    searchable: true,
                                                })
                                            </script>
                                        </div>
                                        <br>
                                        <label for="emailadresa" class="form-label">Adresa e email-it</label>
                                        <input type="text" name="email" id="email" class="form-control rounded-5 border border-2" placeholder="Shëno adresen e email-it" required on>
                                        <br>
                                        <label for="youtube_id" class="form-label">ID-ja e kanalit në YouTube</label>
                                        <input type="text" name="youtube_id" id="youtube_id" class="form-control rounded-5 border border-2" readonly>
                                        <br>
                                        <label for="emriartistik" class="form-label">Emri artistik</label>
                                        <input type="text" name="emriartistik" id="emriartistik" class="form-control rounded-5 border border-2" placeholder="Shëno emrin artistik">
                                        <br>
                                        <label for="numri_xhiroBanka" class="form-label">Numri i xhirollogaris&euml;
                                            bankare</label>
                                        <input type="text" name="numri_xhiroBanka" id="numri_xhiroBanka" class="form-control rounded-5 border border-2" placeholder="Shëno numrin e xhirollogaris&euml;" required oninvalid="this.setCustomValidity('Numri i xhirollogarisë bankare duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="accordion" id="perqindja-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#pdp" aria-expanded="true" aria-controls="pdp">
                                        <i class="fi fi-rr-wallet me-2"></i> Përqindja dhe informatat e bankës
                                    </button>
                                </h2>
                                <div id="pdp" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#perqindja-accordion">
                                    <div class="accordion-body">
                                        <div class="col">
                                            <label for="tvsh" class="form-label">P&euml;rqindja</label>
                                            <input type="number" name="tvsh" id="tvsh" class="form-control rounded-5 border border-2" placeholder="Shëno përqindjen" required oninvalid="this.setCustomValidity('Përqindja duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                            <br>
                                            <label for="pronari_xhiroBanka" class="form-label">Pronari i
                                                xhirollogaris&euml;
                                                bankare</label>
                                            <input type="text" name="pronari_xhiroBanka" class="form-control rounded-5 border border-2" placeholder="Shëno pronarin e xhirollogaris&euml;" required oninvalid="this.setCustomValidity('Pronari i xhirollogarisë bankare duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                            <br>
                                            <label for="kodi_swift" class="form-label">Kodi SWIFT</label>
                                            <input type="text" name="kodi_swift" class="form-control rounded-5 border border-2" placeholder="Shëno kodin SWIFT" required oninvalid="this.setCustomValidity('Kodi SWIFT duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                            <br>
                                            <label for="iban" class="form-label">IBAN</label>
                                            <input type="text" name="iban" class="form-control rounded-5 border border-2" placeholder="Shëno IBAN" required oninvalid="this.setCustomValidity('IBAN duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                            <br>
                                            <label for="emri_bankes" class="form-label">Emri i bank&euml;s</label>
                                            <input type="text" name="emri_bankes" class="form-control rounded-5 border border-2" placeholder="Shëno emrin e bank&euml;s" required oninvalid="this.setCustomValidity('Emri i bank&euml;s duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                            <br>
                                            <label for="adresa_bankes" class="form-label">Adresa e bank&euml;s</label>
                                            <input type="text" name="adresa_bankes" class="form-control rounded-5 border border-2" placeholder="Shëno adresen e bank&euml;s" required oninvalid="this.setCustomValidity('Adresa e bank&euml;s duhet te plotesohet')" oninput="this.setCustomValidity('')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="accordion" id="information-accordion">
                            <div class="accordion-item border-1 rounded-5">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button border-0 rounded-5" type="button" data-bs-toggle="collapse" data-bs-target="#iib" aria-expanded="true" aria-controls="iib">
                                        <i class="fi fi-rr-info me-5"></i> Informacion i brendshëm
                                    </button>
                                </h2>
                                <div id="iib" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#information-accordion">
                                    <div class="accordion-body">
                                        <?php
                                        // Function to fetch countries data and cache it
                                        function getCountriesData()
                                        {
                                            $cacheFile = 'countries_cache.json';
                                            $cacheTime = 24 * 60 * 60; // Cache for 24 hours
                                            // If cache file exists and is younger than 24 hours, use cached data
                                            if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTime) {
                                                $data = file_get_contents($cacheFile);
                                            } else {
                                                // Fetch data from API
                                                $url = "https://restcountries.com/v3.1/all";
                                                $data = file_get_contents($url);
                                                // Save data to cache file
                                                file_put_contents($cacheFile, $data);
                                            }
                                            return json_decode($data, true);
                                        }
                                        // Fetch countries data
                                        $countries = getCountriesData();
                                        // Check if countries data retrieval was successful
                                        if ($countries === null) {
                                            echo '<p>Error: Unable to retrieve countries data. Please try again later.</p>';
                                        } else {
                                            // Sort countries alphabetically by name
                                            usort($countries, function ($a, $b) {
                                                return strcmp($a['name']['common'], $b['name']['common']);
                                            });
                                            // Generate the select dropdown menu
                                            echo '<label class="form-label" for="shteti">Shtet&euml;sia:</label>';
                                            echo '<select class="form-select rounded-5 border border-2" name="shteti" id="shteti">';
                                            foreach ($countries as $country) {
                                                $countryName = $country['name']['common'];
                                                echo '<option value="' . $countryName . '">' . $countryName . '</option>';
                                            }
                                            echo '</select>';
                                        ?>
                                            <script>
                                                new Selectr('#shteti', {});
                                            </script>
                                            <br>
                                            <p class="form-label">Koh&euml;zgjatja n&euml; muaj<span style="font-size: 12px;"> (Vendos vetem nje numer)</span></p>
                                            <input type="number" name="kohezgjatja" class="form-control rounded-5 border border-2" placeholder="Shëno koh&euml;zgjatjen e kontratës">
                                            <hr>

                                        <?php } ?> <button type="submit" class="input-custom-css px-3 py-2">
                                            <i class="fi fi-rr-memo-circle-check me-2"></i>
                                            Krijo kontratën
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include('partials/footer.php') ?>
<script>
    function showEmail(select) {
        var selectedOption = select.options[select.selectedIndex];
        var nameAndEmail = selectedOption.value.split("|");
        if (nameAndEmail.length < 5 || nameAndEmail[2] === "" || nameAndEmail[0] === "") {
            document.getElementById("email").value = "Klienti që keni zgjedhur nuk ka adresë të emailit.";
            document.getElementById("emriartistik").value = "";
            document.getElementById("youtube_id").value = "";
            document.getElementById("numri_xhiroBanka").value = "";
        } else {
            var email = nameAndEmail[1];
            var emriartistik = nameAndEmail[2];
            var youtube_id = nameAndEmail[3];
            var numri_xhiroBanka = nameAndEmail[4];
            // Sanitize values before setting them to prevent XSS attacks
            document.getElementById("email").value = sanitize(email);
            document.getElementById("emriartistik").value = sanitize(emriartistik);
            document.getElementById("youtube_id").value = sanitize(youtube_id);
            document.getElementById("numri_xhiroBanka").value = sanitize(numri_xhiroBanka);
        }
    }
    // Function to sanitize input values
    function sanitize(value) {
        // Replace '<' and '>' characters with their HTML entity equivalents
        return value.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
</script>