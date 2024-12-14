<?php
session_start(); // Start the session

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "realestate";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to validate input data
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$response = ['message' => '', 'not_verified' => false]; // Added 'not_verified' for SweetAlert

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);

    // Check if the user is a regular user
    $user_query = "SELECT uid, upass, isverify FROM users WHERE uemail = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['upass'])) {
            if ($user['isverify'] == 0) {
                $response['message'] = 'You are not verified. Please verify your email.';
                $response['not_verified'] = true; // Set the flag for SweetAlert
            } else {
                $_SESSION['user_id'] = $user['uid']; // Store user ID in session
                header("Location: user/index.php"); // Redirect to user's index page
                exit();
            }
        } else {
            $response['message'] = 'Invalid email or password.';
        }
    }

    // Check if the user is an agent
    $agent_query = "SELECT aid, apass, isverify FROM agents WHERE aemail = ?";
    $stmt = $conn->prepare($agent_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $agent_result = $stmt->get_result();

    if ($agent_result->num_rows > 0) {
        $agent = $agent_result->fetch_assoc();
        // Verify password
        if (password_verify($password, $agent['apass'])) {
            if ($agent['isverify'] == 0) {
                $response['message'] = 'You are not verified. Please verify your email.';
                $response['not_verified'] = true; // Set the flag for SweetAlert
            } else {
                $_SESSION['agent_id'] = $agent['aid']; // Store agent ID in session
                header("Location: agent/index.php"); // Redirect to agent's index page
                exit();
            }
        } else {
            $response['message'] = 'Invalid email or password.';
        }
    }

    if (empty($response['message'])) {
        $response['message'] = 'Invalid email or password.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Login page</title>
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
        .login-area {
            background: linear-gradient(to bottom, #ebebeb 50%, #f9f9f9 100%);
        }
        .box-for {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 5px;
            background-color: #ffffff;
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
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
</head>
<body>
    <?php include("include/header.php"); ?>
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Login</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->
 
    <!-- login-area -->
    <div class="login-area">
        <br>
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 login-blocks">
                        <h2 class="text-center">Login : </h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="remember-forgot">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="remember_me"> Remember Me
                                </label>
                                <a style="color:red" href="forgot_password.php">Forgot Password?</a>
                            </div>
                            <a style="color: blue; text-decoration: underline; text-align: right; display: block; margin-bottom: 20px; padding-left: 0;" href="send_verifycode.php">Please Verify your email address..</a>

                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Log in</button>
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

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
                    window.location.href = 'send_verifycode.php'; // Redirect to verification page
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