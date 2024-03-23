<?php
session_start();
// Function to generate a CSRF token
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
// Check if CSRF token exists in session, generate a new one if not
if (isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generate_csrf_token();
}
include_once 'partials/header.php' ?>
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
            <div class="p-5 shadow-sm rounded-5 mb-4 card">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 <?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile')
                                                                echo 'active'; ?>" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile')
                                                                                                                                                                                                                                                                                                    echo 'true';
                                                                                                                                                                                                                                                                                                else
                                                                                                                                                                                                                                                                                                    echo 'false'; ?>"><i class="fi fi-rr-address-book me-2"></i>Lista e
                            klienteve</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register')
                                                                echo 'active'; ?>" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register')
                                                                                                                                                                                                                                                                                                        echo 'true';
                                                                                                                                                                                                                                                                                                    else
                                                                                                                                                                                                                                                                                                        echo 'false'; ?>">
                            <i class="fi fi-rr-user-add me-2"></i>
                            Regjistro klient
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'adsregister')
                                                                echo 'active'; ?>" id="pills-adsregister-tab" data-bs-toggle="pill" data-bs-target="#pills-adsregister" type="button" role="tab" aria-controls="pills-adsregister" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'adsregister')
                                                                                                                                                                                                                                                                                                                echo 'true';
                                                                                                                                                                                                                                                                                                            else
                                                                                                                                                                                                                                                                                                                echo 'false'; ?>">
                            <i class="fi fi-rr-user-add me-2"></i>
                            Llogarit&euml; e ADS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'emailadd')
                                                                echo 'active'; ?>" id="pills-emailadd-tab" data-bs-toggle="pill" data-bs-target="#pills-emailadd" type="button" role="tab" aria-controls="pills-emailadd" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'emailadd')
                                                                                                                                                                                                                                                                                                        echo 'true';
                                                                                                                                                                                                                                                                                                    else
                                                                                                                                                                                                                                                                                                        echo 'false'; ?>">
                            <i class="fi fi-rr-envelope me-2"></i>
                            Lista e emaila-ve
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kategoria')
                                                                echo 'active'; ?>" id="pills-kategoria-tab" data-bs-toggle="pill" data-bs-target="#pills-kategoria" type="button" role="tab" aria-controls="pills-kategoria" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kategoria')
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
                                                    echo 'show active'; ?>" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab" tabindex="0">
                        <form method="POST" action="add-client.php">
                            <div class="p-5 shadow-sm rounded-5 mb-4 card">
                                <h6 class="card-title" style="text-transform:none;">Plotso formularin per krijimin e
                                    nje klienti te ri ne grupin Facebook</h6>
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="emri_mbiemri" class="form-label">Emri dhe mbiemri</label>
                                        <input type="text" name="emri_mbiemri" id="emri_mbiemri" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col">
                                        <label for="emri_faqes" class="form-label">Emri i faqes</label>
                                        <input type="text" name="emri_faqes" id="emri_faqes" class="form-control shadow-sm rounded-5">
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="dataKrijimit" class="form-label">Data e krijimit te kontrates</label>
                                        <input type="text" name="dataKrijimit" id="dataKrijimit" class="form-control shadow-sm rounded-5 " readonly>
                                    </div>
                                    <div class="col">
                                        <label for="dataSkadimit" class="form-label">Data e skadimit te kontrates</label>
                                        <input type="text" name="dataSkadimit" id="dataSkadimit" class="form-control shadow-sm rounded-5 " readonly>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="linkuFaqes" class="form-label">Linku i faqes</label>
                                        <input type="text" name="linkuFaqes" id="linkuFaqes" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col">
                                        <label for="numriPersonal" class="form-label">Numri personal i
                                            klientit</label>
                                        <input type="number" name="numriPersonal" id="numriPersonal" class="form-control shadow-sm rounded-5">
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <!-- <label for="adsAccount" class="form-label">ADS Account: </label>
                                            <input type="text" name="merre_adresen" id="merre_adresen"
                                                class="form-control shadow-sm rounded-5"> -->
                                        <label for="merre_adresen" class="form-label">ADS Account:
                                        </label>
                                        <select class="form-select shadow-sm rounded-5 py-2" name="merre_adresen" id="merre_adresen">
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
                                        <select class="form-select shadow-sm rounded-5 py-2" name="kategoria" id="kategoria">
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
                                        <input type="text" name="numriTelefonit" id="numriTelefonit" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col">
                                        <label for="perqindja" class="form-label">P&euml;rqindja</label>
                                        <input type="number" name="perqindja" id="perqindja" class="form-control shadow-sm rounded-5">
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="numriXhirollogarise" class="form-label">Numri i
                                            xhirollogaris&euml;</label>
                                        <input type="text" name="numriXhirollogarise" id="numriXhirollogarise" class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col">
                                        <label for="adresa" class="form-label">Adresa</label>
                                        <input type="text" name="adresa" id="adresa" class="form-control shadow-sm rounded-5">
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="infoShtese" class="form-label">Info shtes&euml;</label>
                                        <textarea name="infoShtese" id="infoShtese" cols="30" rows="10" class="form-control shadow-sm rounded-5 w-100"></textarea>
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
                                    <button type="submit" class="input-custom-css px-3 py-2" style="text-transform: none;" name="submit">
                                        Regjistro
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade <?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile')
                                                    echo 'show active'; ?>" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                        <div class="table-responsive">
                            <!-- Table -->
                            <?php
                            // Include the connection file
                            include 'conn-d.php';
                            class FacebookTable
                            {
                                private $conn;
                                // Constructor to initialize the database connection
                                public function __construct($conn)
                                {
                                    $this->conn = $conn;
                                }
                                // Method to fetch and display data in the table
                                public function displayTable()
                                {
                                    // Check if the 'del' parameter is set in the URL
                                    if (isset($_GET['del'])) {
                                        $id = $_GET['del'];
                                        // Retrieve the name of the row before deleting
                                        $nameQuery = "SELECT emri_mbiemri FROM facebook WHERE id = '$id' ORDER BY id ASC";
                                        $nameResult = mysqli_query($this->conn, $nameQuery);
                                        $row = mysqli_fetch_assoc($nameResult);
                                        if ($row) {
                                            $name = $row['emri_mbiemri'];
                                            // Delete the row with the given ID from the "facebook" table
                                            $deleteQuery = "DELETE FROM facebook WHERE id = '$id'";
                                            $deleteResult = mysqli_query($this->conn, $deleteQuery);
                                            if ($deleteResult) {
                                                echo "<p class='text-success p-2 rounded-5 shadow-sm border w-25'>Rreshti i fshirë: $name</p>";
                                            } else {
                                                echo "<p class='text-danger'>Fshirja e rreshtit dështoi. Ju lutemi provoni përsëri.</p>";
                                            }
                                        } else {
                                            echo "<p class='text-danger'></p>";
                                        }
                                    }
                                    // Retrieve data from the "facebook" table
                                    $sql = "SELECT * FROM facebook ORDER BY id desc";
                                    $result = mysqli_query($this->conn, $sql);
                                    // Loop through each row of data and display it in the table
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo '<td> ' . $row['emri_mbiemri'] . '</td>';
                                        echo "<td>" . $row['emri_faqes'] . "</td>";
                                        echo "<td><a class='input-custom-css px-3 py-2' target='_blank' style='text-decoration: none;' href='" . $row['linkuFaqes'] . "'>Linku</a></td>";
                                        echo "<td>" . $row['infoShtese'] . "</td>";
                                        $monetizuar = $row['monetizuar'];
                                        $color = ($monetizuar == 'PO') ? 'green' : 'red';
                                        echo "<td style='color: $color;'>" . $monetizuar . "</td>";
                                        echo "<td>";
                                        echo "<a class='btn btn-primary text-white rounded-5 px-2 py-2' href='facebook-account.php?kid=" . $row['id'] . "'><i class='fi fi-rr-edit'></i></a> &nbsp;";
                                        echo "<a class='btn btn-danger text-white rounded-5 px-2 py-2' onclick='confirmDelete(" . $row['id'] . ")'><i class='fi fi-rr-trash'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                            $table = new FacebookTable($conn);
                            ?>
                            <table id="example" class="table table-border  w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Emri dhe mbiemri</th>
                                        <th>Emri i faqes</th>
                                        <th>Linku i faqes</th>
                                        <th>Info shtesë</th>
                                        <th>Monetizuar</th>
                                        <th>Veprimet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $table->displayTable();
                                    ?>
                                </tbody>
                            </table>
                            <?php
                            mysqli_close($conn);
                            ?>
                        </div>
                    </div>
                    <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'adsregister')
                                                    echo 'show active'; ?>" id="pills-adsregister" role="tabpanel" aria-labelledby="pills-adsregister-tab" tabindex="0">
                        <div class="p-5 shadow-sm rounded-5 mb-4 card">
                            <form action="facebook_ads.php" method="post" onsubmit="return validateForm()">
                                <!-- CSRF Token -->
                                <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <div class="row">
                                    <div class="col">
                                        <label for="email_ads" class="form-label">Email</label>
                                        <input type="email" class="form-control shadow-sm rounded-5" name="email_ads" id="email_ads" placeholder="Shëno email-in" required>
                                        <div id="email_error" class="text-danger"></div>
                                    </div>
                                    <div class="col">
                                        <label for="adsID" class="form-label">ADS ID</label>
                                        <input type="text" class="form-control shadow-sm rounded-5" name="adsID" id="adsID" placeholder="Shëno ADS ID" required>
                                        <div id="adsID_error" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <label for="shteti" class="form-label">Shteti</label>
                                        <input type="text" class="form-control shadow-sm rounded-5" name="shteti" id="shteti" placeholder="Shëno shtetin" required>
                                        <div id="shteti_error" class="text-danger"></div>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <br>
                                <button type="submit" class="input-custom-css px-3 py-2 rounded-5 float-right" style="text-transform:none;" name="submit">
                                    <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                                    <span style="display:inline-block;vertical-align:middle;">Regjistro llogari të re</span>
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
                                            <th>Veprimet</th>
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
                                            echo "<button class='input-custom-css px-3 py-2 edit-btn' data-toggle='modal' data-target='#editModal' data-id='" . $row['id'] . "' data-email='" . $row['email'] . "' data-adsid='" . $row['ads_id'] . "' data-shteti='" . $row['shteti'] . "'><i class='fi fi-rr-edit'></i></button>";
                                            echo " <a style='text-decoration:none;text-transform:none' href='#' onclick='confirmDelete(" . $row['id'] . ")' class='input-custom-css px-3 py-2'><i class='fi fi-rr-trash'></i></a>";
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
                                                    echo 'show active'; ?>" id="pills-emailadd" role="tabpanel" aria-labelledby="pills-emailadd-tab" tabindex="0">
                        <div class="p-5 shadow-sm rounded-5 mb-4 card">
                            <form action="add-email.php" method="post">
                                <label for="email_facebook" class="form-label">Email-i</label>
                                <input type="text" name="email_facebook" id="email_facebook" class="form-control shadow-sm rounded-5">
                                <br>
                                <button type="submit" class="btn btn-light rounded-5 float-right border" style="text-transform:none;" name="submit">
                                    <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
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
                                                    echo 'show active'; ?>" id="pills-kategoria" role="tabpanel" aria-labelledby="pills-kategoria-tab" tabindex="0">
                        <div class="p-5 shadow-sm rounded-5 mb-4 card">
                            <form action="add-category.php" method="POST">
                                <label class="form-label" for="kategori">Kategori e re</label>
                                <input type="text" name="kategori" id="kategori" class="form-control shadow-sm rounded-5">
                                <br>
                                <button type="submit" class="btn btn-light rounded-5 float-right border" style="text-transform:none;" name="submit">
                                    <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
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
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTitle" name="modalTitle"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" action="update-ads.php" method="post">
                                        <div class="form-group">
                                            <label for="email_ads_edit">Email</label>
                                            <input type="text" class="form-control border border-2 rounded-5" name="email_ads_edit" id="email_ads_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="adsID_edit">ADS ID</label>
                                            <input type="text" class="form-control border border-2 rounded-5" name="adsID_edit" id="adsID_edit">
                                        </div>
                                        <div class="form-group">
                                            <label for="shteti_edit">Shteti</label>
                                            <input type="text" class="form-control border border-2 rounded-5" name="shteti_edit" id="shteti_edit">
                                        </div>
                                        <input type="hidden" name="row_id" id="row_id">
                                        <button type="submit" class="input-custom-css px-3 py-2">Përditso të dhënat</button>
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
    $(document).ready(function() {
        $('.edit-btn').click(function() {
            var id = $(this).data('id');
            var email = $(this).data('email');
            var adsID = $(this).data('adsid');
            var shteti = $(this).data('shteti');
            var modalTitle = $(this).data('modal-title');
            $('#row_id').val(id);
            $('#email_ads_edit').val(email);
            $('#adsID_edit').val(adsID);
            $('#shteti_edit').val(shteti);
            $('#modalTitle').text("Përditso llogarinë e " + email);
            $('#editModal').modal('show');
        });
    });

    function getPerqindja() {
        // Get the selected value from the select element
        var select = document.getElementById("nameSurname");
        var selectedValue = select.value;
        // Make an AJAX request to a PHP file to fetch the corresponding "perqindja" value
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
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
            [10, 25, 50, "Te gjitha"]
        ],
        columnDefs: [{
            "targets": [0, 1, 2, 3, 4], // Indexes of the columns you want to apply the style to
            "render": function(data, type, row) {
                // Apply the style to the specified columns
                return type === 'display' && data !== null ? '<div style="white-space: normal;">' + data + '</div>' : data;
            }
        }],
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
            "<'row'<'col-md-12'tr>>" +
            "<'row'<'col-md-6'><'col-md-6'p>>",
        buttons: [{
                extend: "pdfHtml5",
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: "Eksporto tabelen ne formatin PDF",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "lista_klienteve_facebook"
            },
            {
                extend: "copyHtml5",
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: "Kopjo tabelen ne formatin Clipboard",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "lista_klienteve_facebook"
            },
            {
                extend: "excelHtml5",
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: "Eksporto tabelen ne formatin Excel",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                exportOptions: {
                    modifier: {
                        search: "applied",
                        order: "applied",
                        page: "all",
                    },
                },
                filename: "lista_klienteve_facebook"
            },
            {
                extend: "print",
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: "Printo tabel&euml;n",
                className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                filename: "lista_klienteve_facebook"
            },
        ],
        initComplete: function() {
            var btns = $(".dt-buttons");
            btns.addClass("").removeClass("dt-buttons btn-group");
            var lengthSelect = $("div.dataTables_length select");
            lengthSelect.addClass("form-select");
            lengthSelect.css({
                width: "auto",
                margin: "0 8px",
                padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                lineHeight: "1.5",
                border: "1px solid #ced4da",
                borderRadius: "0.25rem",
            });
        },
        fixedHeader: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
        },
        stripeClasses: ['stripe-color'],
        order: false,
    });
