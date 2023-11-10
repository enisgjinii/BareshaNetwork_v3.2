<?php
include 'partials/header.php';
error_reporting(1);
if (isset($_POST['ruaj'])) {
    $emri = $_POST['emri'];
    if (empty($_POST['min'])) {
        $mon = "JO";
    } else {
        $mon = $_POST['min'];
    }
    $dk = mysqli_real_escape_string($conn, $_POST['dk']);
    $np = mysqli_real_escape_string($conn, $_POST['np']);
    $dks = mysqli_real_escape_string($conn, $_POST['dks']);
    $yt = mysqli_real_escape_string($conn, $_POST['yt']);
    $info = mysqli_real_escape_string($conn, $_POST['info']);
    $perq = mysqli_real_escape_string($conn, $_POST['perqindja']);


    $perq2 = mysqli_real_escape_string($conn, $_POST['perqindja2']);
    $ads = mysqli_real_escape_string($conn, $_POST['ads']);
    $fb = mysqli_real_escape_string($conn, $_POST['fb']);
    $ig = mysqli_real_escape_string($conn, $_POST['ig']);
    $adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
    $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
    $nrtel = mysqli_real_escape_string($conn, $_POST['nrtel']);
    $emailadd = mysqli_real_escape_string($conn, $_POST['emailadd']);
    $emailp = mysqli_real_escape_string($conn, $_POST['emailp']);
    $emriart = mysqli_real_escape_string($conn, $_POST['emriart']);
    $nrllog = mysqli_real_escape_string($conn, $_POST['nrllog']);
    $perdoruesi = mysqli_real_escape_string($conn, $_POST['perdoruesi']);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $emails = '';
    if (isset($_POST['emails']) && !empty($_POST['emails'])) {
        $emails = implode(', ', $_POST['emails']);
    }
    $password = md5($password);

    $targetfolder = "dokument/";

    $targetfolder = $targetfolder . basename($_FILES['tipi']['name']);

    $ok = 1;

    $file_type = $_FILES['tipi']['type'];

    if ($file_type == "application/pdf") {

        if (move_uploaded_file($_FILES['tipi']['tmp_name'], $targetfolder)) {
        } else {
        }
    } else {
    }

    $emails = addslashes($emails);
    if ($conn->query(
        "INSERT INTO klientet 
    (emri, np, monetizuar, dk, dks, youtube, info, perqindja, perqindja2, kontrata, ads, fb, ig, adresa, kategoria, nrtel, emailadd, emailp, emriart, nrllog, fjalkalimi, perdoruesi, emails, blocked) VALUES ('$emri', '$np','$mon', '$dk', '$dks', '$yt', '$info', '$perq', '$perq2', '$targetfolder', '$ads', '$fb', '$ig', '$adresa', '$kategoria', '$nrtel', '$emailadd', '$emailp', '$emriart', '$nrllog', '$password', '$perdoruesi', '$emails', '0')"
    )) {
        $kueri = $conn->query("SELECT * FROM klientet ORDER BY id DESC");
        $k = mysqli_fetch_array($kueri);
        $cdata = date("Y-m-d H:i:s");
        $cname = $_SESSION['emri'];
        $cnd = $cname . " ka shtuar  klientin " . $emri;
        $query = "INSERT INTO logs (stafi, ndryshimi, koha) VALUES ('$cname', '$cnd', '$cdata')";
        if ($conn->query($query)) {
        } else {
            echo '<script>alert("' . $conn->error . '")</script>';
        }



        // Add the Sweet Alert with a button to go to the newly added client page
        echo '<script>
    Swal.fire({
      icon: "success",
      title: "Kengetari u shtua me sukses!",
      showConfirmButton: true,
      showCancelButton: true,
      cancelButtonText: "Mbylle",
      allowOutsideClick: false,
      allowEscapeKey: false,
      closeOnClickOutside: false,
      closeOnEsc: false,
    }).then((result) => {
     if (result.isConfirmed) {
       window.location.href = "kontrata_gjenelare_2.php"; // Adjust the link to the actual URL of the client page
     }
   });
   </script>';
    }
}
?>
<style>
    .allocation-div {
        display: none;
    }

    .allocation-div.active {
        display: block;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 rounded-5 shadow-sm mb-4 card">
                    <div class="alert alert-successalert-dismissible" id="success" style="display:none;">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">X</a>
                    </div>
                    <div>
                        <a href="kontrata_gjenelare_2.php" class="btn btn-primary btn-sm rounded-5 text-light"><i class="ti-arrow-left"></i> <i class="fi fi-rr-arrow-left"></i></a>
                    </div>
                    <br>
                    <!-- Page Heading -->
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="col">
                                <label class="form-label" for="emri">Emri & Mbiemri</label>
                                <input type="text" name="emri" id="emri" class="form-control shadow-sm rounded-5" placeholder="Shkruaj Emrin Mbiemrin">
                            </div>
                            <div class="col">
                                <label class="form-label" for="emri">Emri artistik</label>
                                <input type="text" name="emriart" id="emriart" class="form-control shadow-sm rounded-5" placeholder="Emri artistik">
                            </div>

                        </div>


                        <div class="form-group row">
                            <div class="col">
                                <label class="form-label" for="yt">Nr.Tel</label>
                                <input type="text" name="nrtel" id="nrtel" class="form-control shadow-sm rounded-5" placeholder="Nr.Tel" autocomplete="off">
                            </div>
                            <div class="col">
                                <label class="form-label" for="yt">Email Adresa</label>
                                <input type="text" name="emailadd" id="emailadd" class="form-control shadow-sm rounded-5" placeholder="Email Adresa" autocomplete="off">
                            </div>
                        </div>











                </div>
                <br>
                <center> <button type="submit" class="btn btn-primary" name="ruaj"><i class="ti-save"></i> Ruaj</button> </center>
                </form>

            </div>
            <!-- /.container-fluid -->
        </div>
    </div>
</div>

<script>
    const allocationDivs = document.querySelectorAll('.allocation-div');
    const createAllocationBtn = document.querySelector('#create-allocation-btn');
    let currentDivIndex = 0;

    createAllocationBtn.addEventListener('click', () => {
        if (currentDivIndex < allocationDivs.length) {
            allocationDivs[currentDivIndex].classList.add('active');
            currentDivIndex++;
        }

        if (currentDivIndex >= allocationDivs.length) {
            createAllocationBtn.disabled = true;
        }
    });

    // Your YouTube Data API key
</script>
<?php include 'partials/footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#yt').on('input', function() {
            var channelId = $(this).val();

            // Check if the input is empty
            if (!channelId) {
                $('#channelInfo').html('');
                $('#emriart').val(''); // Clear the artistik name input
                return;
            }

            // Your YouTube Data API key
            var apiKey = 'AIzaSyCjlRRPMTbGcM_QE081YCy4zHKI9sUaZTg';

            // Make a request to the YouTube Data API to get channel information
            var url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&id=' + channelId + '&key=' + apiKey;

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data.items && data.items.length > 0) {
                        var channelInfo = data.items[0].snippet;
                        var channelStatistics = data.items[0].statistics;


                        // Set the channel name in the "emriart" input field
                        $('#emriart').val(channelInfo.title);
                    } else {
                        showErrorAlert('Channel not found.');
                    }
                },
                error: function() {
                    showErrorAlert('Unable to fetch channel information.');
                }
            });
        });

        // Function to show error alerts
        function showErrorAlert(message) {
            $('#channelInfo').html('<div class="alert alert-danger">' + message + '</div>');
            $('#emriart').val(''); // Clear the artistik name input
        }
    });
</script>