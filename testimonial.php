<?php
include('include/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Basic validation
        if(empty($_POST['content']) || empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['role'])) {
            echo "missing_fields";
            exit;
        }

        // Prepare the data
        $content = htmlspecialchars($_POST['content']);
        $name = htmlspecialchars($_POST['name']);
        $phone = htmlspecialchars($_POST['phone']);
        $role = htmlspecialchars($_POST['role']);
        $status = 0; // Default status (pending)
        
        // Handle image upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                echo "invalid_file_type";
                exit;
            }
            
            // Validate file size (max 2MB)
            if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                echo "file_too_large";
                exit;
            }
            
            $image = file_get_contents($_FILES['image']['tmp_name']);
        }

        // Debug log
        error_log("Attempting to insert testimonial for: " . $name);

        // Prepare the SQL statement
        $sql = "INSERT INTO testimonials (content, name, phone, role, image, status, submitted_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sssssi", $content, $name, $phone, $role, $image, $status);
        
        // Execute the statement
        if ($stmt->execute()) {
            error_log("Testimonial inserted successfully. ID: " . $stmt->insert_id);
            echo "success";
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        error_log("Error in testimonial.php: " . $e->getMessage());
        echo "error: " . $e->getMessage();
    }

    $conn->close();
    exit;
}
?>
<!DOCTYPE html>
<html class="no-js">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GARO ESTATE | Login page</title>
    <meta name="description" content="GARO is a real-estate template">
    <meta name="author" content="Kimarotec">
    <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

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
  
    <!-- Other scripts -->    <style>
        /* [Previous styles remain the same] */
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            display: none;
        }
        .role-group {
            margin: 15px 0;
        }
        .role-group label {
            margin-right: 15px;
            font-weight: normal;
        }
    </style>
</head>
<body>
    <?php include("include/header.php"); ?>
    <div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
    <!-- [Previous header content remains the same] -->

    <div class="testimonial-area">
        <br>
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <div class="box-for overflow">
                    <div  class="col-md-12 col-xs-12 login-blocks">
                        <h2 class="text-center">Submit Your Testimonial</h2> 
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="form-group role-group">
                                <label>Role <span class="text-danger">*</span></label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="role" value="buyer" required> Buyer
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="role" value="seller" required> Seller
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="role" value="agent" required> Agent
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="content">Your Testimonial <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Your Photo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <img id="imagePreview" class="image-preview" src="#" alt="Image preview" />
                                <small class="text-muted">Max file size: 2MB. Allowed formats: JPG, PNG, GIF</small>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-default">Submit Testimonial</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>      
    <br>

    <?php include("include/footer.php"); ?>

    <!-- [Previous script includes remain the same] -->
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

    <script>
    document.getElementById('image').addEventListener('change', function(event) {
        const preview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        
        if (file) {
            // Validate file size
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                preview.style.display = 'none';
                return;
            }

            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                alert('Please upload only JPG, PNG or GIF images');
                this.value = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('testimonialForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Show processing alert
        showAlert('processing', 'Processing...', 'Please wait while we submit your testimonial.');

        const formData = new FormData(this);

        fetch('testimonial.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('customAlert').classList.remove('show');
            
            if (data === 'success') {
                showAlert('success', 'Thank You!', 'Your testimonial has been submitted successfully.', 'index.php');
                this.reset();
                document.getElementById('imagePreview').style.display = 'none';
            } else if (data === 'invalid_file_type') {
                showAlert('error', 'Invalid File', 'Please upload only JPG, PNG or GIF images.');
            } else if (data === 'file_too_large') {
                showAlert('error', 'File Too Large', 'Please select an image less than 2MB in size.');
            } else if (data === 'missing_fields') {
                showAlert('error', 'Missing Information', 'Please fill in all required fields.');
            } else {
                showAlert('error', 'Error', 'There was an error submitting your testimonial. Please try again.');
                console.error('Server response:', data);
            }
        })
        .catch(error => {
            document.getElementById('customAlert').classList.remove('show');
            showAlert('error', 'Error', 'Something went wrong. Please try again.');
            console.error('Fetch error:', error);
        });
    });
    </script>
    <?php include("include/alert.php"); ?>
</body>
</html>