<?php
include 'partials/header.php';

// Retrieve data from the "ascap" table
$sql = "SELECT * FROM ascap";
$result = $conn->query($sql);
require_once 'vendor/autoload.php';

?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="p-5 rounded-5 shadow-sm mb-4 card text-dark">
                <h2>CSV File Upload</h2>
                <form action="upload-ascap.php" method="post" enctype="multipart/form-data">
                    <label for="file" class="custom-file-upload">
                        <input type="file" name="file" id="file" class="form-control shadow-sm rounded-5">
                    </label>
                    <input type="submit" name="submit" value="Upload"
                        class="btn btn-light shadow-sm rounded-5 ms-2 bordered">
                </form>
            </div>
            <div class="p-5 rounded-5 shadow-sm mb-4 card text-dark">
                <div class="row mb-5">
                    <div class="col">
                        <label for="kerko" class="form-label">Kërko</label>
                        <input type="search" class="form-control shadow-2 rounded-5 py-4" name="kerko" id="kerko"
                            oninput="filterTable()">
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table id="example" class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-dark">Party ID 1</th>
                                <th class="text-dark">Party ID 2</th>
                                <th class="text-dark">Party ID</th>
                                <th class="text-dark">Total Number of Works</th>
                                <th class="text-dark">Member Name</th>
                                <th class="text-dark">Work Title</th>
                                <th class="text-dark">Ascap Work ID</th>
                                <th class="text-dark">Interested Parties</th>
                                <th class="text-dark">IPI Number</th>
                                <th class="text-dark">Interested Party Status</th>
                                <th class="text-dark">Role</th>
                                <th class="text-dark">Society</th>
                                <th class="text-dark">Own Percentage</th>
                                <th class="text-dark">Collect Percentage</th>
                                <th class="text-dark">Registration Date</th>
                                <th class="text-dark">Registration Status</th>
                                <th class="text-dark">Surveyed Work</th>
                                <th class="text-dark">ISWC Number</th>
                                <th class="text-dark">Work Licensed by Ascap</th>
                                <th class="text-dark">Share Licensed by Ascap</th>
                                <th class="text-dark">Length</th>
                                <th class="text-dark">Date of published</th>
                            </tr>
                        </thead>
                        <!-- <tbody id="table-body"> -->
                        <?php


                        // if ($result->num_rows > 0) {
                        
                        //     $videoDetails = [];
                        
                        //     while ($row = $result->fetch_assoc()) {
                        //         $workTitle = $row['Work_Title'];
                        //         $interestedParties = $row['Interested_Parties'];
                        

                        //         $searchQuery = urlencode($workTitle);
                        //         $maxResults = 1;
                        //         $apiEndpoint = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=$searchQuery&type=video&maxResults=$maxResults&key=AIzaSyCTIwBs8tS1JBTS7hoWi-9pd1dYhpkciZU";
                        

                        //         if (!isset($videoDetails[$workTitle])) {
                        //             $response = @file_get_contents($apiEndpoint); 
                        
                        //             if ($response !== false) {
                        //                 $jsonResponse = json_decode($response, true);
                        
                        //                 if (isset($jsonResponse['items'][0]['id']['videoId'])) {
                        //                     $videoId = $jsonResponse['items'][0]['id']['videoId'];
                        

                        //                     $apiEndpoint = "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=$videoId&key=AIzaSyCTIwBs8tS1JBTS7hoWi-9pd1dYhpkciZU";
                        //                     $response = @file_get_contents($apiEndpoint); 
                        
                        //                     if ($response !== false) {
                        //                         $jsonResponse = json_decode($response, true);
                        
                        //                         if (isset($jsonResponse['items'][0]['contentDetails']['duration']) && isset($jsonResponse['items'][0]['snippet']['publishedAt'])) {
                        //                             $duration = $jsonResponse['items'][0]['contentDetails']['duration'];
                        //                             $duration = preg_replace('/^PT(\d+)M(\d+)S$/', '$1:$2', $duration);
                        //                             $durationParts = explode(':', $duration);
                        //                             $minutes = str_pad($durationParts[0], 2, '0', STR_PAD_LEFT);
                        //                             $seconds = str_pad($durationParts[1], 2, '0', STR_PAD_LEFT);
                        //                             $duration = $minutes . ':' . $seconds;
                        //                             $publishedAt = $jsonResponse['items'][0]['snippet']['publishedAt'];
                        //                             $publishedDate = date("Y-m-d", strtotime($publishedAt));
                        

                        //                             $videoDetails[$workTitle] = [
                        //                                 'duration' => $duration,
                        //                                 'publishedDate' => $publishedDate
                        //                             ];
                        //                         } else {
                        
                        //                             $duration = 'N/A';
                        //                             $publishedDate = 'N/A';
                        //                         }
                        //                     } else {
                        
                        //                         $duration = 'N/A';
                        //                         $publishedDate = 'N/A';
                        //                     }
                        //                 } else {
                        
                        //                     $duration = 'N/A';
                        //                     $publishedDate = 'N/A';
                        //                 }
                        //             } else {
                        
                        //                 $duration = 'N/A';
                        //                 $publishedDate = 'N/A';
                        //             }
                        //         } else {
                        
                        //             $duration = $videoDetails[$workTitle]['duration'];
                        //             $publishedDate = $videoDetails[$workTitle]['publishedDate'];
                        //         }
                        

                        //         echo "<tr>";
                        //         echo "<td>" . $row['Party_ID_1'] . "</td>";
                        //         echo "<td>" . $row['Party_ID_2'] . "</td>";
                        //         echo "<td>" . $row['Party_ID'] . "</td>";
                        //         echo "<td>" . $row['Total_Number_Of_Works'] . "</td>";
                        //         echo "<td>" . $row['Member_Name'] . "</td>";
                        //         echo "<td>" . $row['Work_Title'] . "</td>";
                        //         echo "<td>" . $row['ASCAP_Work_ID'] . "</td>";
                        //         echo "<td>" . $row['Interested_Parties'] . "</td>";
                        //         echo "<td>" . $row['IPI_Number'] . "</td>";
                        //         echo "<td>" . $row['Interested_Party_Status'] . "</td>";
                        //         echo "<td>" . $row['Role'] . "</td>";
                        //         echo "<td>" . $row['Society'] . "</td>";
                        //         echo "<td>" . $row['Own_Percentage'] . "</td>";
                        //         echo "<td>" . $row['Collect_Percentage'] . "</td>";
                        //         echo "<td>" . $row['Registration_Date'] . "</td>";
                        //         echo "<td>" . $row['Registration_Status'] . "</td>";
                        //         echo "<td>" . $row['Surveyed_Work'] . "</td>";
                        //         echo "<td>" . $row['ISWC_Number'] . "</td>";
                        //         echo "<td>" . $row['Work_Licensed_By_ASCAP'] . "</td>";
                        //         echo "<td>" . $row['Share_Licensed_By_ASCAP'] . "</td>";
                        //         echo "<td>" . $duration . "</td>";
                        //         echo "<td>" . $publishedDate . "</td>";
                        //         echo "</tr>";
                        //     }
                        // }
                        ?>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php' ?>



