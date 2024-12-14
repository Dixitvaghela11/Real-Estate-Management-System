<?php
session_start(); // Start the session
include("include/alert.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

//database connection
include("include/config.php");



// Check if the property ID is provided in the URL
if (!isset($_GET['p_id'])) {
    echo "<script>showAlert('error', 'Error', 'Property ID not provided.');</script>";
    exit();
}

$p_id = $_GET['p_id'];

// Function to retrieve property data
function getPropertyData($conn, $p_id) {
    $query = "SELECT * FROM property WHERE p_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Retrieve property data
$property = getPropertyData($conn, $p_id);

if (!$property) {
    echo "<script>showAlert('error', 'Error', 'Property not found.');</script>";
    exit();
}

// Retrieve agent/admin details
$aid = $property['aid'];
if ($aid !== null) {
    // Property was created by an agent
    $query = "SELECT * FROM agents WHERE aid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $aid);
    $stmt->execute();
    $agent = $stmt->get_result()->fetch_assoc();
} else {
    // Property was created by admin (admin_id = 2)
    $query = "SELECT * FROM admin WHERE admin_id = 2";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
}

// Retrieve property media (images and videos) from the database
function getPropertyMedia($conn, $property_id) {
    $sql = "SELECT * FROM property_media WHERE property_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

// Retrieve property media (images and videos)
$propertyMedia = getPropertyMedia($conn, $p_id);

// Close the connection at the end
$conn->close();

// Function to extract YouTube video ID from URL
function extractYoutubeId($url) {
    preg_match("/(?:https?:\/\/(?:www\.)?youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)([a-zA-Z0-9_-]{11}))/i", $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>GARO ESTATE | Property Detail Page</title>
        <meta name="description" content="company is a real-estate template">
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
        <link rel="stylesheet" href="assets/css/lightslider.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/responsive.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
<?php include 'include/header.php'; ?>
<div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
        <div class="page-head"> 
            <div class="container">
                <div class="row">
                    <div class="page-head-content">
                        <h1 class="page-title"><?php echo htmlspecialchars($property['property_name']); ?></h1>               
                    </div>
                </div>
            </div>
        </div>
        <!-- End page header -->

        <!-- property area -->
        <div class="content-area single-property" style="background-color: #FCFCFC;">&nbsp;
            <div class="container">   

                <div class="clearfix padding-top-40" >

                    <div class="col-md-8 single-property-content prp-style-1 ">
                        <div class="row">
                            <div class="light-slide-item">            
                                <div class="clearfix">
                                    <div class="favorite-and-print">
                                    <a class="add-to-fav" href="javascript:void(0);" onclick="addToWishlist(<?php echo $property['p_id']; ?>)">
                                    <i class="fa fa-star-o"></i>
                                </a>

<script>
function addToWishlist(p_id) {
    var uid = <?php echo $_SESSION['user_id']; ?>; // Fetching aid from session dynamically
    $.ajax({
        url: 'add_to_wishlist.php',
        type: 'POST',
        data: {
            uid: uid,
            p_id: p_id
        },
        success: function(response) {
            let data = JSON.parse(response);
            if (data.status === 'success') {
                // Show success alert
                showAlert('success', 'Success', 'Property added to wishlist!');

            } else if (data.status === 'exists') {
                // Show info alert
                showAlert('info', 'Info', 'This property is already in your wishlist.');
            } else {
                // Show error alert
                showAlert('error', 'Error', 'An error occurred while adding to the wishlist.');
            }
        },
        error: function() {
            // Show error alert on AJAX failure
            showAlert('error', 'Error', 'An error occurred while processing your request.');
        }
    });
}

</script>

                                        <a class="printer-icon " href="javascript:window.print()">
                                            <i class="fa fa-print"></i> 
                                        </a>
                                    </div> 

                                    <ul id="image-gallery" class="gallery list-unstyled cS-hidden">
                                        <!-- Display main image -->
                                        <li data-thumb="data:image/jpeg;base64,<?php echo base64_encode($property['main_image']); ?>">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($property['main_image']); ?>" />
                                        </li>
                                        <!-- Display floorplan image -->
                                        <li data-thumb="data:image/jpeg;base64,<?php echo base64_encode($property['floorplanimage']); ?>">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($property['floorplanimage']); ?>" />
                                        </li>
                                        <!-- Display other images from property_media -->
                                        <?php foreach ($propertyMedia as $media): ?>
                                            <?php if (!empty($media['photo_data'])): ?>
                                                <li data-thumb="data:image/jpeg;base64,<?php echo base64_encode($media['photo_data']); ?>">
                                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($media['photo_data']); ?>" />
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="single-property-wrapper">
                            <div class="single-property-header">                                          
                                <h1 class="property-title pull-left"><?php echo htmlspecialchars($property['property_name']); ?></h1>
                                <span class="property-price pull-right"> INR <?php echo htmlspecialchars($property['property_price']); ?></span>
                            </div>

                            <div class="property-meta entry-meta clearfix ">   

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-tag">                                        
                                        <img src="assets/img/icon/propertyview/status.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Status</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['status']); ?></span>
                                    </span>
                                </div>

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info icon-area">
                                        <img src="assets/img/icon/propertyview/sq.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Area</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['property_geo']); ?><b class="property-info-unit">Sq Ft</b></span>
                                    </span>
                                </div>

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-bed">
                                        <img src="assets/img/icon/propertyview/bed.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Bedrooms</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['min_bed']); ?></span>
                                    </span>
                                </div>

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-bed">
                                        <img src="assets/img/icon/propertyview/hall.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Halls</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['min_hall']); ?></span>
                                    </span>
                                </div>

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-bath">
                                        <img src="assets/img/icon/propertyview/kit1.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Kitchens</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['min_kitchen']); ?></span>
                                    </span>
                                </div>

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-garage">
                                        <img src="assets/img/icon/propertyview/balcony.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Balconies</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['min_balcony']); ?></span>
                                    </span>
                                </div>
                                
                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-garage">
                                        <img src="assets/img/icon/propertyview/floor.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Total Floors</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['total_floor']); ?></span>
                                    </span>
                                </div>

                                <div class="col-xs-6 col-sm-3 col-md-3 p-b-15">
                                    <span class="property-info-icon icon-garage">
                                        <img src="assets/img/icon/propertyview/floor.png">
                                    </span>
                                    <span class="property-info-entry">
                                        <span class="property-info-label">Floor</span>
                                        <span class="property-info-value"><?php echo htmlspecialchars($property['floor']); ?></span>
                                    </span>
                                </div>
                            </div>
                            <!-- .property-meta -->

                            
                            <!-- End description area  -->
                            <div class="section">
                                <h4 class="s-property-title">Decription</h4>
                                <div  class="s-property-content">
                                    <ul  class="property-features">
                                        <?php
                                         if (isset($property['description'])) {
                                            $description = explode(',', $property['description']);
                                            foreach ($description as $desc) {
                                                // Output each description item
                                                echo "<li>$desc</li>";
                                            }
                                        } else {
                                            echo "<li>No description available.</li>"; // Fallback if no description is available
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- End description area  -->
                            <div class="section">
                                <h4 class="s-property-title">Fetures</h4>
                                <div class="s-property-content">
                                    <ul class="property-features">
                                        <?php
                                        $features = explode(',', $property['other_details']);
                                        foreach ($features as $feature) {
                                            echo "$feature";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="section property-video">
    <h4 class="s-property-title">Property Videos</h4>
    <div class="slider-container">
        <div class="video-container">
            <video id="videoPlayer" controls>
                <?php
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "realestate";

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Assume p_id is passed via GET or other means (e.g., URL or session)
                $p_id = isset($_GET['p_id']) ? intval($_GET['p_id']) : 0;

                if ($p_id > 0) {
                    // Fetch only video_data for the specific property
                    $sql = "SELECT video_data FROM property_media WHERE property_id = ? AND video_data IS NOT NULL";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $p_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // Fetch all video data into an array
                        $videos = [];
                        while ($row = $result->fetch_assoc()) {
                            $videos[] = base64_encode($row['video_data']);
                        }
                        // Output the first video as the default
                        echo "<source src='data:video/mp4;base64,{$videos[0]}' type='video/mp4'>";
                    } else {
                        echo "<p>No videos available for this property.</p>";
                    }

                    $stmt->close();
                } else {
                    echo "<p>Invalid property ID.</p>";
                }

                $conn->close();
                ?>
                Your browser does not support the video tag.
            </video>
            <div id="loadingSpinner" class="loading-spinner"></div>
        </div>
        <div class="progress-bar">
            <div id="progressBar" class="progress"></div>
        </div>
        <div class="controls">
            <button onclick="prevVideo()">Previous</button>
            <button onclick="nextVideo()">Next</button>
        </div>
        <a id="downloadLink" class="download-link" href="#" download>Download Current Video</a>
    </div>
</div>

<script>
    const videos = [
        <?php
        // Convert PHP videos array to JavaScript array
        if (!empty($videos)) {
            foreach ($videos as $video) {
                echo "{ src: 'data:video/mp4;base64,$video' },";
            }
        }
        ?>
    ];
    let currentVideoIndex = 0;
    const videoPlayer = document.getElementById('videoPlayer');
    const downloadLink = document.getElementById('downloadLink');
    const progressBar = document.getElementById('progressBar');
    const loadingSpinner = document.getElementById('loadingSpinner');

    function loadVideo(index) {
        if (!videos.length) return;

        videoPlayer.style.opacity = 0;
        loadingSpinner.style.display = 'block';

        setTimeout(() => {
            videoPlayer.src = videos[index].src;
            videoPlayer.load();
            updateDownloadLink();

            videoPlayer.oncanplay = () => {
                loadingSpinner.style.display = 'none';
                videoPlayer.style.opacity = 1;
                videoPlayer.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    videoPlayer.style.transform = 'scale(1)';
                }, 300);
            };
        }, 300);
    }

    function nextVideo() {
        currentVideoIndex = (currentVideoIndex + 1) % videos.length;
        loadVideo(currentVideoIndex);
    }

    function prevVideo() {
        currentVideoIndex = (currentVideoIndex - 1 + videos.length) % videos.length;
        loadVideo(currentVideoIndex);
    }

    function updateDownloadLink() {
        downloadLink.href = videos[currentVideoIndex].src;
        downloadLink.style.animation = 'pulse 0.5s ease-out';
        setTimeout(() => {
            downloadLink.style.animation = '';
        }, 500);
    }

    videoPlayer.addEventListener('timeupdate', function() {
        const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;
        progressBar.style.width = progress + '%';
    });

    // Initialize with the first video
    loadVideo(currentVideoIndex);
</script>


<!-- Add Bootstrap JS for carousel functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php 
include("include/config.php");

// Get the property ID from the URL (assuming you have it as a GET parameter)
$p_id = isset($_GET['p_id']) ? intval($_GET['p_id']) : 0;

// Query to fetch reviews for the given property ID
$sql = "SELECT r.review_title, r.review_content, r.rating, r.review_date, 
                u.uname AS user_name, a.aname AS agent_name
        FROM reviews r
        LEFT JOIN users u ON r.uid = u.uid
        LEFT JOIN agents a ON r.aid = a.aid
        WHERE r.p_id = ?
        ORDER BY r.review_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $p_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are reviews
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine reviewer's name (could be either user or agent)
        $reviewer_name = $row['user_name'] ? $row['user_name'] : $row['agent_name'];
        $review_title = htmlspecialchars($row['review_title']);
        $review_content = nl2br(htmlspecialchars($row['review_content']));
        $rating = $row['rating'];
        $review_date = date('Y-m-d', strtotime($row['review_date']));

        // Display review
        echo '<div class="review-item">
                <div class="review-header">
                    <h5>' . $reviewer_name . '</h5>
                    <span class="review-date">' . $review_date . '</span>
                </div>
                <div class="review-rating">
                    <span>Rating:</span>
                    <strong>' . $rating . '</strong> / 5
                </div>
                <h6 class="review-title">' . $review_title . '</h6>
                <p class="review-content">' . $review_content . '</p>
              </div>';
    }
} else {
    echo "<p>No reviews available for this property.</p>";
}

$stmt->close();
?>
<?php
// Include necessary files
include("include/config.php");
include("include/alert.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'review_form') {
    // Sanitize and validate inputs
    $review_title = isset($_POST['review_title']) ? htmlspecialchars(trim($_POST['review_title'])) : null;
    $review_content = isset($_POST['review_content']) ? htmlspecialchars(trim($_POST['review_content'])) : null;
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null;
    $p_id = isset($_GET['p_id']) ? intval($_GET['p_id']) : null;
    $u_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null; // User ID
    $a_id = isset($_SESSION['agent_id']) ? intval($_SESSION['agent_id']) : null; // Agent ID

    // Validate required fields
    if (!$p_id || (!$u_id && !$a_id)) {
        echo "<script>showAlert('error', 'Submission Failed', 'You must be logged in to submit a review.');</script>";
        exit();
    }

    if (empty($review_title) || empty($review_content) || !$rating) {
        echo "<script>showAlert('error', 'Submission Failed', 'All fields are required.');</script>";
        exit();
    }

    // Insert the review into the database
    $sql = "INSERT INTO reviews (uid, aid, p_id, review_title, review_content, rating) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Database Error: " . $conn->error);
        echo "<script>showAlert('error', 'System Error', 'Failed to submit the review. Please try again later.');</script>";
        exit();
    }

    $stmt->bind_param("iiissd", $u_id, $a_id, $p_id, $review_title, $review_content, $rating);

    if ($stmt->execute()) {
        echo "<script>showAlert('success', 'Success', 'Review submitted successfully!', 'property-detail.php?p_id=$p_id');</script>";
    } else {
        error_log("Database Error: " . $conn->error);
        echo "<script>showAlert('error', 'Submission Failed', 'Failed to submit the review. Please try again later.');</script>";
    }

    $stmt->close();
    exit();
}
?>

 <!-- Property Review Form -->
 <div class="add-review-form">
        <h4 class="s-property-title">Property Reviews</h4>
        <form id="reviewForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?p_id=" . $_GET['p_id']); ?>">
            <input type="hidden" name="form_type" value="review_form"> <!-- Hidden field to identify this form -->

            <!-- Review Title -->
            <div class="form-group">
                <label for="review_title">Review Title</label>
                <input type="text" id="review_title" name="review_title" placeholder="Enter Review Title" required>
            </div>

            <!-- Review Content -->
            <div class="form-group">
                <label for="review_content">Review Content</label>
                <textarea id="review_content" name="review_content" placeholder="Write your review here..." required></textarea>
            </div>

            <!-- Rating -->
            <div class="form-group">
                <label for="rating">Rating (1.0 - 5.0)</label>
                <input type="number" step="0.1" id="rating" name="rating" min="1.0" max="5.0" placeholder="Rate out of 5" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">Submit Review</button>
        </form>
    </div>
                        </div>
                    </div>

                    
                    <div class="col-md-4 p0">
                        <aside class="sidebar sidebar-property blog-asside-right">
                          <!-- HTML to display the agent info -->
