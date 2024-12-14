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

//display city and state
$query_cities = "SELECT * FROM city";
$result_cities = $conn->query($query_cities);

$query_states = "SELECT * FROM state";
$result_states = $conn->query($query_states);


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

// Retrieve properties associated with the agent
$query = "SELECT * FROM property WHERE aid = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $agent_id, $limit, $offset);
$stmt->execute();
$properties_result = $stmt->get_result();

// Get total number of properties for pagination
$total_query = "SELECT COUNT(*) AS total FROM property WHERE aid = ?";
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
    <title>GARO ESTATE | User properties Page</title>
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px; 
            margin-bottom: 20px;
        }
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
                    <h1 class="page-title">My Properties</h1>               
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
                        <div class="page-subheader sorting pl0 pr-10">
                            <ul class="sort-by-list pull-left">
                                <li class="active">
                                    <a href="javascript:void(0);" class="order_by_date" data-orderby="property_date" data-order="ASC">
                                        Property Date <i class="fa fa-sort-amount-asc"></i>					
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);" class="order_by_price" data-orderby="property_price" data-order="DESC">
                                        Property Price <i class="fa fa-sort-numeric-desc"></i>						
                                    </a>
                                </li>
                            </ul><!--/ .sort-by-list-->
                        </div>
                    </div>
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
                                                <a href="property-edit.php?p_id=<?php echo $property['p_id']; ?>" class="button">Edit </a>
                                                <a href="deleteproperty.php?p_id=<?php echo $property['p_id']; ?>" class="button delete_user_car" data-id="<?php echo $property['p_id']; ?>">Delete</a>
                                                <a href="property-detail.php?p_id=<?php echo $property['p_id']; ?>" class="button">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo "<p>No properties found for this agent.</p>";
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Pagination Section -->
                    <div class="section"> 
                        <div style="margin-top: 20px;" class="navigation">
                            <a href="?page=<?php echo max(1, $page - 1); ?>" class="nav-button previous">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                                </svg>
                                Previous
                            </a>
                            <div class="page-numbers">
                                <?php
                                $visible_pages = 5; // Number of visible page numbers
                                $start_page = max(1, $page - floor($visible_pages / 2));
                                $end_page = min($total_pages, $start_page + $visible_pages - 1);
                                $start_page = max(1, $end_page - $visible_pages + 1);

                                if ($start_page > 1) {
                                    echo '<a href="?page=1">1</a>';
                                    if ($start_page > 2) {
                                        echo '<span>...</span>';
                                    }
                                }

                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    echo '<a href="?page=' . $i . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
                                }

                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<span>...</span>';
                                    }
                                    echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
                                }
                                ?>
                            </div>
                            <a href="?page=<?php echo min($total_pages, $page + 1); ?>" class="nav-button next">
                                Next
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <!-- End Pagination Section -->

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
            document.querySelectorAll('.delete_user_car').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const propertyId = this.getAttribute('data-id');
                    
                    console.log('Delete button clicked for property ID:', propertyId);
                    
                    showAlert('question', 'Confirm Deletion', 'Are you sure you want to delete this property?', function() {
                        const url = `deleteproperty.php?p_id=${propertyId}`;
                        console.log('Sending delete request to:', url);
                        
                        fetch(url)
                            .then(response => {
                                console.log('Raw response:', response);
                                return response.text();
                            })
                            .then(text => {
                                console.log('Response text:', text);
                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    console.error('JSON parse error:', e);
                                    throw new Error('Invalid JSON response');
                                }
                            })
                            .then(data => {
                                console.log('Parsed response:', data);
                                if (data.success) {
                                    showAlert('success', 'Success', data.message, function() {
                                        location.reload();
                                    });
                                } else {
                                    showAlert('error', 'Error', data.message || 'Failed to delete property');
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                showAlert('error', 'Error', 'An unexpected error occurred while deleting the property');
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