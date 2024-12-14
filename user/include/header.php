<?php

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Destroy the session and redirect to login page

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

// Retrieve user details
$user_id = $_SESSION['user_id'];
$query = "SELECT uid, uemail, uname, uphone, uimage FROM users WHERE uid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Log the error or provide a more informative message
    error_log("User ID $user_id not found in the database.");
    echo "User not found.";
    exit();
}

$conn->close();
?>
<?php



//database connection
include("include/config.php");

// Retrieve all agents
$query = "SELECT * FROM agents ";
$result = $conn->query($query);
$conn->close();
?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Adjust path as necessary -->
    <link rel="stylesheet" href="path/to/font-awesome.css"> <!-- Adjust path as necessary -->
    <style>
        /* Agent image styling */
        .agent-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-left: 10px; /* Margin to separate from navbar brand */
            cursor: pointer; /* Add pointer cursor to indicate clickable */
        }

        /* Align agent image in navbar */
        .navbar-nav .agent-image-item {
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        /* Ensure the agent image is visible on mobile */
        @media (max-width: 768px) {
            .navbar-header .agent-image {
                display: inline-block; /* Show agent image on mobile */
                vertical-align: middle; /* Align vertically in the middle */
            }

            .navbar-header .navbar-brand {
                display: inline-block; /* Ensure the brand is inline */
                vertical-align: middle; /* Align vertically in the middle */
            }

            .navbar-header .navbar-toggle {
                display: inline-block; /* Ensure the toggle is inline */
                vertical-align: middle; /* Align vertically in the middle */
            }

            .navbar-header {
                display: flex; /* Use flexbox for better alignment */
                align-items: center; /* Center align items vertically */
                justify-content: space-between; /* Space between toggle and agent image */
            }

            /* Add left margin when toggle is active */
            .navbar-header.toggle-active {
                margin-left: 15px;
            }

            /* Position agent image to the right when toggle is active */
            .navbar-header.toggle-active .agent-image-item {
                order: 2; /* Move agent image to the right */
                margin-left: -3px; /* Adjust agent image position */
            }

            /* Center the logo and position toggle slightly to the left */
            .navbar-header .navbar-brand {
                margin-left: -15px; /* Adjust toggle position */
                margin-right: 3px; /* Adjust toggle position */
                
            }

            .navbar-header .navbar-toggle {
                margin-left: -15px; /* Adjust toggle position */
                margin-right: 3px; /* Adjust toggle position */
            }

            /* Half-show the toggle button when active */
            .navbar-header.toggle-active .navbar-toggle {
                position: relative;
                left: -100%; /* Half-show the toggle button */
            }

            /* Ensure the agent image has enough space */
            .navbar-header.toggle-active .agent-image-item {
                margin-left: 90px; /* Increase margin to ensure visibility */
            }

            /* Adjust the position of the agent image */
            .navbar-header.toggle-active .agent-image-item img {
                position: relative;
                right: 40px; /* Adjust position to avoid overlap */
            }

            /* Move the toggle button to the right when active */
            .navbar-header.toggle-active .navbar-toggle {
                position: absolute;
                right: 15px; /* Adjust position to the right */
            }
        }
    </style>
</head>
<body>
 <!-- Header Section -->
 <div class="header-connect">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-8 col-xs-12">
                    <div class="header-half header-call">
                        <p>
                            <span><i class="pe-7s-call"></i> +91 97 24 901 801</span>
                            <span><i class="pe-7s-mail"></i> dixvaghela11@gmail.com</span>
                        </p>
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-5 col-sm-3 col-sm-offset-1 col-xs-12">
                    <div class="header-half header-social">
                        <ul class="list-inline">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="https://www.linkedin.com/in/dixit-vaghela-611588274/"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="https://instagram.com/dixit_vaghela11"><i class="fa fa-instagram"></i></a></li>
                            <li><a href="https://github.com/dixitvaghela11"><i class="fa fa-github"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

<nav style="min-height: 100px; margin-bottom: 0px; background-color: #fff" class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div  class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navigation" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="assets/img/logo.png" alt="Company Logo" class="logo-img" style="width: auto;">
            </a>
            <!-- Agent image (for mobile view) -->
            <div class="agent-image-item visible-xs">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php if (!empty($user['uimage'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['uimage']); ?>" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                    <?php else: ?>
                        <img src="assets/img/default-user.png" alt="Default User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                    <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="wishlist.php">Wishlist</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navbar links -->
        <div style="color: blanchedalmond;" class="collapse navbar-collapse ymm" id="navigation">
            <ul class="main-nav nav navbar-nav navbar-right">
                <li><a href="index.php">Home</a></li>
                <li><a href="propertygrid.php">Properties</a></li>
                <li><a href="inquiries.php">Property Inquiries</a></li>
                <li><a href="agentgrid.php">Agents</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <!-- Agent image (for larger screens) -->
                <li class="agent-image-item hidden-xs"> <!-- Show only on larger screens -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php if (!empty($user['uimage'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['uimage']); ?>" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                    <?php else: ?>
                        <img src="assets/img/default-user.png" alt="Default User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                    <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="wishlist.php">Wishlist</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<!-- End of navbar -->

<script src="path/to/jquery.js"></script> <!-- Adjust path as necessary -->
<script src="path/to/bootstrap.js"></script> <!-- Adjust path as necessary -->
<script>
    // Ensure the dropdown remains open when clicked
    $('.navbar-toggle').click(function() {
        $('#navigation').toggleClass('in');
        $('.navbar-header').toggleClass('toggle-active');

        // Move the toggle button to the right when active
        if ($('.navbar-header').hasClass('toggle-active')) {
            $('.navbar-toggle').css('position', 'absolute');
            $('.navbar-toggle').css('right', '-15px');
        } else {
            $('.navbar-toggle').css('position', 'static');
            $('.navbar-toggle').css('right', 'auto');
        }
    });
</script>
</body>
</html>