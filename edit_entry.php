<?php
ob_start();
// Check if the ID parameter is set
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
// Connect to the database
include 'partials/header.php';
require_once "conn-d.php";
// Get the entry data based on the ID parameter
$id = mysqli_real_escape_string($conn, $_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM filet WHERE id='$id'");
if (mysqli_num_rows($result) == 0) {
    header("Location: filet.php");
    exit();
}
$row = mysqli_fetch_assoc($result);
// Handle form submission
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $maxFileSize = 2 * 1024 * 1024; // 2MB file size limit
    if (isset($_FILES['newfile']) && $_FILES['newfile']['error'] == 0) {
        $fileType = $_FILES['newfile']['type'];
        $fileSize = $_FILES['newfile']['size'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'text/plain'];
        if ($fileSize > $maxFileSize) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'File size must be less than 2MB.',
                });
            </script>";
        } elseif (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'dokument/';
            $newFileName = basename($_FILES['newfile']['name']);
            $uploadFilePath = $uploadDir . $newFileName;
            if (move_uploaded_file($_FILES['newfile']['tmp_name'], $uploadFilePath)) {
                $sql = "UPDATE filet SET pershkrimi='$name', file='$uploadFilePath' WHERE id='$id'";
                mysqli_query($conn, $sql);
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Dokumenti u përditësua me sukses',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location = 'filet.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gabim në ngarkimin e dokumentit',
                        text: 'Ju lutem provoni përsëri.',
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Formati i gabuar i dokumentit',
                    text: 'Ju lutem ngarkoni vetëm dokumente PDF, imazhe ose skedarë teksti.',
                });
            </script>";
        }
    } else {
        $sql = "UPDATE filet SET pershkrimi='$name' WHERE id='$id'";
        mysqli_query($conn, $sql);
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Të dhënat u përditësuan me sukses',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location = 'filet.php';
            });
        </script>";
    }
}
ob_flush();
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="p-5 rounded-5 bg-white mb-4 card">
                    <h4 class="card-title mb-4">Përditso Dokumentin</h4>
                    <form method="post" enctype="multipart/form-data" id="updateForm">
                        <div class="form-group mb-3">
                            <label for="id" class="form-label"><i class="fas fa-id-badge"></i> ID:</label>
                            <input type="text" id="id" name="id" value="<?php echo $row['id']; ?>" class="form-control rounded-5 shadow-sm border mt-2" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label"><i class="fas fa-align-left"></i> Përshkrimi:</label>
                            <input type="text" id="description" name="name" value="<?php echo $row['pershkrimi']; ?>" class="form-control rounded-5 shadow-sm border mt-2" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="file" class="form-label"><i class="fas fa-file"></i> Dokumenti aktual:</label>
                            <p>
                                <button type="button" class="input-custom-css px-3 py-2" onclick="previewFile('<?php echo $row['file']; ?>')">Shiko</button>
                                <a href="<?php echo $row['file']; ?>" style="text-decoration: none;" class="input-custom-css px-3 py-2" download>Shkarko</a>
                            </p>
                        </div>
                        <div class="form-group mb-3">
                            <label for="newfile" class="form-label"><i class="fas fa-upload"></i> Ngarko dokument të ri:</label>
                            <input type="file" id="newfile" name="newfile" class="form-control rounded-5 shadow-sm border mt-2" accept=".pdf,.jpeg,.jpg,.png,.txt">
                            <small id="fileHelp" class="form-text text-muted">Lejohen vetëm dokumente PDF, imazhe dhe skedarë teksti. Madhësia maksimale 2MB.</small>
                            <div id="filePreview" class="mt-3" style="display:none;">
                                <label for="filePreview" class="form-label"><i class="fas fa-eye"></i> Pamje paraprake:</label>
                                <div id="previewContainer"></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <input type="submit" name="update" value="Përditso" class="input-custom-css px-3 py-2" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Preview file button action
    function previewFile(filePath) {
        const fileType = filePath.split('.').pop().toLowerCase();
        let previewContent;
        if (fileType === 'pdf') {
            previewContent = `<embed src="${filePath}" type="application/pdf" width="100%" height="400px" />`;
        } else if (['jpeg', 'jpg', 'png'].includes(fileType)) {
            previewContent = `<img src="${filePath}" alt="Image Preview" style="max-width:100%; height:auto;" />`;
        } else if (fileType === 'txt') {
            fetch(filePath)
                .then(response => response.text())
                .then(text => {
                    previewContent = `<pre>${text}</pre>`;
                    Swal.fire({
                        title: 'Pamje paraprake e dokumentit',
                        html: previewContent,
                        width: '600px',
                        showConfirmButton: true,
                    });
                });
            return;
        }
        Swal.fire({
            title: 'Pamje paraprake e dokumentit',
            html: previewContent,
            width: '600px',
            showConfirmButton: true,
        });
    }
    // File size validation and preview
    document.getElementById('newfile').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('filePreview');
        const previewDiv = document.getElementById('previewContainer');
        previewDiv.innerHTML = ''; // Clear previous content
        if (file) {
            const fileType = file.type;
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Skedari shumë i madh',
                    text: 'Madhësia e skedarit duhet të jetë më pak se 2 MB.',
                });
                event.target.value = ''; // Clear the file input
                previewContainer.style.display = 'none';
                return;
            }
            if (fileType === 'application/pdf') {
                const fileURL = URL.createObjectURL(file);
                previewDiv.innerHTML = `<embed src="${fileURL}" type="application/pdf" width="100%" height="400px" />`;
                previewContainer.style.display = 'block';
            } else if (fileType.startsWith('image/')) {
                const fileURL = URL.createObjectURL(file);
                previewDiv.innerHTML = `<img src="${fileURL}" alt="Image Preview" style="max-width:100%; height:auto;" />`;
                previewContainer.style.display = 'block';
            } else if (fileType === 'text/plain') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `<pre>${e.target.result}</pre>`;
                };
                reader.readAsText(file);
                previewContainer.style.display = 'block';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Formati i gabuar i dokumentit',
                    text: 'Ju lutem ngarkoni vetëm dokumente PDF, imazhe ose skedarë teksti.',
                });
                event.target.value = ''; // Clear the file input
                previewContainer.style.display = 'none';
            }
        } else {
            previewContainer.style.display = 'none';
        }
    });
</script>
<?php include 'partials/footer.php'; ?>