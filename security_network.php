<?php
session_start();
include 'conn-d.php';
// Get user email and visitor IP
$user_email = strtolower($_COOKIE['email'] ?? '');
$visitor_ip = $_SERVER['REMOTE_ADDR'];
$filename = basename($_SERVER['PHP_SELF']);
$time = date("Y-m-d H:i:s");
$location = "Unknown";
// Attempt to get location using IP (skip if localhost)
if ($visitor_ip !== '127.0.0.1' && $visitor_ip !== '::1') {
    $geo_info = @file_get_contents("http://ip-api.com/json/$visitor_ip");
    if ($geo_info === FALSE) {
        $location = "Location lookup failed";
    } else {
        $geo_data = json_decode($geo_info, true);
        if ($geo_data['status'] === 'success') {
            $location = "{$geo_data['city']}, {$geo_data['regionName']}, {$geo_data['country']}";
        } else {
            $location = "Unable to determine location";
        }
    }
} else {
    $location = "Localhost";
}
// Prepare log data and file
$log_file = 'access_log.csv';
$log_entry = [$time, $filename, $user_email, $visitor_ip, $location];
$header = ['Time', 'Filename', 'User Email', 'IP Address', 'Location'];
// Check if file exists, if not, add header
if (!file_exists($log_file)) {
    file_put_contents($log_file, implode(',', $header) . PHP_EOL);
}
// Log data to CSV
file_put_contents($log_file, implode(',', $log_entry) . PHP_EOL, FILE_APPEND);
// IP range check function
function ip_in_range($ip, $range)
{
    if (strpos($range, '/') !== false) {
        list($range, $netmask) = explode('/', $range, 2);
        $mask = str_pad(str_repeat('1', $netmask) . str_repeat('0', 32 - $netmask), 32, '0', STR_PAD_RIGHT);
        $range = ip2long($range) & bindec($mask);
        $ip = ip2long($ip) & bindec($mask);
        return $ip == $range;
    } elseif (strpos($range, '-') !== false) {
        list($start, $end) = explode('-', $range, 2);
        return (ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end));
    } else {
        return $ip == ip2long($range);
    }
}
// Fetch allowed IPs and check access
$query = "SELECT * FROM allowed_ips";
$result = $conn->query($query);
$ip_allowed = false;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (ip_in_range($visitor_ip, $row['ip_address'])) {
            $ip_allowed = true;
            break;
        }
    }
}
// Access control
if (strtolower($user_email) === 'egjini17@gmail.com' || $ip_allowed) {
    include 'partials/header.php';
?>
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="container-fluid">
                <nav class="bg-white px-2 rounded-5" style="width:fit-content;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-reset" style="text-decoration: none;">Menaxhimi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="<?php echo __FILE__; ?>" class="text-reset" style="text-decoration: none;">Siguria permes rrjetit</a>
                        </li>
                    </ol>
                </nav>
                <div class="row mb-2">
                    <div>
                        <button class="input-custom-css px-3 py-2 mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Shto një rekord të ri</button>
                    </div>
                </div>
                <div class="p-3 shadow-sm rounded-5 mb-4 card text-dark">
                    <table id="allowedIpsTable" class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="text-dark">ID-ja</th>
                                <th class="text-dark">IP Adresa</th>
                                <th class="text-dark">Tipi</th>
                                <th class="text-dark">Veprimet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
                <!-- Add Modal -->
                <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Shto një rekord të ri</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="addForm">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="ip_address" class="form-label">IP Adresa</label>
                                        <input type="text" class="form-control" id="ip_address" name="ip_address" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Tipi</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="single">Single</option>
                                            <option value="range">Diapazon</option>
                                            <option value="cidr">CIDR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                                    <button type="submit" class="input-custom-css px-3 py-2">Shto rekord</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Redakto regjistrimin</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="editForm">
                                <div class="modal-body">
                                    <input type="hidden" id="edit_id" name="id">
                                    <div class="mb-3">
                                        <label for="edit_ip_address" class="form-label">IP Adresa</label>
                                        <input type="text" class="form-control" id="edit_ip_address" name="ip_address" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_type" class="form-label">Tipi</label>
                                        <select class="form-select" id="edit_type" name="type" required>
                                            <option value="single">Single</option>
                                            <option value="range">Diapazon</option>
                                            <option value="cidr">CIDR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="input-custom-css px-3 py-2" data-bs-dismiss="modal">Mbylle</button>
                                    <button type="submit" class="input-custom-css px-3 py-2">Ruaj ndryshimet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include('partials/footer.php');
    ?>
    <script>
        $(document).ready(function() {
            var table = $('#allowedIpsTable').DataTable({
                ajax: {
                    url: 'data.php',
                    type: 'POST',
                    data: {
                        action: 'fetch'
                    },
                    dataSrc: 'data'
                },
                initComplete: function() {
                    var btns = $(".dt-buttons");
                    btns.addClass("").removeClass("dt-buttons btn-group");
                    var lengthSelect = $("div.dataTables_length select");
                    lengthSelect.addClass("form-select").css({
                        width: "auto",
                        margin: "0 8px",
                        padding: "0.375rem 1.75rem 0.375rem 0.75rem",
                        lineHeight: "1.5",
                        border: "1px solid #ced4da",
                        borderRadius: "0.25rem"
                    });
                },
                fixedHeader: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/sq.json"
                },
                stripeClasses: ['stripe-color'],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'ip_address'
                    },
                    {
                        data: 'type',
                        render: function(data) {
                            if (data === 'single') {
                                return 'Single';
                            } else if (data === 'range') {
                                return 'Diapazon';
                            } else if (data === 'cidr') {
                                return 'CIDR';
                            }
                        }
                    },
                    {
                        data: null,
                        className: "dt-center",
                        defaultContent: `
                        <button class="input-custom-css px-3 py-2 edit-btn">Ndrysho</button>
                        <button class="input-custom-css px-3 py-2 delete-btn">Fshije</button>
                    `,
                        orderable: false
                    }
                ]
            });
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'data.php',
                    type: 'POST',
                    data: {
                        action: 'add',
                        ip_address: $('#ip_address').val(),
                        type: $('#type').val()
                    },
                    success: function(response) {
                        $('#addModal').modal('hide');
                        Swal.fire('Success', 'Regjistrimi i ri u shtua me sukses!', 'success');
                        table.ajax.reload();
                    }
                });
            });
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'data.php',
                    type: 'POST',
                    data: {
                        action: 'edit',
                        id: $('#edit_id').val(),
                        ip_address: $('#edit_ip_address').val(),
                        type: $('#edit_type').val()
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');
                        Swal.fire('Success', 'Regjistrimi u përditësua me sukses!', 'success');
                        table.ajax.reload();
                    }
                });
            });
            $('#allowedIpsTable').on('click', 'button.delete-btn', function() {
                var row = table.row($(this).parents('tr')).data();
                Swal.fire({
                    title: 'A je i sigurt?',
                    text: 'Nuk do të mund ta rikuperoni këtë rekord!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Po, fshije atë!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'data.php',
                            type: 'POST',
                            data: {
                                action: 'delete',
                                id: row.id
                            },
                            success: function(response) {
                                Swal.fire('Fshirë!', 'Regjistri është fshirë.', 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });
            $('#allowedIpsTable').on('click', 'button.edit-btn', function() {
                var row = table.row($(this).parents('tr')).data();
                $('#edit_id').val(row.id);
                $('#edit_ip_address').val(row.ip_address);
                $('#edit_type').val(row.type);
                $('#editModal').modal('show');
            });
        });
    </script>
<?php
} else {
    die("Access denied: Your IP address ($visitor_ip) is not allowed to access this page.");
}
?>