<div style="background-color: #96a0b5;" class="dealer-widget">
    <div class="dealer-content">
        <div class="inner-wrapper">
            <?php if ($aid !== null && $agent): ?>
                <!-- Display agent information -->
                <div class="clear">
                    <div class="col-xs-4 col-sm-4 dealer-face">
                        <a href="useragent.php?aid=<?php echo $agent['aid']; ?>">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($agent['aimage']); ?>" 
                                 class="img-circle" 
                                 style="width: 90px; height: 90px; object-fit: cover;" 
                                 alt="Agent Image">
                        </a>
                    </div>
                    <div class="col-xs-8 col-sm-8">
                        <h3 class="dealer-name">
                            <a href="useragent.php?aid=<?php echo $agent['aid']; ?>">
                                <?php echo htmlspecialchars($agent['aname']); ?>
                            </a>
                            <span>Real Estate Agent</span>
                        </h3>
                        <ul class="dealer-contacts">
                            <li><i class="pe-7s-mail strong"></i> <?php echo htmlspecialchars($agent['aemail']); ?></li>
                            <li><i class="pe-7s-call strong"></i> <?php echo htmlspecialchars($agent['aphone']); ?></li>
                            <li><i class="pe-7s-user strong"></i> Agent</li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Simplified admin display -->
                <div class="clear">
                    <div class="col-xs-8 col-sm-8">
                        <h4>Property Created by Admin</h4>
                        <ul class="dealer-contacts">
                            <li><i class="pe-7s-mail strong"></i> <?php echo htmlspecialchars($admin['aemail']); ?></li>
                            <li><i class="pe-7s-call strong"></i> <?php echo htmlspecialchars($admin['aphone']); ?></li>
                            <li><i class="pe-7s-user strong"></i> <?php echo htmlspecialchars($admin['auser']); ?></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

                            <?php
                            include("include/config.php");
                            $sql = "SELECT * FROM property LIMIT 6";
                            $result = $conn->query($sql); // Assuming $conn is your database connection object
                            ?>
                            <div class="panel panel-default sidebar-menu similar-property-wdg wow fadeInRight animated">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Similar Properties</h3>
                                </div>
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
                            <?php
