<?php
// Start the session
session_start();

// Check if the agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

// Assuming you have a database connection established
$conn = mysqli_connect("localhost", "root", "", "realestate");

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the agent's name from the database
$agent_id = $_SESSION['agent_id'];
$query = "SELECT * FROM agents WHERE aid = $agent_id";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
if ($row) {
    $agent_name = $row['aname'];
    $agent_aemail = $row['aemail'];
    $phone = $row['aphone'];
    $facebook = $row['facebook'];
    $twitter = $row['twitter'];
    $instagram = $row['instagram'];
    $linkedin = $row['linkedin'];
    $office_add = $row['office_address'];
    $content = $row['content'];
    $dob = $row['dob'];
    $languages = $row['Languages'];
    $specialization = $row['Specialization'];
    $experience = $row['Experience'];
    $whatsappnumber = $row['whatsappnumber'];
    $aimage = $row['aimage'];
} else {
    $agent_name = "Unknown Agent";
}

// Retrieve past projects for the agent
$query = "SELECT * FROM past_projects WHERE agent_id = $agent_id";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

$past_projects = [];

while ($row = mysqli_fetch_assoc($result)) {
    $past_projects[] = $row;
}

// Retrieve project images for each project
foreach ($past_projects as &$project) {
    $project_id = $project['id'];
    $query = "SELECT * FROM project_images WHERE project_id = $project_id";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if (!$result) {
        die("Error executing query: " . mysqli_error($conn));
    }

    $project['images'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $project['images'][] = $row['image'];
    }
}

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Real ESTATE | Home page</title>
<meta name="description" content="GARO is a real-estate template">
<meta name="author" content="Kimarotec">
<meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

<!-- Place favicon.ico  the root directory -->
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
        <h1 style="display: inline-block; color: #1e40af; font-size: 4rem; margin-right: 20px;">Agent Profile</h1>
<a href="edit_profile.php" class="btn" style="float: right; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Edit Profile</a>

        <div class="profile-header">
            <div class="profile-image">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($aimage); ?>" alt="John Doe">
            </div>
            <h2 class="profile-name"><?php echo $agent_name; ?></h2>
        </div>
        <div class="profile-section">
            <h2>Contact Information</h2>
            <div class="info-item">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo $agent_aemail; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone:</div>
                <div class="info-value"><?php echo $phone; ?> ,</div>
            </div>
            <div class="info-item">
                <div class="info-label">Whatsapp Number:</div>
                <div class="info-value"><?php echo (!empty($whatsappnumber)) ? $whatsappnumber : "<b>N/A</b>"; ?></div>
            </div>

            <div class="info-item">
                <div class="info-label">Location:</div>
                <div class="info-value"><?php echo $office_add; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Content:</div>
                <div class="info-value"><?php echo $content; ?></div>
            </div>
            <div class="social-links">
    <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank">
        <img src="assets/img/icon/social/facebook.png" alt="Facebook">
    </a>
    <a href="<?php echo htmlspecialchars($twitter); ?>" target="_blank">
        <img src="assets/img/icon/social/twitter.png" alt="Twitter">
    </a>
    <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank">
        <img src="assets/img/icon/social/instagram.png" alt="Instagram">
    </a>
    <a href="<?php echo htmlspecialchars($linkedin); ?>" target="_blank">
        <img src="assets/img/icon/social/linkedin.png" alt="LinkedIn">
    </a>
</div>

        </div>
        <div class="profile-section">
            <h2>Personal Details</h2>
            <div class="info-item">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value">
    <?php echo (!empty($dob)) ? $dob : "dd/mm/yyyy"; ?>
</div>
            </div>
            <div class="info-item">
                <div class="info-label">Languages:</div>
                <div class="info-value"><?php echo (!empty($languages)) ? $languages : "<b>N/A</b>"; ?></div>
            </div>
        </div>
        <div class="profile-section">
            <h2>Professional Information</h2>
            <div class="info-item">
                <div class="info-label">Specialization:</div>
                <div class="info-value"><?php echo (!empty($specialization)) ? $specialization : "<b>N/A</b>"; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Experience:</div>
                <div class="info-value"><?php echo (!empty($experience)) ? $experience : "<b>N/A</b>"; ?></div>
            </div>
        </div>
        <div class="profile-section">
    <h2>Past Projects</h2>

    <!-- Project 1 -->
    <div class="past-projects">
    <?php foreach ($past_projects as $project): ?>
        <div class="project-item">
            <h3><?php echo htmlspecialchars($project['project_name']); ?></h3>
            <p><?php echo htmlspecialchars($project['project_description']); ?>.</p>
            <p>Start Date: <?php echo htmlspecialchars($project['start_date']); ?></p>
            <p>End Date: <?php echo htmlspecialchars($project['end_date']); ?></p>

            <!-- Image Slider for Project 1 -->
            <div class="project-images">
            <div class="slider-container">
                    <?php foreach ($project['images'] as $image): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" alt="Project Image 1" class="slider-image active">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($image); ?>" alt="Project Image 2" class="slider-image">
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-edit">Edit</a>
            <button class="btn btn-delete" data-id="<?php echo $project['id']; ?>">Delete</button>
        </div>
        <?php endforeach; ?>

    </div>

    <a href="add_project.php" class="btn">Add Project</a>
