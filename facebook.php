<?php include_once 'partials/header.php' ?>

<style>
    .btn-facebook {
        position: relative;
        overflow: hidden;
    }

    .btn-facebook:hover .icon {
        transform: translateY(-140%);
    }

    .btn-facebook .icon {
        position: absolute;
        top: 100%;
        left: 15px;
        transition: transform 0.3s ease;
    }
</style>


<?php
// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve the form data
    $emri = $_POST['emri_mbiemri'];
    $emrifull = $_POST['emri_faqes'];
    $data = $_POST['dataKrijimit'];
    $fatura = ''; // Leave this empty since it will be auto-generated
    $klientit = ''; // Leave this empty
    $mbetja = ''; // Leave this empty
    $totali = ''; // Leave this empty
    $dataSkadimit = $_POST['dataSkadimit'];
    $gjendja_e_fatures = ''; // Leave this empty

    // Perform any necessary validation on the form data

    // Process the data or save it to the database
    // Assuming you have a connection to the database established in conn-d.php

    // Insert the form data into the 'fatura_facebook' table
    $query = "INSERT INTO fatura_facebook (emri, emrifull, data, fatura,gjendja_e_fatures) VALUES ('$emri', '$emrifull', '$fatura', '$klientit', '$mbetja', '$totali', '$data', '$dataSkadimit', '$gjendja_e_fatures')";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        // The data was successfully inserted into the database
        echo "Klienti u regjistrua me sukses!";
    } else {
        // There was an error with the query
        echo "Gabim gjat&euml; regjistrimit t&euml; klientit: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>



<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile')
                                echo 'active'; ?>" id="pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                                style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile')
                                    echo 'true';
                                else
                                    echo 'false'; ?>"><i class="fi fi-rr-address-book me-2"></i>Lista e
                                klienteve</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register')
                                echo 'active'; ?>" id="pills-register-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register"
                                style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register')
                                    echo 'true';
                                else
                                    echo 'false'; ?>">
                                <i class="fi fi-rr-user-add me-2"></i>
                                Regjistro klient
                            </button>
                        </li>


                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'adsregister')
                                echo 'active'; ?>" id="pills-adsregister-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-adsregister" type="button" role="tab"
                                aria-controls="pills-adsregister"
                                style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'adsregister')
                                    echo 'true';
                                else
                                    echo 'false'; ?>">
                                <i class="fi fi-rr-user-add me-2"></i>
                                Llogarit&euml; e ADS
                            </button>
                        </li>



                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'emailadd')
                                echo 'active'; ?>" id="pills-emailadd-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-emailadd" type="button" role="tab" aria-controls="pills-emailadd"
                                style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'emailadd')
                                    echo 'true';
                                else
                                    echo 'false'; ?>">
                                <i class="fi fi-rr-envelope me-2"></i>
                                Lista e emaila-ve
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kategoria')
                                echo 'active'; ?>" id="pills-kategoria-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-kategoria" type="button" role="tab"
                                aria-controls="pills-kategoria" style="text-transform: none;border:1px solid lightgrey;"
                                aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kategoria')
                                    echo 'true';
                                else
                                    echo 'false'; ?>">
                                <i class="fi fi-rr-note me-2"></i>
                                Lista e kategorive
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register')
                            echo 'show active'; ?>" id="pills-register" role="tabpanel"
                            aria-labelledby="pills-register-tab" tabindex="0">
                            <form method="POST" action="add-client.php">
                                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                                    <h6 class="card-title" style="text-transform:none;">Plotso formularin per krijimin e
                                        nje klienti te ri ne grupin Facebook</h6>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="emri_mbiemri" class="form-label">Emri dhe mbiemri</label>
                                            <input type="text" name="emri_mbiemri" id="emri_mbiemri"
                                                class="form-control shadow-sm rounded-5">

                                        </div>
                                        <div class="col">
                                            <label for="emri_faqes" class="form-label">Emri i faqes</label>
                                            <input type="text" name="emri_faqes" id="emri_faqes"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="dataKrijimit" class="form-label">Data e krijimit te
                                                kontrates</label>
                                            <input type="date" name="dataKrijimit" id="dataKrijimit"
                                                class="form-control shadow-sm rounded-5">

                                        </div>
                                        <div class="col">
                                            <label for="dataSkadimit" class="form-label">Data e skadimit te
                                                kontrates</label>
                                            <input type="date" name="dataSkadimit" id="dataSkadimit"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="linkuFaqes" class="form-label">Linku i faqes</label>
                                            <input type="text" name="linkuFaqes" id="linkuFaqes"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="numriPersonal" class="form-label">Numri personal i
                                                klientit</label>
                                            <input type="number" name="numriPersonal" id="numriPersonal"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <!-- <label for="adsAccount" class="form-label">ADS Account: </label>
                                            <input type="text" name="merre_adresen" id="merre_adresen"
                                                class="form-control shadow-sm rounded-5"> -->
                                            <label for="exampleFormControlSelect2" class="form-label">ADS Account:
                                            </label>
                                            <select class="form-select shadow-sm rounded-5 py-2" name="merre_adresen"
                                                id="exampleFormControlSelect2">
                                                <?php
                                                include 'conn-d.php';

                                                $adresa = $conn->query("SELECT * FROM facebook_ads");

                                                while ($merre_adresen = mysqli_fetch_array($adresa)) {
                                                    $selected = ($merre_adresen['id'] == $editcl['merre_adresen']) ? "selected" : "";
                                                    echo '<option value="' . $merre_adresen['email'] . '" ' . $selected . '>' . $merre_adresen['email'] . ' | ' . $merre_adresen['adsID'] . ' (' . $merre_adresen['shteti'] . ')</option>';
                                                }

                                                mysqli_close($conn);
                                                ?>
                                            </select>

                                        </div>
                                        <div class="col">
                                            <label for="kategoria" class="form-label">Kategoria</label>
                                            <!-- <input type="text" name="kategoria" id="kategoria"
                                                class="form-control shadow-sm rounded-5"> -->

                                            <!-- <select class="form-select shadow-sm rounded-5 py-2" name="kategoria">
                                                <option value="ASCAP">ASCAP</option>
                                                <option value="IBM">IBM</option>
                                                <option value="PRS">PRS</option>
                                                <option value="GEMA.DE">GEMA.DE</option>
                                            </select> -->
                                            <select class="form-select shadow-sm rounded-5 py-2" name="kategoria"
                                                id="kategoria">
                                                <?php
                                                include 'conn-d.php';

                                                $adresa = $conn->query("SELECT * FROM facebook_category");

                                                while ($merre_adresen = mysqli_fetch_array($adresa)) {
                                                    $selected = ($merre_adresen['id'] == $editcl['merre_adresen']) ? "selected" : "";
                                                    echo '<option value="' . $merre_adresen['kategoria'] . '" ' . $selected . '>' . $merre_adresen['kategoria'] . '</option>';
                                                }

                                                mysqli_close($conn);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="numriTelefonit" class="form-label">Numri i telefonit</label>
                                            <input type="text" name="numriTelefonit" id="numriTelefonit"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="perqindja" class="form-label">P&euml;rqindja</label>
                                            <input type="number" name="perqindja" id="perqindja"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="numriXhirollogarise" class="form-label">Numri i
                                                xhirollogaris&euml;</label>
                                            <input type="text" name="numriXhirollogarise" id="numriXhirollogarise"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="adresa" class="form-label">Adresa</label>
                                            <input type="text" name="adresa" id="adresa"
                                                class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="infoShtese" class="form-label">Info shtes&euml;</label>
                                            <textarea name="infoShtese" id="infoShtese" cols="30" rows="10"
                                                class="form-control shadow-sm rounded-5 w-100"></textarea>
                                        </div>
                                        <div class="col">
                                            <label for="tel">Monetizuar ? </label><br>
                                            <input type="radio" id="html" name="min" value="PO">
                                            <label for="html" style="color:green;">PO</label>
                                            <input type="radio" id="css" name="min" value="JO">
                                            <label for="css" style="color:red;">JO</label><br>

                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn rounded-5 btn-facebook px-5"
                                            style="text-transform: none;" name="submit">
                                            <i class="fa-solid fa-plus icon"
                                                style="display: inline-block; vertical-align: middle;"></i>
                                            <span
                                                style="display: inline-block; vertical-align: middle;">Regjistro</span>
                                        </button>

                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade <?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile')
                            echo 'show active'; ?>" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab" tabindex="0">
                            <div class="table-responsive">
                                <!-- Table -->
                                <table id="example" class="table table-border">
                                    <thead class="bg-light">
                                        <tr>

                                            <th>Emri dhe mbiemri</th>
                                            <th>Emri i faqes</th>
                                            <th>Data e krijimit</th>
                                            <th>Data e skadimit</th>
                                            <th>Linku i faqes</th>
                                            <th>Numri personal</th>
                                            <th>ADS Account</th>
                                            <th>Kategoria</th>
                                            <th>Numri i telefonit</th>
                                            <th>P&euml;rqindja</th>
                                            <th>Numri i xhirollogaris&euml;</th>
                                            <th>Adresa</th>
                                            <th>Info shtes&euml;</th>
                                            <th>Monetizuar</th>
                                            <th>Veprimet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'conn-d.php';

                                        // Check if the 'del' parameter is set in the URL
                                        if (isset($_GET['del'])) {
                                            $id = $_GET['del'];

                                            // Retrieve the name of the row before deleting
                                            $nameQuery = "SELECT emri_mbiemri FROM facebook WHERE id = '$id' ORDER BY id ASC";
                                            $nameResult = mysqli_query($conn, $nameQuery);
                                            $row = mysqli_fetch_assoc($nameResult);

                                            if ($row) {
                                                $name = $row['emri_mbiemri'];

                                                // Delete the row with the given ID from the "facebook" table
                                                $deleteQuery = "DELETE FROM facebook WHERE id = '$id'";
                                                $deleteResult = mysqli_query($conn, $deleteQuery);

                                                if ($deleteResult) {
                                                    echo "<p class='text-success p-2 rounded-5 shadow-sm border w-25'>Rreshti i fshir&euml;: $name</p>";
                                                } else {
                                                    echo "<p class='text-danger'>Fshirja e rreshtit d&euml;shtoi. Ju lutemi provoni p&euml;rs&euml;ri.</p>";
                                                }
                                            } else {
                                                echo "<p class='text-danger'></p>";
                                            }
                                        }

                                        // Retrieve data from the "facebook" table
                                        $sql = "SELECT * FROM facebook ORDER BY id desc";
                                        $result = mysqli_query($conn, $sql);

                                        // Loop through each row of data and display it in the table
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";

                                            echo '<td><a class="badge rounded-pill text-bg-success text-white w-100 shadow-sm" href="facebook-account.php?kid=' . $row['id'] . '">' . $row['emri_mbiemri'] . '</a></td>';

                                            echo "<td>" . $row['emri_faqes'] . "</td>";
                                            echo "<td>" . $row['dataKrijimit'] . "</td>";
                                            echo "<td>" . $row['dataSkadimit'] . "</td>";
                                            echo "<td>" . $row['linkuFaqes'] . "</td>";
                                            echo "<td>" . $row['numriPersonal'] . "</td>";
                                            echo "<td>" . $row['adsAccount'] . "</td>";
                                            echo "<td>" . $row['kategoria'] . "</td>";
                                            echo "<td>" . $row['numriTelefonit'] . "</td>";
                                            echo "<td>" . $row['perqindja'] . "</td>";
                                            echo "<td>" . $row['numriXhirollogarise'] . "</td>";
                                            echo "<td>" . $row['adresa'] . "</td>";
                                            echo "<td>" . $row['infoShtese'] . "</td>";
                                            echo "<td>" . $row['monetizuar'] . "</td>";

                                            // Buttons for edit and delete
                                            echo "<td>";

                                            echo "<a class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#edit" . $row['id'] . "'><i class='fi fi-rr-edit'></i></a> &nbsp;";
                                            echo "<a class='btn btn-danger' href='?del=" . $row['id'] . "'><i class='fi fi-rr-trash'></i></a>";
                                            echo "</td>";

                                            echo "</tr>";
                                        }

                                        // Close the database conn
                                        mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>



                            </div>
                        </div>
                        <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'adsregister')
                            echo 'show active'; ?>" id="pills-adsregister" role="tabpanel"
                            aria-labelledby="pills-adsregister-tab" tabindex="0">
                            <div class="p-5 shadow-sm rounded-5 mb-4 card">
                                <form action="facebook_ads.php" method="post">
                                    <div class="row">
                                        <div class="col">
                                            <label for="email_ads" class="form-label">Email</label>
                                            <input type="text" class="form-control shadow-sm rounded-5" name="email_ads"
                                                id="email_ads">
                                        </div>
                                        <div class="col">
                                            <label for="adsID" class="form-label">ADS ID</label>
                                            <input type="text" class="form-control shadow-sm rounded-5" name="adsID"
                                                id="adsID">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <label for="shteti" class="form-label">Shteti</label>
                                            <input type="text" class="form-control shadow-sm rounded-5" name="shteti"
                                                id="shteti">
                                        </div>
                                        <div class="col">

                                        </div>
                                    </div>
                                    <br>
                                    <button type="submit" class="btn btn-light rounded-5 float-right border"
                                        style="text-transform:none;" name="submit">
                                        <i class="fi fi-rr-paper-plane"
                                            style="display:inline-block;vertical-align:middle;"></i>
                                        <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                    </button>
                                </form>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Email</th>
                                                <th>Ads ID</th>
                                                <th>Shteti</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include 'conn-d.php';

                                            // Retrieve data from the "facebook_ads" table
                                            $query = "SELECT * FROM facebook_ads ORDER BY id DESC";
                                            $result = mysqli_query($conn, $query);

                                            // Loop through each row of data and display it in the table
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['email'] . "</td>";
                                                echo "<td>" . $row['ads_id'] . "</td>";
                                                echo "<td>" . $row['shteti'] . "</td>";
                                                echo "<td>";
                                                echo "<button class='btn btn-primary edit-btn' data-toggle='modal' data-target='#editModal' data-id='" . $row['id'] . "' data-email='" . $row['email'] . "' data-adsid='" . $row['ads_id'] . "' data-shteti='" . $row['shteti'] . "'>Edit</button>";

                                                echo " <a href='delete-ads.php?id=" . $row['id'] . "' class='btn btn-danger'>Delete</a>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }

                                            mysqli_close($conn);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>


                            </div>


                        </div>



                        <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'emailadd')
                            echo 'show active'; ?>" id="pills-emailadd" role="tabpanel"
                            aria-labelledby="pills-emailadd-tab" tabindex="0">
                            <div class="p-5 shadow-sm rounded-5 mb-4 card">
                                <form action="add-email.php" method="post">
                                    <label for="email_facebook" class="form-label">Email-i</label>
                                    <input type="text" name="email_facebook" id="email_facebook"
                                        class="form-control shadow-sm rounded-5">
                                    <br>
                                    <button type="submit" class="btn btn-light rounded-5 float-right border"
                                        style="text-transform:none;" name="submit">
                                        <i class="fi fi-rr-paper-plane"
                                            style="display:inline-block;vertical-align:middle;"></i>
                                        <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                    </button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table id="tabelaEmails" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'conn-d.php';

                                        // Retrieve data from the "facebook_emails" table
                                        $sql = "SELECT * FROM facebook_emails ORDER BY id DESC";
                                        $result = mysqli_query($conn, $sql);

                                        // Loop through each row of data and display it in the table
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['email'] . "</td>";
                                            echo "<td><a class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editModal" . $row['id'] . "'><i class='fi fi-rr-edit'></i></a></td>";
                                            echo "<td><a class='btn btn-danger' href='delete-email.php?id=" . $row['id'] . "'><i class='fi fi-rr-trash'></i></a></td>";
                                            echo "</tr>";

                                            // Edit Modal for each email
                                            echo "<div class='modal fade' id='editModal" . $row['id'] . "' tabindex='-1' aria-labelledby='editModalLabel" . $row['id'] . "' aria-hidden='true'>";
                                            echo "<div class='modal-dialog'>";
                                            echo "<div class='modal-content'>";
                                            echo "<div class='modal-header'>";
                                            echo "<h5 class='modal-title' id='editModalLabel" . $row['id'] . "'>Edit Email</h5>";
                                            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                            echo "</div>";
                                            echo "<div class='modal-body'>";
                                            echo "<form action='edit-email.php' method='post'>";
                                            echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                                            echo "<label for='email_edit' class='form-label'>Email</label>";
                                            echo "<input type='text' name='email_edit' id='email_edit' class='form-control shadow-sm rounded-5' value='" . $row['email'] . "'>";
                                            echo "</div>";
                                            echo "<div class='modal-footer'>";
                                            echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
                                            echo "<button type='submit' class='btn btn-primary'>Save changes</button>";
                                            echo "</form>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }

                                        mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>


                        <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kategoria')
                            echo 'show active'; ?>" id="pills-kategoria" role="tabpanel"
                            aria-labelledby="pills-kategoria-tab" tabindex="0">
                            <div class="p-5 shadow-sm rounded-5 mb-4 card">
                                <form action="add-category.php" method="POST">
                                    <label class="form-label" for="kategori">Kategori e re</label>
                                    <input type="text" name="kategori" id="kategori"
                                        class="form-control shadow-sm rounded-5">
                                    <br>
                                    <button type="submit" class="btn btn-light rounded-5 float-right border"
                                        style="text-transform:none;" name="submit">
                                        <i class="fi fi-rr-paper-plane"
                                            style="display:inline-block;vertical-align:middle;"></i>
                                        <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                    </button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table id="tabelaKategorite" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kategoria</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'conn-d.php';

                                        // Retrieve data from the "facebook_category" table
                                        $sql = "SELECT * FROM facebook_category ORDER BY id DESC";
                                        $result = mysqli_query($conn, $sql);

                                        // Loop through each row of data and display it in the table
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['kategoria'] . "</td>";
                                            echo "<td>";
                                            echo "<a class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editModal" . $row['id'] . "'><i class='fi fi-rr-edit'></i></a> &nbsp;";
                                            echo "<a class='btn btn-danger' href='delete-category.php?id=" . $row['id'] . "'><i class='fi fi-rr-trash'></i></a>";
                                            echo "</td>";
                                            echo "</tr>";

                                            // Edit Modal
                                            echo "<div class='modal fade' id='editModal" . $row['id'] . "' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>";
                                            echo "<div class='modal-dialog'>";
                                            echo "<div class='modal-content'>";
                                            echo "<div class='modal-header'>";
                                            echo "<h5 class='modal-title' id='editModalLabel'>Edit Category</h5>";
                                            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                            echo "</div>";
                                            echo "<div class='modal-body'>";
                                            echo "<form action='edit-category.php?id=" . $row['id'] . "' method='POST'>";
                                            echo "<label class='form-label' for='kategori'>Kategoria e re</label>";
                                            echo "<input type='text' name='kategori' id='kategori' class='form-control shadow-sm rounded-5' value='" . $row['kategoria'] . "'>";
                                            echo "<br>";
                                            echo "<button type='submit' class='btn btn-primary' name='submit'>Save Changes</button>";
                                            echo "</form>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }

                                        mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="modal fade" id="editModal" tabindex="-1" role="dialog"
                            aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Row</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="editForm" action="update-ads.php" method="post">
                                            <div class="form-group">
                                                <label for="email_ads_edit">Email</label>
                                                <input type="text" class="form-control" name="email_ads_edit"
                                                    id="email_ads_edit">
                                            </div>
                                            <div class="form-group">
                                                <label for="adsID_edit">ADS ID</label>
                                                <input type="text" class="form-control" name="adsID_edit"
                                                    id="adsID_edit">
                                            </div>
                                            <div class="form-group">
                                                <label for="shteti_edit">Shteti</label>
                                                <input type="text" class="form-control" name="shteti_edit"
                                                    id="shteti_edit">
                                            </div>
                                            <input type="hidden" name="row_id" id="row_id">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include 'partials/footer.php'; ?>
