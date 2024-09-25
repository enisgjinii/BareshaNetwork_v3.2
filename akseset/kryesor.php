<?php
// Start the session to access session variables
// session_start();

// Include database connection
require_once 'conn-d.php'; // Update the path accordingly

// ==================================
// 1. Authorization Check
// ==================================
// Define the authorized email address
$authorized_email = 'info@bareshamusic.com';

// Assume $user_info is already populated with user details
// Example:
// $user_info = [
//     'email' => 'user@example.com',
//     'givenName' => 'John',
//     'familyName' => 'Doe'
// ];

// Sanitize the user's email to prevent SQL injection
$email = isset($user_info['email']) ? $conn->real_escape_string($user_info['email']) : '';

// Initialize a flag to determine if the user is authorized
$is_authorized = ($user_info['email'] ?? '') === $authorized_email;

// Function to format page names (Implement as needed)
function format_page_name($page)
{
  return ucwords(str_replace('_', ' ', $page));
}

// ==================================
// 4. HTML Structure and Content
// ==================================
?>
<!DOCTYPE html>
<html lang="sq">

<head>
  <!-- Include necessary meta tags and CSS files -->
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="path/to/bootstrap.min.css">
  <!-- Include any other CSS files -->
  <link rel="stylesheet" href="path/to/your/custom.css">
  <!-- Include Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="container-fluid py-4">
        <?php if ($is_authorized): ?>
          <!-- Authorized User Dashboard -->
          <div class="mb-4">
            <h2 class="fw-bold">Mirë se vini, <?= htmlspecialchars($user_info['givenName'] . ' ' . $user_info['familyName']) ?></h2>
          </div>
          <!-- Summary Cards -->
          <div class="row g-4 mb-4">
            <?php
            // Define the summary cards to display
            $cards = [
              ['title' => 'Të ardhurat totale', 'id' => 'totalRevenue', 'value' => '€0.00', 'icon' => 'bi-cash-stack', 'bg' => 'bg-light'],
              ['title' => 'Fitimi total', 'id' => 'totalProfit', 'value' => '€0.00', 'icon' => 'bi-graph-up-arrow', 'bg' => 'bg-light'],
            ];
            foreach ($cards as $card): ?>
              <div class="col-6">
                <div class="card rounded-5 text-white <?= htmlspecialchars($card['bg']) ?> h-100">
                  <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title text-dark"><?= htmlspecialchars($card['title']) ?></h5>
                      <h3 class="text-dark" id="<?= htmlspecialchars($card['id']) ?>"><?= htmlspecialchars($card['value']) ?></h3>
                    </div>
                    <i class="<?= htmlspecialchars($card['icon']) ?> display-4"></i>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <!-- Revenue and Profit Chart -->
          <div class="card mb-4">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
              <h5 class="mb-2 mb-md-0">Të ardhurat dhe fitimet mujore</h5>
              <!-- Year and Month Filters -->
              <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Year Filter -->
                <select id="yearFilter" class="form-select form-select-sm" style="width: auto;">
                  <?php
                  // Populate the year filter dropdown with the current year and the past 5 years
                  $currentYear = date('Y');
                  foreach (range($currentYear, $currentYear - 5) as $year):
                    $selected = ($year == $currentYear) ? 'selected' : '';
                    echo "<option value='$year' $selected>$year</option>";
                  endforeach;
                  ?>
                </select>

                <!-- Month Filter -->
                <select id="monthFilter" class="form-select form-select-sm" style="width: auto;">
                  <?php
                  // Array of months in Albanian
                  $months = [
                    '01' => 'Janar',
                    '02' => 'Shkurt',
                    '03' => 'Mars',
                    '04' => 'Prill',
                    '05' => 'Maj',
                    '06' => 'Qershor',
                    '07' => 'Korrik',
                    '08' => 'Gusht',
                    '09' => 'Shtator',
                    '10' => 'Tetor',
                    '11' => 'Nëntor',
                    '12' => 'Dhjetor'
                  ];
                  $currentMonth = date('m');
                  foreach ($months as $num => $name):
                    $selected = ($num == $currentMonth) ? 'selected' : '';
                    echo "<option value='$num' $selected>$name</option>";
                  endforeach;
                  ?>
                </select>

                <!-- Filter Button -->
                <button id="filterButton" class="btn btn-primary btn-sm">
                  <i class="bi bi-filter"></i> Filtroni
                </button>
              </div>
            </div>
            <div class="card-body">
              <div id="revenueChart" style="height: 400px;"></div>
            </div>
          </div>
        <?php else: ?>
          <!-- Unauthorized User Access: Display Accessible Pages Based on Roles -->
          <div class="mb-4">
            <h2 class="fw-bold">Mirë se vini, <?= htmlspecialchars($user_info['givenName'] . ' ' . $user_info['familyName']) ?></h2>
            <p>Ju keni qasje në këto faqe:</p>
          </div>
          <?php
          // ==================================
          // 5. Fetch Accessible Pages for Unauthorized Users
          // ==================================
          // Prepare the SQL statement using prepared statements to prevent SQL injection
          $sql = 'SELECT roles.name AS role_name, GROUP_CONCAT(DISTINCT role_pages.page) AS pages
                        FROM googleauth
                        LEFT JOIN user_roles ON googleauth.id = user_roles.user_id
                        LEFT JOIN roles ON user_roles.role_id = roles.id
                        LEFT JOIN role_pages ON roles.id = role_pages.role_id
                        WHERE googleauth.email = ?
                        GROUP BY roles.id';
          // Initialize the prepared statement
          if ($stmt = $conn->prepare($sql)) {
            // Bind the email parameter to the statement
            $stmt->bind_param("s", $email);
            // Execute the statement
            $stmt->execute();
            // Bind the result variables
            $stmt->bind_result($role_name, $pages);
            // Initialize an array to hold accessible pages
            $accessiblePages = [];
            // Fetch the results
            while ($stmt->fetch()) {
              if (!empty($pages)) {
                $pageArray = explode(',', $pages);
                $accessiblePages = array_merge($accessiblePages, $pageArray);
              }
            }
            // Close the statement
            $stmt->close();
            // Filter unique accessible pages
            $accessiblePages = array_unique($accessiblePages);
          } else {
            // Handle SQL preparation error
            echo '<div class="alert alert-danger">Ndodhi një gabim gjatë ngarkimit të të dhënave. Ju lutemi provoni më vonë.</div>';
          }
          // Display accessible pages if any
          if (!empty($accessiblePages)):
          ?>
            <div class="row g-4">
              <?php foreach ($accessiblePages as $page): ?>
                <div class="col-sm-6 col-lg-3">
                  <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                      <h5 class="card-title"><?= htmlspecialchars(format_page_name(trim($page))) ?></h5>
                      <a href="<?= htmlspecialchars($page) ?>" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-door-open me-2"></i> Hape faqen
                      </a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">Nuk keni qasje në asnjë faqe. Ju lutemi kontaktoni administratorin për më shumë informacion.</div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php if ($is_authorized): ?>
    <!-- Include necessary JavaScript files -->
    <!-- Include jQuery -->
    <script src="path/to/jquery.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="path/to/bootstrap.bundle.min.js"></script>
    <!-- Include ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- JavaScript Section -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize the ApexCharts chart with desired options
        const options = {
          series: [],
          chart: {
            type: 'line',
            height: 400,
            stacked: false,
            toolbar: {
              show: true,
              tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true,
                customIcons: []
              }
            },
            zoom: {
              enabled: true,
              type: 'xy'
            },
            animations: {
              enabled: true,
              easing: 'easeinout',
              speed: 800,
            },
            fontFamily: 'Arial, sans-serif',
            responsive: [{
              breakpoint: 1000,
              options: {
                chart: {
                  width: '100%'
                }
              }
            }]
          },
          dataLabels: {
            enabled: false
          },
          markers: {
            size: 5,
            colors: ['#FFA41B', '#1cc88a', '#36b9cc', '#f6c23e'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
              size: 7
            }
          },
          stroke: {
            curve: 'smooth',
            width: 3
          },
          xaxis: {
            categories: [],
            labels: {
              rotate: -45
            }
          },
          yaxis: [{
            title: {
              text: 'Shuma'
            },
            labels: {
              formatter: function(val) {
                return val.toFixed(2);
              }
            }
          }, {
            opposite: true,
            title: {
              text: ''
            }
          }],
          tooltip: {
            shared: true,
            intersect: false,
            y: {
              formatter: function(val) {
                return val.toFixed(2);
              }
            },
            custom: function({
              series,
              seriesIndex,
              dataPointIndex,
              w
            }) {
              return `<div class="custom-tooltip">
                                  <span>${w.globals.labels[dataPointIndex]}: ${series[seriesIndex][dataPointIndex].toFixed(2)}</span>
                                </div>`;
            }
          },
          fill: {
            opacity: 0.85,
            type: 'gradient',
            gradient: {
              shade: 'dark',
              type: "vertical",
              shadeIntensity: 0.5,
              inverseColors: true,
              opacityFrom: 0.85,
              opacityTo: 0.85,
              stops: [0, 100]
            },
          },
          legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetX: 40
          },
          colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
        };
        const chart = new ApexCharts(document.querySelector("#revenueChart"), options);
        chart.render();

        // Function to convert USD to EUR using an external API
        async function convertToEUR(usdAmount) {
          try {
            const response = await fetch(`https://api.exchangerate-api.com/v4/latest/USD`);
            const data = await response.json();
            const rate = data.rates.EUR;
            return usdAmount * rate;
          } catch (error) {
            console.error('Error converting currency:', error);
            throw error;
          }
        }

        // Function to update the chart and summary cards with new data
        async function updateChartAndSummary(data) {
          const currency = data.year >= 2023 ? 'EUR' : 'USD';
          const currencySymbol = currency === 'EUR' ? '€' : '$';
          let totalValues = data.totalUSD;
          let profitValues = data.profitUSD;
          let yearTotal = data.yearTotalUSD;
          let yearProfit = data.yearProfitUsd;

          if (currency === 'EUR') {
            try {
              totalValues = await Promise.all(data.totalUSD.map(val => val !== null ? convertToEUR(val) : null));
              profitValues = await Promise.all(data.profitUSD.map(val => val !== null ? convertToEUR(val) : null));
              yearTotal = await convertToEUR(data.yearTotalUSD);
              yearProfit = await convertToEUR(data.yearProfitUsd);
            } catch {
              // Fallback to USD if conversion fails
            }
          }

          chart.updateSeries([{
              name: `Të ardhurat totale (${currency})`,
              data: totalValues
            },
            {
              name: `Fitimi total (${currency})`,
              data: profitValues
            }
          ]);

          $('#totalRevenue').text(`${currencySymbol}${yearTotal.toFixed(2)}`);
          $('#totalProfit').text(`${currencySymbol}${yearProfit.toFixed(2)}`);

          chart.updateOptions({
            xaxis: {
              categories: data.categories, // Update categories based on the selected month
              labels: {
                rotate: -45
              }
            },
            yaxis: [{
              labels: {
                formatter: function(val) {
                  return `${currencySymbol}${val.toFixed(2)}`;
                }
              }
            }, {
              opposite: true
            }]
          });
        }

        // Function to fetch and update dashboard data
        function updateDashboard() {
          const year = $('#yearFilter').val();
          const month = $('#monthFilter').val(); // Get selected month
          $.ajax({
            url: 'api/get_methods/get_monthly_data.php',
            method: 'POST',
            data: {
              year,
              month // Send both year and month to the backend
            },
            success: function(response) {
              if (response.error) {
                console.error('Error:', response.error);
                alert('Gabim gjatë marrjes së të dhënave: ' + response.error);
                return;
              }
              updateChartAndSummary(response);
            },
            error: function(xhr, status, error) {
            console.error('Error fetching data:', error);
              alert('Ndodhi një gabim gjatë marrjes së të dhënave.');
            }
          });
        }

        // Event listener for filter button
        $('#filterButton').on('click', updateDashboard);

        // Initial dashboard update
        updateDashboard();
      });
    </script>
  <?php endif; ?>
</body>

</html>