</div>

<!-- Full-Screen Image Modal -->
<div id="fullScreenModal" class="fullscreen-modal">
    <span class="close" onclick="closeFullScreen()">×</span>
    <img class="fullscreen-image" id="fullScreenImage" />
</div>

<!-- Add your styles and JS below -->

<style>


    .past-projects {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 projects per row */
        gap: 20px; /* Space between the projects */
        margin-bottom: 20px;
    }

    .project-item {
        border: 2px solid #ddd;
        padding: 15px;
        background-color: #cfcccc;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .project-item h3 {
        margin-top: 0;
        font-size: 1.2em;
    }

    .project-item p {
        margin: 5px 0;
        font-size: 1em;
    }

    /* Adjusting the height for slider images */
    .slider-container {
        position: relative;
        width: 100%;
        height: 200px; /* Set a fixed height */
        overflow: hidden; /* Hide any overflowed images */
    }

    .slider-image {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensure images cover the container area */
        position: absolute;
        top: 0;
        left: 100%;
        transition: left 1s ease;
    }

    .slider-image.active {
        left: 0;
    }

    .slider-image.next {
        left: -100%;
    }

    .project-item a.btn, .project-item button.btn {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 15px;
        font-size: 0.9em;
        text-decoration: none;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .project-item button.btn {
        background-color: #dc3545;
    }

    .project-item a.btn:hover, .project-item button.btn:hover {
        opacity: 0.8;
    }

    .profile-section .btn {
        background-color: #28a745;
        text-decoration: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        margin-top: 20px;
        display: inline-block;
    }

    .profile-section .btn:hover {
        opacity: 0.8;
    }

    /* Fullscreen Modal */
    .fullscreen-modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        background-color: rgba(0, 0, 0, 0.8); /* Background color with transparency */
        align-items: center;
        justify-content: center;
    }

    .fullscreen-modal img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }

    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #f1f1f1;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<script>
    // Function to slide through images for each project
    function slideProjectImages(projectSelector, intervalTime) {
        let currentIndex = 0;
        let images = document.querySelectorAll(`${projectSelector} .slider-image`);
        let totalImages = images.length;

        // Function to show the current image
        function showSlide() {
            images.forEach((img) => img.classList.remove('active', 'next'));
            images[currentIndex].classList.add('active');
            let nextIndex = (currentIndex + 1) % totalImages;
            images[nextIndex].classList.add('next');
            currentIndex = nextIndex;
        }

        // Start sliding automatically
        setInterval(showSlide, intervalTime);
    }

    // Initialize sliding for each project
    slideProjectImages('.project-item:nth-child(1)', 3000);  // Project 1 slides every 3 seconds
    slideProjectImages('.project-item:nth-child(2)', 4000);  // Project 2 slides every 4 seconds

    // Open Fullscreen Modal with Image
    function openFullScreen(imgElement) {
        var modal = document.getElementById("fullScreenModal");
        var modalImage = document.getElementById("fullScreenImage");
        
        modal.style.display = "flex"; // Show the modal
        modalImage.src = imgElement.src; // Set the image source to the clicked image
    }

    // Close Fullscreen Modal
    function closeFullScreen() {
        var modal = document.getElementById("fullScreenModal");
        modal.style.display = "none"; // Hide the modal
    }
</script>
        </div>
    </div>
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
    <?php include 'include/alert.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.btn-delete').click(function(e) {
            e.preventDefault();
            const projectId = $(this).data('id');
            const projectElement = $(this).closest('.project-item');
            
            // Show confirmation alert
            showAlert(
                'warning',
                'Delete Project',
                'Are you sure you want to delete this project? This action cannot be undone.',
                null // No redirect URL
            );

            // Handle OK button click for deletion
            document.getElementById('alertOkButton').onclick = function() {
                // Send delete request
                $.ajax({
                    url: 'delete_project.php',
                    type: 'POST',
                    data: { project_id: projectId },
                    success: function(response) {
                        if (response === 'success') {
                            // Show success message
                            showAlert(
                                'success',
                                'Success!',
                                'Project deleted successfully.',
                                'profile.php' // Redirect to refresh the page
                            );
                            
                            // Remove the project element from the page
                            projectElement.fadeOut(400);
                        } else if (response === 'unauthorized') {
                            showAlert(
                                'error',
                                'Error!',
                                'You are not authorized to delete this project.',
                                null
                            );
                        } else {
                            showAlert(
                                'error',
                                'Error!',
                                'Failed to delete the project. Please try again.',
                                null
                            );
                        }
                    },
                    error: function() {
                        showAlert(
                            'error',
                            'Error!',
                            'An error occurred while deleting the project.',
                            null
                        );
                    }
                });
            };

            // Handle Cancel button click
            document.getElementById('alertCancelButton').onclick = function() {
                document.getElementById('customAlert').classList.remove('show');
            };
        });
    });
    </script>
</body>
</html>