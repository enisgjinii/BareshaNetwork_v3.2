<?php
header("X-Frame-Options: DENY");
include 'partials/header.php';

// Check if the user is logged in with Google
if (isset($_SESSION['id'])) {
  $user_id = $_SESSION['id'];

  include 'akseset/kryesor.php';
} else {
  // User is not logged in with Google, redirect to the login page
  header("Location: kycu_1.php");
}

include 'partials/footer.php';