// Include necessary files
include("include/config.php");
include("include/alert.php");
include("include/send_otp_email.php"); // Include the email sending function

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'inquiry_form') {
    // Sanitize and validate inputs
    $p_id = isset($_GET['p_id']) ? intval($_GET['p_id']) : null;
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : null;
    $u_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

    // Validate required fields
    if (!$p_id) {
        echo "<script>showAlert('error', 'Submission Failed', 'Property ID is missing!');</script>";
        exit();
    }

    if (!$u_id) {
        echo "<script>showAlert('error', 'Submission Failed', 'You must be logged in to submit an inquiry.');</script>";
        exit();
    }

    if (empty($message)) {
        echo "<script>showAlert('error', 'Submission Failed', 'Message cannot be empty.');</script>";
        exit();
    }

    // Fetch agent details (aid and email) in a single query
    $sql = "SELECT p.aid, a.aemail FROM property p 
            INNER JOIN agents a ON p.aid = a.aid 
            WHERE p.p_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Database Error: " . $conn->error);
        echo "<script>showAlert('error', 'System Error', 'Failed to fetch agent details. Please try again later.');</script>";
        exit();
    }
    $stmt->bind_param("i", $p_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>showAlert('error', 'Submission Failed', 'No agent found for this property.');</script>";
        exit();
    }

    $row = $result->fetch_assoc();
    $aid = $row['aid'];
    $agent_email = $row['aemail']; // Fetch the agent's email
    $stmt->close();

    // Insert the inquiry into the database
    $sql_insert = "INSERT INTO property_enquiries (p_id, u_id, aid, message, status, enquiry_date) VALUES (?, ?, ?, ?, 'pending', NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    if ($stmt_insert === false) {
        error_log("Database Error: " . $conn->error);
        echo "<script>showAlert('error', 'System Error', 'Failed to submit inquiry. Please try again later.');</script>";
        exit();
    }
    $stmt_insert->bind_param("iiis", $p_id, $u_id, $aid, $message);

    if ($stmt_insert->execute()) {
        // Send email to the agent
        $email_subject = "New Property Inquiry";
        $email_message = "You have received a new inquiry for property ID: $p_id. <br> Message: $message";

        if (sendOTPEmail($agent_email, $email_subject, $email_message)) {
            echo "<script>showAlert('success', 'Success', 'Inquiry submitted successfully and email sent to the agent.', 'inquiries.php');</script>";
        } else {
            echo "<script>showAlert('error', 'Submission Failed', 'Inquiry submitted successfully but failed to send email to the agent.');</script>";
        }
    } else {
        error_log("Database Error: " . $stmt_insert->error);
        echo "<script>showAlert('error', 'Submission Failed', 'Failed to submit inquiry. Please try again later.');</script>";
    }
    $stmt_insert->close();
    exit();
}
?>

