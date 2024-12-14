<?php
session_start(); // Start the session

// Check if the agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "realestate";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve agent details
$agent_id = $_SESSION['agent_id'];
$query = "SELECT aemail, aname, apass FROM agents WHERE aid = ?";
$stmt = $conn->prepare($query);

// Check if the prepare statement was successful
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $agent = $result->fetch_assoc();
} else {
    echo "Agent not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate current password
    if (!password_verify($current_password, $agent['apass'])) {
        $response['message'] = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $response['message'] = "New password and confirm password do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_query = "UPDATE agents SET apass = ? WHERE aid = ?";
        $update_stmt = $conn->prepare($update_query);

        // Check if the prepare statement was successful
        if ($update_stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $update_stmt->bind_param("si", $hashed_password, $agent_id);

        if ($update_stmt->execute()) {
            $response['message'] = "Password changed successfully.";
        } else {
            $response['message'] = "Failed to change password. Please try again.";
        }
    }
}

$conn->close();
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
                        <h2 class="text-center">Change Password</h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
                        <form action="change_password.php" method="post">
                            <input type="hidden" name="agent_id" value="<?php echo $agent_id; ?>">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
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
    
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (!empty($response['message']) && $response['message'] === 'Password changed successfully.'): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Password Changed',
                text: 'Your password has been changed successfully.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'profile.php'; // Redirect to login page
                }
            });
        </script>
    <?php endif; ?>

    <?php include("include/footer.php"); ?>
</body>
</html>