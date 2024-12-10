<?php
// index.php

// Include the database connection
include "partials/header.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define YouTube API Key
$youtubeApiKey = 'AIzaSyBQD3hhckJv5uxPcbRk3b8nlNogG9781Lk'; // Replace with your actual YouTube API key

// Ensure that Bootstrap and jQuery are included. You can include them via CDN or your local files.
// It's assumed that 'partials/header.php' contains the necessary HTML <head> elements, Bootstrap CSS, and jQuery.
// If not, include them directly here.

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>YouTube Channels</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .input-custom-css {
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .input-custom-css:hover {
            background-color: #45a049;
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        table.dataTable tbody tr {
            height: 60px;
        }

        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 50px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid my-4">
        <?php
        // Fetch all clients with YouTube IDs
        $sql = "SELECT * FROM klientet WHERE youtube LIKE 'UC%' ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);
        ?>
        <div class="table-responsive">
            <table id="klientetTable" class="display table table-striped table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Youtube ID</th>
                        <th>Emri</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $youtubeId = htmlspecialchars($row['youtube']);
                            $emri = htmlspecialchars($row['emri']);
                            // Use data attributes and a generic class. We'll trigger the modal via JavaScript
                            echo "<tr>
                                  <td>{$youtubeId}</td>
                                  <td>{$emri}</td>
                                  <td>
                                    <button class='input-custom-css px-3 py-2 view-videos' 
                                            data-channel-id='{$youtubeId}' 
                                            data-channel-name='{$emri}'
                                            data-bs-toggle='modal' 
                                            data-bs-target='#videosModal'>
                                        View Videos
                                    </button>
                                  </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="videosModal" tabindex="-1" aria-labelledby="videosModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="videosModalLabel">Videos from <span id="modalChannelName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="videosContainer">
                        <p class="text-center">Click "View Videos" to load videos.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery, Bootstrap JS, and DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#klientetTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "lengthChange": false,
                    "pageLength": 10,
                    "language": {
                        "search": "Search:",
                        "paginate": {
                            "previous": "Prev",
                            "next": "Next"
                        }
                    }
                });

                var selectedChannelId = '';
                var selectedChannelName = '';

                // Capture channel info on button click before modal shows
                $('#klientetTable').on('click', '.view-videos', function() {
                    selectedChannelId = $(this).data('channel-id');
                    selectedChannelName = $(this).data('channel-name');
                    $('#modalChannelName').text(selectedChannelName);
                    $('#videosContainer').html('<div class="loader"></div>');
                });

                // When the modal is fully shown, fetch videos
                $('#videosModal').on('shown.bs.modal', function() {
                    if (selectedChannelId) {
                        $.ajax({
                            url: 'fetch_videos.php', // Ensure this path is correct
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'fetch_videos',
                                channelId: selectedChannelId
                            },
                            success: function(response) {
                                if (response.error) {
                                    $('#videosContainer').html('<p class="text-center text-danger">' + response.error + '</p>');
                                } else {
                                    if (response.channelName) {
                                        $('#modalChannelName').text(response.channelName);
                                    }
                                    $('#videosContainer').html(response.html);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('AJAX Error:', textStatus, errorThrown);
                                $('#videosContainer').html('<p class="text-center text-danger">An error occurred while fetching videos.</p>');
                            }
                        });
                    } else {
                        $('#videosContainer').html('<p class="text-center text-danger">No channel selected.</p>');
                    }
                });
            });
        </script>
    </div>
</body>

</html>

<?php
// Close the database connection
if ($conn) {
    mysqli_close($conn);
}
?>