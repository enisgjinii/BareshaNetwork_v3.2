<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Update Password</title>
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.5/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.5/sweetalert2.min.css">

    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>

<body>
    <?php
    // Include database connection file
    include 'conn-d.php';
    // Check if form is submitted
    if (isset($_POST['submit'])) {
        // Get the email and password from the form
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        // Check if the password field is empty
        if ($_POST['password'] == "") {
            // Show SweetAlert 2 alert with error message
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ju lutemi shkruani fjal&euml;kalimin tuaj t&euml; ri',
            });
        </script>";
        } else {
            // Prepare an SQL query to update the user's password
            $sql = "UPDATE users SET fjalkalimi = '" . mysqli_real_escape_string($conn, $password) . "' WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
            // Execute the query
            mysqli_query($conn, $sql);
            // Display success message
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Fjal&euml;kalimi u p&euml;rdit&euml;sua me sukses!',
                showConfirmButton: false,
                timer: 1500
            }).then(function(){
                window.location.href = 'index.php';
            });
        </script>";
        }
    }
    ?>
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 rounded-6 shadow-4">
                        <div class="brand-logo">
                            <img src="images/logob.png" alt="logo">
                        </div>
                        <h5>P&euml;rdit&euml;so fjal&euml;kalimin</h5>
                        <h6 class="font-weight-light">Ju lutemi shkruani fjal&euml;kalimin tuaj t&euml; ri.</h6>
                        <form class="pt-3" method="POST" action="">
                            <div class="form-group">
                                <input type="email" class="form-control p-3 rounded-3" id="exampleInputEmail1" placeholder="Email-i juaj" name="email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>" readonly>
                            </div>
                            <div class="form-group position-relative">
                                <input type="password" class="form-control p-3 rounded-3" id="password" placeholder="Fjal&euml;kalim i ri" name="password">
                                <i class="fa fa-eye position-absolute" id="togglePasswordIcon" style="top:50%; right:10px; transform: translateY(-50%); cursor:pointer;" onclick="togglePasswordVisibility()"></i>
                            </div>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm rounded-3" name="submit" style="text-transform:none;"><i class="fi fi-rr-lock-alt"></i> P&euml;rdit&euml;so fjal&euml;kalimin</button>
                            </div>
                            <div class="text-center mt-4 font-weight-light">
                                Keni nj&euml; llogari tashm&euml;? <a href="index.php" class="text-primary">Hyr</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var passwordIcon = document.getElementById("togglePasswordIcon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove("fa-eye");
                passwordIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove("fa-eye-slash");
                passwordIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>