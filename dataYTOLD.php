<?php include 'partials/header.php' ?>
<script src="https://d3js.org/d3.v7.min.js"></script>

<style>
    #card {
        width: 200px;
        height: 150px;
        background-color: #f2f2f2;
        position: absolute;
    }

    #header {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
    }

    #content {
        padding: 10px;
    }

    #deleteBtn {
        position: absolute;
        bottom: 10px;
        right: 10px;
    }
</style>

<!-- const apiKey = "AIzaSyCvc0tIeB58Sz0hpDFSEYxDXFT8tg0VGGQ"; -->
<!-- const channelId = "UCV6ZBT0ZUfNbtZMbsy-L3CQ"; -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <div class="card rounded-5 shadow-sm p-3">
                            <p>Zgjedhni nj&euml; kanal</p>
                            <div class="input-group mb-3">
                                <input type="text" id="search-input" placeholder="K&euml;rko" class="form-control rounded-5 shadow-sm" onkeyup="filterOptions()">

                            </div>
                            <?php

                            echo '<select id="video-select" multiple name="select" class="form-select rounded-5 shadow-sm" onchange="updateChannelId(this.value)">';

                            $result = $conn->query("SELECT * FROM klientet ORDER BY emri ASC");
                            $options = '';
                            while ($row = mysqli_fetch_array($result)) {
                                $options .= '<option class="rounded-3 p-3 my-3" value="' . htmlspecialchars($row['youtube']) . '">'
                                    . htmlspecialchars($row['emri'])
                                    . '</option>';
                            }
                            echo $options;

                            echo '</select>';

                            ?>

                        </div>
                    </div>
                    <div class="col-6" ">
                        <div class=" card rounded-5 shadow-sm p-3">
                        <table class="table table-bordered" style="height:300px;">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col">Emri i artistit</th>
                                    <th scope="col">ID e kanalit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <p id="selectedOptionText"></p>
                                    </td>
                                    <td>
                                        <p id="selectedOptionText2"></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row my-3">
                <div class="col-12">
                    <div class="card rounded-5 shadow-sm p-3">
                        <div class="d-flex align-items-start ">
                            <div class="nav flex-column nav-pills me-3 border-end border-2 border-grey" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" id="v-pills-p&euml;rshkrimiIKanalit-tab" data-bs-toggle="pill" data-bs-target="#v-pills-p&euml;rshkrimiIKanalit" type="button" role="tab" aria-controls="v-pills-p&euml;rshkrimiIKanalit" aria-selected="true" style="text-transform:none;">P&euml;rshkrimi i kanalit</button>
                                <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Profile</button>
                                <!-- <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Profile</button>
                                <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages" type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false">Messages</button>
                                <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings" aria-selected="false">Settings</button> -->
                            </div>
                            <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-p&euml;rshkrimiIKanalit" role="tabpanel" aria-labelledby="v-pills-p&euml;rshkrimiIKanalit-tab">
                                    <p id="pershkrimiIParagrafit" class="text-wrap"></p>
                                </div>

                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                    <div class="row">
                                        <div class="col">
                                            <img src="" alt="">
                                        </div>
                                    </div>
                                </div>




                                <!-- <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">...</div>
                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">...</div>
                                <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">...</div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row my-3">
                <div class="col-12">
                    <div class="card rounded-5 shadow-sm p-3">








                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-5" id="pills-insertCSV-tab" data-bs-toggle="pill" data-bs-target="#pills-insertCSV" type="button" role="tab" aria-controls="pills-insertCSV" aria-selected="true" style="text-transform:none;"><span class="mdi mdi-file"></span>
                                    Inserto CSV</button>
                            </li>

                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-insertCSV" role="tabpanel" aria-labelledby="pills-insertCSV-tab">
                                <form action="upload.php" method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="formFile" class="form-label">Zgjidhni CSV file</label>
                                        <input class="form-control rounded-5 shadow-sm" type="file" id="formFile" name="file">
                                    </div>
                                    <button type="submit" class="btn btn-light rounded-5 float-right border" style="text-transform:none;">
                                        <i class="fi fi-rr-paper-plane" style="display:inline-block;vertical-align:middle;"></i>
                                        <span style="display:inline-block;vertical-align:middle;">D&euml;rgo</span>
                                    </button>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Replace the values in brackets with your actual values -->
            <script src="https://apis.google.com/js/api.js"></script>

            <div id="revenue"></div>
            <script>
                window.addEventListener('load', function() {
                    gapi.auth.authorize({
                        'client_id': '[960761078464-8s52outlfvtq9ondrvafpdarsvpgpqru.apps.googleusercontent.com]',
                        'scope': 'https://www.googleapis.com/auth/yt-analytics.readonly',
                        'immediate': true
                    }, function(authResult) {
                        if (authResult && !authResult.error) {
                            var request = gapi.client.youtubeAnalytics.reports.query({
                                'ids': 'channel==[YOUR_CHANNEL_ID]',
                                'metrics': 'estimatedRevenue',
                                'start-date': '30daysAgo',
                                'end-date': 'today'
                            });
                            request.execute(function(response) {
                                document.getElementById('revenue').textContent = 'Last 30 days revenue: ' + response.rows[0][0];
                            });
                        }
                    });
                });
            </script>










            <div class="col" style="margin-top:900px;">


                <div class="row">
                    <div class="col">
                        <p id="selectedOptionText" class="shadow-2 border border-1 rounded py-3 bg-white px-3"></p>
                    </div>
                    <div class="col">
                        <p id="selectedOptionText2" class="shadow-2 border border-1 rounded py-3 bg-white px-3"></p>
                    </div>
                    <div class="col">
                        <p id="selectedOptionText2" class="shadow-2 border border-1 rounded py-3 bg-white px-3"></p>
                    </div>
                </div>

            </div>




            <div class="row">
                <div class="col w-50">
                    <div class="card ">
                        <div id="chart" class="rounded rounded-5 py-4"></div>


                    </div>
                </div>
                <div class="col w-50">
                    <div class="card ">
                        <div id="chart2" class="rounded rounded-5 py-4"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script>
    function filterOptions() {
        var input, filter, options, i, txtValue;
        input = document.getElementById("search-input");
        filter = input.value.toUpperCase();
        options = document.getElementById("video-select").options;
        for (i = 0; i < options.length; i++) {
            txtValue = options[i].text;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
            } else {
                options[i].style.display = "none";
            }
        }
    }
