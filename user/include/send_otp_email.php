<?php
function sendOTPEmail($email, $subject, $message, $property = null) {
    require_once 'smtp/PHPMailerAutoload.php';

    // Create detailed email message with property information
    $email_message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Property Inquiry Notification</title>
    </head>
    <body style="margin:0;padding:20px;font-family:Arial,sans-serif;background:#f8f9fa;">
        <table style="width:100%;max-width:600px;margin:0 auto;background:#fff;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
            <tr>
                <td style="padding:20px;text-align:center;background:#2c3e50;border-radius:8px 8px 0 0;">
                    <h1 style="color:#ffffff;margin:0;">New Property Inquiry</h1>
                </td>
            </tr>
            <tr>
                <td style="padding:30px;">
                    <h2 style="color:#2c3e50;font-size:24px;margin:0 0 20px;">Property Details:</h2>';
    
    // Add property details if available
    if ($property) {
        $email_message .= '
        <table style="width:100%;margin-bottom:20px;border-collapse:collapse;">
            <tr>
                <td style="padding:10px;border-bottom:1px solid #eee;"><strong>Property Name:</strong></td>
                <td style="padding:10px;border-bottom:1px solid #eee;">' . htmlspecialchars($property['property_name']) . '</td>
            </tr>
            <tr>
                <td style="padding:10px;border-bottom:1px solid #eee;"><strong>Property ID:</strong></td>
                <td style="padding:10px;border-bottom:1px solid #eee;">' . htmlspecialchars($property['p_id']) . '</td>
            </tr>
            <tr>
                <td style="padding:10px;border-bottom:1px solid #eee;"><strong>Price:</strong></td>
                <td style="padding:10px;border-bottom:1px solid #eee;">INR ' . htmlspecialchars($property['property_price']) . '</td>
            </tr>
            <tr>
                <td style="padding:10px;border-bottom:1px solid #eee;"><strong>Location:</strong></td>
                <td style="padding:10px;border-bottom:1px solid #eee;">' . htmlspecialchars($property['property_address']) . '</td>
            </tr>
        </table>';
    }

    $email_message .= '
                    <h3 style="color:#2c3e50;margin:20px 0;">Inquiry Message:</h3>
                    <div style="background:#f8f9fa;padding:15px;border-radius:4px;margin-bottom:20px;">
                        <p style="margin:0;color:#34495e;">' . nl2br(htmlspecialchars($message)) . '</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding:20px;text-align:center;background:#ecf0f1;border-radius:0 0 8px 8px;">
                    <p style="font-size:14px;color:#7f8c8d;margin:0;">© ' . date('Y') . ' Real Estate Property Portal</p>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    // PHPMailer configuration
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;
    $mail->Timeout = 10;
    $mail->SMTPKeepAlive = false;

    $mail->Username = 'dixvaghela11@gmail.com';
    $mail->Password = 'dllpvpblkdkyeqeo';
    $mail->setFrom('dixvaghela11@gmail.com', 'RealEstate');
    $mail->Subject = $subject;
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