<script>
    function filterTable() {
        var input = document.getElementById("kerko").value.toLowerCase();
        var tableRows = document.getElementById("table-body").getElementsByTagName("tr");

        for (var i = 0; i < tableRows.length; i++) {
            var row = tableRows[i];
            var rowData = row.getElementsByTagName("td");
            var found = false;

            for (var j = 0; j < rowData.length; j++) {
                var cell = rowData[j];
                if (cell) {
                    var text = cell.textContent || cell.innerText;
                    if (text.toLowerCase().indexOf(input) > -1) {
                        found = true;
                        break;
                    }
                }
            }

            if (found) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
</script>

<script>
    $(document).ready(function () {
        var dataTables = $('#example').DataTable({
            responsive: false,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Të gjitha"]
            ],
            initComplete: function () {
                var btns = $('.dt-buttons');
                btns.addClass('');
                btns.removeClass('dt-buttons btn-group');
                var lengthSelect = $('div.dataTables_length select');
                lengthSelect.addClass('form-select');
                lengthSelect.css({
                    'width': 'auto',
                    'margin': '0 8px',
                    'padding': '0.375rem 1.75rem 0.375rem 0.75rem',
                    'line-height': '1.5',
                    'border': '1px solid #ced4da',
                    'border-radius': '0.25rem',
                });
            },
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>' +
                'Brtip',
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                titleAttr: 'Eksporto tabelen ne formatin PDF',
                className: 'btn btn-light border shadow-2 me-2'
            }, {
                extend: 'copyHtml5',
                text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                titleAttr: 'Kopjo tabelen ne formatin Clipboard',
                className: 'btn btn-light border shadow-2 me-2'
            }, {
                extend: 'excelHtml5',
                text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                titleAttr: 'Eksporto tabelen ne formatin Excel',
                className: 'btn btn-light border shadow-2 me-2',
                exportOptions: {
                    modifier: {
                        search: 'applied',
                        order: 'applied',
                        page: 'all'
                    }
                }
            }, {
                text: 'Select all',
                className: 'btn btn-light border shadow-2 me-2',
                action: function () {
                    dataTables.rows().select();
                }
            },
            {
                text: 'Select none',
                className: 'btn btn-light border shadow-2 me-2',
                action: function () {
                    dataTables.rows().deselect();
                }
            }, {
                extend: 'print',
                text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                titleAttr: 'Printo tabelën',
                className: 'btn btn-light border shadow-2 me-2'
            },
            ],
            fixedHeader: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            stripeClasses: ['stripe-color'],
            select: true,
            ajax: {
                url: 'fetch_ascap.php',
                type: 'POST',
                dataType: 'json',
                dataSrc: ''
            },
            columns: [
                { data: 'Party_ID_1' },
                { data: 'Party_ID_2' },
                { data: 'Party_ID' },
                { data: 'Total_Number_Of_Works' },
                { data: 'Member_Name' },
                { data: 'Work_Title' },
                { data: 'ASCAP_Work_ID' },
                { data: 'Interested_Parties' },
                { data: 'IPI_Number' },
                { data: 'Interested_Party_Status' },
                { data: 'Role' },
                { data: 'Society' },
                { data: 'Own_Percentage' },
                { data: 'Collect_Percentage' },
                { data: 'Registration_Date' },
                { data: 'Registration_Status' },
                { data: 'Surveyed_Work' },
                { data: 'ISWC_Number' },
                { data: 'Work_Licensed_By_ASCAP' },
                { data: 'Share_Licensed_By_ASCAP' },
                {
                    data: null,
                    render: function (data, type, row) {
                        var duration = data.duration || 'N/A'; // Use 'N/A' if duration is not available
                        return duration;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        var publishedDate = data.publishedDate || 'N/A'; // Use 'N/A' if published date is not available
                        return publishedDate;
                    }
                }
            ]
        });

        $('#example_filter input').on('keyup', function () {
            dataTables.search(this.value).draw();
        });
    });
</script>