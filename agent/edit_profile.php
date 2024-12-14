<?php
session_start(); // Start the session
ob_start(); // Start output buffering
include("include/alert.php");

// Check if the agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

// Retrieve the agent ID from session
$agent_id = $_SESSION['agent_id'];

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
$agent_query = "SELECT * FROM agents WHERE aid = ?";
$agent_stmt = $conn->prepare($agent_query);
$agent_stmt->bind_param("i", $agent_id);
$agent_stmt->execute();
$agent_result = $agent_stmt->get_result();
$agent = $agent_result->fetch_assoc();

// Handle form submission for editing agent details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_agent'])) {
    // Collect form data
    $aname = $_POST['aname'];
    $aemail = $_POST['aemail'];
    $aphone = $_POST['aphone'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $office_address = $_POST['office_address'];
    $content = $_POST['content'];
    $dob = $_POST['dob'];
    $languages = $_POST['languages'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $whatsappnumber = $_POST['whatsappnumber'];

    // Handle image upload (if a new image is selected)
    if (!empty($_FILES['aimage']['name'])) {
        $aimage = file_get_contents($_FILES['aimage']['tmp_name']);
        $update_image_query = "UPDATE agents SET aimage = ? WHERE aid = ?";
        $update_image_stmt = $conn->prepare($update_image_query);
        $update_image_stmt->bind_param("si", $aimage, $agent_id);
        $update_image_stmt->execute();
    }

    // Update agent details
    $update_agent_query = "UPDATE agents SET aname = ?, aemail = ?, aphone = ?, facebook = ?, twitter = ?, instagram = ?, linkedin = ?, office_address = ?, content = ?, dob = ?, Languages = ?, Specialization = ?, Experience = ?, whatsappnumber = ? WHERE aid = ?";
    
    $update_agent_stmt = $conn->prepare($update_agent_query);
    $update_agent_stmt->bind_param(
        "ssssssssssssssi", 
        $aname, 
        $aemail, 
        $aphone, 
        $facebook, 
        $twitter, 
        $instagram, 
        $linkedin, 
        $office_address, 
        $content, 
        $dob, 
        $languages, 
        $specialization, 
        $experience, 
        $whatsappnumber, 
        $agent_id
    );
    
    $update_agent_stmt->execute();

    // Display success message and redirect
    $_SESSION['message'] = "Agent details updated successfully.";
    header("Location: profile.php"); // Redirect to refresh the page
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Edit Agent</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    .edit-agent-area {
        background: linear-gradient(to bottom, #ebebeb 50%, #f9f9f9 100%);
        padding: 20px 0;
    }
    .box-for {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 10px;
        background-color: #ffffff;
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
        transition: background-color 0.3s, transform 0.3s;
    }
    .btn-default:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
    .alert {
        margin-top: 20px;
        padding: 15px;
        border-radius: 5px;
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
    .agent-image {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
</style>
<body>
    <?php include("include/header.php"); ?>
    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title animate__animated animate__fadeInDown"><a style="color: white;" href="index.php">Home</a> | Edit Agent</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->
 
    <!-- edit-agent-area -->
    <div class="edit-agent-area">
        <br>
        <div class="container">
            <div class="col-md-8 col-md-offset-2">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 edit-agent-blocks">
                        <h2 class="text-center">Edit Agent Details</h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
<!-- Form for updating the profile -->
<form action="edit_profile.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="aid" value="<?php echo $agent['aid']; ?>">
    <div class="form-group">
        <label for="aname">Agent Name :</label>
        <input type="text" class="form-control" id="aname" name="aname" value="<?php echo htmlspecialchars($agent['aname']); ?>" required>
    </div>
    <div class="form-group">
        <label for="aemail">Agent Email :</label>
        <input type="email" class="form-control" id="aemail" name="aemail" value="<?php echo htmlspecialchars($agent['aemail']); ?>" required>
    </div>
    <div class="form-group">
        <label for="aphone">Agent Phone :</label>
        <input type="text" class="form-control" id="aphone" name="aphone" value="<?php echo htmlspecialchars($agent['aphone']); ?>">
    </div>
    <div class="form-group">
        <label for="facebook">Facebook: (Enter URL)</label>
        <input type="text" class="form-control" id="facebook" name="facebook" value="<?php echo htmlspecialchars($agent['facebook']); ?>">
    </div>
    <div class="form-group">
        <label for="twitter">Twitter: (Enter URL)</label>
        <input type="text" class="form-control" id="twitter" name="twitter" value="<?php echo htmlspecialchars($agent['twitter']); ?>">
    </div>
    <div class="form-group">
        <label for="instagram">Instagram: (Enter URL)</label>
        <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo htmlspecialchars($agent['instagram']); ?>">
    </div>
    <div class="form-group">
        <label for="linkedin">LinkedIn: (Enter URL)</label>
        <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($agent['linkedin']); ?>">
    </div>
    <div class="form-group">
        <label for="office_address">Office Address</label>
        <textarea class="form-control" id="office_address" name="office_address"><?php echo htmlspecialchars($agent['office_address']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="content">Content</label>
        <textarea class="form-control" id="content" name="content"><?php echo htmlspecialchars($agent['content']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($agent['dob']); ?>">
    </div>
    <div class="form-group">
        <label for="languages">Languages: (Like English, Hindi, Gujarati)</label>
        <input type="text" class="form-control" id="languages" name="languages" value="<?php echo htmlspecialchars($agent['Languages']); ?>">
    </div>
    <div class="form-group">
        <label for="specialization">Specialization: (Like Residential, Commercial)</label>
        <input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo htmlspecialchars($agent['Specialization']); ?>">
    </div>
    <div class="form-group">
        <label for="experience">Experience (in years)</label>
        <input type="text" class="form-control" id="experience" name="experience" value="<?php echo htmlspecialchars($agent['Experience']); ?>">
    </div>
    <div class="form-group">
        <label for="whatsappnumber">WhatsApp Number:</label>
        <input type="text" class="form-control" id="whatsappnumber" name="whatsappnumber" value="<?php echo htmlspecialchars($agent['whatsappnumber']); ?>">
    </div>
    <div class="form-group">
        <label for="aimage">Agent Image:</label>
        <input type="file" class="form-control" id="aimage" name="aimage">
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-default" name="edit_agent">Update Agent</button>
    </div>
</form>

                    </div>
                </div>
            </div>
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

    <!-- Custom Alert Box -->
    <style>
        .custom-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 50px;
            height-bottam : 50px;
            max-width: 500px;
            width: 55%;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1000; /* Ensure it appears on top */
        }

        .custom-alert.show {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
        }

        .custom-alert-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-10px); }
        }

        .custom-alert-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            animation: fadeInDown 0.5s;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-alert-message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #666;
            line-height: 1.5;
            animation: fadeIn 0.5s 0.2s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .custom-alert-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            animation: slideUp 0.5s 0.4s both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-alert-button {
            background-color: #3085d6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            outline: none;
            position: relative;
            overflow: hidden;
        }

        .custom-alert-button:hover {
            background-color: #2778c4;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .custom-alert-button:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .custom-alert-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .custom-alert-button:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% { transform: scale(0, 0); opacity: 1; }
            20% { transform: scale(25, 25); opacity: 1; }
            100% { opacity: 0; transform: scale(40, 40); }
        }

        .custom-alert-button.cancel {
            background-color: #dc3545;
        }

        .custom-alert-button.cancel:hover {
            background-color: #c82333;
        }

        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        .question { color: #6f42c1; }
        .custom { color: #fd7e14; }
    </style>

    <div id="customAlert" class="custom-alert" role="alertdialog" aria-modal="true">
        <div id="alertIcon" class="custom-alert-icon" aria-hidden="true"></div>
        <div id="alertTitle" class="custom-alert-title"></div>
        <div id="alertMessage" class="custom-alert-message"></div>
        <div class="custom-alert-buttons">
            <button id="alertOkButton" class="custom-alert-button">OK</button>
            <button id="alertCancelButton" class="custom-alert-button cancel">Cancel</button>
        </div>
    </div>

    <script>
        function showAlert(type, title, message, redirectUrl = null) {
            const alert = document.getElementById('customAlert');
            const icon = document.getElementById('alertIcon');
            const titleElement = document.getElementById('alertTitle');
            const messageElement = document.getElementById('alertMessage');
            const okButton = document.getElementById('alertOkButton');
            const cancelButton = document.getElementById('alertCancelButton');

            // Set icon based on alert type
            switch (type) {
                case 'success':
                    icon.innerHTML = '✅';
                    icon.className = 'custom-alert-icon success';
                    break;
                case 'error':
                    icon.innerHTML = '❌';
                    icon.className = 'custom-alert-icon error';
                    break;
                case 'info':
                    icon.innerHTML = 'ℹ️';
                    icon.className = 'custom-alert-icon info';
                    break;
                case 'warning':
                    icon.innerHTML = '⚠️';
                    icon.className = 'custom-alert-icon warning';
                    break;
                case 'question':
                    icon.innerHTML = '❓';
                    icon.className = 'custom-alert-icon question';
                    break;
                default:
                    icon.innerHTML = '🔔';
                    icon.className = 'custom-alert-icon custom';
                    break;
            }

            titleElement.textContent = title;
            messageElement.textContent = message;

            alert.classList.add('show');

            okButton.onclick = function() {
                alert.classList.remove('show');
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            };

            cancelButton.onclick = function() {
                alert.classList.remove('show');
            };

            // Close alert when clicking outside
            window.onclick = function(event) {
                if (event.target == alert) {
                    alert.classList.remove('show');
                }
            };

            // Handle keyboard events for accessibility
            alert.onkeydown = function(event) {
                if (event.key === 'Escape') {
                    alert.classList.remove('show');
                }
            };

            // Set focus to the OK button when the alert is shown
            setTimeout(() => okButton.focus(), 100);
        }

        // Example usage
        // showAlert('success', 'Success', 'Agent details updated successfully.', 'edit_profile.php');
    </script>

    <?php include("include/footer.php"); ?>
</body>
</html>