<?php

session_start();
if (!isset($_SESSION['token'])) {
    header('Location: kycu_1.php');
    exit;
}

include('./config.php');

$client->setAccessToken($_SESSION['token']);

if ($client->isAccessTokenExpired()) {
    header('Location: logout.php');
    exit;
}

$google_oauth = new Google_Service_Oauth2($client);
$user_info = $google_oauth->userinfo->get();

if (empty($user_info['email']) || strpos($user_info['email'], '@bareshamusic.com') === false) {
    // If the user doesn't have a valid email or is not from the allowed domain, deny access.
    header('Location: denied.php');
    exit;
}

$gender = $user_info['gender']; // Retrieve the user's gender

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            padding: 50px;
        }

        ul {
            list-style: none;
        }

        li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <ul>
        <li><img src="<?= $user_info['picture']; ?>"></li>
        <li><strong>ID:</strong> <?= $user_info['id']; ?></li>
        <li><strong>Full Name:</strong> <?= $user_info['givenName']; ?> <?= $user_info['familyName']; ?></li>
        <li><strong>Email:</strong> <?= $user_info['email']; ?></li>
        <li><strong>Gender:</strong> <?= $gender; ?></li> <!-- Display the user's gender -->
        <li><a href="./logout.php">Logout</a></li>
    </ul>
</body>
</html>
