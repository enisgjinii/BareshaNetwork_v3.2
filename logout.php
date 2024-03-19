<?php
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 1000);  // Set expiration time to a past value
    }
}

// Clear all other cookies (if needed)
$cookiesToClear = ['accessToken', 'email', 'f_name', 'gender', 'google_id', 'l_name', 'picture', 'refreshToken', 'session_id'];
foreach ($cookiesToClear as $cookieName) {
    if (isset($_COOKIE[$cookieName])) {
        unset($_COOKIE[$cookieName]);
        setcookie($cookieName, '', time() - 900000, '/');  // Set expiration time to a past value
    }
}

// Redirect to the login page or any other desired location
header("Location: kycu_1.php");
exit;
?>
