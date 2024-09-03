<div class="main-panel">
  <div class="content-wrapper">
    <div class="container-fluid">
      <?php
      // Function to fetch a random quote from the Quotable API
      function fetchQuoteFromAPI()
      {
        $api_url = 'https://api.quotable.io/random';
        $ch = curl_init();
        // Set the options for the cURL request
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Execute the request
        $response = curl_exec($ch);
        // Check for errors
        if (curl_errno($ch)) {
          // Handle error
          return "Error fetching quote: " . curl_error($ch);
        }
        // Close the cURL session
        curl_close($ch);
        // Decode the JSON response
        $data = json_decode($response, true);
        // Return the quote and author from the response
        return $data['content'] . ' - ' . $data['author'];
      }
      // Fetch a random quote
      $random_quote = fetchQuoteFromAPI();
      ?>
      <!-- Main content -->
      <div>
        <h1 class="h2">Mirë se vini <?php echo $user_info['givenName'] . ' ' . $user_info['familyName']; ?></h1>
        <p><?php echo $random_quote; ?></p>
      </div>
      <br>
      <div class=" row mb-4">
        <?php
        $cards = [
          ['Total Revenue', 'totalRevenue', '€0.00', 'fi-rr-euro'], // Default to EUR for the current year
          ['Total Profit', 'totalProfit', '€0.00', 'fi-rr-chart-line-up']
        ];
        foreach ($cards as $card) { ?>
          <div class="col-md-6 mb-4">
            <div class="card summary-card rounded-5">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="card-title mb-0"><?= $card[0] ?></h6>
                    <h3 class="mt-2 mb-0" id="<?= $card[1] ?>"><?= $card[2] ?></h3>
                  </div>
                  <div class="summary-icon">
                    <i class="fi <?= $card[3] ?>"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="card mb-4 rounded-5">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Të ardhurat dhe fitimet mujore</h5>
            <div class="d-flex">
              <select id="yearFilter" class="form-select me-2">
                <?php
                $currentYear = date('Y');
                for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
                  echo "<option value='$year'>$year</option>";
                }
                ?>
              </select>
              <button id="filterButton" class="input-custom-css px-3 py-2">
                <i class="fi fi-rr-filter me-2"></i>Filter
              </button>
            </div>
          </div>
          <div id="revenueChart" style="height: 500px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  // Initialize chart with enhanced features
  var options = {
    series: [],
    chart: {
      type: 'line',
      height: 500,
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
          customIcons: [] // You can add custom tools if needed
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
      fontFamily: 'Arial, sans-serif', // Custom font
      // background: '#f4f4f4', // Background color for the chart
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
        x: new Date('01 Mar 2024').getTime(),
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
        return '<div class="custom-tooltip">' +
          '<span>' + w.globals.labels[dataPointIndex] + ': ' + series[seriesIndex][dataPointIndex].toFixed(2) + '</span>' +
          '</div>';
      }
    },
    fill: {
      opacity: 0.85,
      type: 'gradient',
      gradient: {
        shade: 'dark',
        type: "vertical",
        shadeIntensity: 0.5,
        gradientToColors: undefined,
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
  var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
  chart.render();
  function updateDashboard() {
    var year = document.getElementById('yearFilter').value;
    $.ajax({
      url: 'api/get_methods/get_monthly_data.php',
      method: 'POST',
      data: {
        year: year
      },
      success: function(response) {
        var data = JSON.parse(response);
        var currency = (data.year >= 2023) ? 'EUR' : 'USD';
        var currencySymbol = currency === 'EUR' ? '€' : '$';
        function convertToEUR(usdAmount) {
          return new Promise((resolve, reject) => {
            $.ajax({
              url: `https://api.exconvert.com/convert?from=USD&to=EUR&amount=${usdAmount}`,
              method: 'GET',
              success: function(response) {
                resolve(parseFloat(response.result));
              },
              error: function(error) {
                console.error('Error converting currency:', error);
                reject(error);
              }
            });
          });
        }
        function updateChartAndSummary(totalValues, profitValues, yearTotal, yearProfit) {
          chart.updateSeries([{
              name: 'Total ' + currency,
              data: totalValues
            },
            {
              name: 'Profit ' + currency,
              data: profitValues
            }
          ]);
          $('#totalRevenue').text(currencySymbol + yearTotal.toFixed(2));
          $('#totalProfit').text(currencySymbol + yearProfit.toFixed(2));
          chart.updateOptions({
            chart: {
              animations: {
                enabled: true
              }
            },
            yaxis: {
              labels: {
                formatter: function(val) {
                  return currencySymbol + val.toFixed(2);
                }
              }
            }
          });
        }
        if (currency === 'USD') {
          updateChartAndSummary(data.totalUSD, data.profitUSD, data.yearTotalUSD, data.yearProfitUSD);
        } else {
          Promise.all([
            Promise.all(data.totalUSD.map(val => val !== null ? convertToEUR(val) : null)),
            Promise.all(data.profitUSD.map(val => val !== null ? convertToEUR(val) : null)),
            convertToEUR(data.yearTotalUSD),
            convertToEUR(data.yearProfitUSD)
          ]).then(([totalEUR, profitEUR, yearTotalEUR, yearProfitEUR]) => {
            updateChartAndSummary(totalEUR, profitEUR, yearTotalEUR, yearProfitEUR);
          }).catch(error => {
            console.error('Error converting currencies:', error);
            // Fallback to USD if conversion fails
            updateChartAndSummary(data.totalUSD, data.profitUSD, data.yearTotalUSD, data.yearProfitUSD);
          });
        }
      }
    });
  }
  document.getElementById('filterButton').addEventListener('click', updateDashboard);
  updateDashboard();
</script>