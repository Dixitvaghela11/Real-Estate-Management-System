<?php
session_start(); // Start the session
include("include/alert.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
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
$query = "SELECT uemail, uname FROM users WHERE uid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Retrieve enquiries associated with the logged-in user
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
                pe.u_id = ?"; // Filter by logged-in user's ID

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id); // Bind the logged-in user's ID
$stmt->execute();
$enquiries_result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARO ESTATE | My Enquiries</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">

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

    <style>


/* Table Styles */
table.table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

table.table thead {
    background-color: #007bff;
    color: white;
}

table.table th, table.table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table.table tr:hover {
    background-color: #f1f1f1;
}

/* Button Styles */
.button-container {
    display: flex;
    gap: 10px; /* Space between buttons */
}

.button {
    display: inline-block;
    padding: 10px 15px;
    color: #fff;
    background-color: #007bff;
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: 4px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #0056b3;
    text-decoration: none;
    color: #fff;
}

.delete_enquiry {
    background-color: #dc3545;
}

.delete_enquiry:hover {
    background-color: #c82333;
}


/* Sidebar Styles */
.recent-property-widget ul {
    list-style: none;
    padding: 0;
}

.recent-property-widget ul li {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.recent-property-widget ul li .blg-thumb img {
    border-radius: 4px;
    margin-right: 10px;
}

.property-price {
    font-size: 14px;
    font-weight: bold;
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
                    <h1 class="page-title">My Enquiries</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->

    <!-- Enquiries area -->
    <div class="content-area recent-property" style="background-color: #FFF;">
        <div class="container">   
            <div class="row">

                <div class="col-md-9 pr-30 padding-top-40 properties-page user-properties">

                    <div class="section"> 
                        <div id="list-type" class="proerty-th-list">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Enquiry ID</th>
                                        <th>Property Name</th>
                                        <th>Agent Name</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Enquiry Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($enquiries_result->num_rows > 0) {
                                        while($enquiry = $enquiries_result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $enquiry['enquiry_id']; ?></td>
                                        <td><?php echo htmlspecialchars($enquiry['property_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enquiry['agent_name']); ?></td>
                                        <td><?php echo htmlspecialchars($enquiry['message']); ?></td>
                                        <td><?php echo $enquiry['status']; ?></td>
                                        <td><?php echo $enquiry['enquiry_date']; ?></td>
                                        <td>
    <div class="button-container">
        <a href="enquiry-detail.php?enquiry_id=<?php echo $enquiry['enquiry_id']; ?>" class="button">View</a>
        <a href="deleteenquiry.php?enquiry_id=<?php echo $enquiry['enquiry_id']; ?>" class="button delete_enquiry" data-id="<?php echo $enquiry['enquiry_id']; ?>">Delete</a>
    </div>
</td>

                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No enquiries found for this user.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="col-md-3">                    
                    <div class="blog-asside-right">  
                        <div class="panel panel-default sidebar-menu wow fadeInRight animated">
                            <div class="panel-heading">
                                <h3 class="panel-title">Recommended</h3>
                            </div>
                            <!-- property list -->
                            <?php
                            include("include/config.php");
                            $sql = "SELECT * FROM property LIMIT 6";
                            $result = $conn->query($sql); // Assuming $conn is your database connection object
                            ?>
                            <div class="panel-body recent-property-widget">
                                <ul>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            ?>
                                            <li>
                                                <div class="col-md-3 blg-thumb p0">
                                                    <a href="single.html">
                                                        <img style="width: 100px; height: 50px;" src="data:image/jpeg;base64,<?php echo base64_encode($row['main_image']); ?>">
                                                    </a>
                                                </div>
                                                <div class="col-md-8 blg-entry">
                                                    <h6><a href="property-detail.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['property_name']; ?></a></h6>
                                                    <span style="color : green; font-weight: bold;" class="property-price"><?php echo $row['property_price']; ?></span>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        echo "No properties found.";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>                     
                </div>
            </div>
        </div>
    </div>

    <?php include("include/footer.php"); ?>
    <script src="assets/js/vendor/modernizr-2.6.2.min.js"></script>
    <script src="assets/js//jquery-1.10.2.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/bootstrap-hover-dropdown.js"></script>
    <script src="assets/js/easypiechart.min.js"></script>
    <script src="assets/js/jquery.easypiechart.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/wow.js"></script>
    <script src="assets/js/icheck.min.js"></script>

    <script src="assets/js/price-range.js"></script> 
    <script src="assets/js/jquery.bootstrap.wizard.js" type="text/javascript"></script>
    <script src="assets/js/jquery.validate.min.js"></script>
    <script src="assets/js/wizard.js"></script>

    <script src="assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete_enquiry').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const enquiryId = this.getAttribute('data-id');
                    showAlert('question', 'Confirm Deletion', 'Are you sure you want to delete this enquiry?', function() {
                        // AJAX request to delete the enquiry
                        fetch('deleteenquiry.php?id=' + enquiryId, {
                                method: 'GET'
                            }).then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showAlert('success', 'Success', 'Enquiry deleted successfully', function() {
                                        location.reload();
                                    });
                                } else {
                                    console.error(data.error || 'An unknown error occurred');
                                    showAlert('error', 'Error', 'Failed to delete enquiry');
                                }
                            }).catch(error => {
                                console.error('Fetch error:', error);
                                showAlert('error', 'Error', 'An unexpected error occurred');
                            });

                    });
                });
            });
        });

        function showAlert(type, title, message, callback = null) {
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
                if (callback) callback();
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
    </script>
</body>
</html>