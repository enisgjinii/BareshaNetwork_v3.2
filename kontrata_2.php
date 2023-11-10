<?php
include('partials/header.php');
?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Kontrata e re</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Kontrata</a>
                            <span>/</span>
                            <a href="kontrata_2.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Kontrata e re</u></a>
                        </h6>
                    </nav>
                </div>
                <!-- <div class="card p-5 shadow-sm rounded-5 mb-4">
                    <h4 class="card-title">
                        Informacione rreth kontrates
                    </h4>
                    <audio src="audio/hyrje_kontrata.mp3" controls ></audio>
                </div> -->
                <div class="card p-5 shadow-sm rounded-5">
                    <h4 class="card-title">Kontrata n&euml; form&euml; elektronike</h4>
                    <p>Ju lutemi plot&euml;soni formularin dhe n&euml;nshkruani m&euml; posht&euml; p&euml;r t&euml; konfirmuar pajtimin tuaj me termat dhe kushtet e k&euml;saj kontrate:</p>
                    <hr>

                    <form method="post" action="submitSignature.php" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label" for="emri">Emri:</label>
                                <input type="text" name="emri" id="emri" class="form-control rounded-5 border-1">
                            </div>
                            <div class="col">
                                <label class="form-label" for="mbiemri">Mbiemri:</label>
                                <input type="text" name="mbiemri" class="form-control rounded-5 border-1">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label" for="numri_tel">Numri i telefonit:</label>
                                <input type="text" name="numri_tel" class="form-control rounded-5 border-1">
                            </div>
                            <div class="col">
                                <label class="form-label" for="numri_personal">Numri personal:</label>
                                <input type="text" name="numri_personal" class="form-control rounded-5 border-1">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label" for="klienti">Klienti:</label>
                                <?php
                                $sql = "SELECT * FROM klientet ORDER BY emri ASC";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    echo "<select name='klienti' class='form-select rounded-5 border-1' onchange='showEmail(this)'>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['emri'] . "|" . $row['emailadd'] . "|" . $row['emriart'] . "'>" . $row['emri'] . "</option>";
                                    }
                                    echo "</select>";
                                }
                                ?>
                            </div>
                            <div class="col">
                                <label class="form-label" for="emailadresa">Adresa e email-it:</label>
                                <input type="text" name="email" id="email" class="form-control rounded-5 border-1">
                            </div>
                            <div class="col">
                                <label class="form-label" for="emriartistik">Emri artistik:</label>
                                <input type="text" name="emriartistik" id="emriartistik" class="form-control rounded-5 border-1">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label" for="vepra">Vepra</label>
                                <input type="text" name="vepra" class="form-control rounded-5 border-1">
                            </div>
                            <div class="col">
                                <label class="form-label" for="data">Data</label>
                                <input type="date" name="data" class="form-control rounded-5 border-1">
                            </div>
                            <div class="col">
                                <label class="form-label" for="data">Ngarko kontrat&euml;n:</label>
                                <input type="file" name="pdf_file" class="form-control rounded-5 border-1">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label" for="perqindja">P&euml;rqindja:</label>
                                <input type="text" name="perqindja" class="form-control rounded-5 border-1">
                            </div>
                            <div class="col">
                                <label class="form-label" for="shenime">Sh&euml;nime:</label>
                                <textarea type="text" name="shenime" placeholder="Sh&euml;nime" class="form-control rounded-5 border-1" rows="5"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-light rounded-5 float-right shadow" style="text-transform: none; border: 1px solid grey;">
                            <i class="fi fi-rr-paper-plane" style="display: inline-block; vertical-align: middle;"></i>
                            <span style="display: inline-block; vertical-align: middle;">D&euml;rgo</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include('partials/footer.php') ?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>s -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>

<script>
    var canvas = document.getElementById('signature');
    var signaturePad = new SignaturePad(canvas);

    document.querySelector('form').addEventListener('submit', function(event) {
        var signatureData = signaturePad.toDataURL();
        document.getElementById('signatureData').value = signatureData;
    });
</script>

<script>
    function showEmail(select) {
        var selectedOption = select.options[select.selectedIndex];
        var nameAndEmail = selectedOption.value.split("|");
        if (nameAndEmail.length < 3 || nameAndEmail[2] === "" || nameAndEmail[0] === "") {
            document.getElementById("email").value = "Klienti qe keni zgjedhur nuk ka adres&euml; te emailit";
            document.getElementById("emriartistik").value = "";
        } else {
            var email = nameAndEmail[1];
            var emriartistik = nameAndEmail[2];
            document.getElementById("email").value = email;
            document.getElementById("emriartistik").value = emriartistik;
        }
    }
</script>

<!-- <script>
    document.getElementById('myForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var signatureData = signaturePad.toDataURL();
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'submitSignature.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Signature submitted successfully');
            } else {
                alert('There was an error submitting the signature');
            }
        };
        xhr.send('signatureData=' + encodeURIComponent(signatureData));
    });
</script> -->