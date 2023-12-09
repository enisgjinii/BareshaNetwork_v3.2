<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row shadow-1 mt-0">
  <div class="navbar-brand-wrapper d-flex justify-content-center">
    <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
      <a class="navbar-brand brand-logo" href="index.php"><img src="images/brand-icon.png" alt="logo" style="object-fit:contain;" /></a>
      <a class="navbar-brand brand-logo-mini" href="index.php"><img src="images/brand-icon.png" alt="logo" style="object-fit:contain;width:min-content;" /></a>
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-mdb-toggle="minimize" data-mdb-placement="bottom" title="Mbylle menun duke shtypur tastin m">
        <span class="mdi mdi-sort-variant"></span>
      </button>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <span class="badge rounded-pill text-bg-primary" id="token-countdown">Hover over me</span>
    <span class="badge rounded-pill text-bg-danger text-white ms-2" id="time_of_token_expiry">This is shown on hover</span>
    <ul class="navbar-nav mr-lg-4 w-100">
      <li class="nav-item nav-search d-none d-lg-block w-100">
        <div class="toggle-button shadow">
          <span class="toggle-icon mt-1"><i class="fi fi-rr-brightness"></i></span>
          <span class="toggle-switch">
            <input type="checkbox" id="toggle-mode">
            <label for="toggle-mode"></label>
          </span>
          <span class="toggle-icon mt-1"><i class="fi fi-rr-moon-stars"></i></span>
        </div>
      </li>
    </ul>
    <ul class="navbar-nav navbar-nav-right d-flex align-items-center ms-2">
      <li class="dropdown-center ms-auto mt-2">
        <button class="btn btn-light btn-sm shadow-sm rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:1px solid lightgrey;">
          <img src="<?php echo $user_info['picture']; ?>" alt="profile" width="25px" style="border-radius:50%;margin-right:15px" />
          <?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?>
        </button>

        <ul class="dropdown-menu p-2">
          <li class="rounded-5 mt-1 text-center py-1" style="border:1px solid lightgrey;font-size:11px;"> <?php echo $user_info['email'] ?>

          </li>
          <li>
            <a class="dropdown-item rounded-5 mt-1" style="border:1px solid lightgrey;" href="logout.php">
              <i class="fi fi-rr-exit me-2"></i>
              Dilni
            </a>
          </li>
        </ul>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-mdb-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>

<script>
  function updateTokenCountdown() {
    const countdownElement = document.getElementById('token-countdown');
    const timeOfTokenExpiryElement = document.getElementById('time_of_token_expiry');

    if (countdownElement) {
      const tokenExpiration = <?php echo isset($_SESSION['tokenExpiration']) ? $_SESSION['tokenExpiration'] : '0'; ?>;
      const now = Math.floor(Date.now() / 1000);

      if (tokenExpiration > now) {
        const timeRemaining = tokenExpiration - now;
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        countdownElement.innerText = 'Shfletimi juaj mbaron: ' + minutes + ' minuta ' + seconds + ' sekonda';

        // Calculate the time of token expiry
        const expirationDate = new Date(now * 1000 + timeRemaining * 1000);
        const hours = expirationDate.getHours();
        const minutesExpiry = expirationDate.getMinutes();
        timeOfTokenExpiryElement.innerText = 'Ju do largoheni nga faqja automatikisht në orën: ' + hours + ':' + (minutesExpiry < 10 ? '0' : '') + minutesExpiry;
      } else {
        countdownElement.innerText = 'Tokeni ka skaduar';
        timeOfTokenExpiryElement.innerText = '';
      }
    }
  }

  // Update the countdown every second
  setInterval(updateTokenCountdown, 1000);

  // Initial update
  updateTokenCountdown();
</script>
<script>
  $(document).ready(function() {
    const firstSpan = $('#token-countdown');
    const secondSpan = $('#time_of_token_expiry');

    // secondSpan.hide(); // Hide the second span initially

    firstSpan.on('mouseover', function() {
      secondSpan.fadeIn(300); // 300 milliseconds for the fade-in effect
    });

    firstSpan.on('mouseout', function() {
      secondSpan.fadeOut(400); // 300 milliseconds for the fade-out effect
    });
  });
</script>