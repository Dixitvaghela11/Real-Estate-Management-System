<?php
require 'smtp/PHPMailerAutoload.php';

function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->Username = "dixvaghela11@gmail.com";
    $mail->Password = "dllpvpblkdkyeqeo";
    $mail->SetFrom("dixvaghela11@gmail.com");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));
    if (!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
        return 'Sent';
    }
}

$to = "recipient@example.com";
$subject = "Test Email";
$message = "This is a test email.";

$result = smtp_mailer($to, $subject, $message);

if ($result === 'Sent') {
    echo "Email sent successfully.";
} else {
    echo "Email could not be sent. Error: $result";
}
?>