<?php
include 'conn-d.php';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $kategoria = mysqli_real_escape_string($conn, $_POST['kategoria']);
    $data_pageses = mysqli_real_escape_string($conn, $_POST['data_pageses']);
    $pershkrimi = mysqli_real_escape_string($conn, $_POST['pershkrimi']);
    $periudha = mysqli_real_escape_string($conn, $_POST['periudha']);
    $vlera = floatval($_POST['vlera']);
    $forma_pageses = mysqli_real_escape_string($conn, $_POST['forma_pageses']);
    $invoice_id = mysqli_real_escape_string($conn, $_POST['invoice_id']);

    // Handle file upload
    if (isset($_FILES['dokument']) && $_FILES['dokument']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $file_name = $_FILES['dokument']['name'];
        $file_tmp = $_FILES['dokument']['tmp_name'];
        $file_size = $_FILES['dokument']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            if ($file_size <= 5242880) { // 5MB
                $new_filename = uniqid() . '.' . $file_ext;
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $destination = $upload_dir . $new_filename;
                if (move_uploaded_file($file_tmp, $destination)) {
                    // Insert into database
                    $query = "INSERT INTO tatimi (kategoria, data_pageses, pershkrimi, periudha, vlera, forma_pageses, dokument, invoice_id) VALUES ('$kategoria', '$data_pageses', '$pershkrimi', '$periudha', '$vlera', '$forma_pageses', '$destination', '$invoice_id')";
                    if (mysqli_query($conn, $query)) {
                        $response['status'] = 'success';
                        $response['message'] = 'Transaksioni u shtua me sukses!';
                    } else {
                        $response['message'] = 'Gabim në shtimin e transaksionit: ' . mysqli_error($conn);
                        // Delete the uploaded file if DB insert fails
                        unlink($destination);
                    }
                } else {
                    $response['message'] = 'Gabim në ngarkimin e dokumentit.';
                }
            } else {
                $response['message'] = 'Madhësia e dokumentit nuk mund të kalojë 5MB.';
            }
        } else {
            $response['message'] = 'Formati i dokumentit nuk është i lejuar.';
        }
    } else {
        $response['message'] = 'Dokumenti është i detyrueshëm.';
    }
} else {
    $response['message'] = 'Metoda e kërkesës nuk është e lejuar.';
}

echo json_encode($response);
