<?php
include('include/config.php');
include('include/send_otp_email.php'); // Include the OTP email function

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing
    $otp = rand(100000, 999999); // Generate OTP
    $isverify = 0;

    // File upload (optional for image)
    $image = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Check if email already exists in `users` or `agents`
    $emailExists = false;

    // Check in `users` table
    $stmt = $conn->prepare("SELECT uid FROM users WHERE uemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $emailExists = true;
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
        }
        $stmt->close();
    }

    // If email already exists, show error message
    if ($emailExists) {
        echo "email_exists";
    } else {
        // Try to send OTP via email before saving to the database
        if (sendOTPEmail($email, $otp)) {
            // Email is valid, proceed to store user or agent data
            if ($role == 'user') {
                $stmt = $conn->prepare("INSERT INTO users (uemail, uname, upass, uphone, otp, uimage, isverify) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $email, $name, $password, $phone, $otp, $image, $isverify);
            } else if ($role == 'agent') {
                $stmt = $conn->prepare("INSERT INTO agents (aemail, aname, apass, aphone, otp, aimage, isverify) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $email, $name, $password, $phone, $otp, $image, $isverify);
            }

            if ($stmt->execute()) {
                // Redirect to OTP verification page
                echo "success";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            // OTP sending failed, show error message
            echo "Error: Unable to send OTP to this email address. Please try again with a valid email.";
        }
    }

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Register page</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

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
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Register</h1>               
                </div>
            </div>
        </div>
    </div>
     <!-- login-area -->
     <div class="login-area">
        <br>
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 login-blocks">
                        <h2 class="text-center">Register : </h2> 
                        <form method="POST" enctype="multipart/form-data" id="registerForm">
                        <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="role" value="user" required> User
                                    <input type="radio" name="role" value="agent" required> Agent
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Upload Image</label>
                                <input type="file" class="form-control" id="image" name="image" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>      
    <br>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'email_exists') {
                    showAlert('error', 'Oops...', 'This email is already registered.');
                } else if (data === 'success') {
                    showAlert('success', 'Success!', 'Registration successful! Redirecting to OTP verification page...', 'verify_otp.php?email=' + encodeURIComponent(formData.get('email')) + '&role=' + formData.get('role'));
                } else {
                    showAlert('error', 'Oops...', data);
                }
            });
        });
    </script>
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
    
    <?php include("include/alert.php"); ?>
    <?php include("include/footer.php"); ?>
</body>
</html>