</script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Jeni i sigurt?',
            text: "Nuk do të keni mundësi të ktheheni mbrapsht!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Po, fshije!',
            cancelButtonText: 'Anulo',
            // Custom icon
            iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
            // Custom timer
            timer: 5000,
            timerProgressBar: true,
            allowOutsideClick: false, // Prevents users from clicking outside the dialog to close it
            allowEscapeKey: false // Prevents users from using the escape key to close the dialog
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to delete script with the row ID
                window.location.href = "?del=" + id;
            }
        });
    }
</script>
<script>
    function validateForm() {
        // Merr vlerat nga fushat e formës
        var email = document.getElementById('email_ads').value.trim();
        var adsID = document.getElementById('adsID').value.trim();
        var shteti = document.getElementById('shteti').value.trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        // Ndryshojeni në të vërtetë nëse formës i mungon validimi
        var isValid = true;
        // Fshini mesazhet e gabimeve nga fushat e mëparshme
        document.getElementById('email_error').innerHTML = '';
        document.getElementById('adsID_error').innerHTML = '';
        document.getElementById('shteti_error').innerHTML = '';
        // Validimi i fushës së emailit
        if (!emailRegex.test(email)) {
            document.getElementById('email_error').innerHTML = 'Adresa email është e pavlefshme';
            isValid = false;
        }
        // Validimi i fushës së ADS ID
        if (adsID.length < 3) {
            document.getElementById('adsID_error').innerHTML = 'ID e ADS duhet të jetë të paktën 3 karaktere';
            isValid = false;
        }
        // Validimi i fushës së shtetit
        if (shteti.length === 0) {
            document.getElementById('shteti_error').innerHTML = 'Ju lutem shkruani shtetin';
            isValid = false;
        }
        // Kthe vlerën përfundimtare të validitetit të formës
        return isValid;
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Define today's date
        var today = new Date();
        flatpickr('#dataKrijimit', {
            dateFormat: 'Y-m-d', // Set desired date format
            allowInput: true, // Allow manual input
            maxDate: today, // Restrict selection to today or earlier
            onClose: function(selectedDates, dateStr, instance) {
                // Validate selected date
                if (selectedDates.length === 0) {
                    // Show error message if no date is selected
                    instance.redraw();
                    instance._input.classList.add('is-invalid');
                } else {
                    // Remove error message if a valid date is selected
                    instance._input.classList.remove('is-invalid');
                }
            }
        });
        flatpickr('#dataSkadimit', {
            dateFormat: 'Y-m-d', // Set desired date format
            allowInput: true, // Allow manual input
            minDate: today, // Restrict selection to today or later
            onClose: function(selectedDates, dateStr, instance) {
                // Validate selected date
                if (selectedDates.length === 0) {
                    // Show error message if no date is selected
                    instance.redraw();
                    instance._input.classList.add('is-invalid');
                } else {
                    // Remove error message if a valid date is selected
                    instance._input.classList.remove('is-invalid');
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Selectr('#merre_adresen', {
            searchEnabled: true
        })
        new Selectr('#kategoria', {
            searchEnabled: true
        });
    });
</script>
<script>
    function confirmDelete(id) {
        // Thirrni SweetAlert2 për të kërkuar konfirmimin para largimit
        Swal.fire({
            title: 'Konfirmo fshirjen',
            text: 'A jeni të sigurt që dëshironi të fshini këtë artikull?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Po, fshije!',
            cancelButtonText: 'Anulo',
            preConfirm: () => {
                return new Promise((resolve) => {
                    // Këtu mund të bëni validime shtesë ose veprime të tjera përpara se të konfirmohet fshirja
                    // Për shembull, mund të bëni një verifikim shtesë
                    resolve();
                });
            }
        }).then((result) => {
            // Nëse përdoruesi konfirmon, përcaktojuni në lidhjen e fshirjes
            if (result.isConfirmed) {
                // Përdorni JavaScript për të përcaktuar URL-në e fshirjes duke përdorur id
                var deleteURL = 'delete-ads.php?id=' + id;
                // Përcaktimi në lidhjen e fshirjes
                window.location.href = deleteURL;
            }
        });
    }
</script>