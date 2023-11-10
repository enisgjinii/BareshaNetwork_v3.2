<?php session_start();

if (!isset($_SESSION['token'])) {
    header('Location: kycu_1.php');
    exit;
}

include('./config.php');
$client = new Google_Client();
$client->setAccessToken($_SESSION['token']);

# Revoke the Google access token
$client->revokeToken();

# Capture the session data before clearing
$clearedSessionData = $_SESSION;

# Clear all session data
session_unset();

# Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

# Destroy the session
session_destroy();

# Save the cleared session data to a JSON file
file_put_contents('cleared_session_data.json', json_encode($clearedSessionData));

# Redirect to a login page or any other desired location
header("Location: kycu_1.php");
exit;
