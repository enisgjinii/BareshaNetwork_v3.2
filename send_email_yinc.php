<?php
require './vendor/autoload.php'; // Make sure this points to the autoload.php file from Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'egjini@bareshamusic.com';
    $mail->Password = 'pazvpeihqiekpkiv';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Set sender and recipient
    $mail->setFrom('egjini@bareshamusic.com', 'Enis Gjini');
    $mail->addAddress($_POST['email']); // Assuming the email address is sent via POST

    // Email content setup
    $mail->isHTML(true);
    $mail->Subject = 'Kërkesë e Re';

    // Form data
    $formContent = '<form>
                        <div class="mb-3">
                            <label for="emailadd" class="form-label">Email:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="emailadd" value="' . $_POST['email'] . '" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="emri" class="form-label">Emri:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="emri" value="' . $gstafi['emri'] . '" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="shuma" class="form-label">Shuma e borgjit:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="shuma" value="' . $k['shuma'] . '&euro;" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="pagoi" class="form-label">Shuma e paguar e borgjit:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="pagoi" value="' . $k['pagoi'] . '&euro;" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="obligimit" class="form-label">Shuma e obligimit:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="obligimit" value="' . ($k['shuma'] - $k['pagoi']) . '&euro;" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="lloji" class="form-label">LLoji:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="lloji" value="' . $k['lloji'] . '" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="pershkrimi" class="form-label">Përshkrimi:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="pershkrimi" value="' . $k['pershkrimi'] . '" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="data" class="form-label">Data e krijimit të borgjit:</label>
                            <input type="text" class="form-control rounded-5 border border-2" id="data" value="' . $k['data'] . '" readonly>
                        </div>
                    </form>';

    // Include form content in email body
    $mail->Body = $formContent;

    // Send the email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
