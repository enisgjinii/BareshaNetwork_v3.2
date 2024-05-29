<?php include 'partials/header.php'; ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Back button -->
            <a href="strike-platform.php" class="input-custom-css px-3 py-2" style="text-decoration:none;">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Kthehu</a>
            <br> <br>
            <!-- Content for editing strike details -->
            <?php
            // Check if an ID parameter is provided in the URL
            if (isset($_GET['id'])) {
                // Retrieve strike details from the database based on the provided ID
                $strike_id = $_GET['id'];

                // Connect to the database
                include 'conn-d.php';

                // Perform database query to fetch strike details
                $query = "SELECT * FROM platforms WHERE id = $strike_id";
                $result = mysqli_query($conn, $query);

                // Check if the query was successful
                if (mysqli_num_rows($result) > 0) {
                    $strike_details = mysqli_fetch_assoc($result);
                    // Extract strike details
                    $platform = $strike_details['platform'];
                    $titulli = $strike_details['titulli'];
                    $pershkrimi = $strike_details['pershkrimi'];
                    $data_e_krijimit = $strike_details['data_e_krijimit'];
                    $email_used = $strike_details['email_used'];
            ?>
                    <form id="editPlatformForm" class="bg-white p-4 rounded-4">
                        <input type="hidden" name="strike_id" value="<?php echo $strike_id; ?>">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="platforma" class="form-label">Zgjedh platformën</label>
                                    <select name="platforma" id="platforma"></select>
                                    <script>
                                        fetch('platforms_names.json')
                                            .then(response => response.json())
                                            .then(data => {
                                                const selectElement = document.getElementById('platforma');
                                                data.platforms.forEach(platform => {
                                                    const option = document.createElement('option');
                                                    option.value = platform;
                                                    option.textContent = platform;
                                                    selectElement.appendChild(option);
                                                });
                                                new Selectr('#platforma', {
                                                    searchable: true
                                                });
                                            })
                                            .catch(error => {
                                                console.error('Gabim gjatë ngarkimit të platforms_names.json:', error);
                                                Swal.fire(
                                                    'Gabim!',
                                                    'Pati një problem gjatë ngarkimit të zgjedhësit të platformës.',
                                                    'error'
                                                );
                                            });
                                    </script>

                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="titulli" class="form-label">Titulli</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="titulli" id="titulli" value="<?php echo $titulli; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="pershkrimi" class="form-label">Pershkrimi</label>
                                <input type="text" class="form-control rounded-5 border border-2" name="pershkrimi" id="pershkrimi" value="<?php echo $pershkrimi; ?>" required>
                            </div>
                            <div class="col-6">
                                <label for="data_e_krijimit" class="form-label">Data e krijimit</label>
                                <input type="text" class="form-control rounded-5 border border-2" name="data_e_krijimit" id="data_e_krijimit" value="<?php echo $data_e_krijimit; ?>" required>
                                <script>
                                    flatpickr("#data_e_krijimit", {
                                        enableTime: true,
                                        dateFormat: "Y-m-d H:i",
                                        maxDate: new Date().toISOString().split("T")[0]
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="emaili" class="form-label">Emaili qe eshte derguar per strike</label>
                                    <input type="text" class="form-control rounded-5 border border-2" name="emaili" id="emaili" value="<?php echo $email_used; ?>" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="input-custom-css px-3 py-2">Ruaj ndryshimet</button>
                    </form>
            <?php
                } else {
                    echo "Nuk u gjetën detaje për këtë goditje.";
                }

                // Close database connection
                mysqli_close($conn);
            } else {
                echo "ID e goditjes nuk është dhënë.";
            }
            ?>
        </div>
    </div>
</div>
<?php include 'partials/footer.php'; ?>
<script>
    // Create regec for email
    var emailRegEx = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    // Validate email
    function validateEmail(email) {
        return emailRegEx.test(email);
    }

    // Validate form
    function validateForm() {
        var email = document.getElementById("emaili").value;
        if (!validateEmail(email)) {
            // Do with sweet alert 2
            Swal.fire({
                icon: 'error',
                title: 'Emaili nuk eshte valid',
                text: 'Ju lutem shkruani emailin valid.',
                confirmButtonText: 'Fshi',
                confirmButtonColor: '#dc3545'
            })
            return false;
        }
        return true;
    }

    // Submit form
    document.getElementById("editPlatformForm").addEventListener("submit", function(event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    });
</script>

<script>
    document.getElementById('editPlatformForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way

        const formData = new FormData(this); // Gather all the form data

        fetch('process_edit_platform_strike.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Display success message
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: 'Të dhënat u përditësuan me sukses.',
                    confirmButtonText: 'OK',
                    // in top right 
                    position: 'top-end',
                    // Remove backdrop 
                    backdrop: false,
                    // Make smaller
                    width: 300,
                    // Add more features
                    showConfirmButton: true

                }).then(() => {});
            })
            .catch(error => {
                // Display error message
                Swal.fire({
                    icon: 'error',
                    title: 'Gabim',
                    text: 'Gabim gjatë përditësimit të të dhënave: ' + error,
                    confirmButtonText: 'OK'
                });
            });
    });
</script>