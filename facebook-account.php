<?php
include 'partials/header.php';

// Retrieve the value of 'kid' from the query string
$kid = $_GET['kid'];

// Fetch the data associated with the provided 'kid' from the database
$query = "SELECT * FROM facebook WHERE id = $kid";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Assuming the columns exist in the "facebook" table
    $emri_mbiemri = $row['emri_mbiemri'];
    $emri_faqes = $row['emri_faqes'];
    // Retrieve other required columns similarly
} else {
    echo "Data not found.";
}
?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <div class="my-3">
                        <a type="button" class="btn btn-light shadow-sm rounded-3 btn-sm"
                            style="border: 1px solid lightgrey; text-transform: none;" href="vegla_facebook.php">
                            <i class="fi fi-rr-arrow-left"></i>
                            &nbsp; Kthehu
                        </a>

                        <h6 class="card-title my-3" style="text-transform:none;">
                            Te dh&euml;nat p&euml;r "
                            <?php echo $emri_mbiemri; ?>"
                        </h6>
                    </div>

                    <div class="table-responsive">
                        <form action="update-facebook-account.php" method="POST">
                            <table class="table table-bordered"> <input type="hidden" name="kid"
                                    value="<?php echo $kid; ?>">

                                <tr>
                                    <th>ID</th>
                                    <td>
                                        <?php echo $row['id']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Emri Mbiemri</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="emri_mbiemri"
                                            value="<?php echo $emri_mbiemri; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Emri Faqes</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" class="form-control shado"
                                            type="text" name="emri_faqes" value="<?php echo $emri_faqes; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Data Krijimit</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="date" name="data_krijimit"
                                            value="<?php echo $row['dataKrijimit']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Data Skadimit</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="date" name="data_skadimit"
                                            value="<?php echo $row['dataSkadimit']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Linku Faqes</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="linku_faqes"
                                            value="<?php echo $row['linkuFaqes']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Numri Personal</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text"
                                            name="numri_personal" value="<?php echo $row['numriPersonal']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ads Account</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="ads_account"
                                            value="<?php echo $row['adsAccount']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kategoria</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="kategoria"
                                            value="<?php echo $row['kategoria']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Numri Telefonit</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text"
                                            name="numri_telefonit" value="<?php echo $row['numriTelefonit']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Perqindja</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="perqindja"
                                            value="<?php echo $row['perqindja']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Numri Xhirollogarise</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text"
                                            name="numri_xhirollogarise"
                                            value="<?php echo $row['numriXhirollogarise']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Adresa</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="adresa"
                                            value="<?php echo $row['adresa']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Informacion Shtese</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="info_shtese"
                                            value="<?php echo $row['infoShtese']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Monetizuar</th>
                                    <td>
                                        <input class="form-control shadow-sm rounded-5" type="text" name="monetizuar"
                                            value="<?php echo $row['monetizuar']; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button type="submit" class="btn btn-primary shadow-sm rounded-5 text-white"
                                            style="text-transform:none;">P&euml;rditso t&euml; dh&euml;nat</button>
                                    </td>
                                </tr>
                            </table>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>