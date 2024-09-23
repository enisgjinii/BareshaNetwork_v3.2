<?php
// Start the session to access session variables
session_start();
// ==================================
// 1. Authorization Check
// ==================================
// Define the authorized email address
$authorized_email = 'info@bareshamusic.com';
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
            ['title' => 'Total Revenue', 'id' => 'totalRevenue', 'value' => '€0.00', 'icon' => 'bi-cash-stack', 'bg' => 'bg-light'],
            ['title' => 'Total Profit', 'id' => 'totalProfit', 'value' => '€0.00', 'icon' => 'bi-graph-up-arrow', 'bg' => 'bg-light'],
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
            <div class="d-flex flex-wrap align-items-center gap-2">
              <select id="yearFilter" class="form-select form-select-sm" style="width: auto;">
                <?php
                // Populate the year filter dropdown with the current year and the past 5 years
                $currentYear = date('Y');
                foreach (range($currentYear, $currentYear - 5) as $year):
                  echo "<option value='$year'>$year</option>";
                endforeach;
                ?>
              </select>
              <button id="filterButton" class="btn btn-primary btn-sm">
                <i class="bi bi-filter"></i> Filter
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
        annotations: {
          yaxis: [{
            y: 5000,
            borderColor: '#FF4560',
            label: {
              borderColor: '#FF4560',
              style: {
                color: '#fff',
                background: '#FF4560'
              },
              text: 'Y-axis annotation'
            }
          }],
          xaxis: [{
            x: new Date('2024-03-01').getTime(),
            borderColor: '#775DD0',
            label: {
              style: {
                color: '#fff',
                background: '#775DD0'
              },
              text: 'X-axis annotation'
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
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            dataLabels: {
              total: {
                enabled: true,
                style: {
                  fontSize: '13px',
                  fontWeight: 900
                }
              }
            }
          },
        },
        stroke: {
          curve: 'smooth',
          width: 3
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          labels: {
            rotate: -45
          }
        },
        yaxis: [{
          title: {
            text: 'Amount'
          },
          labels: {
            formatter: function(val) {
              return val.toFixed(2);
            }
          }
        }, {
          opposite: true,
          title: {
            text: 'Secondary Y-Axis'
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
          const response = await fetch(`https://api.exconvert.com/convert?from=USD&to=EUR&amount=${usdAmount}`);
          const data = await response.json();
          return parseFloat(data.result);
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
        let yearProfit = data.yearProfitUSD;
        if (currency === 'EUR') {
          try {
            totalValues = await Promise.all(data.totalUSD.map(val => val !== null ? convertToEUR(val) : null));
            profitValues = await Promise.all(data.profitUSD.map(val => val !== null ? convertToEUR(val) : null));
            yearTotal = await convertToEUR(data.yearTotalUSD);
            yearProfit = await convertToEUR(data.yearProfitUSD);
          } catch {
            // Fallback to USD if conversion fails
          }
        }
        chart.updateSeries([{
            name: `Total ${currency}`,
            data: totalValues
          },
          {
            name: `Profit ${currency}`,
            data: profitValues
          }
        ]);
        $('#totalRevenue').text(`${currencySymbol}${yearTotal.toFixed(2)}`);
        $('#totalProfit').text(`${currencySymbol}${yearProfit.toFixed(2)}`);
        chart.updateOptions({
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
        $.ajax({
          url: 'api/get_methods/get_monthly_data.php',
          method: 'POST',
          data: {
            year
          },
          success: function(response) {
            const data = JSON.parse(response);
            updateChartAndSummary(data);
          },
          error: function(xhr, status, error) {
            console.error('Error fetching data:', error);
            // Optionally, display an error message to the user
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