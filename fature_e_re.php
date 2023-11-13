<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fatur&euml; e re</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col">
                            <label for="emri" class="form-label">Emri & Mbiemri</label>
                            <input type="text" id="searchInputFirst" onkeyup="filterOptions()"
                                class="form-control shadow-sm rounded-5 py-3" style="border:1"
                                placeholder="Search for names..">
                            <br>
                            <select id="emriSelect" name="emri" class="form-select shadow-sm rounded-5 py-3">
                                <?php
                                // PHP: Fetching data from the "klientet" table where blocked='0'
                                $gsta = $conn->query("SELECT * FROM klientet WHERE blocked='0'");
                                while ($gst = mysqli_fetch_array($gsta)) {
                                    // PHP: Generating option elements with values from the database
                                    echo '<option value="' . $gst['id'] . '">' . $gst['emri'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <script>
                            function filterOptions() {
                                var input, filter;
                                input = document.getElementById('searchInputFirst');
                                filter = input.value.toLowerCase();

                                // Create an AJAX request
                                var xmlhttp = new XMLHttpRequest();
                                xmlhttp.onreadystatechange = function () {
                                    if (this.readyState == 4 && this.status == 200) {
                                        // Update the select dropdown with the response
                                        document.getElementById('emriSelect').innerHTML = this.responseText;
                                    }
                                };

                                // Send the request to the server-side script
                                xmlhttp.open('GET', 'filter_names.php?filter=' + filter, true);
                                xmlhttp.send();
                            }
                        </script>


                        <div class="col">
                            <!-- Input field for entering Data -->
                            <label for="datas" class="form-label">Data:</label>
                            <input type="text" name="data" class="form-control shadow-sm rounded-5 py-3"
                                value="<?php echo date("Y-m-d"); ?>">

                        </div>
                    </div>

                    <div class="row my-3">
                        <div class="col">
                            <!-- Input field for displaying Fatura -->
                            <label for="imei" class="form-label">Fatura:</label>
                            <input type="text" name="fatura" class="form-control shadow-sm rounded-5 py-3"
                                value="<?php echo date('dmYhis'); ?>" readonly>
                        </div>
                        <div class="col">
                            <?php
                            // Checking if the user's session is set to '1'
                            if ($_SESSION['acc'] == '1') {
                                ?>
                                <!-- Select field for choosing gjendjaFatures -->
                                <label for="gjendjaFatures" class="form-label">Zgjidhni gjendjen e fatur&euml;s:</label>
                                <select name="gjendjaFatures" id="gjendjaFatures"
                                    class="form-select shadow-sm rounded-5 py-3">
                                    <option value="Rregullt">Rregullt</option>
                                    <option value="Pa rregullt">Pa rregullt</option>
                                </select>
                                <?php
                            } else {
                            }
                            ?>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mbylle</button>
                <input type="submit" class="btn btn-primary" name="ruaj" value="Ruaj">
                </form>
            </div>
        </div>
    </div>
</div>