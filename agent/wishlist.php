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

// Retrieve agent details
$agent_id = $_SESSION['agent_id'];
$query = "SELECT aemail, aname FROM agents WHERE aid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $agent = $result->fetch_assoc();
} else {
    echo "Agent not found.";
    exit();
}

// Pagination variables
$limit = 6; // Number of properties per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Offset for SQL query

// Retrieve properties associated with the agent from the wishlist
$query = "SELECT p.* FROM wishlist w JOIN property p ON w.p_id = p.p_id WHERE w.aid = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $agent_id, $limit, $offset);
$stmt->execute();
$properties_result = $stmt->get_result();

// Get total number of properties for pagination
$total_query = "SELECT COUNT(*) AS total FROM wishlist WHERE aid = ?";
$stmt = $conn->prepare($total_query);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_properties = $total_result->fetch_assoc()['total'];

$total_pages = ceil($total_properties / $limit);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REAL ESTATE | User Wishlist Page</title>
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
 

.nav-button {
    display: flex;
    height: 40px;
    width: 150px;
    align-items: center;
    padding: 10px 20px;
    font-size: 16px;
    border: 2px solid #000066;
    background-color: white;
    color: #000066;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
    text-decoration: none;
}

.nav-button:hover {
    background-color: #000066;
    color: white;
}

.nav-icon {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

.previous .nav-icon {
    margin-right: 10px;
}

.next .nav-icon {
    margin-left: 10px;
}

.page-numbers {
    display: flex;
    gap: 10px;
    margin: 0 20px;
}

.page-numbers a {
    padding: 5px 10px;
    border: 1px solid #ccc;
    text-decoration: none;
    color: #333;
}

.page-numbers a.active {
    background-color: #000066;
    color: white;
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
        <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Wishlist</h1>               
      </div>
    </div>
  </div>
</div>
<!-- End page header -->

    <!-- property area -->
    <div class="content-area recent-property" style="background-color: #FFF;">
        <div class="container">   
            <div class="row">

                <div class="col-md-9 pr-30 padding-top-40 properties-page user-properties">

                    
                    <div class="section"> 
                        <div id="list-type" class="proerty-th-list">
                            <?php
                            if ($properties_result->num_rows > 0) {
                                while($property = $properties_result->fetch_assoc()) {
                            ?>
                            <div class="col-md-4 p0">
                                <div class="box-two proerty-item">
                                    <div class="item-thumb">
                                        <a href="property-detail.php?p_id=<?php echo $property['p_id']; ?>" ><img style="height: 275px;" src="data:image/jpeg;base64,<?php echo base64_encode($property['main_image']); ?>"></a>
                                    </div>
                                    <div class="item-entry overflow">
                                        <h5><a href="property-detail.php?p_id=<?php echo $property['p_id']; ?>"><?php echo $property['property_name']; ?> <?php echo $property['type']; ?> <?php echo $property['status']; ?></a></h5>
                                        <div class="dot-hr"></div>
                                        <span class="pull-left"><b> Area :</b> <?php echo $property['property_geo']; ?>sq </span>
                                        <br>
                                        <span class="pull-left"><b> City:</b> <?php echo $property['city']; ?>, <?php echo $property['state'];?> </span>
                                        <span style="font-weight: bold; color: green;" class="proerty-price pull-right"> INR <?php echo $property['property_price']; ?></span>
                                        <?php
$description = htmlspecialchars_decode($property['description'], ENT_QUOTES); // Decode HTML entities
$words = explode(' ', strip_tags($description)); // Remove HTML tags and split into words
$first25Words = implode(' ', array_slice($words, 0, 25)); // Get the first 25 words
?>
<p>
  <?php echo htmlspecialchars($first25Words); ?> 
  <a href="property-detail.php?p_id=<?php echo htmlspecialchars($property['p_id']); ?>" style="color: #000066; cursor: pointer; font-size: 15px; font-weight: bold;">
    ...More
  </a>
</p>

                                        <div class="property-icon">
                                            <img src="assets/img/icon/bed1.png">(<?php echo $property['min_bed']; ?>)|
                                            <img src="../assets/img/icon/bath.png">(<?php echo $property['min_baths']; ?>)|
                                            <img src="../assets/img/icon/kit.png">(<?php echo $property['min_kitchen']; ?>)  

                                            <div class="dealer-action pull-right">                                        
                                                <a href="#" class="button delete_user_car" data-id="<?php echo $property['p_id']; ?>">Delete from Wishlist</a>
                                                <a href="property-detail.php?p_id=<?php echo $property['p_id']; ?>" class="button">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo "<p>No properties found in your wishlist.</p>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="navigation">
    <a href="?page=<?php echo max(1, $page - 1); ?>" class="nav-button previous"><svg class="nav-icon" viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>Previous</a>
    <div class="page-numbers"><?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) echo '<a href="?page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>'; ?></div>
    <a href="?page=<?php echo min($total_pages, $page + 1); ?>" class="nav-button next">Next<svg class="nav-icon" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></a>
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
                                                    <h6><a href="property-detail.php?p_id=<?php echo $row['p_id']; ?>"><?php echo $row['property_name']; ?></a></h6>
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
<script>document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete_user_car').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const propertyId = this.getAttribute('data-id');
            showAlert('question', 'Confirm Deletion', 'Are you sure you want to delete this property from your wishlist?', function() {
                // AJAX request to delete the property from the wishlist
                fetch('deletewishlist.php?id=' + propertyId, {
                    method: 'GET'
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          showAlert('success', 'Success', 'Property removed from wishlist successfully', function() {
                              // Redirect to index.php
                              window.location.href = data.redirect;
                          });
                      } else {
                          showAlert('error', 'Error', 'Failed to remove property from wishlist');
                      }
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
}</script>
</body>
</html>