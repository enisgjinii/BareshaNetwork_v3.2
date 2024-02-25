<?php
include 'partials/header.php';
include 'page_access_controller.php';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" style="width:fit-content" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="text-reset" style="text-decoration: none;">Kontratat</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Kontrata e re (Këngë)</a>
                    </li>
                </ol>
            </nav>
            <div class="card p-5 shadow-sm rounded-5">
                <form method="post" action="submitSignature.php" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="emri">Emri</label>
                            <input type="text" name="emri" id="emri" class="form-control rounded-5 border-1" required placeholder="Sheno emrin">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mbiemri">Mbiemri</label>
                            <input type="text" name="mbiemri" class="form-control rounded-5 border-1" required placeholder="Sheno mbiemrin">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="numri_tel">Numri i telefonit</label>
                            <input type="text" name="numri_tel" class="form-control rounded-5 border-1" required placeholder="Sheno numrin e telefonit">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="numri_personal">Numri personal</label>
                            <input type="text" name="numri_personal" class="form-control rounded-5 border-1" required placeholder="Sheno numrin personal">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="klienti">Klienti</label>
                            <?php
                            // Assuming $conn is your database connection
                            $sql = "SELECT * FROM klientet ORDER BY emri ASC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                echo "<select name='klienti' class='form-select rounded-5 border-1' onchange='showEmail(this)' required>";
                                echo "<option value='' disabled selected>Zgjidhni një klient</option>";
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['emri'] . "|" . $row['emailadd'] . "|" . $row['emriart'] . "'>" . $row['emri'] . "</option>";
                                }
                                echo "</select>";
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emailadresa">Adresa e email-it</label>
                            <input type="email" name="email" id="email" class="form-control rounded-5 border-1" required placeholder="Sheno adresen e email-it">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="emriartistik">Emri artistik</label>
                            <input type="text" name="emriartistik" id="emriartistik" class="form-control rounded-5 border-1" required placeholder="Sheno emrin artistik">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="vepra">Vepra</label>
                            <input type="text" name="vepra" class="form-control rounded-5 border-1" required placeholder="Sheno veprën">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="data">Data</label>
                            <input type="date" name="data" class="form-control rounded-5 border-1" required>
                        </div>
                        <script>
                            $("input[name='data']").flatpickr({
                                dateFormat: "Y-m-d",
                            })
                        </script>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="pdf_file">Ngarko kontratën</label>
                            <input type="file" name="pdf_file" class="form-control rounded-5 border-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="perqindja">Përqindja</label>
                            <input type="text" name="perqindja" class="form-control rounded-5 border-1" required placeholder="Sheno përqindjen">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label" for="shenime">Shënime</label>
                            <textarea name="shenime" class="form-control rounded-5 border-1" rows="5" placeholder="Shëno..." required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="input-custom-css px-3 py-2 rounded-5">
                        <i class="fi fi-rr-paper-plane me-2"></i>Dërgo</span>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
</div>
<?php include('partials/footer.php'); ?>
<script>
    function showEmail(select) {
        var selectedOption = select.options[select.selectedIndex];
        var nameAndEmail = selectedOption.value.split("|");
        if (nameAndEmail.length < 3 || nameAndEmail[2] === "" || nameAndEmail[0] === "") {
            document.getElementById("email").value = "Klienti që keni zgjedhur nuk ka adresë te emailit";
            document.getElementById("emriartistik").value = "";
        } else {
            var email = nameAndEmail[1];
            var emriartistik = nameAndEmail[2];
            document.getElementById("email").value = email;
            document.getElementById("emriartistik").value = emriartistik;
        }
    }
</script>