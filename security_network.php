<?php
session_start();

// Include the database connection file
include 'conn-d.php';

// Function to check if IP is in range
function ip_in_range($ip, $range)
{
    if (strpos($range, '/') !== false) {
        list($range, $netmask) = explode('/', $range, 2);
        if (strpos($netmask, '.') !== false) {
            $netmask = str_replace('*', '0', $netmask);
            $netmask = str_replace('255', '1', str_replace('0', '0', $netmask));
            $netmask = str_pad($netmask, 32, '0', STR_PAD_RIGHT);
            $range = ip2long($range) & ip2long($netmask);
            $ip = ip2long($ip) & ip2long($netmask);
            return $ip == $range;
        } else {
            $range = ip2long($range);
            $mask = 0xffffffff << (32 - $netmask);
            $ip = ip2long($ip);
            return ($ip & $mask) == $range;
        }
    } elseif (strpos($range, '-') !== false) {
        list($start, $end) = explode('-', $range, 2);
        return (ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end));
    } else {
        return $ip == $range;
    }
}
// Get visitor's IP address
$visitor_ip = $_SERVER['REMOTE_ADDR'];

// Fetch allowed IP addresses from the database
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

if (!$ip_allowed) {
    die("Access denied: Your IP address ($visitor_ip) is not allowed to access this page.");
}

include 'partials/header.php';
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <nav class="bg-white px-2 rounded-5" class="bg-white px-2 rounded-5" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);width:fit-content;border-style:1px solid black;" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a class="text-reset" style="text-decoration: none;">Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <a href="invoice.php" class="text-reset" style="text-decoration: none;">
                            Roles
                        </a>
                    </li>
            </nav>
            <div class="container">
                <h1 class="mb-4">Allowed IPs Dashboard</h1>
                <!-- Add New Record Button -->
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add New Record</button>
                <table id="allowedIpsTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IP Address</th>
                            <th>Type</th>
                            <th>Actions</th>
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
                            <h5 class="modal-title">Add New Record</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="ip_address" class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="ip_address" name="ip_address" required>
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="single">Single</option>
                                        <option value="range">Range</option>
                                        <option value="cidr">CIDR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Record</button>
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
                            <h5 class="modal-title">Edit Record</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editForm">
                            <div class="modal-body">
                                <input type="hidden" id="edit_id" name="id">
                                <div class="mb-3">
                                    <label for="edit_ip_address" class="form-label">IP Address</label>
                                    <input type="text" class="form-control" id="edit_ip_address" name="ip_address" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_type" class="form-label">Type</label>
                                    <select class="form-select" id="edit_type" name="type" required>
                                        <option value="single">Single</option>
                                        <option value="range">Range</option>
                                        <option value="cidr">CIDR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            columns: [{
                    data: 'id'
                },
                {
                    data: 'ip_address'
                },
                {
                    data: 'type'
                },
                {
                    data: null,
                    className: "dt-center",
                    defaultContent: `<button class="btn btn-sm btn-primary">Edit</button> <button class="btn btn-sm btn-danger">Delete</button>`,
                    orderable: false
                }
            ]
        });

        // Handle form submission for adding new records
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
                    table.ajax.reload();
                }
            });
        });

        // Handle form submission for editing records
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
                    table.ajax.reload();
                }
            });
        });

        // Handle record deletion
        $('#allowedIpsTable').on('click', 'button.btn-danger', function() {
            var row = table.row($(this).parents('tr')).data();
            if (confirm('Are you sure you want to delete this record?')) {
                $.ajax({
                    url: 'data.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: row.id
                    },
                    success: function(response) {
                        table.ajax.reload();
                    }
                });
            }
        });

        // Populate edit modal with selected record data
        $('#allowedIpsTable').on('click', 'button.btn-primary', function() {
            var row = table.row($(this).parents('tr')).data();
            $('#edit_id').val(row.id);
            $('#edit_ip_address').val(row.ip_address);
            $('#edit_type').val(row.type);
            $('#editModal').modal('show');
        });
    });
</script>

<?php include('partials/footer.php'); ?>