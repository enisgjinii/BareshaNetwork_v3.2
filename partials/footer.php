<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration for GitHub Repository
define('GITHUB_REPO', 'enisgjinii/BareshaNetwork_v3.2'); // Your GitHub repo in "owner/repo" format
define('GITHUB_BRANCH', 'main'); // Target branch

// Retrieve GitHub PAT from Environment Variables for security
$githubPat = ''; // Ensure GITHUB_PAT is set in your server environment

/**
 * Fetches the latest commit ID and date from GitHub.
 *
 * @param string|null $githubPat GitHub Personal Access Token
 * @return array Associative array with 'commitId', 'date', and optionally 'error'
 */
function getCommitDetails($githubPat)
{
  $details = ['commitId' => 'N/A', 'date' => 'N/A'];

  if ($githubPat) {
    $apiUrl = "https://api.github.com/repos/" . GITHUB_REPO . "/commits/" . GITHUB_BRANCH;
    $headers = [
      "User-Agent: PHP Script", // GitHub requires a User-Agent header
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
    if (curl_errno($ch)) {
      // Return cURL error
      $details['error'] = 'cURL Error: ' . curl_error($ch);
    } else {
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['sha']) && isset($data['commit']['author']['date'])) {
          $details['commitId'] = substr($data['sha'], 0, 7);
          $details['date'] = date('Y-m-d', strtotime($data['commit']['author']['date']));
        } else {
          $details['error'] = 'Unexpected API response structure.';
        }
      } else {
        $details['error'] = "GitHub API returned HTTP code $httpCode. Response: $response";
      }
    }
    curl_close($ch);
  } else {
    $details['error'] = 'GitHub PAT is not set. Unable to fetch commit details.';
  }

  return $details;
}

// Fetch commit details
$commitDetails = getCommitDetails($githubPat);

// Display error messages for debugging (remove in production)
if (isset($commitDetails['error'])) {
  echo '<div style="color: red;">Error: ' . htmlspecialchars($commitDetails['error']) . '</div>';
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