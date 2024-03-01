<?php
require './vendor/autoload.php'; // Sigurohuni që kjo të tregojë tek skedari autoload.php nga Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'conn-d.php';
// Parandaloni qasjen drejtpërdrejtë
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Merrni komandat SQL
    $sqlCommands = urldecode($_POST['sqlCommands']);
    // Ndajeni komandat me pikëpresje dhe reja për t'i ekzekutuar ato një nga një
    $commands = explode(";\n", $sqlCommands);
    $commandsArray = array(); // Array për të ruajtur komandat SQL
    foreach ($commands as $command) {
        if (trim($command)) {
            if ($conn->query($command) === TRUE) {
                // Shtoni komandën SQL në array
                $commandsArray[] = $command;
                // Ekstraktoni vlerat nga komanda SQL
                preg_match("/VALUES\s*\((.*?)\)/", $command, $matches);
                $values = explode(",", $matches[1]);
                // Hiqni thonjëzat e vetëm nga vlerat
                $values = array_map(function ($value) {
                    return trim($value, " \t\n\r\0\x0B'"); // Hiqni hapësirat, rreshtat e reja dhe thonjëzat e vetëm
                }, $values);
                // Query to get 'emri' from 'klientet' table where 'id' matches $values[1]
                $id = $values[1];
                $query = "SELECT emri FROM klientet WHERE id = '$id'";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $emri = $row["emri"];
                        echo "Emri: " . $emri . "<br>";
                    }
                } else {
                    echo "Nuk u gjet asnjë rezultat";
                }
                echo "<div class='alert alert-success' role='alert'>Regjistër i ri u krijua me sukses</div>";
                // Formatoni vlerat si HTML
                $htmlBody = "<html>
                <head>
                </head>
                <body style='font-family: Poppins; background-color: #f4f4f4;'>
                <div style='background-color: #fff; padding: 30px; margin: 50px auto; max-width: 800px; border-style: 1px solid #ddd; border-radius: 5px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);'>
                    <h1 style='font-size: 32px; color: #333; margin-bottom: 20px;'>Fatura #" . trim($values[0], "'") . "</h1>
                    <img src='cid:favicon' alt='Logo' style='display: block; margin: 0 auto; max-width: 125px;'>
                    <br>
                    <div style='border: 2px solid #ddd; padding: 30px; margin-bottom: 30px;'>
                        <p style='margin: 10px 0;'><strong>Përshkrimi:</strong> " . trim($values[2], "'") . "</p>
                        <p style='margin: 10px 0;'><strong>Numri i Faturës:</strong> #" . trim($values[0], "'") . "</p>
                        <p style='margin: 10px 0;'><strong>Data e Krijimit të Faturës:</strong> " . trim($values[5], "'") . "</p>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>Numri i faturës : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . trim($values[0], "'") . "</div>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>ID e klientit : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . trim($values[1], "'") . "</div>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>Emri i Klientit : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . $emri . "</div>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>Data : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . trim($values[2], "'") . "</div>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>Fitimi : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . number_format(round(trim($values[3], "'"), 2), 2) . " €</div>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>Fitimi pas përqindjes : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . number_format(round(trim($values[4], "'"), 2), 2) . " €</div>
                    </div>
                    <div style='border-bottom: 1px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;'>
                        <div style='flex: 1;'>Data e Krijimit : &nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'>" . trim($values[5], "'") . "</div>
                    </div>
                    <div style='border-top: 2px solid #ddd; padding: 20px 0; display: flex; justify-content: space-between;  margin-top: 40px;'>
                        <div style='flex: 1;'>Shuma Totale :&nbsp;&nbsp;</div>
                        <div style='font-weight: bold;'> " . number_format(round(trim($values[3], "'"), 2), 2) . " €</div>
                    </div>
                    <div style='border-top: 2px solid #ddd; margin-top: 40px; padding: 20px 0; text-align: center; color: #999;'>Faleminderit!</div>
                </div>
                </body>
                </html>";
                // Kode për dërgimin e emailit
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'egjini17@gmail.com';
                    $mail->Password = 'rhydniijtqzijjdy';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->setFrom('egjini17@gmail.com', 'Dërguesi');
                    $mail->addAddress('egjini17@gmail.com', 'Emri i Marresit');
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = '=?utf-8?B?' . base64_encode('Krijimi i faturës me numër: ' . trim($values[0], "'")) . '?=';
                    $mail->Body = $htmlBody;
                    $mail->AddEmbeddedImage('./images/favicon.png', 'favicon');
                    $mail->send();
                } catch (Exception $e) {
                    echo "<div class='alert alert-danger' role='alert'>Mesazhi nuk mund të dërgohet. Gabimi i Mailer: {$mail->ErrorInfo}</div>";
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'>Gabim: {$conn->error}</div>";
            }
        }
    }
    // Ruajeni komandat SQL në një skedar JSON
    file_put_contents('commands.json', json_encode($commandsArray, JSON_PRETTY_PRINT));
} else {
    echo "<div class='alert alert-warning' role='alert'>Qasja drejtpërdrejtë në skript nuk lejohet.</div>";
}
