<?php
// Configuration for GitHub Repository
define('GITHUB_REPO', 'enisgjinii/BareshaNetwork_v3.2'); // Your GitHub repo in "owner/repo" format
define('GITHUB_BRANCH', 'main'); // Target branch
define('COMMIT_CACHE_FILE', __DIR__ . '/commit_id.cache');
define('COMMIT_CACHE_DURATION', 3600); // Cache duration in seconds (1 hour)
// Retrieve GitHub PAT from Environment Variables for security
$githubPat = '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'; // Ensure you set this in your server environment
/**
 * Fetches the latest commit ID and date from GitHub with caching.
 *
 * @param string|null $githubPat GitHub Personal Access Token
 * @return array Associative array with 'commitId' and 'date'
 */
function getCommitDetails($githubPat)
{
  // Initialize default values
  $details = ['commitId' => 'N/A', 'date' => 'N/A'];
  // Check if cached data exists and is fresh
  if (file_exists(COMMIT_CACHE_FILE) && (time() - filemtime(COMMIT_CACHE_FILE)) < COMMIT_CACHE_DURATION) {
    $cachedData = file_get_contents(COMMIT_CACHE_FILE);
    $decodedData = json_decode($cachedData, true);
    if (is_array($decodedData) && isset($decodedData['commitId'], $decodedData['date'])) {
      return $decodedData;
    }
  }
  // Proceed only if PAT is available
  if ($githubPat) {
    $apiUrl = "https://api.github.com/repos/" . GITHUB_REPO . "/commits/" . GITHUB_BRANCH;
    $headers = [
      "User-Agent: PHP", // Required by GitHub API
      "Authorization: token " . $githubPat
    ];
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $apiUrl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_TIMEOUT => 10,
    ]);
    $response = curl_exec($ch);
    if (!curl_errno($ch)) {
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['sha']) && isset($data['commit']['author']['date'])) {
          $details['commitId'] = substr($data['sha'], 0, 7);
          $details['date'] = date('Y-m-d', strtotime($data['commit']['author']['date']));
          // Cache the details
          file_put_contents(COMMIT_CACHE_FILE, json_encode($details));
        }
      } else {
        // Log non-200 HTTP responses
        error_log("GitHub API returned HTTP code $httpCode");
      }
    } else {
      // Log the cURL error
      error_log('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);
  } else {
    // Log absence of PAT
    error_log('GitHub PAT is not set. Unable to fetch commit details.');
  }
  return $details;
}
// Fetch commit details
$commitDetails = getCommitDetails($githubPat);
// Fallback in case $commitDetails is not an array
if (!is_array($commitDetails)) {
  $commitDetails = ['commitId' => 'N/A', 'date' => 'N/A'];
}
?>
</div>
</div>
<!-- Footer -->
<footer class="footer bg-light border-top py-3 mt-5">
  <div class="container text-center">
    <p class="mb-0">
      <span>Version / Commit ID:</span> <?= htmlspecialchars($commitDetails['commitId'] ?? 'N/A'); ?> |
      <span>Date:</span> <?= htmlspecialchars($commitDetails['date'] ?? 'N/A'); ?>
    </p>
  </div>
</footer>