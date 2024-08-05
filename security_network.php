<?php
session_start();

// Include the database connection file
include 'conn-d.php';


// Function to get threat color
function get_threat_color($threat_level) {
    switch (strtolower($threat_level)) {
        case 'low':
            return 'success';
        case 'medium':
            return 'warning';
        case 'high':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $ip = $conn->real_escape_string($_POST['ip_address']);
                $status = $conn->real_escape_string($_POST['status']);
                $threat = $conn->real_escape_string($_POST['threat_level']);
                $query = "INSERT INTO network_security (ip_address, status, last_check, threat_level) VALUES ('$ip', '$status', NOW(), '$threat')";
                $conn->query($query);
                break;
            case 'edit':
                $id = $conn->real_escape_string($_POST['id']);
                $ip = $conn->real_escape_string($_POST['ip_address']);
                $status = $conn->real_escape_string($_POST['status']);
                $threat = $conn->real_escape_string($_POST['threat_level']);
                $query = "UPDATE network_security SET ip_address='$ip', status='$status', last_check=NOW(), threat_level='$threat' WHERE id=$id";
                $conn->query($query);
                break;
            case 'delete':
                $id = $conn->real_escape_string($_POST['id']);
                $query = "DELETE FROM network_security WHERE id=$id";
                $conn->query($query);
                break;
        }
    }
}

// Fetch network security data from database
$query = "SELECT * FROM network_security";
$result = $conn->query($query);

$security_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $security_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Security Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>
        body { padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Network Security Dashboard</h1>
        
        <!-- Add New Record Button -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Add New Record</button>
        
        <table id="securityTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IP Address</th>
                    <th>Status</th>
                    <th>Last Check</th>
                    <th>Threat Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($security_data as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['id']); ?></td>
                    <td><?php echo htmlspecialchars($data['ip_address']); ?></td>
                    <td><?php echo htmlspecialchars($data['status']); ?></td>
                    <td><?php echo htmlspecialchars($data['last_check']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo get_threat_color($data['threat_level']); ?>">
                            <?php echo htmlspecialchars($data['threat_level']); ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editRecord(<?php echo htmlspecialchars(json_encode($data)); ?>)">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteRecord(<?php echo $data['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
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
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control" id="ip_address" name="ip_address" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Blocked">Blocked</option>
                                <option value="Suspicious">Suspicious</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="threat_level" class="form-label">Threat Level</label>
                            <select class="form-select" id="threat_level" name="threat_level" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
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
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control" id="edit_ip_address" name="ip_address" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Blocked">Blocked</option>
                                <option value="Suspicious">Suspicious</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_threat_level" class="form-label">Threat Level</label>
                            <select class="form-select" id="edit_threat_level" name="threat_level" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_id" name="id">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#securityTable').DataTable();
        });

        function editRecord(data) {
            $('#edit_id').val(data.id);
            $('#edit_ip_address').val(data.ip_address);
            $('#edit_status').val(data.status);
            $('#edit_threat_level').val(data.threat_level);
            $('#editModal').modal('show');
        }

        function deleteRecord(id) {
            $('#delete_id').val(id);
            $('#deleteModal').modal('show');
        }
    </script>
</body>
</html>