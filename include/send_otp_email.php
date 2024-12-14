<?php
function sendOTPEmail($email, $otp) {
    require_once 'smtp/PHPMailerAutoload.php'; // Changed to require_once

    // Simplified email template - removed unnecessary styles and animations
    $email_message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Your Real Estate Verification Code</title>
    </head>
    <body style="margin:0;padding:20px;font-family:Arial,sans-serif;background:#f8f9fa;">
        <table style="width:100%;max-width:600px;margin:0 auto;background:#fff;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <tr>
                <td style="padding:20px;text-align:center;background:#2c3e50;border-radius:8px 8px 0 0;">
                    <img src="https://i.postimg.cc/3NQ7bChT/Screenshot-2024-10-18-224817.png" alt="Logo" style="max-width:150px;">
                </td>
            </tr>
            <tr>
                <td style="padding:30px;">
                    <h1 style="color:#2c3e50;font-size:24px;margin:0 0 20px;text-align:center;">Verify Your Account</h1>
                    <p style="color:#34495e;">Your verification code:</p>
                    <div style="text-align:center;margin:20px 0;padding:15px;background:#ecf0f1;border-radius:4px;">
                        <p style="font-size:32px;font-weight:bold;color:#2c3e50;margin:0;">' . $otp . '</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding:20px;text-align:center;background:#ecf0f1;border-radius:0 0 8px 8px;">
                    <p style="font-size:14px;color:#7f8c8d;margin:0;">© 2023 Your Real Estate Company</p>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    // Optimized PHPMailer configuration
    $mail = new PHPMailer(true); // Enable exceptions
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    
    // Disable debug output
    $mail->SMTPDebug = 0;
    
    // Set timeout values
    $mail->Timeout = 10;
    $mail->SMTPKeepAlive = false;

    $mail->Username = 'dixvaghela11@gmail.com';
    $mail->Password = 'dllpvpblkdkyeqeo';
    $mail->setFrom('dixvaghela11@gmail.com', 'RealEstate');
    $mail->Subject = 'OTP Verification';
    $mail->msgHTML($email_message);
    $mail->addAddress($email);

    try {
        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail sending failed: " . $e->getMessage());
        return false;
    }
}
?>
