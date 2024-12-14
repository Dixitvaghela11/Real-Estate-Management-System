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

// Handle change password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $role = validate_input($_POST['role']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $response['message'] = "Error: Passwords do not match.";
    } else {
        $password = password_hash($password, PASSWORD_BCRYPT); // Secure password hashing

        // Update password in the database
        if ($role == 'user') {
            $stmt = $conn->prepare("UPDATE users SET upass = ? WHERE uemail = ?");
        } else {
            $stmt = $conn->prepare("UPDATE agents SET apass = ? WHERE aemail = ?");
        }
        $stmt->bind_param("ss", $password, $email);
        if ($stmt->execute()) {
            $response['message'] = "Password changed successfully. You can now login with your new password.";
        } else {
            $response['message'] = "Error: Unable to change password. Please try again.";
        }
        $stmt->close();
    }

    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Change Password</title>
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
    .change-password-area {
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
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Change Password</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->
 
    <!-- change-password-area -->
    <div class="change-password-area">
        <br>
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 change-password-blocks">
                        <h2 class="text-center">Change Password : </h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
                        <form action="" method="post" id="changePasswordForm">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <input type="hidden" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                            <input type="hidden" name="role" value="<?php echo isset($_GET['role']) ? htmlspecialchars($_GET['role']) : ''; ?>">
                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Change Password</button>
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
    
    <?php include("include/alert.php"); ?>

    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('changepassword.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    if (data.message.includes('Error: Passwords do not match.')) {
                        showAlert('error', 'Oops...', data.message);
                    } else {
                        showAlert('success', 'Success!', data.message, 'login.php');
                    }
                }
            });
        });
    </script>

    <?php include("include/footer.php"); ?>
</body>
</html>