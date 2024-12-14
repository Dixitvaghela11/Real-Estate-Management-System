<?php
include('include/config.php');
include('include/send_otp_email.php');
    

// Helper function to validate input data
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$response = ['message' => '', 'not_verified' => false]; // Added 'not_verified' for SweetAlert

// Handle forget password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = validate_input($_POST['email']);

    // Check if the email exists in `users` or `agents`
    $emailExists = false;

    // Check in `users` table
    $stmt = $conn->prepare("SELECT uid FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $emailExists = true;
        $role = 'user';
    }
    $stmt->close();

    // Check in `agents` table if not found in `users`
    if (!$emailExists) {
        $stmt = $conn->prepare("SELECT aid FROM agents WHERE aemail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $emailExists = true;
            $role = 'agent';
        }
        $stmt->close();
    }

    // If email exists, generate and send OTP
    if ($emailExists) {
        $otp = rand(100000, 999999); // Generate OTP

        // Update OTP in the database
        if ($role == 'user') {
            $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE uemail = ?");
        } else {
            $stmt = $conn->prepare("UPDATE agents SET otp = ? WHERE aemail = ?");
        }
        $stmt->bind_param("is", $otp, $email);
        $stmt->execute();
        $stmt->close();

        // Send OTP via email
        if (sendOTPEmail($email, $otp)) {
            // Redirect to OTP verification page
            header("Location: forgetverifyotp.php?email=" . urlencode($email) . "&role=" . urlencode($role));
            exit;
        } else {
            $response['message'] = "Error: Unable to send OTP to this email address. Please try again with a valid email.";
        }
    } else {
        $response['message'] = "Error: This email is not registered.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Forget Password</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
  
    <!-- Other scripts -->
</head>
<style>
    .forget-password-area {
        background: linear-gradient(to bottom, #ebebeb 50%, #f9f9f9 100%);
    }
    .box-for {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 5px;
        background-color: #ffffff;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .btn-default {
        padding: 10px 20px;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-default:hover {
        background-color: #0056b3;
    }
    .alert {
        margin-top: 20px;
        padding: 15px;
        border-radius: 5px;
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
</style>
<body>
    <?php include("include/header.php"); ?>
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Forget Password</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->
 
    <!-- forget-password-area -->
    <div class="forget-password-area">
        <br>
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 forget-password-blocks">
                        <h2 class="text-center">Forget Password : </h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="email">Email</label>
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
    <br>

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
    
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($response['not_verified']): ?>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Email Not Verified',
                text: 'Please verify your email before logging in.',
                confirmButtonText: 'Go to Verification'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'verification.php'; // Redirect to verification page
                }
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['verification_success']) && $_SESSION['verification_success']): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Verification Successful',
                text: 'Your email has been verified successfully. Please enter your email and password to log in.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear the session variable after displaying the message
                    <?php unset($_SESSION['verification_success']); ?>
                }
            });
        </script>
    <?php endif; ?>
    <?php include("include/footer.php"); ?>
</body>
</html>