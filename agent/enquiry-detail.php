<?php
session_start(); // Start the session
include("include/alert.php");

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

// Check if enquiry_id is provided
if (!isset($_GET['enquiry_id']) || empty($_GET['enquiry_id'])) {
    echo "Enquiry ID is missing.";
    exit();
}

$enquiry_id = intval($_GET['enquiry_id']);
$agent_id = $_SESSION['agent_id']; // Get the logged-in agent's ID

// Retrieve enquiry details for the logged-in agent
$query = "SELECT 
                pe.enquiry_id, 
                p.property_name, 
                u.uname AS user_name, 
                a.aname AS agent_name, 
                pe.message, 
                pe.status, 
                pe.enquiry_date 
          FROM 
                property_enquiries pe
          LEFT JOIN 
                property p ON pe.p_id = p.p_id
          LEFT JOIN 
                users u ON pe.u_id = u.uid
          LEFT JOIN 
                agents a ON p.aid = a.aid
          WHERE 
                pe.enquiry_id = ? AND pe.aid = ?"; // Filter by enquiry_id and agent_id

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("ii", $enquiry_id, $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Enquiry not found or you do not have permission to view it.";
    exit();
}

$enquiry = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARO ESTATE | Enquiry Details</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
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
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
       
        .enquiry-details {
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .enquiry-details h2 {
            color: black;
            margin-bottom: 20px;
        }
        .enquiry-details p {
            margin-bottom: 10px;
        }
        .enquiry-details strong {
            color: black;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #000066;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include("include/header.php"); ?>
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <!-- Body content -->

    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Enquiry Details</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->

    <!-- Enquiry Details -->
    <div class="enquiry-details">
        <h2>Enquiry ID: <?php echo $enquiry['enquiry_id']; ?></h2>
        <p><strong>Property Name:</strong> <?php echo htmlspecialchars($enquiry['property_name']); ?></p>
        <p><strong>User Name:</strong> <?php echo htmlspecialchars($enquiry['user_name']); ?></p>
        <p><strong>Agent Name:</strong> <?php echo htmlspecialchars($enquiry['agent_name']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($enquiry['message']); ?></p>
        <p><strong>Status:</strong> <?php echo $enquiry['status']; ?></p>
        <p><strong>Enquiry Date:</strong> <?php echo $enquiry['enquiry_date']; ?></p>
        <a href="manageenquiries.php" class="btn-back">Back to Enquiries</a>
    </div>

    <?php include("include/footer.php"); ?>
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
</body>
</html>