<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pagat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="emrib" class="form-label">Muaji</label>
                                <select name="muaji" class="form-select">
                                    <option value="Janar">Janar</option>
                                    <option value="Shkurt">Shkurt</option>
                                    <option value="Mars">Mars</option>
                                    <option value="Prill">Prill</option>
                                    <option value="Maj">Maj</option>
                                    <option value="Qershor">Qershror</option>
                                    <option value="Korrik">Korrik</option>
                                    <option value="Gusht">Gusht</option>
                                    <option value="Shtator">Shtator</option>
                                    <option value="Tetor">Tetor</option>
                                    <option value="Nentor">Nentor</option>
                                    <option value="Dhjetor">Dhjetor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="emrit" class="form-label">Viti</label>
                                <select class="form-select" name="viti">
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                </select>
                                <script>
                                    // Use Selectr
                                    new Selectr('select[name="viti"]', {
                                        searchable: true,
                                    })

                                    new Selectr('select[name="muaji"]', {
                                        searchable: true,
                                    })



                                    // Get the current year
                                    var currentYear = new Date().getFullYear();

                                    // Select the dropdown element
                                    var dropdown = document.querySelector('select[name="viti"]');

                                    // Loop through the options and disable future years
                                    for (var year = currentYear + 1; year <= currentYear + 5; year++) {
                                        var option = document.createElement('option');
                                        option.value = year;
                                        option.textContent = year;

                                        // Disable options for the years 2024 and 2025 and 2026 and 2027 and 2028 
                                        if (year === 2024 || year === 2025 || year === 2026 || year === 2027 || year === 2028) {
                                            option.disabled = true;
                                        }

                                        // Append the option to the dropdown
                                        dropdown.appendChild(option);
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="emri" class="form-label">Zgjidh njërin nga stafi</label>
                                <select name="stafi" id="stafi" class="form-select">
                                    <?php
                                    $get_employees = $conn->query("SELECT * FROM googleauth");
                                    if ($get_employees->num_rows > 0) {
                                        while ($row = $get_employees->fetch_assoc()) {
                                            echo '<option value="' . $row['id'] . '">' . $row['firstName'] . ' ' . $row['last_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>


                                <script>
                                    new Selectr('select[name="stafi"]', {
                                        searchable: true,
                                    })
                                </script>
                            </div>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="row">
                            <div class="col"><label for="salary-display" class="form-label"> Rroga statike</label>
                                <input type="text" class="form-control rounded-5 shadow-sm border border-2" id="salary-display"></input>
                                <script>
                                    document.getElementById('stafi').addEventListener('change', function() {
                                        var selectedId = this.value;

                                        // Make an AJAX request to fetch salary based on the selected employee ID
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('GET', 'get_salary.php?id=' + selectedId, true);

                                        xhr.onload = function() {
                                            if (xhr.status == 200) {
                                                document.getElementById('salary-display').value = xhr.responseText;
                                            }
                                        };

                                        xhr.send();
                                    });
                                </script>
                            </div>
                            <div class="col">
                                <label for="datab" class="form-label">Shuma</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">&euro;</span>
                                    </div>
                                    <input type="text" class="form-control" name="shuma" class="form-control" id="inlineFormInputGroup" value="0.00" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="emrib" class="form-label">Kontributi i punëdhënësit i shprehur në %</label>
                                <input type="text" class="form-control rounded-5 shadow-sm" name="kontributi" value="5">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="emrib" class="form-label">Kontributi i punëtorit i shprehur në %</label>
                                <input type="text" class="form-control rounded-5 shadow-sm" name="kontributi2" value="5">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="datas" class="form-label">Data e pages&euml;s</label>
                                <input type="date" name="data" class="form-control rounded-5 shadow-sm" value="<?php echo date("Y-m-d"); ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="imei" class="form-label">Forma e pages&euml;s</label>
                                <select name="forma" class="form-select rounded-5 shadow-sm">

                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                                <script>
                                    new Selectr('select[name="forma"]', {
                                        searchable: true,
                                    })
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <!-- I want to make a checkbox like if that month salary is prepaid -->
                                <label for="parapagim" class="form-label">Pagesa e para-kohshme</label>
                                <br>
                                <input type="checkbox" name="parapagim" style="width: 20px; height: 20px;">
                            </div>
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