<script>
    $(document).ready(function () {
        $('.edit-btn').click(function () {
            var id = $(this).data('id');
            var email = $(this).data('email');
            var adsID = $(this).data('adsid');
            var shteti = $(this).data('shteti');

            $('#row_id').val(id);
            $('#email_ads_edit').val(email);
            $('#adsID_edit').val(adsID);
            $('#shteti_edit').val(shteti);

            $('#editModal').modal('show');
        });
    });

    function getPerqindja() {
        // Get the selected value from the select element
        var select = document.getElementById("nameSurname");
        var selectedValue = select.value;

        // Make an AJAX request to a PHP file to fetch the corresponding "perqindja" value
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                // Update the "perqindja" input field with the fetched value
                document.getElementById("perqindjaE").value = this.responseText;
            }
        };
        xhttp.open("GET", "fetch_perqindja.php?nameSurname=" + selectedValue, true);
        xhttp.send();
    }
</script>

<script>
    // Retrieve the active tab from local storage if available
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        // Remove the 'active' class from all tabs and tab panes
        const tabs = document.querySelectorAll('.nav-link');
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabs.forEach(tab => tab.classList.remove('active'));
        tabPanes.forEach(pane => pane.classList.remove('show', 'active'));

        // Add the 'active' class to the previously active tab and tab pane
        const activeTabButton = document.querySelector(`#${activeTab}-tab`);
        const activeTabPane = document.querySelector(`#${activeTab}`);
        if (activeTabButton && activeTabPane) {
            activeTabButton.classList.add('active');
            activeTabPane.classList.add('show', 'active');
        }
    }

    // Save the active tab to local storage when a tab is clicked
    const tabButtons = document.querySelectorAll('.nav-link');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const activeTabId = button.getAttribute('aria-controls');
            localStorage.setItem('activeTab', activeTabId);
        });
    });
