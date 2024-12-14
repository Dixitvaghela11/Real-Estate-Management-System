<?php
include('smtp/PHPMailerAutoload.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validate inputs
    if (empty($firstname) || empty($lastname) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Prepare the email content
    $emailSubject = "Contact Form Submission: $subject";
    $emailBody = "
    <style>
        .wrapper { background-color: #f5f5f5; padding: 20px; }
        .center { margin: 0 auto; }
        .top-panel { background-color: white; border-radius: 8px 8px 0 0; padding: 20px; }
        .title { font-size: 24px; color: #333; font-weight: bold; }
        .subject a { color: #0066cc; text-decoration: none; }
        .border { border-bottom: 2px solid #eee; }
        .spacer { height: 20px; }
        .main { background-color: white; }
        .column { padding: 0 20px; }
        .column-top { height: 20px; }
        .column-bottom { height: 20px; }
        .padded { padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        table.content { width: 100%; border-collapse: collapse; }
        table.content td { padding: 8px; border-bottom: 1px solid #eee; }
        .footer { background-color: white; border-radius: 0 0 8px 8px; padding: 20px; }
        .signature { color: #666; }
        .signature a { color: #0066cc; text-decoration: none; }
        .subscription { text-align: right; color: #666; }
        .logo-image {
            margin: 15px 0;
            text-align: right;
            display: block;
        }
        .logo-image img {
            max-width: 150px;
            height: auto;
            display: inline-block;
            vertical-align: middle;
            border: none;
            outline: none;
        }
        .subscription {
            text-align: right;
            color: #666;
            padding-right: 20px;
        }
        .strong { font-weight: bold; }
    </style>
    <center class='wrapper'>
        <table class='top-panel center' width='602' border='0' cellspacing='0' cellpadding='0'>
            <tbody>
            <tr>
                <td class='title' width='300'>Real Estate</td>
                <td class='subject' width='300'><a class='strong' href='#' target='_blank'>www.realetate.com</a></td>
            </tr>
            <tr>
                <td class='border' colspan='2'>&nbsp;</td>
            </tr>
            </tbody>
        </table>

        <div class='spacer'>&nbsp;</div>

        <table class='main center' width='602' border='0' cellspacing='0' cellpadding='0'>
            <tbody>
            <tr>
                <td class='column'>
                    <div class='column-top'>&nbsp;</div>
                    <table class='content' border='0' cellspacing='0' cellpadding='0'>
                        <tbody>
                        <tr>
                            <td class='padded'>
                              <h1>Contact US</h1>
                              <p>Content:</p>
                              <table style='width:100%'>
                              <tr>
                                <td><strong>Name</strong></td>
                                <td>$firstname $lastname</td>
                              </tr>
                              <tr>
                                <td><strong>Email</strong></td>
                                <td>$email</td>
                              </tr>
                              <tr>
                                <td><strong>Message</strong></td>
                                <td>$message</td>
                              </tr>
                            </table><br>
                            <p>Thank you for contacting us. We will get back to you as soon as possible.</p>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class='column-bottom'>&nbsp;</div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class='spacer'>&nbsp;</div>

        <table class='footer center' width='602' border='0' cellspacing='0' cellpadding='0'>
            <tbody>
            <tr>
                <td class='border' colspan='2'>&nbsp;</td>
            </tr>
            <tr>
                <td class='signature' width='300'>
                    <p>
                        With best regards,<br>
                        Real Estate<br>
                        +91 97 24 901 801, Dix Vaghela<br>
                        </p>
                    <p>
                        Support: <a class='strong' href='mailto:#' target='_blank'>dixvaghela11@gmail.com</a>
                    </p>
                </td>
                <td class='subscription' width='300'>
                    <div class='logo-image'>
                        <img src='https://i.postimg.cc/3NQ7bChT/Screenshot-2024-10-18-224817.png' 
                             alt='Real Estate Logo' 
                             style='max-width: 150px; height: auto; display: inline-block; border: none;'
                        />
                    </div>
                    <p>
                        <a class='strong' href='#' target='_blank'>www.realetate.com</a>
                    </p>
                    <p>
                        Copyright © 2024. All rights reserved.
                    </p>

                </td>
            </tr>
            </tbody>
        </table>
    </center>";

    // Send the email
    $result = smtp_mailer('dixvaghela11@gmail.com', $emailSubject, $emailBody);

    if ($result === 'Sent') {
        echo json_encode(['status' => 'success', 'message' => 'Message has been sent']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Error: $result"]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    //$mail->SMTPDebug = 2; 
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
?>