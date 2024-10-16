<nav class="navbar fixed-top navbar-expand-lg shadow-none navbar-light border border-2 mx-2" style="border-radius: 0px 0px 10px 10px;background-color: #fff;">
  <div class="container-fluid">
    <!-- Brand Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="images/brand-icon.png" width="50" alt="logo" style="object-fit: contain; border-radius: 10px;" />
    </a>
    <!-- Navbar Toggler -->
    <!-- <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" style="border-radius: 50%;">
      <span class="navbar-toggler-icon"></span>
    </button> -->
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-mdb-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
    <!-- Collapsible Content -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left-aligned Links (Optional) -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Add your left-aligned nav links here if needed -->
      </ul>
      <!-- Right-aligned Elements -->
      <ul class="navbar-nav d-flex align-items-center">
        <!-- Dark Mode Toggle Button -->
        <li class="nav-item me-2">
          <button id="darkModeButton" class="input-custom-css px-3 py-2" style="border-radius: 6px;">
            <i id="modeIcon" class="fi fi-rr-brightness"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- 
<li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo $user_info['picture']; ?>" alt="profile" width="25" style="border-radius: 50%; margin-right: 8px;" />
            <?php echo htmlspecialchars($user_info['givenName'] . ' ' . $user_info['familyName']); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end p-2 border-0" style="border-radius: 10px;">
            <li class="text-center py-1" style="font-size: 11px;">
              <?php echo htmlspecialchars($user_info['email']); ?>
            </li>
            <?php
            // Function to get geolocation data from an external API
            function get_ip_geolocation($ip)
            {
              $api_url = "http://ip-api.com/json/{$ip}?fields=country,regionName,city";
              $response = file_get_contents($api_url);
              return json_decode($response, true);
            }
            // Get the IP address of the user
            $visitor_ip = $_SERVER['REMOTE_ADDR'];
            $geo_data = get_ip_geolocation($visitor_ip);
            $country = isset($geo_data['country']) ? $geo_data['country'] : 'Unknown';
            $region = isset($geo_data['regionName']) ? $geo_data['regionName'] : 'Unknown';
            $city = isset($geo_data['city']) ? $geo_data['city'] : 'Unknown';
            ?>
            <li class="text-center py-2 px-3" style="font-size: 12px; background-color: #f9f9f9; color: #333;">
              <strong>IP Address:</strong> <?php echo htmlspecialchars($visitor_ip); ?><br>
              <small><strong>Location:</strong> <?php echo htmlspecialchars("$city, $region, $country"); ?></small>
            </li>
            <li>
              <a class="dropdown-item mt-1 border-0" href="sendLogoutEmail.php">
                <i class="fi fi-rr-exit me-2"></i>
                Dilni
              </a>
            </li>
          </ul>
        </li>
-->