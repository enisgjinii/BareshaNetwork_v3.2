<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>
    <!-- Include SweetAlert2 JS file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.5/sweetalert2.min.js"></script>
    <!-- Include SweetAlert2 CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.5/sweetalert2.min.css">

    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
</head>

<body>
    <?php
    include 'conn-d.php';
    function check_email_exists($conn, $email)
    {
        $sql = "SELECT * FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
        $result = mysqli_query($conn, $sql);
        return mysqli_num_rows($result) > 0;
    }
    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        // Check if the email input field is empty
        if (empty($email)) {
            echo "<script>
                   Swal.fire({
                       icon: 'warning',
                       title: 'Fusha e emailit &euml;sht&euml; bosh! Ju lutemi kontrolloni t&euml; dh&euml;nat tuaja',
                       showConfirmButton: false,
                       timer: 2500
                   });
                 </script>";
        } else {
            $email_exists = check_email_exists($conn, $email);
            if (!$email_exists) {
                echo "<script>
                       Swal.fire({
                           icon: 'error',
                           title: 'Email-i nuk ekziston n&euml; baz&euml;n e t&euml; dh&euml;nave!',
                           showConfirmButton: false,
                           timer: 1500
                       });
                     </script>";
            } else {
                echo "<script>
                       Swal.fire({
                           icon: 'success',
                           title: 'Emaili ekziston n&euml; baz&euml;n e t&euml; dh&euml;nave!',
                           showConfirmButton: false,
                           timer: 1500
                       }).then(function(){
                           window.location.href = 'update_password.php?email=$email';
                       });
                     </script>";
            }
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
                        <h5>Keni harruar fjal&euml;kalimin ?</h5>
                        <h6 class="font-weight-light">Shkruani emailin tuaj p&euml;r t&euml; rivendosur fjal&euml;kalimin tuaj.</h6>
                        <form class="pt-3" method="POST" action="">
                            <div class="form-group">
                                <input type="email" class="form-control p-3 rounded-3" id="exampleInputEmail1" placeholder="Email-i juaj" name="email">
                            </div>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <button type="submit" name="submit" class="btn btn-primary"><i class="fi fi-rr-shield-check"></i> Kontrollo</button>
                                <a href="index.php" class="auth-link text-black">Kthehuni te identifikimi</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>