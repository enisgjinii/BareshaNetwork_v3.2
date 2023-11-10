<?php
include 'partials/header.php';
include 'conn-d.php';

// Check if the delete form is submitted
if (isset($_POST['delete_channel'])) {
    $channelId = $_POST['delete_channel'];

    // Perform the deletion query
    $sql = "DELETE FROM youtube_refresh_tokens WHERE channel_id = '$channelId'";
    if ($conn->query($sql) === TRUE) {
        // Display success message using Sweet Alert 2
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Kanali &euml;sht&euml; fshir&euml; me sukses",
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Redirect to the same page
                location.href = "";
            });
        </script>';
    } else {
        // Display error message using Sweet Alert 2
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Gabim gjat&euml; fshirjes s&euml; kanalit",
                text: "' . $conn->error . '"
            });
        </script>';
    }
}

$sql = "SELECT channel_id, channel_name FROM youtube_refresh_tokens";
$result = $conn->query($sql);
$channels = array();
while ($row = $result->fetch_assoc()) {
    $channels[] = array(
        'channel_id' => $row['channel_id'],
        'channel_name' => $row['channel_name']
    );
}
$conn->close();
?>



<style>
    .form {
        display: inline-block;
        vertical-align: middle;
        margin-right: 10px;
        /* Add some spacing between the forms */
    }

    .btn-facebook {
        position: relative;
        overflow: hidden;
    }

    .btn-facebook:hover .icon {
        transform: translateY(-125%);
    }

    .btn-facebook .icon {
        position: absolute;
        top: 100%;
        left: 15px;
        transition: transform 0.3s ease;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col-12">

                        <div class="d-flex justify-content-end mb-3">

                            <div class="" role="group">
                                <form id="search-form">
                                    <div class="input-group mb-3">
                                        <input type="text" id="search-query" class="form-control shadow-sm rounded-5" placeholder="K&euml;rko p&euml;r emrat e kanaleve">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-white mx-2 rounded-5 shadow-sm border border-1"><i class="fi fi-rr-search"></i></span>
                                        </div>
                                    </div>
                                </form>

                                <button type="button" class="btn btn-sm btn-primary rounded-5 text-white px-3" id="view-gallery-btn" style="text-transform:none;">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="fi fi-rr-apps"></i>
                                        </div>
                                        <div class="col">
                                            Galeri
                                        </div>
                                    </div>
                                </button>

                                <button type="button" class="btn btn-sm btn-primary rounded-5 text-white px-3" id="view-list-btn" style="text-transform:none;">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="fi fi-rr-list"></i>
                                        </div>
                                        <div class="col">
                                            List&euml;
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div id="search-results">
                            <!-- The search results will be displayed here -->
                        </div>
                    </div>
                </div>
                <div class="row" id="channels-gallery">
                    <?php foreach ($channels as $channel) : ?>
                        <div class="col-6">
                            <!-- Card content -->
                            <div class="card rounded-5 my-3 shadow-sm" style="border:1px solid lightgrey;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $channel['channel_name']; ?></h5>
                                    <p class="card-text">Channel ID: <?php echo $channel['channel_id']; ?></p>
                                    <div class="">
                                        <form method="post" action="youtube_studio.php" class="form">
                                            <input type="hidden" name="channel" value="<?php echo $channel['channel_id']; ?>">
                                            <button type="submit" class="btn btn-primary rounded-5 text-white" style="text-transform:none;" name="submit">Shiko raportin</button>
                                        </form>
                                        <form method="post" action="" class="form">
                                            <input type="hidden" name="delete_channel" value="<?php echo $channel['channel_id']; ?>">
                                            <button type="submit" class="btn btn-danger rounded-5 text-white" style="text-transform:none;" name="submit">Fshije kanalin</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="row" id="channels-list" style="display: none;">
                    <div class="col-12">
                        <div class="p-5 mb-4 card rounded-5 shadow-sm">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Emri i kanalit</th>
                                        <th>ID-ja e kanalit</th>
                                        <th>Veproni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($channels as $channel) : ?>
                                        <tr>
                                            <td><?php echo $channel['channel_name']; ?></td>
                                            <td><?php echo $channel['channel_id']; ?></td>
                                            <td>
                                                <form method="post" action="youtube_studio.php" class="form" target="_blank">
                                                    <input type="hidden" name="channel" value="<?php echo $channel['channel_id']; ?>">
                                                    <button type="submit" class="btn btn-primary rounded-5 text-white" style="text-transform:none;" name="submit">Shiko raportin</button>
                                                </form>

                                                <form method="post" action="" class="form">
                                                    <input type="hidden" name="delete_channel" value="<?php echo $channel['channel_id']; ?>">
                                                    <button type="submit" class="btn btn-danger rounded-5 text-white" style="text-transform:none;" name="submit">Fshije kanalin</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'partials/footer.php'; ?>
<script>
    $(document).ready(function() {
        // Switch to gallery view
        $('#view-gallery-btn').click(function() {
            $('#channels-list').fadeOut(200, function() {
                $('#channels-gallery').fadeIn(200);
            });
        });

        // Switch to list view
        $('#view-list-btn').click(function() {
            $('#channels-gallery').fadeOut(200, function() {
                $('#channels-list').fadeIn(200);
            });
        });
    });
</script>
<!-- Place this script section after the jQuery library and before the closing </body> tag -->
<script>
    $(document).ready(function() {
        // Function to perform AJAX search
        function performSearch() {
            var searchQuery = $('#search-query').val();

            $.ajax({
                url: 'search_channels.php',
                type: 'GET',
                data: {
                    search_query: searchQuery
                },
                dataType: 'json',
                success: function(response) {
                    // Update the search results dynamically
                    var searchResults = '';
                    if (response.length > 0) {
                        $.each(response, function(index, channel) {
                            if (index % 2 === 0) {
                                // Add an opening row div for even index
                                searchResults += '<div class="row">';
                            }

                            searchResults += '<div class="col-6">';
                            searchResults += '<div class="card rounded-5 my-3 shadow-sm" style="border:1px solid lightgrey;">';
                            searchResults += '<div class="card-body">';
                            searchResults += '<h5 class="card-title">' + channel.channel_name + '</h5>';
                            searchResults += '<p class="card-text">Channel ID: ' + channel.channel_id + '</p>';
                            searchResults += '<div class="">';
                            searchResults += '<form method="post" action="youtube_studio.php" class="form">';
                            searchResults += '<input type="hidden" name="channel" value="' + channel.channel_id + '">';
                            searchResults += '<button type="submit" class="btn btn-primary rounded-5 text-white" style="text-transform:none;" name="submit">Shiko raportin</button>';
                            searchResults += '</form>';
                            searchResults += '<form method="post" action="" class="form">';
                            searchResults += '<input type="hidden" name="delete_channel" value="' + channel.channel_id + '">';
                            searchResults += '<button type="submit" class="btn btn-danger rounded-5 text-white" style="text-transform:none;" name="submit">Fshije kanalin</button>';
                            searchResults += '</form>';
                            searchResults += '</div>';
                            searchResults += '</div>';
                            searchResults += '</div>';
                            searchResults += '</div>';

                            if (index % 2 !== 0 || index === response.length - 1) {
                                // Add a closing row div for odd index or the last channel
                                searchResults += '</div>';
                            }
                        });
                    } else {
                        // If no results found, display the warning message
                        searchResults = '<p class="shadow p-3 bg-light rounded-5 my-5" style="border:1px solid lightgrey;width:fit-content;">Nuk u gjet asnj&euml; kanal q&euml; p&euml;rputhet me kriteret e k&euml;rkimit tuaj.</p>';
                    }

                    // Display the search results or empty if there's no query
                    $('#search-results').html(searchQuery ? searchResults : '');
                    $('#search-results').append('<hr>');

                },
                error: function() {
                    // Handle error, if any
                }
            });
        }

        // Trigger the search on keyup event in the search input field
        $('#search-query').on('keyup', function() {
            performSearch();
        });
    });
</script>