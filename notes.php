<?php
include 'partials/header.php';

// Kontrollo nese formulari eshte derguar me metoden POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // marre te dhenat e shenimit nga formulari
    $shenimi = $_POST["note"];
    // Encrypt the shenimi data
    $encrypted_shenimi = openssl_encrypt($shenimi, 'AES-256-CBC', 'encryption_key');

    // Get the current date and time
    $data = date("Y-m-d H:i:s");

    // Insert the encrypted shenimi data into the database
    $sql = "INSERT INTO shenime (shenimi, data) VALUES ('$encrypted_shenimi', '$data')";
    $result = mysqli_query($conn, $sql);


    if ($result) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        Ka ndodhur nje gabim gjate krijimit te shenimit!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}

?>




<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="card shadow-sm rounded-5 p-4">
                            <h4 class="card-title">Shenimi</h4>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                                <textarea id="note" name="note" required class="form-control shadow-sm rounded-5" rows="9"></textarea>
                                <br>
                                <input class="btn btn-light shadow-sm rounded-5 shadow-sm border" type="submit"
                                    value="Krijo shenim">
                            </form>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col">
                        <div class="card shadow-sm rounded-5 p-4">
                            <?php
                            $sql = "SELECT * FROM shenime";
                            $result = mysqli_query($conn, $sql);

                            // Display the notes
                            echo '<div class="row"><table class="table w-100 table-bordered">
                        <thead class="bg-light">
                        <tr>
                            <th>Shenimi</th>
                            <th>Data</th>
                            <th>Perditso</th>
                        </tr>
                        </thead>
                        <tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                $encrypted_shenimi = $row['shenimi'];
                                $decrypted_shenimi = openssl_decrypt($encrypted_shenimi, 'AES-256-CBC', 'encryption_key');
                                // Create a link to edit the note
                                $edit_link = '<a class="btn btn-primary border border-1 shadow-1 btn-sm" href="edit_note.php?id=' . $row['id'] . '"> <i class="fi fi-rr-edit"></i>  </a>';
                                $delete_link = '<a class="btn btn-danger border border-1 shadow-1 btn-sm" href="#" onclick="showConfirmDialog(' . $row['id'] . ')"> <i class="fi fi-rr-trash"></i></a>';


                                echo '<tr>
                                    <td>' . $decrypted_shenimi . '</td>
                                    <td>' . $row['data'] . '</td>
                                    <td>' . $edit_link . ' ' . $delete_link . '</td>

                                  </tr>';
                            }

                            echo '</tbody><tfoot class="bg-light">
                            <tr>
                            <th>Shenimi</th>
                            <th>Data</th>
                            <th>Perditso</th>
                            </tr>

                                    </tfoot>
      </table></div>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showConfirmDialog(id) {
            Swal.fire({
                title: 'A jeni i sigurt?',
                text: "Nuk do t&euml; mund ta kthesh k&euml;t&euml;!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Po, fshijeni!',
                cancelButtonText: 'Jo, anuloje',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Duke fshir&euml;...',
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'U fshi!',
                            text: 'Skedari juaj &euml;sht&euml; fshir&euml;.',
                            timer: 3000
                        });
                        window.location.href = 'delete_note.php?id=' + id;
                    }, 2000);
                }

            })
        }
    </script>












    <?php include('partials/footer.php') ?>