<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- <meta charset="utf-8">
 
  <title>BareshaNetwork - <?php echo date("Y"); ?></title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="bootstrap-5.2.3-dist/css/bootstrap.min.css">
  <script src="bootstrap-5.2.3-dist/js/bootstrap.min.js"></script>
  <link rel="shortcut icon" href="images/logos.png" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="datatables/Bootstrap-5-5.1.3/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="datatables/AutoFill-2.5.1/css/autoFill.bootstrap5.css" />
  <link rel="stylesheet" type="text/css" href="datatables/Buttons-2.3.3/css/buttons.bootstrap5.min.css" />
  <link rel="stylesheet" type="text/css" href="datatables/DateTime-1.2.0/css/dataTables.dateTime.min.css" />
  <script type="text/javascript" src="datatables/jQuery-3.6.0/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" src="datatables/Bootstrap-5-5.1.3/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="datatables/JSZip-2.5.0/jszip.min.js"></script>
  <script type="text/javascript" src="datatables/pdfmake-0.1.36/pdfmake.min.js"></script>
  <script type="text/javascript" src="datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
  <script type="text/javascript" src="datatables/AutoFill-2.5.1/js/dataTables.autoFill.min.js"></script>
  <script type="text/javascript" src="datatables/AutoFill-2.5.1/js/autoFill.bootstrap5.min.js"></script>
  <script type="text/javascript" src="datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.bootstrap5.min.js"></script>
  <script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.colVis.min.js"></script>
  <script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.html5.min.js"></script>
  <script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.print.min.js"></script>
  <script type="text/javascript" src="datatables/DateTime-1.2.0/js/dataTables.dateTime.min.js"></script> -->

    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" href="./vendors/mdi/css/materialdesignicons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>

    <!-- <link rel="stylesheet" type="text/css" href="datatables/Bootstrap-5-5.1.3/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="datatables/AutoFill-2.5.1/css/autoFill.bootstrap5.css" />
  <link rel="stylesheet" type="text/css" href="datatables/Buttons-2.3.3/css/buttons.bootstrap5.min.css" />
  <link rel="stylesheet" type="text/css" href="datatables/DateTime-1.2.0/css/dataTables.dateTime.min.css" /> -->

    <script src="https://cdn.zinggrid.com/zinggrid.min.js"></script>

    <link rel="stylesheet" type="text/css" href="./datatables/DataTables-1.13.1/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/AutoFill-2.5.1/css/autoFill.bootstrap5.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/Buttons-2.3.3/css/buttons.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/ColReorder-1.6.1/css/colReorder.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/DateTime-1.2.0/css/dataTables.dateTime.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/FixedColumns-4.2.1/css/fixedColumns.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/FixedHeader-3.3.1/css/fixedHeader.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="../datatables/KeyTable-2.8.0/css/keyTable.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/Responsive-2.4.0/css/responsive.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/RowGroup-1.3.0/css/rowGroup.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/RowReorder-1.3.1/css/rowReorder.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/Scroller-2.0.7/css/scroller.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/SearchBuilder-1.4.0/css/searchBuilder.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/SearchPanes-2.1.0/css/searchPanes.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/Select-1.5.0/css/select.bootstrap5.min.css" />
    <link rel="stylesheet" type="text/css" href="./datatables/StateRestore-1.2.0/css/stateRestore.bootstrap5.min.css" />
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <script type="text/javascript" src="../datatables/datatables.min.js"></script>
    <link href="mdb5/css/mdb.min.css" rel="stylesheet" />
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <style>
        .alert {
            display: none;
        }

        .stripe-color {
            background-color: transparent !important;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script> -->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.9.0/datepicker.min.css" integrity="sha512-KSPOwJnFz+1KkMzi9Jv1ayW7tjEmfk5c5/bSy10oq3gWfZ39jKpkIbH9lzKj3fVgOuEhEPHGLiBxdLbO8V7h2Q==" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.9.0/datepicker.min.js" integrity="sha512-rgSe6Mk6QjP6MavU6qbC1sJxT6TuhhuKjStZbfoJZzT9Xjg3q7cfd1Yd0ah7hKjRz8/N7VZlJz+wE7V9tRJ8xA==" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <title>BareshaNetwork -
        <?php echo date("Y"); ?>
    </title>



    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Majestic Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css"> -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <!-- <script src="https://cdn.jsdelivr.net/npm/darkmode-js@1.5.7/lib/darkmode-js.min.js"></script> -->



    <link rel="stylesheet" href="style.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" /> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.4.0/css/dataTables.dateTime.min.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.4.0/css/dataTables.dateTime.min.css"> -->
</head>

<body>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Pagesat Youtube</h4>
                    <nav class="d-flex">
                        <h6 class="mb-0">
                            <a href="" class="text-reset">Financat</a>
                            <span>/</span>
                            <a href="faturat.php" class="text-reset" data-bs-placement="top" data-bs-toggle="tooltip" title="<?php echo __FILE__; ?>"><u>Pagesat Youtube</u></a>
                            <br>
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-sm btn-primary text-white rounded-5 shadow-sm mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="fi fi-rr-info"></i>
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Video udh&euml;zuese</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe width="100%" height="315" src="assets/video-udh&euml;zuese.mp4" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </h6>
                    </nav>
                </div>
                <div class="p-5 shadow-sm rounded-5 mb-4 card">
                    <h4 class="font-weight-bold text-gray-800 mb-4">Pagesat Youtube</h4>

                    <form method="POST">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="start_date">Prej :</label>
                                <input type="date" class="form-control shadow-sm rounded-5 mt-2" id="start_date" name="start_date">
                            </div>
                            <div class="col mb-3">
                                <label for="end_date">Deri :</label>
                                <input type="date" class="form-control shadow-sm rounded-5 mt-2" id="end_date" name="end_date">
                            </div>

                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" name="submit" class="btn btn-sm btn-primary mt-3 text-white shadow-sm rounded-5 mt-2"><i class="fi fi-rr-filter"></i></button>
                        </div>
                    </form>
                </div>

                <div class="card shadow-sm rounded-5">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th>Emri</th>
                                        <th>Emri artistik</th>
                                        <th>Fatura</th>
                                        <th>Data</th>
                                        <th>Shuma</th>
                                        <th>Sh.Paguar</th>
                                        <th>Obligim</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'conn-d.php';

                                    if (isset($_POST['submit'])) {
                                        $start_date = $_POST['start_date'];
                                        $end_date = $_POST['end_date'];

                                        // Add the filter to the query
                                        $query = "SELECT * FROM fatura WHERE data >= '$start_date' AND data <= '$end_date'";
                                    } else {
                                        $query = "SELECT * FROM fatura";
                                    }

                                    $result = mysqli_query($conn, $query);

                                    while ($row = mysqli_fetch_array($result)) {
                                        $id = $row['id'];
                                        // $emri = $row['emri']; - Kjo eshte rubrik per emer por po e shfaq pjesen e ID-s&euml;
                                        $emri_artikullit = $row['emrifull'];
                                        $fatura = $row['fatura'];
                                        $pagesa_e_mbetur = $row['klientit'];
                                        $totali = $row['mbetja'];
                                        $pagesa = $row['totali'];
                                        $data = $row['data'];

                                        $dda = $row['data'];
                                        $date = date_create($dda);
                                        $dats = date_format($date, 'Y-m-d');
                                        $sid = $row['fatura'];

                                        $q4 = $conn->query("SELECT SUM(`totali`) as `sum` FROM `shitje` WHERE fatura='$sid'");
                                        $qq4 = mysqli_fetch_array($q4);

                                        $merrpagesen = $conn->query("SELECT SUM(`shuma`) as `sum` FROM `pagesat` WHERE fatura='$sid'");
                                        $merrep = mysqli_fetch_array($merrpagesen);

                                        $klientiid = $row['emri'];

                                        $queryy = "SELECT * FROM klientet WHERE id=" . $klientiid . " ";
                                        $mkl = $conn->query($queryy);
                                        $k4 = mysqli_fetch_array($mkl);

                                        $obli = $qq4['sum'] - $merrep['sum'];

                                        if ($qq4['sum'] > $merrep['sum']) {
                                            $pagesaaa = '<span class="badge rounded-pill text-bg-danger text-white w-100">' . $row["emrifull"] . '</span>';
                                        } else {
                                            $pagesaaa = '<span class="badge rounded-pill text-bg-primary text-white w-100">' . $row["emrifull"] . '</span>';
                                        }

                                        $shuma = $qq4["sum"];
                                        $shuma_e_paguar = $merrep['sum'];

                                        if ($obli == '0') {

                                            echo "
                                            <tr>
                                                <td>$pagesaaa</td>
                                                <td>$emri_artikullit</td>
                                                <td>$fatura</td>
                                                <td>$data</td>
                                                <td>$shuma</td>
                                                <td>$shuma_e_paguar</td>
                                                <td>$obli</td>
                                                
                                                <td>
                                                    <a href='shitje.php?fatura=$fatura' target='_blank' class='btn btn-primary'>Pagesa</a>
                                                </td>
                                            </tr>
                                            ";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <footer class="footer">


        <div class="d-sm-flex justify-content-between justify-content-sm-between">
            <span class="text-muted">Copyright ©
                <?php echo date("Y"); ?> <a href="" target="_blank">BareshaNetwork</a>. All rights reserved.
            </span>
            <span><b>Version: </b> 3.0 </span>
        </div>
    </footer>
    <script src="plugins/dark-reader/darkreader.js"></script>
    <script>
        // Select the buttons and the toggle icon
        const systemThemeButton = document.querySelector('#system-theme');
        const lightThemeButton = document.querySelector('#light-theme');
        const darkThemeButton = document.querySelector('#dark-theme');
        const toggleIcon = document.querySelector('#toggle-icon');

        // Add the click event listeners to the buttons
        systemThemeButton.addEventListener('click', () => {
            DarkReader.auto({
                brightness: 100,
                contrast: 90,
                sepia: 10
            });
            toggleIcon.classList.remove('fa-sun', 'fa-moon');
        });

        lightThemeButton.addEventListener('click', () => {
            DarkReader.disable();
            toggleIcon.classList.remove('fa-moon');
            toggleIcon.classList.add('fa-sun');
        });

        darkThemeButton.addEventListener('click', () => {
            DarkReader.enable({
                brightness: 100,
                contrast: 90,
                sepia: 10
            });
            toggleIcon.classList.remove('fa-sun');
            toggleIcon.classList.add('fa-moon');
        });

        // Open the dropdown when the cog button is clicked
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        dropdownToggle.addEventListener('click', () => {
            dropdownToggle.nextElementSibling.classList.toggle('show');
        });
    </script>
    <script>
        // DarkReader.enable({
        //   brightness: 100,
        //   contrast: 90,
        //   sepia: 10
        // });

        // const toggleButton = document.querySelector('#toggle-dark-mode');

        // toggleButton.addEventListener('click', () => {
        //   if (DarkReader.isEnabled()) {
        //     DarkReader.disable();
        //   } else {
        //     DarkReader.enable({
        //       brightness: 100,
        //       contrast: 90,
        //       sepia: 10
        //     });
        //   }
        // });
        // const toggleIcon = document.querySelector('#toggle-icon');
        // const systemThemeButton = document.querySelector('#system-theme');
        // const lightThemeButton = document.querySelector('#light-theme');
        // const darkThemeButton = document.querySelector('#dark-theme');

        // systemThemeButton.addEventListener('click', () => {
        //   DarkReader.auto({
        //     brightness: 100,
        //     contrast: 90,
        //     sepia: 10
        //   });
        //   toggleIcon.classList.remove('fi', 'fi-rr-brightness', 'fi-rr-moon-stars');
        // });

        // lightThemeButton.addEventListener('click', () => {
        //   DarkReader.disable();
        //   toggleIcon.classList.remove('fi-rr-moon-stars');
        //   toggleIcon.classList.add('fi', 'fi-rr-brightness');
        // });

        // darkThemeButton.addEventListener('click', () => {
        //   DarkReader.enable({
        //     brightness: 100,
        //     contrast: 90,
        //     sepia: 10
        //   });
        //   toggleIcon.classList.remove('fi-rr-brightness');
        //   toggleIcon.classList.add('fi', 'fi-rr-moon-stars');
        // });

        // Get the dropdown menu
        const dropdownMenu = document.querySelector('#dropdown-menu');

        // Get the menu items
        const systemThemeItem = document.querySelector('#system-theme-item');
        const lightThemeItem = document.querySelector('#light-theme-item');
        const darkThemeItem = document.querySelector('#dark-theme-item');

        // Add event listener to toggle button to show/hide dropdown menu
        toggleButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('show');
        });

        // Add event listener to close dropdown menu when user clicks outside of it
        window.addEventListener('click', (event) => {
            if (!event.target.matches('#toggle-button')) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Add event listeners to menu items to apply themes
        systemThemeItem.addEventListener('click', () => {
            DarkReader.auto({
                brightness: 100,
                contrast: 90,
                sepia: 10
            });
            toggleIcon.classList.remove('fi fi-rr-brightness', 'fi fi-rr-moon-stars');
            dropdownMenu.classList.remove('show');
        });

        lightThemeItem.addEventListener('click', () => {
            DarkReader.disable();
            toggleIcon.classList.remove('fi fi-rr-moon-stars');
            toggleIcon.classList.add('fi-rr-brightness');
            dropdownMenu.classList.remove('show');
        });

        darkThemeItem.addEventListener('click', () => {
            DarkReader.enable({
                brightness: 100,
                contrast: 90,
                sepia: 10
            });
            toggleIcon.classList.remove('fi fi-rr-brightness');
            toggleIcon.classList.add('fi fi-rr-moon-stars');
            dropdownMenu.classList.remove('show');
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <!--
<script src="vendors/simplemde/simplemde.min.js"></script>
<scmoript src="vendors/jquery-file-upload/jquery.uploadfile.min.js"></scmoript>
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>s
<script src="js/template.js"></script>
<script src="js/settings.js"></script>
<script src="js/todolist.js"></script>
<script src="js/editorDemo.js"></script>
<script src="js/file-upload.js"></script>
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/file-upload.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="js/select2.js"></script>
<script src="vendors/select2/select2.min.js"></script> -->

    <!--
<script type="text/javascript" src="datatables/datatables.min.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/dataTables.autoFill.min.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/autoFill.bootstrap5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="datatables/JSZip-2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="datatables/pdfmake-0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/dataTables.autoFill.min.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/autoFill.bootstrap5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.print.min.js"></script>
<script type="text/javascript" src="datatables/DateTime-1.2.0/js/dataTables.dateTime.min.js"></script> 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/autofill/2.5.1/css/autoFill.bootstrap5.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.3/css/buttons.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.6.1/css/colReorder.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.2.0/css/dataTables.dateTime.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.2.1/css/fixedColumns.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/keytable/2.8.0/css/keyTable.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.3.0/css/rowGroup.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.3.1/css/rowReorder.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/2.0.7/css/scroller.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchbuilder/1.4.0/css/searchBuilder.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/searchpanes/2.1.0/css/searchPanes.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.5.0/css/select.bootstrap5.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/staterestore/1.2.0/css/stateRestore.bootstrap5.min.css" />



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="vendors/chart.js/Chart.min.js"></script>
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/template.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script> 
<script type="text/javascript" src="datatables/datatables.min.js"></script> -->



    <!-- Local Files -->
    <!-- <script src="vendors/base/vendor.bundle.base.js"></script>
<script src="vendors/chart.js/Chart.min.js"></script>
<script src="vendors/datatables.net/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/template.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/data-table.js"></script>
<script src="js/jquery.dataTables.js"></script>
<script src="js/dataTables.bootstrap4.js"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script> -->

    <!-- plugins:js -->
    <script src="vendors/base/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <script src="vendors/chart.js/Chart.min.js"></script>
    <script src="vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <script src="js/data-table.js"></script>
    <script src="js/jquery.dataTables.js"></script>
    <script src="js/dataTables.bootstrap4.js"></script>
    <!-- End custom js for this page-->

    <script src="js/jquery.cookie.js" type="text/javascript"></script>


    <!-- Datatables Files  CDN -->
    <!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/autofill/2.5.1/js/dataTables.autoFill.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/autofill/2.5.1/js/autoFill.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/colreorder/1.6.1/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/datetime/1.2.0/js/dataTables.dateTime.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/keytable/2.8.0/js/dataTables.keyTable.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.3.1/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/scroller/2.0.7/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.4.0/js/dataTables.searchBuilder.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/searchbuilder/1.4.0/js/searchBuilder.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.1.0/js/dataTables.searchPanes.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/searchpanes/2.1.0/js/searchPanes.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.5.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/staterestore/1.2.0/js/dataTables.stateRestore.min.js"></script>
-->
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"></script>
    <!-- Datatables Files Local -->

    <!-- <script type="text/javascript" src="datatables/AutoFill-2.5.1/js/dataTables.autoFill.min.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/autoFill.bootstrap5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="datatables/JSZip-2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="datatables/pdfmake-0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/dataTables.autoFill.min.js"></script>
<script type="text/javascript" src="datatables/AutoFill-2.5.1/js/autoFill.bootstrap5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="datatables/Buttons-2.3.3/js/buttons.print.min.js"></script>
<script type="text/javascript" src="datatables/DateTime-1.2.0/js/dataTables.dateTime.min.js"></script>  -->

    <!-- <script type="text/javascript" src="jQuery-3.6.0/jquery-3.6.0.min.js"></script> -->
    <script type="text/javascript" src="./datatables/JSZip-2.5.0/jszip.min.js"></script>
    <script type="text/javascript" src="./datatables/pdfmake-0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="./datatables/pdfmake-0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="./datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="./datatables/DataTables-1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="./datatables/AutoFill-2.5.1/js/dataTables.autoFill.min.js"></script>
    <script type="text/javascript" src="./datatables/AutoFill-2.5.1/js/autoFill.bootstrap5.min.js"></script>
    <script type="text/javascript" src="./datatables/Buttons-2.3.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="./datatables/Buttons-2.3.3/js/buttons.bootstrap5.min.js"></script>
    <script type="text/javascript" src="./datatables/Buttons-2.3.3/js/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="./datatables/Buttons-2.3.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="./datatables/Buttons-2.3.3/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="./datatables/ColReorder-1.6.1/js/dataTables.colReorder.min.js"></script>
    <script type="text/javascript" src="./datatables/DateTime-1.2.0/js/dataTables.dateTime.min.js"></script>
    <script type="text/javascript" src="./datatables/FixedColumns-4.2.1/js/dataTables.fixedColumns.min.js"></script>
    <script type="text/javascript" src="./datatables/FixedHeader-3.3.1/js/dataTables.fixedHeader.min.js"></script>
    <script type="text/javascript" src="./datatables/KeyTable-2.8.0/js/dataTables.keyTable.min.js"></script>
    <script type="text/javascript" src="./datatables/Responsive-2.4.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="./datatables/Responsive-2.4.0/js/responsive.bootstrap5.js"></script>
    <script type="text/javascript" src="./datatables/RowGroup-1.3.0/js/dataTables.rowGroup.min.js"></script>
    <script type="text/javascript" src="./datatables/RowReorder-1.3.1/js/dataTables.rowReorder.min.js"></script>
    <script type="text/javascript" src="./datatables/Scroller-2.0.7/js/dataTables.scroller.min.js"></script>
    <script type="text/javascript" src="./datatables/SearchBuilder-1.4.0/js/dataTables.searchBuilder.min.js"></script>
    <script type="text/javascript" src="./datatables/SearchBuilder-1.4.0/js/searchBuilder.bootstrap5.min.js"></script>
    <script type="text/javascript" src="./datatables/SearchPanes-2.1.0/js/dataTables.searchPanes.min.js"></script>
    <script type="text/javascript" src="./datatables/SearchPanes-2.1.0/js/searchPanes.bootstrap5.min.js"></script>
    <script type="text/javascript" src="./datatables/Select-1.5.0/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="./datatables/StateRestore-1.2.0/js/dataTables.stateRestore.min.js"></script>
    <script type="text/javascript" src="./datatables/StateRestore-1.2.0/js/stateRestore.bootstrap5.min.js"></script>



    <script>
        $('#example').DataTable({
            responsive: true,
            search: {
                return: true,
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light border shadow-2 me-2',
                filename: 'Pagesat e kryera gjate:  <?php echo $start_date; ?> - <?php echo $end_date; ?>',
                title: "Pagesat e kryera gjate periudhes <?php echo $start_date; ?> - <?php echo $end_date; ?> ne formatin PDF"
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
                filename: 'Pagesat e kryera gjate:  <?php echo $start_date; ?> - <?php echo $end_date; ?>',
                title: "Pagesat e kryera gjate periudhes <?php echo $start_date; ?> - <?php echo $end_date; ?> ne formatin Excel"
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabel&euml;n',
                className: 'btn btn-light border shadow-2 me-2'
            }],
            initComplete: function() {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');

            },


            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],

        })
    </script>
</body>

</html>