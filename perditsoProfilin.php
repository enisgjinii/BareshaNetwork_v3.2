<?php
include "partials/header.php";
include "conn-d.php";
// Get the user ID
$user_id = $_SESSION['id'];
// Define a variable to hold the status message
$statusMessage = '';


// Retrieve the profile image data from the database
$sql = "SELECT * FROM googleauth WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mx-auto">
                <div class="col-4 ">
                    <div class="card border rounded-5 shadow-sm text-center py-3">
                        <h3>
                            <?php echo $_SESSION["email"]; ?>
                        </h3>
                        <p>
                            <?php echo $row["perdoruesi"]; ?>
                        </p>
                        <div class="text-center my-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                // Fetch the user's profile data
                                $sql = "SELECT * FROM googleauth WHERE id = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_assoc($result);

                                // Check if the user has uploaded a profile image
                                if ($row['profile_image']) {
                                    // Display the profile image
                                    echo '<img src="' . $row['profile_image'] . '" alt="Imazhi i profilit" class="img-fluid w-25 rounded-circle shadow-sm">';
                                } else {
                                    // Display a default profile image
                                    echo '<img src="images/default_profile.webp" alt="Imazhi i parazgjedhur i profilit" class="img-fluid w-25 rounded-circle shadow-sm">';
                                }
                                ?>
                                <form action="delete-image.php" method="POST" id="delete-form">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="button"
                                        class="position-absolute top-0 start-00 translate-middle btn btn-danger p-2 text-white"
                                        style="margin-top:23%;margin-left:50px;border-radius:15%;"
                                        onclick="showSweetAlert()" <?php if (!$row['profile_image']) {
                                            echo "disabled";
                                        } ?>>
                                        <i class="fi fi-rr-trash"></i>
                                    </button>

                                    <script>
                                        function showSweetAlert() {
                                            Swal.fire({
                                                title: 'A je i sigurt?',
                                                text: "Ju nuk do t&euml; jeni n&euml; gjendje ta riktheni k&euml;t&euml;!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#d33',
                                                cancelButtonColor: '#3085d6',
                                                confirmButtonText: 'Po, fshijeni!',
                                                cancelButtonText: 'Jo, anulo!'

                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    // If the user confirms, submit the form to delete the row
                                                    document.getElementById("delete-form").submit();
                                                }
                                            });
                                        }
                                    </script>
                                </form>
                            </div>
                        </div>
                        <div class="my-4">
                            <button type="button" class="btn btn-primary rounded-5 text-white" data-bs-toggle="modal"
                                data-bs-target="#updateImage" style="text-transform:none;">
                                <i class="fi fi-rr-edit" style="display:inline-block;vertical-align:middle;"></i>
                                <span style="display:inline-block;vertical-align:middle;">P&euml;rditso</span>
                            </button>
                            <form action="delete_profile.php" method="POST" style="display:inline;">
                                <input type="hidden" name="profile_id" value="<?php echo $row['id']; ?>">
                                <button type="button" class="btn btn-danger rounded-5 text-white"
                                    style="text-transform:none;" onclick="deleteProfileConfirmation(this)">
                                    <i class="fi fi-rr-trash" style="display:inline-block;vertical-align:middle;"></i>
                                    <span style="display:inline-block;vertical-align:middle;">Fshije profilin</span>
                                </button>
                            </form>
                            <script>
                                function deleteProfileConfirmation(button) {
                                    // Merr ID-ne e profilit nga atributi data-id
                                    var profileId = button.getAttribute("data-id");

                                    Swal.fire({
                                        title: "Jeni i sigurt q&euml; d&euml;shironi t&euml; fshini k&euml;t&euml; profil?",
                                        text: "Nuk mund t&euml; ktheheni pas pasi t&euml; keni fshir&euml; profilin!",
                                        icon: "warning",
                                        showCancelButton: true,
                                        confirmButtonColor: "#d33",
                                        cancelButtonColor: "#3085d6",
                                        confirmButtonText: "Fshij profilin"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Nese perdoruesi konfirmon fshirjen, dergo formen
                                            var form = button.closest('form');
                                            form.submit();
                                        }
                                    });
                                }
                            </script>


                        </div>


                        <div class="modal fade" id="updateImage" tabindex="-1" aria-labelledby="updateImageLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="updateImageLabel">Ndrysho foton e profilit</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="uploadImage.php" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="profileImage">Zgjidhni imazhin</label>
                                                <input type="file" name="profileImage" id="profileImage"
                                                    class="form-control rounded-5 shadow-sm" accept=".jpg,.jpeg,.png"
                                                    maxlength="5000000">

                                            </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" name="close"
                                            class="btn btn-secondary rounded-5 text-white" style="text-transform:none;"
                                            data-bs-dismiss="modal">
                                            <i class="fi fi-rr-cross-circle"
                                                style="display:inline-block;vertical-align:middle;"></i>
                                            <span style="display:inline-block;vertical-align:middle;">Mbylle</span>
                                        </button>
                                        <!-- <button type="submit" name="upload" class="btn btn-primary text-white">Upload</button> -->
                                        <button type="submit" name="upload" class="btn btn-primary rounded-5 text-white"
                                            style="text-transform:none;">
                                            <i class="fi fi-rr-paper-plane"
                                                style="display:inline-block;vertical-align:middle;"></i>
                                            <span style="display:inline-block;vertical-align:middle;">Ngarko</span>
                                        </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                <div class="col-8 card border rounded-5 shadow-sm text-center p-3">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active shadow-sm rounded-5" style="text-transform:none;"
                                id="pills-userInfos-tab" data-bs-toggle="pill" data-bs-target="#pills-userInfos"
                                type="button" role="tab" aria-controls="pills-userInfos" aria-selected="true">Informatat
                                e p&euml;rdoruesit</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link shadow-sm rounded-5" style="text-transform:none;"
                                id="pills-userInfosBank-tab" data-bs-toggle="pill" data-bs-target="#pills-userInfosBank"
                                type="button" role="tab" aria-controls="pills-userInfosBank"
                                aria-selected="false">Informacion p&euml;r faturimin</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link shadow-sm rounded-5" style="text-transform:none;"
                                id="pills-changePassword-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-changePassword" type="button" role="tab"
                                aria-controls="pills-changePassword" aria-selected="false">Ndrysho fjalkalimin</button>
                        </li>


                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-userInfos" role="tabpanel"
                            aria-labelledby="pills-userInfos-tab" tabindex="0">
                            <div class="row my-3">
                                <div class="col-6 text-start">
                                    <label class="emri" class="form-label">Emri</label>
                                    <input type="text" name="emri" id="emri" class="form-control shadow-sm rounded-5"
                                        value="<?php echo $row['name']; ?>">
                                </div>
                                <div class="col-6 text-start">
                                    <label class="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control shadow-sm rounded-5"
                                        value="<?php echo $row['email']; ?>">
                                </div>


                            </div>
                            <div class="row my-3">
                                <div class="col-6 text-start">
                                    <label class="adresa" class="form-label">Adresa</label>
                                    <input type="text" name="adresa" id="adresa"
                                        class="form-control shadow-sm rounded-5" value="<?php echo $row['adresa']; ?>">
                                </div>
                                <div class="col-6 text-start">
                                    <label class="telefoni" class="form-label">Telefoni</label>
                                    <input type="tel" name="telefoni" id="telefoni"
                                        class="form-control shadow-sm rounded-5" value="<?php echo $row['tel']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-userInfosBank" role="tabpanel"
                            aria-labelledby="pills-userInfosBank-tab" tabindex="0">
                            <div class="row my-3">
                                <div class="col-6 text-start">
                                    <label class="adresa" class="form-label">Emri i bankes</label>
                                    <input type="text" name="adresa" id="adresa"
                                        class="form-control shadow-sm rounded-5" value="<?php echo $row['emrib']; ?>">
                                </div>
                                <div class="col-6 text-start">
                                    <label class="telefoni" class="form-label">Llogaria e bankes</label>
                                    <input type="tel" name="telefoni" id="telefoni"
                                        class="form-control shadow-sm rounded-5"
                                        value="<?php echo $row['llogariab']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-changePassword" role="tabpanel"
                            aria-labelledby="pills-changePassword-tab" tabindex="0">
                            <form action="" method="post">
                                <div class="row my-3">
                                    <div class="col-6 text-start">
                                        <label class="form-label">Fjalkalimi i ri</label>
                                        <input type="password" name="fjalkalimi" id="fjalkalimi"
                                            class="form-control shadow-sm rounded-5">
                                    </div>
                                    <div class="col-6 text-start">
                                        <label class="form-label">Konfirmo Fjalkalimin e ri</label>
                                        <input type="password" name="konfirmo_fjalkalimin" id="konfirmo_fjalkalimin"
                                            class="form-control shadow-sm rounded-5">
                                        <p id="passwordWarning"
                                            class="border shadow-sm rounded-5 p-3 my-2 bg-danger text-white"
                                            style="display: none;">Fjalkalimi nuk perkon
                                            me konfirmimin!</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div>
                                            <button id="submitButton" class="btn btn-primary text-white rounded-5"
                                                style="display: none;">
                                                Ndrysho
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php if (!empty($statusMessage)): ?>
                                <p>
                                    <?php echo $statusMessage; ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <script>
                            const fjalkalimiInput = document.getElementById('fjalkalimi');
                            const konfirmoFjalkaliminInput = document.getElementById('konfirmo_fjalkalimin');
                            const passwordWarning = document.getElementById('passwordWarning');
                            const submitButton = document.getElementById('submitButton');

                            konfirmoFjalkaliminInput.addEventListener('input', function () {
                                if (fjalkalimiInput.value === konfirmoFjalkaliminInput.value) {
                                    passwordWarning.style.display = 'none';
                                    submitButton.style.display = 'block';
                                } else {
                                    passwordWarning.style.display = 'block';
                                    submitButton.style.display = 'none';
                                }
                            });
                        </script>

                    </div>
                </div>
            </div>
            <div class="row my-3">
                <div class="col">
                    <div class="card border rounded-5 shadow-sm p-3">
                        <div class="table-responsive">
                            <table id="example1" class="table w-100 table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Stafi</th>
                                        <th>Sherbimi</th>
                                        <th>Koha</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th>Stafi</th>
                                        <th>Sherbimi</th>
                                        <th>Koha</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'partials/footer.php'; ?>

    <script>
        $('#example1').DataTable({
            responsive: true,
            search: {
                return: true,
            },
            dom: 'frtip',
            // buttons: [{
            //     extend: 'pdfHtml5',
            //     text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
            //     titleAttr: 'Eksporto tabelen ne formatin PDF',
            //     className: 'btn btn-light border shadow-2 me-2'
            // }, {
            //     extend: 'copyHtml5',
            //     text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
            //     titleAttr: 'Kopjo tabelen ne formatin Clipboard',
            //     className: 'btn btn-light border shadow-2 me-2'
            // }, {
            //     extend: 'excelHtml5',
            //     text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
            //     titleAttr: 'Eksporto tabelen ne formatin CSV',
            //     className: 'btn btn-light border shadow-2 me-2'
            // }, {
            //     extend: 'print',
            //     text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
            //     titleAttr: 'Printo tabel&euml;n',
            //     className: 'btn btn-light border shadow-2 me-2'
            // }],
            // initComplete: function() {
            //     var btns = $('.dt-buttons');
            //     btns.addClass('');
            //     btns.removeClass('dt-buttons btn-group');
            // },
            fixedHeader: true,
            "ajax": "api/fetchDISTINCTLogs.php",
            "columns": [{
                "data": 0
            },
            {
                "data": 1
            },
            {
                "data": 2
            }
            ],
            "paging": true,
            "searching": true,
            "processing": true,
            "info": true,
            "fixedHeader": true,
            "language": {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
        })
    </script>




    <script>
        const deleteProfileBtn = document.querySelector('#delete-profile-btn');
        deleteProfileBtn.addEventListener('click', function () {
            // show SweetAlert confirmation dialog
            swal({
                title: 'A jeni t&euml; sigurt&euml;?',
                text: 'Kjo do t&euml; fshij&euml; profilin tuaj p&euml;rfundimisht.',
                icon: 'warning',
                buttons: ['Anulo', 'Fshij'],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // send AJAX request to delete profile
                    const xhr = new XMLHttpRequest();
                    xhr.open('DELETE', 'delete_profile.php');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            // redirect to kycu_1.php
                            window.location.href = 'kycu_1.php';
                        } else {
                            swal('Gabim', 'Ka ndodhur nj&euml; gabim n&euml; fshirjen e profilin.', 'error');
                        }
                    };
                    xhr.send();
                }
            });
        });
    </script>