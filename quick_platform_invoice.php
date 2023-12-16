<?php include 'partials/header.php' ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container"> <!-- Added 'mt-4' for top margin -->
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Platformat</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="invoice.php" class="text-reset" style="text-decoration: none;">
                            Raportet
                        </a>
                    </li>
            </nav>
            <!-- Button trigger modal -->
            <button type="button" class="input-custom-css px-3 py-2 mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fi fi-rr-add"></i> &nbsp; Krijo raport te ri
            </button>
            <button id="deleteRowsBtn" class="input-custom-css px-3 py-2 mb-2">Fshi rreshtat e përzgjedhur</button>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Krijo raport</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form for reporting office damages -->
                            <form id="damageForm" action="process_new_platform_invoice.php" method="post">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="id_of_client" class="form-label">Zgjedh klientin</label>
                                        <select name="id_of_client" id="id_of_client" class="form-select rounded-5">
                                            <?php
                                            $result = $conn->query("SELECT * FROM klientet");
                                            while ($row = mysqli_fetch_array($result)) {
                                                echo '<option value="' . $row['id'] . '">' . $row['emri'] . '</option>';
                                            }
                                            ?>
                                        </select>

                                        <div id="client-details">
                                            <!-- Display additional client details here -->
                                        </div>


                                        <script>
                                            new Selectr('#id_of_client', {
                                                searchable: true,
                                            });
                                        </script>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="platform" class="form-label">Zgjedh platformen</label>
                                        <select name="platform" id="platform" class="form-select rounded-5">
                                            <!-- Populate with platform names -->
                                            <option value="Spotify">Spotify</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Tiktok">Tiktok</option>
                                            <option value="Instagram">Instagram</option>
                                            <option value="YouTube">YouTube</option>
                                            <option value="WhatsApp">WhatsApp</option>
                                            <option value="Snapchat">Snapchat</option>
                                            <option value="Twitter">Twitter</option>
                                            <option value="LinkedIn">LinkedIn</option>
                                            <option value="Reddit">Reddit</option>
                                            <option value="Tjera">Tjera</option>
                                        </select>
                                        <p class="text-muted" id="platform-description"></p>
                                        <div id="platform-icon" class="mt-2"></div>
                                        <script>
                                            new Selectr('#platform', {
                                                searchable: true
                                            })
                                            document.getElementById('platform-description').innerHTML = '';
                                            // Update the icon based on the selected platform
                                            document.getElementById('platform').addEventListener('change', function() {
                                                // Hide that paragrah then display 
                                                document.getElementById('platform-description').innerHTML = 'Kjo ikon do perdoret per fatur';
                                                var platform = this.value;
                                                var iconClass = getIconClass(platform);
                                                document.getElementById('platform-icon').innerHTML = '<i class="' + iconClass + ' fa-2x"></i>';
                                            });

                                            // Function to map platform names to corresponding Font Awesome icons
                                            function getIconClass(platform) {
                                                switch (platform) {
                                                    case 'Spotify':
                                                        return 'fab fa-spotify';
                                                    case 'Facebook':
                                                        return 'fab fa-facebook';
                                                    case 'Tiktok':
                                                        return 'fab fa-tiktok';
                                                    case 'Instagram':
                                                        return 'fab fa-instagram';
                                                    case 'YouTube':
                                                        return 'fab fa-youtube';
                                                    case 'WhatsApp':
                                                        return 'fab fa-whatsapp';
                                                    case 'Snapchat':
                                                        return 'fab fa-snapchat';
                                                    case 'Twitter':
                                                        return 'fab fa-twitter';
                                                    case 'LinkedIn':
                                                        return 'fab fa-linkedin';
                                                    case 'Reddit':
                                                        return 'fab fa-reddit';
                                                    case 'Tjera':
                                                        return 'fas fa-question'; // Default icon for "Tjera"
                                                    default:
                                                        return 'fas fa-question'; // Default icon for unknown platforms
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="platform_income" class="form-label">Të ardhurat nga platforma</label>
                                        <input type="text" class="form-control rounded-5 border border-2" id="platform_income" name="platform_income" required>
                                    </div>

                                    <div class="col mb-3">
                                        <label for="platform_income_after_precentage" class="form-label">Të ardhurat nga platforma me %</label>
                                        <input type="text" class="form-control rounded-5 border border-2" id="platform_income_after_percentage" name="platform_income_after_percentage">
                                    </div>
                                </div>
                                <!-- Add another for date who get actual date -->
                                <div class="mb-3">
                                    <label for="date" class="form-label">Data</label>
                                    <input type="date" class="form-control rounded-5 border border-2" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <!-- Add another for description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Pershkrimi</label>
                                    <textarea class="form-control rounded-5 border border-2" id="description" name="description" rows="3"></textarea>
                                </div>
                                <button type="submit" class="input-custom-css px-3 py-2 float-end">Dergo</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Table for Displaying Investments -->
            <div class="card p-3">
                <table class='table table-bordered' id="platform_table">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Emri i klientit</th>
                            <th>Platforma</th>
                            <th>Të ardhurat</th>
                            <th>Të ardhurat pas përqindjes</th>
                            <th>Data</th>
                            <th>Përshkrim</th>
                            <th>Vepro</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<script>
    // Use jQuery to handle change event on the select element
    $(document).ready(function() {
        $("#id_of_client").change(function() {
            // Retrieve the selected client ID
            var selectedClientId = $(this).val();

            // Use AJAX to fetch additional data for the selected client
            $.ajax({
                type: "POST",
                url: "get_client_details.php", // Create a new PHP file for handling AJAX requests
                data: {
                    id_of_client: selectedClientId
                },
                success: function(response) {
                    console.log(response); // Log the response to the console
                    // Update the client details div with the fetched data
                    $("#client-details").html(response);

                    // After updating the client details, calculate the income after percentage
                    calculateIncomeAfterPercentage();
                }

            });
        });
    });

    // Calculate income after percentage when platform income is input
    $("#platform_income").on('input', function() {
        calculateIncomeAfterPercentage();
    });

    // Function to calculate income after percentage
    function calculateIncomeAfterPercentage() {
        var platformIncome = parseFloat($("#platform_income").val());
        var percentage = parseFloat($("#perqindja").val());

        if (!isNaN(platformIncome) && !isNaN(percentage)) {
            var incomeAfterPercentage = platformIncome - (platformIncome * percentage / 100);
            $("#platform_income_after_percentage").val(incomeAfterPercentage.toFixed(2));
        }
    }

    // Call the function when the percentage input changes
    $("#perqindja").on("input", calculateIncomeAfterPercentage);
</script>
<script>
    $(document).ready(function() {
        var table = $('#platform_table').DataTable({
            stripeClasses: ['stripe-color'],
            dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'><'col-md-6'p>>",
            buttons: [{
                    extend: "pdfHtml5",
                    text: '<i class="fi fi-rr-file-pdf fa-lg"></i>&nbsp;&nbsp; PDF',
                    titleAttr: "Eksporto tabelen ne formatin PDF",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",

                },
                {
                    extend: "copyHtml5",
                    text: '<i class="fi fi-rr-copy fa-lg"></i>&nbsp;&nbsp; Kopjo',
                    titleAttr: "Kopjo tabelen ne formatin Clipboard",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
                {
                    extend: "excelHtml5",
                    text: '<i class="fi fi-rr-file-excel fa-lg"></i>&nbsp;&nbsp; Excel',
                    titleAttr: "Eksporto tabelen ne formatin Excel",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                    exportOptions: {
                        modifier: {
                            search: "applied",
                            order: "applied",
                            page: "all",
                        },
                    },
                },
                {
                    extend: "print",
                    text: '<i class="fi fi-rr-print fa-lg"></i>&nbsp;&nbsp; Printo',
                    titleAttr: "Printo tabel&euml;n",
                    className: "btn btn-light btn-sm bg-light border me-2 rounded-5",
                },
            ],
            initComplete: function() {
                var btns = $(".dt-buttons");
                btns.addClass("").removeClass("dt-buttons btn-group");
                var lengthSelect = $("div.dataTables_length select");
                lengthSelect.addClass("form-select");
                lengthSelect.css({
                    width: "auto",
                    margin: "0 8px",
                    padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                    lineHeight: "1.5",
                    border: "1px solid #ced4da",
                    borderRadius: "0.25rem",
                });
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json",
            },
            serverSide: true,
            processing: true,
            "ajax": {
                "url": "fetch_platform_invoices.php", // Path to your PHP script
                "dataSrc": "data" // Specify the data source as "data"
            },
            "columns": [{
                    data: null,
                    defaultContent: '<input type="checkbox" class="deleteCheckbox">'
                },
                {
                    "data": "id"
                },
                {
                    "data": "klient_emri"
                },
                {
                    "data": "platform"
                },
                {
                    "data": "platform_income"
                },
                {
                    "data": "platform_income_after_percentage"
                },
                {
                    "data": "date"
                },
                {
                    "data": "description"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<form action="view_platformInvoice.php" method="post" target="_blank">' +
                            '<input type="hidden" name="id" value="' + row.id + '">' +
                            '<button type="submit" class="btn btn-primary rounded-5 px-2 py-2 text-white"><i class="fi fi-rr-print"></i></button>' +
                            '</form>';
                    }
                }
            ],
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "pageLength": 10,
            "order": [
                [1, 'asc']
            ], // Order by the second column (change as needed)
            "searching": true,
            "paging": true
        });

        $('#deleteRowsBtn').on('click', function() {
            // Get all checked checkboxes
            var checkboxes = $('.deleteCheckbox:checked');

            // Get the IDs of the selected rows
            var ids = checkboxes.map(function() {
                return table.row($(this).closest('tr')).data().id;
            }).get();

            // Perform deletion using AJAX
            $.ajax({
                url: 'delete_platformInvoice.php',
                method: 'POST',
                data: {
                    ids: ids
                },
                dataType: 'json',
                success: function(response) {
                    // Check if the deletion was successful
                    if (response.success) {
                        // Update DataTable
                        table.ajax.reload();

                        // Show success message with SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Rreshtat janë fshirë',
                            text: 'Rreshtat e përzgjedhura janë fshirë me sukses.',
                        });
                    } else {
                        // Show error message with SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                        });
                    }
                },
                error: function() {
                    // Show error message with SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gabim gjatë fshirjes së rreshtave. Ju lutemi provoni përsëri.',
                    });
                }
            });
        });
    });
</script>


<?php include 'partials/footer.php'; ?>