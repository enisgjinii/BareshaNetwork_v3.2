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
      <li class="nav-item dropdown me-4">
        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center notification-dropdown" id="notificationDropdown" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal2">
          <i class="fi fi-rr-bell text-dark mx-0"></i>
          <span class="count"></span>
        </a>
        <!-- Modal -->

      </li>
      <li class="dropdown-center ms-auto mt-2">
        <button class="btn btn-light btn-sm shadow-sm rounded-6 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:1px solid lightgrey;">
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
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Lajmrimet</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        include 'conn-d.php';

        // Replace 'YOUR_API_KEY' with your actual YouTube API key
        $api_key = 'AIzaSyDKt-ziSnLKQfYGgAxqwjRtCc6ss-PFIaM';
        // $api_key = 'AIzaSyBrE0kFGTQJwn36FeR4NIyf4FEw2HqSSIQ';

        // The channel ID of the YouTube channel you want to fetch videos from
        $channel_id = 'UCV6ZBT0ZUfNbtZMbsy-L3CQ';

        // Define the time periods for filtering
        $time_periods = [
          '24 hours' => strtotime('-1 day'),
          '48 hours' => strtotime('-2 days'),
          '3 days' => strtotime('-3 days'),
          '7 days' => strtotime('-7 days'),
          '14 days ( Përdor shumë tokena , mos e perdorni shpesh ne afate te shkurta kohore)' => strtotime('-14 days'),
          '30 days ( Përdor shumë tokena , mos e perdorni shpesh ne afate te shkurta kohore)' => strtotime('-30 days'),
        ];

        // Check if a time period is selected
        $selected_period = isset($_GET['period']) ? $_GET['period'] : '24 hours';

        // Calculate the start date for the selected period
        $start_date = date('Y-m-d\TH:i:s\Z', $time_periods[$selected_period]);

        // Initialize variables for pagination
        $next_page_token = null;
        $max_results = 10; // Number of videos to fetch per page

        // Initialize an empty array to store videos
        $videos = [];

        do {
          // Construct the API request URL with the nextPageToken
          $url = "https://www.googleapis.com/youtube/v3/search?key=$api_key&channelId=$channel_id&order=date&publishedAfter=$start_date&maxResults=$max_results&pageToken=$next_page_token&type=video&part=snippet";

          // Make the API request
          $response = file_get_contents($url);

          if ($response) {
            $data = json_decode($response);

            foreach ($data->items as $item) {
              // Get video snippet data
              $snippet = $item->snippet;

              // Extract video details
              $video_title = $snippet->title;

              // $published_date = date('mm/dd/yyyy/hh:mm', strtotime($snippet->publishedAt));

              // Make this published date to look good formated
              $published_date = date('d/m/Y H:i:s', strtotime($snippet->publishedAt));
              // Add video details to the array
              $videos[] = [
                'title' => $video_title,
                'published' => $published_date,
              ];
            }

            $next_page_token = isset($data->nextPageToken) ? $data->nextPageToken : null;
          }
        } while ($next_page_token);

        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="mb-4">
          <div class="form-group">
            <label for="period">Filtro</label>
            <select id="period" name="period" class="form-control">
              <?php foreach ($time_periods as $period => $start) { ?>
                <option value="<?php echo $period; ?>" <?php echo $selected_period === $period ? 'selected' : ''; ?>>
                  <?php echo $period; ?>
                </option>
              <?php } ?>
            </select>
            <br>
            <button type="submit" class="input-custom-css px-3 py-2">
              <i class="fi fi-rr-filter"></i> Filtro</button>
          </div>
        </form>
        <table class="table table-bordered">
          <thead>
            <tr>
              <td>ID</td>
              <th>Titulli i këngës</th>
              <th>Data dhe ora e publikimit</th>
            </tr>
          </thead>
          <tbody>
            <?php

            $valueOfMusic = 0;

            foreach ($videos as $video) {
              $valueOfMusic++;
            ?>
              <tr>
                <td><?php echo $valueOfMusic ?></td>
                <td><?php echo $video['title']; ?></td>
                <td><?php echo $video['published']; ?></td>
                <!-- <td><input type="checkbox" name="videos[]" value="<?php echo $video['title']; ?>"></td> -->
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>
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