</script>
<script>
    $('#example').DataTable({
        responsive: true,

        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "T&euml; gjitha"]
        ],
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' + // length menu and search input layout with margin bottom
            'Brtip',
        buttons: [{
            extend: 'pdfHtml5',
            text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            titleAttr: 'Eksporto tabelen ne formatin PDF',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'copyHtml5',
            text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            titleAttr: 'Kopjo tabelen ne formatin Clipboard',
            className: 'btn btn-light border shadow-2 me-2'
        }, {
            extend: 'excelHtml5',
            text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
            titleAttr: 'Eksporto tabelen ne formatin Excel',
            className: 'btn btn-light border shadow-2 me-2',
        }, {
            extend: 'print',
            text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            titleAttr: 'Printo tabel&euml;n',
            className: 'btn btn-light border shadow-2 me-2'
        },],
        initComplete: function () {
            var btns = $('.dt-buttons');
            btns.addClass('');
            btns.removeClass('dt-buttons btn-group');
            var lengthSelect = $('div.dataTables_length select');
            lengthSelect.addClass('form-select'); // add Bootstrap form-select class
            lengthSelect.css({
                'width': 'auto', // adjust width to fit content
                'margin': '0 8px', // add some margin around the element
                'padding': '0.375rem 1.75rem 0.375rem 0.75rem', // adjust padding to match Bootstrap's styles
                'line-height': '1.5', // adjust line-height to match Bootstrap's styles
                'border': '1px solid #ced4da', // add border to match Bootstrap's styles
                'border-radius': '0.25rem', // add border radius to match Bootstrap's styles
            }); // adjust width to fit content
        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],
        order:false,

    });
</script>