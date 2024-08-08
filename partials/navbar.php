<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row shadow-1 mt-0">
  <div class="navbar-brand-wrapper d-flex justify-content-center">
    <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
      <a class="navbar-brand brand-logo" href="index.php"><img src="images/brand-icon.png" alt="logo" style="object-fit:contain;" /></a>
      <a class="navbar-brand brand-logo-miP" href="index.php"><img src="images/brand-icon.png" alt="logo" style="object-fit:contain;width:min-content;" /></a>
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-mdb-toggle="minimize" data-mdb-placement="bottom" title="Mbylle menun duke shtypur tastin m">
        <span class="mdi mdi-sort-variant"></span>
      </button>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <ul class="navbar-nav navbar-nav-right d-flex align-items-center ms-2">
      <li class="dropdown-center ms-auto mt-2">
        <button class="btn btn-light btn-sm shadow-sm rounded-6 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:1px solid lightgrey;">
          <img src="<?php echo $user_info['picture']; ?>" alt="profile" width="25" style="border-radius:50%;margin-right:15px" />
          <?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?>
        </button>
        <button id="darkModeButton" class="input-custom-css px-3 py-2">
          <i id="modeIcon" class="fi fi-rr-brightness"></i>
        </button>
        <ul class="dropdown-menu p-2">
          <li class="rounded-5 mt-1 text-center py-1" style="border:1px solid lightgrey;font-size:11px;"> <?php echo $user_info['email'] ?>
          </li>
          <?php
          // Function to get geolocation data from an external API
          function get_ip_geolocation($ip)
          {
            $api_url = "http://ip-api.com/json/{$ip}?fields=country,regionName,city";
            $response = file_get_contents($api_url);
            return json_decode($response, true);
            // 185.82.108.183
          }

          // Get the IP address of the user
          $visitor_ip = $_SERVER['REMOTE_ADDR'];
          $geo_data = get_ip_geolocation($visitor_ip);
          $country = isset($geo_data['country']) ? $geo_data['country'] : 'Unknown';
          $region = isset($geo_data['regionName']) ? $geo_data['regionName'] : 'Unknown';
          $city = isset($geo_data['city']) ? $geo_data['city'] : 'Unknown';
          ?>

          <li class="rounded-5 mt-1 text-center py-2 px-3" style="border:1px solid lightgrey; font-size:12px; background-color: #f9f9f9; color: #333;">
            <strong>IP Address:</strong> <?php echo htmlspecialchars($visitor_ip); ?><br>
            <small><strong>Location:</strong> <?php echo htmlspecialchars("$city, $region, $country"); ?></small>
          </li>

          <li>
            <a class="dropdown-item rounded-5 mt-1" style="border:1px solid lightgrey;" href="sendLogoutEmail.php">
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