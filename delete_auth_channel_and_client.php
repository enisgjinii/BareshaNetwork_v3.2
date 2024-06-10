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
    // Start a transaction
    $conn->begin_transaction();
    try {
        // Retrieve the channel_id from the refresh_tokens table
        $result = $conn->query("SELECT channel_id FROM refresh_tokens WHERE id = $id");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $channel_id = $row['channel_id'];
            // Delete the record from the refresh_tokens table
            if ($conn->query("DELETE FROM refresh_tokens WHERE id = $id") === TRUE) {
                // Delete the record from the klientet table where youtube column matches the channel_id
                if ($conn->query("DELETE FROM klientet WHERE youtube = '$channel_id'") === TRUE) {
                    // Commit the transaction
                    $conn->commit();
                    echo "Records deleted successfully.";
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
                    throw new Exception("Error deleting record from klientet table: " . $conn->error);
                }
            } else {
                throw new Exception("Error deleting record from refresh_tokens table: " . $conn->error);
            }
        } else {
            throw new Exception("Error retrieving channel_id from refresh_tokens table: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo $e->getMessage();
    }
} else {
    // Return an error message if ID parameter is missing or invalid
    echo "Error: Invalid request.";
}
// Close the database connection
$conn->close();
