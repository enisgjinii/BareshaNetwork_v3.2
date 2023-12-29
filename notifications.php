<?php
include 'partials/header.php';

// Replace 'YOUR_API_KEY' with your actual YouTube API key
$api_key = 'AIzaSyDKt-ziSnLKQfYGgAxqwjRtCc6ss-PFIaM';

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

<div class="container mt-5">
    <h1 class="mb-4">Lajmrimet</h1>

    <!-- Filter options -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="mb-4">
        <div class="form-group">
            <label for="period">Filter by:</label>
            <select id="period" name="period" class="form-control">
                <?php foreach ($time_periods as $period => $start) { ?>
                    <option value="<?php echo $period; ?>" <?php echo $selected_period === $period ? 'selected' : ''; ?>>
                        <?php echo $period; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Display videos in a table -->
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Published on</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($videos as $video) { ?>
                <tr>
                    <td><?php echo $video['title']; ?></td>
                    <td><?php echo $video['published']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>