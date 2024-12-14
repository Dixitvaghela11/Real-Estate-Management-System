<?php
// Include your database connection and the function to send OTP via email
include('include/config.php');
include('include/send_otp_email.php'); // Make sure this file contains your sendOTPEmail function

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Email entered by the user
    $role = ''; // Will store whether user is found in users or agents table
    $otp = rand(100000, 999999); // Generate a 6-digit OTP
    $alert_message = ''; // Variable to store the custom alert script

    // Check if email exists in `users` table
    $stmt = $conn->prepare("SELECT isverify FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($isverify); // Bind the isverify field

    if ($stmt->num_rows > 0) {
        // Email found in `users` table
        $stmt->fetch();
        $role = 'user';
    } else {
        // Check in `agents` table if not found in `users`
        $stmt = $conn->prepare("SELECT isverify FROM agents WHERE aemail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($isverify);

        if ($stmt->num_rows > 0) {
            // Email found in `agents` table
            $stmt->fetch();
            $role = 'agent';
        }
    }
    $stmt->close();

    // If the email exists in either table
    if ($role !== '') {
        if ($isverify == 1) {
            // Email is already verified
            $alert_message = "showAlert('info', 'Already Verified', 'This email is already registered and verified. Please reset your password.', 'forgot_password.php');";
        } else {
            // Email is found but not verified, proceed with OTP sending
            // Store OTP in the relevant table and send OTP
            if ($role == 'user') {
                $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE uemail = ?");
                $stmt->bind_param("ss", $otp, $email);
            } else if ($role == 'agent') {
                $stmt = $conn->prepare("UPDATE agents SET otp = ? WHERE aemail = ?");
                $stmt->bind_param("ss", $otp, $email);
            }

            if ($stmt->execute()) {
                // Send OTP via email
                if (sendOTPEmail($email, $otp)) {
                    // Redirect to verify OTP page if email is successfully sent
                    $alert_message = "showAlert('success', 'OTP Sent', 'An OTP has been sent to your email. Please check your inbox.', 'verify_otp.php?email=" . urlencode($email) . "&role=" . $role . "');";
                } else {
                    // OTP email sending failed
                    $alert_message = "showAlert('error', 'Error', 'Unable to send OTP. Please try again.');";
                }
            } else {
                $alert_message = "showAlert('error', 'Error', 'Unable to update OTP. Please try again.');";
            }
            $stmt->close();
        }
    } else {
        // Email not found in either table
        $alert_message = "showAlert('error', 'Error', 'This email is not registered. Please register first.');";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>REAL ESTATE | Verify Email</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/fontello.css">
    <link href="assets/fonts/icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet">
    <link href="assets/fonts/icon-7-stroke/css/helper.css" rel="stylesheet">
    <link href="assets/css/animate.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/bootstrap-select.min.css"> 
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/icheck.min_all.css">
    <link rel="stylesheet" href="assets/css/price-range.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css">  
    <link rel="stylesheet" href="assets/css/owl.theme.css">
    <link rel="stylesheet" href="assets/css/owl.transitions.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .login-area {
            background: linear-gradient(to bottom, #ebebeb 50%, #f9f9f9 100%);
        }
        .box-for {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            background-color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-control {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }
        .btn-default {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-default:hover {
            background-color: #0056b3;
        }
        .text-center {
            text-align: center;
        }
    </style>

</head>
<body>
    <?php include("include/header.php"); ?>

    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Verify Email</h1>               
                </div>
            </div>
        </div>
    </div>

    <div class="login-area">
        <br>
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 login-blocks">
                        <h2 class="text-center">Verify Email :</h2> 
                        <form action="send_verifycode.php" method="post">
                            <div class="form-group">
                                <label for="email">Email Address:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Send OTP</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>

    <!-- Scripts -->
    <script src="assets/js/modernizr-2.6.2.min.js"></script>
    <script src="assets/js/jquery-1.10.2.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.js"></script>
    <script src="assets/js/easypiechart.min.js"></script>
    <script src="assets/js/jquery.easypiechart.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>        
    <script src="assets/js/wow.js"></script>
    <script src="assets/js/icheck.min.js"></script>
    <script src="assets/js/price-range.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <?php include("include/alert.php"); ?>

    <script>
        <?php
        // Echo the custom alert script if it's set
        if (!empty($alert_message)) {
            echo $alert_message;
        }
        ?>
    </script>

    <?php include("include/footer.php"); ?>
</body>
</html>