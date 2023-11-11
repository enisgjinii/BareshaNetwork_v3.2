<?php
// error_reporting(E_ALL);
ini_set('display_errors', 1);

include('./config.php');

# the createAuthUrl() method generates the login URL.
$login_url = $client->createAuthUrl();

/* 
 * After obtaining permission from the user,
 * Google will redirect to the kycu_1.php with the "code" query parameter.
 */
if (isset($_GET['code'])) :
    session_start();
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        header('Location: kycu_1.php');
        exit;
    }
    $_SESSION['token'] = $token;


    if (isset($token['expires_in'])) {
        // Calculate the token expiration time
        $tokenExpiration = time() + $token['expires_in'];
        $_SESSION['tokenExpiration'] = $tokenExpiration;
    }
    /* -- Inserting the user data into the database -- */

    # Fetching the user data from the Google account
    $client->setAccessToken($token);
    $google_oauth = new Google_Service_Oauth2($client);
    $user_info = $google_oauth->userinfo->get();

    $email = trim($user_info['email']);

    // if (strpos($email, '@bareshamusic.com') === false) {
    //     // If the email address is not from the allowed domain, deny access and pass the email as a query parameter.
    //     header('Location: denied.php?email=' . urlencode($email)); // Include the email in the URL.
    //     exit;
    // }


    $google_id = trim($user_info['id']);
    $f_name = trim($user_info['given_name']);
    $l_name = trim($user_info['family_name']);
    $gender = trim($user_info['gender']);
    $local = trim($user_info['local']);
    $picture = trim($user_info['picture']);

    # Database connection
    include('conn-d.php');

    # Checking whether the email already exists in our database.
    $check_email = $conn->prepare("SELECT `email` FROM `googleauth` WHERE `email`=?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows === 0) {
        # Inserting the new user into the database
        $query_template = "INSERT INTO `googleauth` (`oauth_uid`, `firstName`, `last_name`,`email`,`profile_pic`,`gender`,`local`) VALUES (?,?,?,?,?,?,?)";
        $insert_stmt = $conn->prepare($query_template);
        $insert_stmt->bind_param("sssssss", $google_id, $f_name, $l_name, $email, $picture, $gender, $local); // Correct the order of parameters

        if ($insert_stmt->execute()) {
            // Store user data in session variables
            $_SESSION['user_id'] = $google_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_first_name'] = $f_name;
            $_SESSION['user_last_name'] = $l_name;

            header('Location: index.php');
            exit;
        } else {
            // Handle database error
            echo "Failed to insert user: " . $conn->error;
            exit;
        }
    }


    header('Location: index.php');
    exit;

endif;
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.css" rel="stylesheet" />
    <title>Baresha Panel - Google Login</title>
    <script src="https://kit.fontawesome.com/a1927a49ea.js" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5 rounded-6 shadow-4">
                            <div class="brand-logo">
                                <img src="images/logob.png" alt="logo">
                            </div>
                            <p class="font-weight-light">P&euml;rsh&euml;ndetje!</p>
                            <p class="text-muted">Identifikohu me llogarinë tënde të Google.</p>
                            <a href="<?= $login_url ?>" style="text-transform: none;" class="btn btn-light border shadow btn-sm "><img src="https://tinyurl.com/46bvrw4s" alt="Google Logo" width="20" class="me-2"> Identifikohu me Google</a>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="vendors/base/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
</body>

</html>