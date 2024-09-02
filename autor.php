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
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <!-- Profile Tab -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile') echo 'active'; ?>" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile') echo 'true';
                                                                                                                                                                                                                                                                                                                                                            else echo 'false'; ?>">
                                <i class="fi fi-rr-address-book me-2"></i>Lista e autorve
                            </button>
                        </li>
                        <!-- Lista e kengeve Tab -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (isset($_GET['tab']) && $_GET['tab'] == 'lista_kenget') echo 'active'; ?>" id="pills-lista_kenget-tab" data-bs-toggle="pill" data-bs-target="#pills-lista_kenget" type="button" role="tab" aria-controls="pills-lista_kenget" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (isset($_GET['tab']) && $_GET['tab'] == 'lista_kenget') echo 'true';
                                                                                                                                                                                                                                                                                                                                                                                else echo 'false'; ?>">
                                <i class="fi-rr-album-circle-user me-2"></i>Lista e k&euml;ngeve
                            </button>
                        </li>
                        <!-- Register Tab -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register') echo 'active'; ?>" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register') echo 'true';
                                                                                                                                                                                                                                                                                                                                                                else echo 'false'; ?>">
                                <i class="fi fi-rr-user-add me-2"></i>Regjistrimi i autorit
                            </button>
                        </li>
                        <!-- Registe Track Tab -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-5 <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kenge') echo 'active'; ?>" id="pills-kenge-tab" data-bs-toggle="pill" data-bs-target="#pills-kenge" type="button" role="tab" aria-controls="pills-kenge" style="text-transform: none;border:1px solid lightgrey;" aria-selected="<?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kenge') echo 'true';
                                                                                                                                                                                                                                                                                                                                                    else echo 'false'; ?>">
                                <i class="fi fi-rr-music-alt me-2"></i>Regjistrimi i k&euml;ngeve
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content text-start text-dark" id="pills-tabContent">
                        <div class="tab-pane fade <?php if (isset($_GET['tab']) && $_GET['tab'] == 'profile') echo 'show active'; ?>" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            <div class=" table-responsive">
                                <!-- Table -->
                                <table id="example" class="table table-border">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-dark">Emri dhe mbiemri</th>
                                            <th class="text-dark">Kategoria</th>
                                            <th class="text-dark">Publisher</th>
                                            <th class="text-dark">Numri personal</th>
                                            <th class="text-dark">Dokument</th>
                                            <th class="text-dark">Kompania</th>
                                            <th class="text-dark">IPI Number</th>
                                            <th class="text-dark">Veprimet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'conn-d.php';
                                        // Check if the 'del' parameter is set in the URL
                                        // Check if the 'del' parameter is set in the URL
                                        if (isset($_GET['del'])) {
                                            $id = $_GET['del'];
                                            // Retrieve the name of the row before deleting
                                            $nameQuery = "SELECT emriDheMbiemriAutorit FROM autori WHERE id = '$id'";
                                            $nameResult = mysqli_query($conn, $nameQuery);
                                            $row = mysqli_fetch_assoc($nameResult);
                                            if ($row) {
                                                $name = $row['emriDheMbiemriAutorit'];
                                                // Delete the row with the given ID from the "autori" table
                                                $deleteQuery = "DELETE FROM autori WHERE id = '$id'";
                                                $deleteResult = mysqli_query($conn, $deleteQuery);
                                                if ($deleteResult) {
                                                }
                                            }
                                        }
                                        // Retrieve data from the "autori" table
                                        $sql = "SELECT * FROM autori ORDER BY id DESC";
                                        $result = mysqli_query($conn, $sql);
                                        // Loop through each row of data and display it in the table
                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['emriDheMbiemriAutorit'] ?> </td>
                                                <td><?php echo  $row['kategoria'] ?></td>
                                                <td><?php echo  $row['publikuesi'] ?></td>
                                                <td><?php echo $row['numriPersonal'] ?></td>
                                                <td>
                                                    <?php
                                                    $filePath = $row['dokument'];
                                                    $fileName = basename($filePath); // Get the file name from the file path
                                                    echo "<a class='btn btn-primary py-2 rounded-5 shadow-sm text-white' href='$filePath' download='$fileName'><i class='fi fi-rr-download'></i></a>";
                                                    ?>
                                                </td>
                                                <td> <?php echo $row['kompania'] ?></td>
                                                <td> <?php echo $row['ipiNumber'] ?></td>
                                                <td>
                                                    <a class='btn btn-primary shadow-sm rounded-5 text-white border' onclick="fetchData(<?php echo $row['id']; ?>)" data-bs-toggle='modal' data-bs-target='#editModal'><i class='fi fi-rr-edit'></i></a>
                                                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel">Ndrysho t&euml; dh&euml;nat e autorit</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="editForm" action="api/edit_methods/edit_author.php" method="post">
                                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                                        <div class="row my-3">
                                                                            <div class="col">
                                                                                <label for="emri_mbiemri" class="form-label">Emri dhe mbiemri</label>
                                                                                <input type="text" name="emri_mbiemri" id="emri_mbiemri" class="form-control shadow-sm rounded-5">
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="kategoria" class="form-label">Kategoria</label>
                                                                                <select name="kategoria" id="kategoria" class="form-select shadow-sm rounded-5 p-3">
                                                                                    <?php
                                                                                    $kategoria = $conn->query("SELECT kategoria FROM autori");
                                                                                    while ($merre_kategorin = mysqli_fetch_array($kategoria)) {
                                                                                        $selected = ($merre_kategorin['id'] == $editcl['merre_kategorin']) ? "selected" : "";
                                                                                        echo '<option>' . $merre_kategorin['kategoria'] . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row my-3">
                                                                            <div class="col">
                                                                                <label for="publikuesi" class="form-label">Publisher</label>
                                                                                <input type="text" name="publikuesi" id="publikuesi" class="form-control shadow-sm rounded-5">
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="numriPersonal" class="form-label">Numri personal i autorit</label>
                                                                                <input type="number" name="numriPersonal" id="numriPersonal" class="form-control shadow-sm rounded-5">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row my-3">
                                                                            <div class="col">
                                                                                <label for="Kompania" class="form-label">Kompania</label>
                                                                                <input type="text" name="Kompania" id="Kompania" class="form-control shadow-sm rounded-5">
                                                                            </div>
                                                                            <div class="col">
                                                                                <!-- <label for="dokument" class="form-label">Dokument</label>
                                                            <input type="file" name="dokument" id="dokument" class="form-control shadow-sm rounded-5"> -->
                                                                                <label for="ipiNumber" class="form-label">IPI Number</label>
                                                                                <input type="text" name="ipiNumber" id="ipiNumber" class="form-control shadow-sm rounded-5">
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn rounded-5 btn-facebook px-5 py-3" style="text-transform: none;" name="submit">
                                                                                <i class="fi fi-rr-edit icon" style="display: inline-block; vertical-align: middle;"></i>
                                                                                <span style="display: inline-block; vertical-align: middle;">Ndrysho</span>
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a class='btn btn-danger shadow-sm rounded-5 text-white' href='?del=<?php echo $row['id'] ?>'><i class='fi fi-rr-trash'></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade <?php if (isset($_GET['tab']) && $_GET['tab'] == 'lista_kenget') echo 'show active'; ?>" id="pills-lista_kenget" role="tabpanel" aria-labelledby="pills-lista_kenget-tab" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="listaEKengeveAutor">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-dark">Emri dhe Mbiemri i Autorit</th>
                                            <th class="text-dark">Emri i K&euml;ng&euml;s</th>
                                            <th class="text-dark">Minutasha e K&euml;ng&euml;s</th>
                                            <th class="text-dark">Link YouTube</th>
                                            <th class="text-dark">Link Platforma</th>
                                            <th class="text-dark">Roli</th>
                                            <th class="text-dark">Kompania</th>
                                            <th class="text-dark">Puntori Regjistrues</th>
                                            <th class="text-dark">Informacion Shtes&euml;</th>
                                            <th class="text-dark">IPI Number</th>
                                            <th class="text-dark">Veprimet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include 'conn-d.php';
                                        // Check if the 'del' parameter is set in the URL
                                        if (isset($_GET['del'])) {
                                            $id = $_GET['del'];
                                            // Retrieve the name of the row before deleting
                                            $nameQuery = "SELECT emri_autorit FROM kenget_autori WHERE id = '$id'";
                                            $nameResult = mysqli_query($conn, $nameQuery);
                                            $row = mysqli_fetch_assoc($nameResult);
                                            if ($row) {
                                                $name = $row['emri_autorit'];
                                                // Delete the row with the given ID from the "facebook" table
                                                $deleteQuery = "DELETE FROM kenget_autori WHERE id = '$id'";
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
                                        // Query p&euml;r t&euml; nxjerr&euml; t&euml; dh&euml;nat nga tabela "kenget_autori"
                                        $query = "SELECT * FROM kenget_autori ORDER BY id DESC";
                                        $result = mysqli_query($conn, $query);
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $row['emri_autorit']; ?></td>
                                                    <td><?php echo $row['emri_i_kenges']; ?></td>
                                                    <td><?php echo $row['minutasha_e_kenges']; ?></td>
                                                    <td><?php echo $row['link_youtube']; ?></td>
                                                    <td><?php echo $row['link_platforma']; ?></td>
                                                    <td><?php echo $row['roli']; ?></td>
                                                    <td><?php echo $row['kompania']; ?></td>
                                                    <td><?php echo $row['puntori_regjistrues']; ?></td>
                                                    <td><?php echo $row['info_shtese']; ?></td>
                                                    <td><?php echo $row['ipiNumber'] ?></td>
                                                    <td>
                                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                                                        <a class='btn btn-danger' href='?del=<?php echo $row['id'] ?>'><i class='fi fi-rr-trash'></i></a>
                                                    </td>
                                                </tr>
                                                <!-- Modal for Editing -->
                                                <div class=" modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content ">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Ndrysho t&euml; dh&euml;nat p&euml;r <?php echo $row['emri_autorit']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Form for editing the row -->
                                                                <form action="edit_trackAuthor.php" method="POST">
                                                                    <div class="row my-3">
                                                                        <div class="col">
                                                                            <label class="form-label" for="edit_emri_mbiemri<?php echo $row['id']; ?>">Emri dhe Mbiemri i Autorit</label>
                                                                            <input type="text" name="edit_emri_mbiemri" id="edit_emri_mbiemri<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['emri_autorit']; ?>">
                                                                        </div>
                                                                        <div class="col">
                                                                            <label class="form-label" for="edit_emri_kenges<?php echo $row['id']; ?>">Emri i K&euml;ng&euml;s</label>
                                                                            <input type="text" name="edit_emri_kenges" id="edit_emri_kenges<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['emri_i_kenges']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row my-3">
                                                                        <div class="col">
                                                                            <label class="form-label" for="edit_minutasha_kenges<?php echo $row['id']; ?>">Minutasha e K&euml;ng&euml;s</label>
                                                                            <input type="text" name="edit_minutasha_kenges" id="edit_minutasha_kenges<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['minutasha_e_kenges']; ?>">
                                                                        </div>
                                                                        <div class="col">
                                                                            <label class="form-label" for="edit_link_youtube<?php echo $row['id']; ?>">Link YouTube</label>
                                                                            <input type="text" name="edit_link_youtube" id="edit_link_youtube<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['link_youtube']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row my-3">
                                                                        <div class="col"><label class="form-label" for="edit_link_platforma<?php echo $row['id']; ?>">Link Platforma</label>
                                                                            <input type="text" name="edit_link_platforma" id="edit_link_platforma<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['link_platforma']; ?>">
                                                                        </div>
                                                                        <div class="col"><label class="form-label" for="edit_roli<?php echo $row['id']; ?>">Roli</label>
                                                                            <input type="text" name="edit_roli" id="edit_roli<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['roli']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row my-3">
                                                                        <div class="col">
                                                                            <label class="form-label" for="edit_kompania<?php echo $row['id']; ?>">Kompania</label>
                                                                            <input type="text" name="edit_kompania" id="edit_kompania<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['kompania']; ?>">
                                                                        </div>
                                                                        <div class="col"><label class="form-label" for="edit_puntori_regjistrues<?php echo $row['id']; ?>">Puntori Regjistrues</label>
                                                                            <input type="text" name="edit_puntori_regjistrues" id="edit_puntori_regjistrues<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5" value="<?php echo $row['puntori_regjistrues']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row my-3">
                                                                        <div class="col">
                                                                            <label class="form-label" for="edit_info_shtese<?php echo $row['id']; ?>">Informacion Shtes&euml;</label>
                                                                            <textarea name="edit_info_shtese" id="edit_info_shtese<?php echo $row['id']; ?>" class="form-control shadow-sm rounded-5"><?php echo $row['info_shtese']; ?></textarea>
                                                                        </div>
                                                                        <div class="col"></div>
                                                                    </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="row_id" value="<?php echo $row['id']; ?>">
                                                                <button type="submit" class="btn rounded-5 btn-facebook px-5" style="text-transform: none;" name="submit">
                                                                    <i class="fi fi-rr-edit icon" style="display: inline-block; vertical-align: middle;"></i>
                                                                    <span style="display: inline-block; vertical-align: middle;">Ndrysho</span>
                                                                </button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal for Deleting -->
                                                <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <!-- Modal content for deleting the row -->
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        } else {
                                            // echo "<tr><td colspan='10'>Nuk ka rekorda t&euml; disponueshme.</td></tr>";
                                        }
                                        mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'register') echo 'show active'; ?>" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab" tabindex="0">
                            <form action="api/post_methods/post_author.php" method="POST" enctype="multipart/form-data">
                                <div class="p-5 shadow-sm text-start rounded-5 mb-4 card">
                                    <h6 class="card-title" style="text-transform:none;">Plotso formularin per krijimin e nje autori</h6>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="emri_mbiemri" class="form-label">Emri dhe mbiemri</label>
                                            <input type="text" name="emri_mbiemri" id="emri_mbiemri" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="kategoria" class="form-label">Kategoria</label>
                                            <select class="form-select shadow-sm rounded-5 py-2" name="kategoria">
                                                <option value="Ascap">Ascap</option>
                                                <option value="IBM">IBM</option>
                                                <option value="PRS">PRS</option>
                                                <option value="GEMA.DE">GEMA.DE</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="publikuesi" class="form-label">Publisher</label>
                                            <input type="text" name="publikuesi" id="publikuesi" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="numriPersonal" class="form-label">Numri personal i autorit</label>
                                            <input type="number" name="numriPersonal" id="numriPersonal" class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="dokument" class="form-label">Dokument</label>
                                            <input type="file" name="dokument" id="dokument" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="Kompania" class="form-label">Kompania</label>
                                            <input type="text" name="Kompania" id="Kompania" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="ipi_number" class="form-label">IPI Number</label>
                                            <input type="text" name="ipiNumber" id="ipiNumber" class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn rounded-5 btn-facebook px-5" style="text-transform: none;" name="submit">
                                            <i class="fa-solid fa-plus icon" style="display: inline-block; vertical-align: middle;"></i>
                                            <span style="display: inline-block; vertical-align: middle;">Regjistro</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'kenge') echo 'show active'; ?>" id="pills-kenge" role="tabpanel" aria-labelledby="pills-kenge-tab" tabindex="0">
                            <form action="api/post_methods/post_trackAuthor.php" method="POST" enctype="multipart/form-data">
                                <div class="p-5 shadow-sm text-start rounded-5 mb-4 card">
                                    <h6 class="card-title" style="text-transform:none;">Plotso formularin p&euml;r krijimin e nj&euml; kenge ne kuader te autorit</h6>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="emri_mbiemri" class="form-label">Emri dhe mbiemri</label>
                                            <input type="text" name="emri_mbiemri" id="emri_mbiemri" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="emri_kenges" class="form-label">Emri i k&euml;ng&euml;s</label>
                                            <input type="text" name="emri_kenges" id="emri_kenges" class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="minutasha_kenges" class="form-label">Minutazha e k&euml;ng&euml;s</label>
                                            <input type="text" name="minutasha_kenges" id="minutasha_kenges" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="link_youtube" class="form-label">Linku i k&euml;ng&euml;s n&euml; YouTube</label>
                                            <input type="text" name="link_youtube" id="link_youtube" class="form-control shadow-sm rounded-5">
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="link_platforma" class="form-label">Linku i k&euml;ng&euml;s n&euml; platforma tjera</label>
                                            <input type="text" name="link_platforma" id="link_platforma" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="roli" class="form-label">Roli</label>
                                            <select name="roli" id="roli" class="form-select shadow-sm rounded-5">
                                                <option value="Kompozitor">Kompozitor</option>
                                                <option value="Tekstshkrues">Tekstshkruar</option>
                                                <option value="Tekstshkrues dhe kompozitor">Tekstshkrues dhe kompozitor</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col">
                                            <label for="kompania" class="form-label">Kompania (opsionale)</label>
                                            <input type="text" name="kompania" id="kompania" class="form-control shadow-sm rounded-5">
                                        </div>
                                        <div class="col">
                                            <label for="info_shtese" class="form-label">Informacion shtes&euml;</label>
                                            <textarea name="info_shtese" id="info_shtese" class="form-control shadow-sm rounded-5"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="puntori_regjistrues" value="<?php echo $_SESSION['emri']; ?>">
                                    <div>
                                        <button type="submit" class="btn rounded-5 btn-facebook px-5" style="text-transform: none;" name="submit">
                                            <i class="fa-solid fa-plus icon" style="display: inline-block; vertical-align: middle;"></i>
                                            <span style="display: inline-block; vertical-align: middle;">Regjistro</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    function fetchData(id) {
        $.ajax({
            url: 'api/get_methods/get_autor.php',
            type: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                // Populate the form fields with the fetched data
                populateForm(response);
            },
            error: function(xhr, status, error) {
                console.log(error); // Handle error if any
            }
        });
    }
    function populateForm(data) {
        // Parse the response data (assuming it's in JSON format)
        const parsedData = JSON.parse(data);
        // Access the form fields and set their values based on the fetched data
        const emriMbiemriField = document.getElementById('emri_mbiemri');
        const kategoriaField = document.getElementById('kategoria');
        const publikuesiField = document.getElementById('publikuesi');
        const numriPersonalField = document.getElementById('numriPersonal');
        const dokumentField = document.getElementById('dokument');
        const kompaniaField = document.getElementById('Kompania');
        const ipiNumberField = document.getElementById('ipiNumber');
        // Set other form field values similarly
        // Set the form field values
        emriMbiemriField.value = parsedData.emriDheMbiemriAutorit;
        kategoriaField.value = parsedData.kategoria;
        publikuesiField.value = parsedData.publikuesi;
        numriPersonalField.value = parsedData.numriPersonal;
        dokumentField.value = parsedData.dokument;
        kompaniaField.value = parsedData.kompania;
        ipiNumberField.value = parsedData.ipiNumber;
        // Set other form field values similarly
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
        responsive: false,
        order: false,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Te gjitha"]
        ],
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' +
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
        }, ],
        initComplete: function() {
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
    });
</script>
<script>
    $('#listaEKengeveAutor').DataTable({
        responsive: false,
        order: false,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Te gjitha"]
        ],
        dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' +
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
        }, ],
        initComplete: function() {
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
    });
</script>