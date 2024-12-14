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

$agent_id = $_SESSION['agent_id'];

// Handle form submission for adding a new project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $project_date = $_POST['project_date'];

    // Insert project details
    $insert_project_query = "INSERT INTO past_projects (agent_id, project_name, project_description, project_date) VALUES (?, ?, ?, ?)";
    $insert_project_stmt = $conn->prepare($insert_project_query);
    $insert_project_stmt->bind_param("isss", $agent_id, $project_name, $project_description, $project_date);
    $insert_project_stmt->execute();

    $project_id = $insert_project_stmt->insert_id;

    // Handle image uploads
    if (!empty($_FILES['project_images']['name'][0])) {
        foreach ($_FILES['project_images']['tmp_name'] as $key => $tmp_name) {
            $image = file_get_contents($tmp_name);
            $insert_image_query = "INSERT INTO project_images (project_id, image) VALUES (?, ?)";
            $insert_image_stmt = $conn->prepare($insert_image_query);
            $insert_image_stmt->bind_param("is", $project_id, $image);
            $insert_image_stmt->execute();
        }
    }

    $response['message'] = "Project added successfully.";
}

// Handle form submission for editing a project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_project'])) {
    $project_id = $_POST['project_id'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $project_date = $_POST['project_date'];

    // Update project details
    $update_project_query = "UPDATE past_projects SET project_name = ?, project_description = ?, project_date = ? WHERE id = ?";
    $update_project_stmt = $conn->prepare($update_project_query);
    $update_project_stmt->bind_param("sssi", $project_name, $project_description, $project_date, $project_id);
    $update_project_stmt->execute();

    // Handle image uploads
    if (!empty($_FILES['project_images']['name'][0])) {
        foreach ($_FILES['project_images']['tmp_name'] as $key => $tmp_name) {
            $image = file_get_contents($tmp_name);
            $insert_image_query = "INSERT INTO project_images (project_id, image) VALUES (?, ?)";
            $insert_image_stmt = $conn->prepare($insert_image_query);
            $insert_image_stmt->bind_param("is", $project_id, $image);
            $insert_image_stmt->execute();
        }
    }

    $response['message'] = "Project updated successfully.";
}

// Handle project deletion
if (isset($_GET['delete_project'])) {
    $project_id = $_GET['delete_project'];

    // Delete project images
    $delete_images_query = "DELETE FROM project_images WHERE project_id = ?";
    $delete_images_stmt = $conn->prepare($delete_images_query);
    $delete_images_stmt->bind_param("i", $project_id);
    $delete_images_stmt->execute();

    // Delete project
    $delete_project_query = "DELETE FROM past_projects WHERE id = ?";
    $delete_project_stmt = $conn->prepare($delete_project_query);
    $delete_project_stmt->bind_param("i", $project_id);
    $delete_project_stmt->execute();

    $response['message'] = "Project deleted successfully.";
}

// Retrieve past projects
$projects_query = "SELECT * FROM past_projects WHERE agent_id = ?";
$projects_stmt = $conn->prepare($projects_query);
$projects_stmt->bind_param("i", $agent_id);
$projects_stmt->execute();
$projects_result = $projects_stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Past Projects</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.5/dist/sweetalert2.min.css">

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
    .past-projects-area {
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
    .project-image {
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
                    <h1 class="page-title animate__animated animate__fadeInDown"><a style="color: white;" href="index.php">Home</a> | Past Projects</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->
 
    <!-- past-projects-area -->
    <div class="past-projects-area">
        <br>
        <div class="container">
            <div class="col-md-8 col-md-offset-2">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 past-projects-blocks">
                        <h2 class="text-center">Add Past Project</h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
                        <form action="past_projects.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="project_name">Project Name</label>
                                <input type="text" class="form-control" id="project_name" name="project_name" required>
                            </div>
                            <div class="form-group">
                                <label for="project_description">Project Description</label>
                                <textarea class="form-control" id="project_description" name="project_description" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="project_date">Project Date</label>
                                <input type="date" class="form-control" id="project_date" name="project_date" required>
                            </div>
                            <div class="form-group">
                                <label for="project_images">Project Images</label>
                                <input type="file" class="form-control" id="project_images" name="project_images[]" multiple>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-default" name="add_project">Add Project</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.5/dist/sweetalert2.min.js"></script>
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.href;
                }
            });
            return false;
        }
    </script>

    <?php include("include/footer.php"); ?>
</body>
</html>