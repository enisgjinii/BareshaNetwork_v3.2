<?php

// Lidhja me bazën e të dhënave
require 'conn-d.php';

// Sigurohemi që kërkesa është dërguar përmes metodes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kontrollojmë nëse tokeni i rifreskimit është dhënë në të dhënat POST
    if (isset($_POST['token']) && !empty($_POST['token'])) {
        // Merr tokenin e rifreskimit nga të dhënat POST
        $refreshToken = $_POST['token'];

        // Përgatisim dhe ekzekutojmë deklaratën SQL për të fshirë tokenin nga baza e të dhënave
        $sql = "DELETE FROM refresh_tokens WHERE token = ?";
        $stmt = $conn->prepare($sql);

        // Parametrat janë bindur me tipin e duhur për të parandaluar sulmet SQL injection
        $stmt->bind_param("s", $refreshToken);
        $stmt->execute();

        // Mbyllim deklaratën
        $stmt->close();

        // Kthehemi në faqen origjinale ose në cilindo vend tjetër të dëshiruar
        header('Location: invoice.php');
        exit();
    } else {
        // Nëse tokeni i rifreskimit nuk është dhënë në të dhënat POST, shfaqim një mesazh gabimi
        echo 'Gabim: Tokeni i rifreskimit nuk është dhënë.';
    }
} else {
    // Nëse forma nuk është dërguar përmes metodes POST, shfaqim një mesazh gabimi
    echo 'Gabim: Kërkesë e pavlefshme.';
}

// Mbyllja e lidhjes me bazën e të dhënave
$conn->close();