</script>


<script>
    const apiKey = "AIzaSyAjEtD_5L5nvy-iihOk7soj9QSBJVhIF2Q";
    let channelId = "UCV6ZBT0ZUfNbtZMbsy-L3CQ";
    let videoContainer = document.getElementById("videoContainer");

    const options = {
        method: 'GET',
        headers: {
            'X-RapidAPI-Key': '335200c4afmsh64cfbbf7fdf4cf2p1aae94jsn05a3bad585de',
            'X-RapidAPI-Host': 'youtube-v31.p.rapidapi.com'
        }
    };




    function updateChannelId(value) {

        channelId = value;
        fetch(`https://www.googleapis.com/youtube/v3/channels?part=statistics&id=${channelId}&key=${apiKey}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                const viewCount = data.items[0].statistics.viewCount;
                const subscriberCount = data.items[0].statistics.subscriberCount;
                const videoCount = data.items[0].statistics.videoCount;

                Highcharts.chart("chart", {
                    chart: {
                        type: "column"
                    },
                    title: {
                        text: "Analiza e kanalit - YouTube"
                    },
                    xAxis: {
                        categories: ['Abonent']
                    },
                    yAxis: {
                        title: {
                            text: 'Vlerat'
                        }
                    },
                    series: [{
                        name: "Te dhenat",
                        data: [parseInt(subscriberCount)]
                    }]
                });

                Highcharts.chart("chart2", {
                    chart: {
                        type: "column"
                    },
                    title: {
                        text: "Analiza e kanalit - YouTube"
                    },
                    xAxis: {
                        categories: ['Videot']
                    },
                    yAxis: {
                        title: {
                            text: 'Vlerat'
                        }
                    },
                    series: [{
                        name: "Te dhenat",
                        data: [parseInt(videoCount)]
                    }]
                });
            });

        const selectElement = document.querySelector("select");
        const shfaqjaEEmrit = selectElement.options[selectElement.selectedIndex].text;
        document.getElementById("selectedOptionText").innerHTML = shfaqjaEEmrit;


        const kanalID = selectElement.options[selectElement.selectedIndex].value;
        document.getElementById("selectedOptionText2").innerHTML = kanalID;
        fetch(`https://youtube-v31.p.rapidapi.com/channels?part=snippet&id=${channelId}`, options)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                const channelDescription = data.items[0].snippet.description;
                const thumbnail = data.items[0].snippet.thumbnails.high.url;
                // Do something with the channel description, such as displaying it in the HTML
                document.getElementById('pershkrimiIParagrafit').innerHTML = channelDescription;
                document.getElementById('thumbnail').src = thumbnail;
            })
            .catch(err => console.error(err));

    }
</script>

<?php include 'partials/footer.php' ?>