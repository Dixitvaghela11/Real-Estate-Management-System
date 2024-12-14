<?php
include('include/config.php');
include('include/send_otp_email.php');

function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $new_otp = generateOTP();

    if ($role == 'user') {
        $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE uemail = ?");
    } else if ($role == 'agent') {
        $stmt = $conn->prepare("UPDATE agents SET otp = ? WHERE aemail = ?");
    }

    $stmt->bind_param("ss", $new_otp, $email);
    if ($stmt->execute()) {
        // Send the new OTP to the user's email (you need to implement this part)
        // For example, using PHPMailer or any other email library
        // mail($email, 'New OTP', 'Your new OTP is: ' . $new_otp);

        echo json_encode(['status' => 'success', 'message' => 'New OTP has been sent to your email.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }
    $stmt->close();
}
?>