<?php if ($aid !== null && $agent): ?>
    <!-- Property Inquiry Form - Only show if property is created by an agent -->
    <div class="add-inquiry-form">
        <h4 class="s-property-title">Property Inquiry</h4>
        <form id="inquiryForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?p_id=" . $_GET['p_id']); ?>">
            <input type="hidden" name="form_type" value="inquiry_form">

            <!-- Message -->
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" placeholder="Write your inquiry here..." required></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">Submit Inquiry</button>
        </form>
    </div>
<?php endif; ?>
        </aside>
                    </div>
                </div>

            </div>
        </div>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

        <script src="assets/js/vendor/modernizr-2.6.2.min.js"></script>
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
        <script type="text/javascript" src="assets/js/lightslider.min.js"></script>
        <script src="assets/js/main.js"></script>
        <link rel="stylesheet" type="text/css" href="assets/css/propertydetail.css">
        <script>
            $(document).ready(function () {

                $('#image-gallery').lightSlider({
                    gallery: true,
                    item: 1,
                    thumbItem: 9,
                    slideMargin: 0,
                    speed: 500,
                    auto: true,
                    loop: true,
                    onSliderLoad: function () {
                        $('#image-gallery').removeClass('cS-hidden');
                    }
                });
            });
        </script>
    <?php include("include/footer.php"); ?>
    </body>
</html>