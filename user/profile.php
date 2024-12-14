<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "realestate");

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the user's details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE uid = $user_id";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
if ($row) {
    $user_name = $row['uname'];
    $user_email = $row['uemail'];
    $phone = $row['uphone'];
    $uimage = $row['uimage'];
} else {
    $user_name = "Unknown User";
}

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Real ESTATE | User Profile</title>
<meta name="description" content="GARO is a real-estate template">
<meta name="author" content="Kimarotec">
<meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

<!-- Place favicon.ico in the root directory -->
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
<link rel="stylesheet" href="assets/css/agents.css">
</head>
<?php include 'include/header.php'; ?>
<body style="font-family: 'Poppins', sans-serif; background-color: #f5f5f5; color: #333333; line-height: 1.6; margin: 0; padding: 0; box-sizing: border-box;">
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>
<div class="container" id="profileContainer">
    <div class="profile-section">
        <h1 style="display: inline-block; color: #1e40af; font-size: 4rem; margin-right: 20px;">User Profile</h1>
        <a href="edit_profile.php" class="btn" style="float: right; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Edit Profile</a>

        <div class="profile-header">
            <div class="profile-image">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($uimage); ?>" alt="User Image">
            </div>
            <h2 class="profile-name"><?php echo $user_name; ?></h2>
        </div>
        <div class="profile-section">
            <h2>Contact Information</h2>
            <div class="info-item">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo $user_email; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone:</div>
                <div class="info-value"><?php echo $phone; ?></div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-section {
        margin-bottom: 30px;
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .profile-image img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin-right: 20px;
    }

    .profile-name {
        font-size: 2rem;
        color: #1e40af;
    }

    .info-item {
        display: flex;
        margin-bottom: 10px;
    }

    .info-label {
        font-weight: bold;
        width: 120px;
    }

    .info-value {
        flex: 1;
    }

    .btn {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }

    .btn:hover {
        opacity: 0.8;
    }
</style>

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
<?php include('include/footer.php'); ?>
</body>
</html>