<?php
include 'partials/header.php';

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the Google API Client Library
require_once 'vendor/autoload.php'; // Ensure this path is correct

// Initialize the Google Client
$client = new Google_Client();
$client->setAuthConfig('client.json'); // Path to your client.json
$client->addScope('https://www.googleapis.com/auth/userinfo.profile');
$client->addScope('https://www.googleapis.com/auth/userinfo.email');

// Retrieve the refresh token from the session or cookie
if (isset($_SESSION['refresh_token'])) {
    $refreshToken = $_SESSION['refresh_token'];
} elseif (isset($_COOKIE['refreshToken'])) {
    $refreshToken = $_COOKIE['refreshToken'];
    // For security, move the refresh token from the cookie to the session
    $_SESSION['refresh_token'] = $refreshToken;
    // Delete the refresh token from the cookie
    setcookie('refreshToken', '', time() - 3600, '/', '', true, true);
} else {
    echo "<p>Refresh token not found in session or cookie.</p>";
    exit;
}

// Set the refresh token to the client
$client->refreshToken($refreshToken);

// Get the new access token
$accessToken = $client->getAccessToken();
if ($accessToken) {
    // Set the access token to the client
    $client->setAccessToken($accessToken);
    // Create a new service instance for the Oauth2 service
    $service = new Google\Service\Oauth2($client);
    try {
        // Fetch user information
        $userInfo = $service->userinfo->get();
        // Access user information
        $email = $userInfo->email;
        $firstName = $userInfo->givenName;
        $lastName = $userInfo->familyName;
        $fullName = $userInfo->name;
        $profilePic = $userInfo->picture;
        $gender = $userInfo->gender;
        $locale = $userInfo->locale;
    } catch (Exception $e) {
        echo '<p>An error occurred: ' . htmlspecialchars($e->getMessage()) . '</p>';
        exit;
    }
} else {
    echo "<p>Failed to refresh access token.</p>";
    exit;
}

// Fetch user's roles and permissions from the database
require_once 'conn-d.php'; // Ensure this path is correct

// Fetch user ID from session or database
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    // If user ID is not in session, fetch it from the database based on email
    $stmt = $conn->prepare("SELECT id FROM googleauth WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId);
        if ($stmt->fetch()) {
            $_SESSION['id'] = $userId;
        } else {
            echo "<p>User not found in database.</p>";
            exit;
        }
        $stmt->close();
    } else {
        echo "<p>Failed to prepare statement.</p>";
        exit;
    }
}

// Fetch user roles
$roles = [];
$stmt = $conn->prepare("
    SELECT roles.name
    FROM roles
    INNER JOIN user_roles ON roles.id = user_roles.role_id
    WHERE user_roles.user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['name'];
    }
    $stmt->close();
} else {
    echo "<p>Failed to fetch user roles.</p>";
}

// Fetch accessible pages
$accessiblePages = [];
$stmt = $conn->prepare("
    SELECT DISTINCT role_pages.page
    FROM role_pages
    INNER JOIN user_roles ON role_pages.role_id = user_roles.role_id
    WHERE user_roles.user_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $accessiblePages[] = $row['page'];
    }
    $stmt->close();
} else {
    echo "<p>Failed to fetch accessible pages.</p>";
}

// Fetch Notifications (For Display Only)
$notifications = [
    ['title' => 'Mirësevini!', 'message' => 'Faleminderit që u bashkuat me ne.'],
    ['title' => 'Event i ardhshëm', 'message' => 'Mos harroni eventin tonë të ardhshëm të biznesit.']
];

// Fetch Linked Accounts (For Display Only)
$linkedAccounts = [
    ['platform' => 'Facebook', 'status' => 'E lidhur'],
    ['platform' => 'Twitter', 'status' => 'Jo e lidhur'],
    ['platform' => 'LinkedIn', 'status' => 'E lidhur']
];
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card shadow-none border border-2 p-4 mb-3 rounded-5">
                <h3 class="mb-4">Profili</h3>
                <div class="d-flex flex-column align-items-center">
                    <img src="<?= htmlspecialchars($profilePic ?? '') ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;">
                    <form id="profilePicForm" enctype="multipart/form-data" class="w-100">
                        <div class="mb-3">
                            <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*" required>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="input-custom-css px-3 py-2 btn-sm">Ndysho foton</button>
                        </div>
                    </form>
                </div>
                <div class="mt-4 w-100">
                    <p><strong>Emri i plotë:</strong> <?= htmlspecialchars($fullName ?? 'I panjohur') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($email ?? 'I panjohur') ?></p>
                    <p><strong>Gjinia:</strong> <?= htmlspecialchars($gender ?? 'I panjohur') ?></p>
                    <p><strong>Gjuha:</strong> <?= htmlspecialchars($locale ?? 'E panjohur') ?></p>
                    <p><strong>Roli:</strong> <?= !empty($roles) ? htmlspecialchars(implode(', ', $roles)) : 'Asnjë Roli' ?></p>
                </div>
            </div>
            <div class="accordion" id="accountAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAccessiblePages">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccessiblePages" aria-expanded="false" aria-controls="collapseAccessiblePages">
                            Faqet që keni qasje
                        </button>
                    </h2>
                    <div id="collapseAccessiblePages" class="accordion-collapse collapse" aria-labelledby="headingAccessiblePages" data-bs-parent="#accountAccordion">
                        <div class="accordion-body">
                            <?php if (!empty($accessiblePages)): ?>
                                <ul class="list-group">
                                    <?php foreach ($accessiblePages as $page): ?>
                                        <li class="list-group-item">
                                            <a href="<?= htmlspecialchars($page ?? '') ?>" class="text-decoration-none"><?= htmlspecialchars(ucwords(str_replace('_', ' ', basename($page, '.php')))) ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Nuk keni qasje në asnjë faqe.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingRoles">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRoles" aria-expanded="false" aria-controls="collapseRoles">
                            Roli
                        </button>
                    </h2>
                    <div id="collapseRoles" class="accordion-collapse collapse" aria-labelledby="headingRoles" data-bs-parent="#accountAccordion">
                        <div class="accordion-body">
                            <?php if (!empty($roles)): ?>
                                <ul class="list-group">
                                    <?php foreach ($roles as $role): ?>
                                        <li class="list-group-item"><?= htmlspecialchars($role ?? '') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Asnjë Roli</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNotifications">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotifications" aria-expanded="false" aria-controls="collapseNotifications">
                            Njoftime
                        </button>
                    </h2>
                    <div id="collapseNotifications" class="accordion-collapse collapse" aria-labelledby="headingNotifications" data-bs-parent="#accountAccordion">
                        <div class="accordion-body">
                            <?php if (!empty($notifications)): ?>
                                <ul class="list-group">
                                    <?php foreach ($notifications as $notification): ?>
                                        <li class="list-group-item">
                                            <strong><?= htmlspecialchars($notification['title'] ?? '') ?></strong>
                                            <p class="mb-0"><?= htmlspecialchars($notification['message'] ?? '') ?></p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Nuk ka njoftime të reja.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    $(document).ready(function() {
        $('#profilePicForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: 'upload_profile_pic.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Duke ngarkuar...',
                        text: 'Ju lutem prisni një moment.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: res.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            $('img[alt="Profile Picture"]').attr('src', res.profile_pic + '?t=' + new Date().getTime());
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gabim',
                            text: res.message
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim',
                        text: 'Ka ndodhur një gabim gjatë ngarkimit të foton.'
                    });
                    console.error('AJAX Error:', textStatus, errorThrown);
                }
            });
        });
    });
</script>
