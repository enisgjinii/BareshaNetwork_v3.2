<?php
// Include your database connection file
include 'conn-d.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the ID parameter is set and is a valid integer
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    // Sanitize the ID to prevent SQL injection
    $id = (int) $_POST['id'];

    // Prepare a SQL statement to delete the record with the given ID
    $sql = "DELETE FROM refresh_tokens WHERE id = $id";

    // Execute the SQL statement
    if ($conn->query($sql) === TRUE) {
        // Return a success message
        echo "Record deleted successfully.";

        // Send email notification
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kastriot@bareshamusic.com';
            $mail->Password = 'xpuurhlkncbzhdyg';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('kastriot@bareshamusic.com', 'Your Name');
            $mail->addAddress('kastriot@bareshamusic.com', 'Recipient Name');
            $mail->addAddress('egjini@bareshamusic.com', 'Recipient Name');

            $mail->Subject = 'Njoftim';
            $mail->isHTML(true);

            $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head>
            </head>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f9f9f9; padding: 20px; text-align: center;">
                <div style="padding: 20px; border: 1px solid #dddddd; border-radius: 5px; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                    <div style="margin-bottom: 20px;">
                        <img src="cid:logo" alt="Baresha Network Logo" style="max-width: 100px;">
                    </div>
                    <div style="font-size: 24px; font-weight: bold; margin-bottom: 20px; background-color: #FF0000; color: #ffffff; padding: 10px; border-radius: 5px;">
                        Lajmërim nga Baresha Network
                    </div>
                    <div style="font-size: 16px; margin-bottom: 20px;">
                        Përshëndetje, me anë te këtij emaili ju njoftojmë që është shkëputur bashkëpunimi me kompaninë Baresha Network. 
                    </div>
                    <div style="font-size: 14px; color: #666666; margin-top: 20px;">
                        Faleminderit, <br>
                        Baresha Network L.L.C
                    </div>
                    <div style="font-size: 12px; color: #999999; margin-top: 20px;">
                        <p>Për çdo ndihmë, na kontaktoni në:</p>
                        <p>Email: info@bareshanetmusic.com | Tel: +383 48 151 200</p>
                    </div>
                    <div style="margin-top: 20px;">
                        <a href="https://www.facebook.com/bareshamusic/" style="margin: 0 10px;">
                            <img src="cid:facebook" alt="Facebook" style="width: 24px;">
                        </a>
                        <a href="https://www.youtube.com/@BareshaNetwork" style="margin: 0 10px;">
                            <img src="cid:youtube" alt="YouTube" style="width: 24px;">
                        </a>
                        <a href="https://www.instagram.com/bareshamusic/" style="margin: 0 10px;">
                            <img src="cid:instagram" alt="Instagram" style="width: 24px;">
                        </a>
                    </div>
                </div>
            </body>
            </html>';

            $mail->AddEmbeddedImage('./images/logo_in_invoice.png', 'logo');
            $mail->AddEmbeddedImage('./images/facebook.jpg', 'facebook');
            $mail->AddEmbeddedImage('./images/youtube.png', 'youtube');
            $mail->AddEmbeddedImage('./images/instagram.png', 'instagram');
            $mail->send();
            echo 'Emaili është dërguar me sukses!';
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Return an error message if execution fails
        echo "Error: Unable to execute SQL statement. " . $conn->error;
    }
} else {
    // Return an error message if ID parameter is missing or invalid
    echo "Error: Invalid request.";
}

// Close the database connection
$conn->close();
