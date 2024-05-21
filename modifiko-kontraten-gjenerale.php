<?php
ob_start();
include 'partials/header.php';
// Include your database connection file
include 'conn-d.php';
// Get the ID from the URL
$id = $_GET['id'];
// Fetch the record from the database
$query = "SELECT * FROM kontrata_gjenerale WHERE id = $id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
ob_flush();
ob_end_clean();
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 mb-4 card shadow-sm rounded-5">
                <form id="updateForm">
                    <div class="row">
                        <div class="col">
                            <label for="emri" class="form-label">Emri</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="emri" id="emri" value="<?php echo $row['emri']; ?>">
                        </div>
                        <div class="col">
                            <label for="mbiemri" class="form-label">Mbiemri</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="mbiemri" id="mbiemri" value="<?php echo $row['mbiemri']; ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="tvsh" class="form-label">P&euml;rqindja</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="tvsh" id="tvsh" value="<?php echo $row['tvsh']; ?>">
                        </div>
                        <div class="col">
                            <label for="numri_personal" class="form-label">Numri personal</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="numri_personal" id="numri_personal" value="<?php echo $row['numri_personal']; ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="pronari_xhirollogarise" class="form-label">Pronari i xhirollogaris&euml; bankare</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="pronari_xhirollogarise" id="pronari_xhirollogarise" value="<?php echo $row['pronari_xhirollogarise']; ?>">
                        </div>
                        <div class="col">
                            <label for="numri_xhirollogarise" class="form-label">Numri i xhirollogaris&euml; bankare</label>
                            <input type="text" class="form-control border border-2 rounded-5 blurred-input" name="numri_xhirollogarise" id="numri_xhirollogarise" value="<?php echo $row['numri_xhirollogarise']; ?>" >
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="kodi_swift" class="form-label">Kodi SWIFT</label>
                            <input type="text" class="form-control border border-2 rounded-5 blurred-input" name="kodi_swift" id="kodi_swift" value="<?php echo $row['kodi_swift']; ?>" >
                        </div>
                        <div class="col">
                            <label for="iban" class="form-label">IBAN</label>
                            <input type="text" class="form-control border border-2 rounded-5 blurred-input" name="iban" id="iban" value="<?php echo $row['iban']; ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="emri_bankes" class="form-label">Emri i bank&euml;s</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="emri_bankes" id="emri_bankes" value="<?php echo $row['emri_bankes']; ?>">
                        </div>
                        <div class="col">
                            <label for="adresa_bankes" class="form-label">Adresa e bank&euml;s</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="adresa_bankes" id="adresa_bankes" value="<?php echo $row['adresa_bankes']; ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <label for="kohezgjatja" class="form-label">Koh&euml;zgjatja n&euml; muaj</label>
                            <input type="text" class="form-control border border-2 rounded-5" name="kohezgjatja" id="kohezgjatja" value="<?php echo $row['kohezgjatja']; ?>">
                            <span class="text-sm" id="specifiedTime"></span>
                        </div>
                    </div>
                    <hr>
                    <?php
                    $data_e_krijimit = $row['data_e_krijimit'];
                    $kohezgjatja = $row['kohezgjatja'];
                    $expiration_date = date('Y-m-d', strtotime($data_e_krijimit . ' + ' . $kohezgjatja . ' months'));
                    $expiration_date_formatted = date('d F Y', strtotime($expiration_date));
                    ?>
                    <div class="mt-4">
                        <p class="fw-bold text-primary">Kjo kontratë është valide deri me:</p>
                        <p class="bg-light p-3 rounded border border-primary">
                            <i class="bi bi-calendar-check-fill"></i> <?php echo $expiration_date_formatted; ?>
                        </p>
                    </div>
                    <div class="mt-4">
                        <label for="verify_email" class="form-label">Verifiko Email-in</label>
                        <input type="email" class="form-control border border-2 rounded-5" name="verify_email" id="verify_email">
                        <button type="button" class="input-custom-css px-3 py-2 mt-3" onclick="verifyEmail()">Verifiko</button>
                        <button type="submit" class="input-custom-css px-3 py-2 mt-3" name="submit" style="text-transform:none;">P&euml;rditso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function verifyEmail() {
        var email = document.getElementById('verify_email').value;
        var correctEmail = "<?php echo $user_info['email']; ?>"; // Assumes the email is stored in the session
        if (email === correctEmail) {
            document.querySelectorAll('.blurred-input').forEach(function(input) {
                input.classList.remove('blurred-input');
            });
            Swal.fire({
                title: 'Sukses!',
                text: 'Email-i është verifikuar me sukses!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Gabim!',
                text: 'Email-i është i pasaktë!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            document.getElementById('verify_email').value = '';
        }
    }
    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('id', '<?php echo $id; ?>');
        fetch('update_contract.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'Kontrata është përditësuar me sukses!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        title: 'Gabim!',
                        text: 'Diçka shkoi keq!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Gabim!',
                    text: 'Diçka shkoi keq!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    });
</script>
<style>
    .blurred-input {
        filter: blur(5px);
        pointer-events: none;
    }
</style>
<?php include('partials/footer.php') ?>