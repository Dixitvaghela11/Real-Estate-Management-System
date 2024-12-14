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

// Retrieve project ID from the URL
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];
} else {
    die("Project ID not provided.");
}

// Retrieve project details
$query = "SELECT * FROM past_projects WHERE id = ?";
$stmt = $conn->prepare($query);

// Check if the prepare statement was successful
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $project = $result->fetch_assoc();
} else {
    echo "Project not found.";
    exit();
}

// Retrieve project images
$image_query = "SELECT * FROM project_images WHERE project_id = ?";
$image_stmt = $conn->prepare($image_query);

// Check if the prepare statement was successful
if ($image_stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$image_stmt->bind_param("i", $project_id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();

// Check if the query was successful
if ($image_result === false) {
    die("Error executing query: " . $conn->error);
}

$project_images = [];
while ($row = $image_result->fetch_assoc()) {
    $project_images[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Update the project details in the database
    $update_query = "UPDATE past_projects SET project_name = ?, project_description = ?, start_date = ?, end_date = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);

    // Check if the prepare statement was successful
    if ($update_stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $update_stmt->bind_param("ssssi", $project_name, $project_description, $start_date, $end_date, $project_id);

    if ($update_stmt->execute()) {
        // Handle image upload
        if (!empty($_FILES['project_images']['name'][0])) {
            foreach ($_FILES['project_images']['tmp_name'] as $key => $tmp_name) {
                $image = file_get_contents($tmp_name);
                $image_query = "INSERT INTO project_images (project_id, image) VALUES (?, ?)";
                $image_stmt = $conn->prepare($image_query);

                // Check if the prepare statement was successful
                if ($image_stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }

                $image_stmt->bind_param("is", $project_id, $image);
                $image_stmt->execute();
            }
        }

        // Handle image removal
        if (!empty($_POST['remove_images'])) {
            foreach ($_POST['remove_images'] as $image_id) {
                $remove_query = "DELETE FROM project_images WHERE id = ?";
                $remove_stmt = $conn->prepare($remove_query);

                // Check if the prepare statement was successful
                if ($remove_stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }

                $remove_stmt->bind_param("i", $image_id);
                $remove_stmt->execute();
            }
        }

        $response['message'] = "Project updated successfully.";
        // Refresh the project details
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();

        // Refresh the project images
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();
        $project_images = [];
        while ($row = $image_result->fetch_assoc()) {
            $project_images[] = $row;
        }
    } else {
        $response['message'] = "Failed to update project. Please try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Edit Project</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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
    .edit-project-area {
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
    .project-image {
        max-width: 250px;
        height: 250px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
        object-fit: cover;
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
    .remove-image-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }
    .remove-image-btn:hover {
        background-color: #c82333;
    }
</style>
<body>
    <?php include("include/header.php"); ?>
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title animate__animated animate__fadeInDown"><a style="color: white;" href="index.php">Home</a> | Edit Project</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->
 
    <!-- edit-project-area -->
    <div class="edit-project-area">
        <div class="container">
            <div class="col-md-8 col-md-offset-2">
                <div class="box-for overflow">
                    <div class="col-md-12 col-xs-12 edit-project-blocks">
                        <h2 class="text-center">Edit Project</h2> 
                        <?php if (!empty($response['message'])): ?>
                            <div class="alert alert-danger" style="color: red;"><?php echo $response['message']; ?></div>
                        <?php endif; ?>
                        <form action="edit_project.php?id=<?php echo $project_id; ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="project_name">Project Name</label>
                                <input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo htmlspecialchars($project['project_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="project_description">Project Description</label>
                                <textarea class="form-control" id="project_description" name="project_description" rows="5" required><?php echo htmlspecialchars($project['project_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($project['end_date'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="project_images">Project Images</label>
                                <input type="file" class="form-control" id="project_images" name="project_images[]" multiple>
                            </div>
                            <div class="form-group">
                                <label>Current Images</label>
                                <?php foreach ($project_images as $image): ?>
                                    <div style="display: flex; align-items: center;">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($image['image']); ?>" alt="Project Image" class="project-image">
                                        <button type="submit" name="remove_images[]" value="<?php echo $image['id']; ?>" class="remove-image-btn">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Update Project</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
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

    <?php include("include/footer.php"); ?>
</body>
</html>