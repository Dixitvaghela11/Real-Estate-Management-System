<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realestate";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Input validation
    $required_fields = [
        'property_name', 'property_price', 'phone', 'description', 'location', 'state', 'city', 'status', 'type',
        'min_bed', 'min_baths', 'min_kitchen', 'min_hall', 'min_balcony', 'bhk', 'property_geo', 'other_details',
        'floor', 'total_floor'
    ];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field]) && $field !== 'other_details') {
            $_SESSION['error_message'] = "Error: Missing required field - $field";
            header("Location: addpro.php");
            exit();
        }
    }

    // Handle file uploads
    if (empty($_FILES['main_image']['tmp_name']) || empty($_FILES['floorplanimage']['tmp_name'])) {
        $_SESSION['error_message'] = "Error: Main image and floor plan image are required.";
        header("Location: addpro.php");
        exit();
    }

    // Insert into property table
    $property_name = $_POST['property_name'];
    $property_price = $_POST['property_price'];
    $phone = $_POST['phone'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $status = $_POST['status'];
    $type = $_POST['type'];
    $min_bed = $_POST['min_bed'];
    $min_baths = $_POST['min_baths'];
    $min_kitchen = $_POST['min_kitchen'];
    $min_hall = $_POST['min_hall'];
    $min_balcony = $_POST['min_balcony'];
    $bhk = $_POST['bhk'];
    $property_geo = $_POST['property_geo'];
    $other_details = $_POST['other_details'];
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;
    
    // New fields
    $floor = $_POST['floor'];
    $total_floor = $_POST['total_floor'];

    // Ensure files are uploaded correctly
    if (is_uploaded_file($_FILES['main_image']['tmp_name'])) {
        $main_image = file_get_contents($_FILES['main_image']['tmp_name']);
    } else {
        $_SESSION['error_message'] = "Error: Main image upload failed.";
        header("Location: addpro.php");
        exit();
    }

    if (is_uploaded_file($_FILES['floorplanimage']['tmp_name'])) {
        $floorplanimage = file_get_contents($_FILES['floorplanimage']['tmp_name']);
    } else {
        $_SESSION['error_message'] = "Error: Floor plan image upload failed.";
        header("Location: addpro.php");
        exit();
    }

    // SQL Insert statement
    $sql = "INSERT INTO property (property_name, property_price, phone, description, location, state, city, status, type, min_bed, min_baths, min_kitchen, min_hall, min_balcony, bhk, property_geo, other_details, main_image, floorplanimage, terms_accepted, floor, total_floor, admin_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '2')";
    
    $stmt = $conn->prepare($sql);
    // Correct type string
    $stmt->bind_param("sdsssssssiiiiiissssiss", 
        $property_name, 
        $property_price, 
        $phone, 
        $description, 
        $location, 
        $state, 
        $city, 
        $status, 
        $type, 
        $min_bed, 
        $min_baths, 
        $min_kitchen, 
        $min_hall, 
        $min_balcony, 
        $bhk, 
        $property_geo, 
        $other_details,
        $main_image,
        $floorplanimage,
        $terms_accepted,
        $floor,
        $total_floor
    );
    
    if ($stmt->execute()) {
        $property_id = $stmt->insert_id;

        // Insert into property_media table for photos
        if (!empty($_FILES['photo_data']['name'][0])) {
            foreach ($_FILES['photo_data']['tmp_name'] as $key => $tmp_name) {
                $photo_data = file_get_contents($tmp_name);
                $sql_media = "INSERT INTO property_media (property_id, photo_data) VALUES (?, ?)";
                $stmt_media = $conn->prepare($sql_media);
                $stmt_media->bind_param("ib", $property_id, $null);
                $stmt_media->send_long_data(1, $photo_data);
                $stmt_media->execute();
            }
        }

        // Insert into property_media table for videos
        if (!empty($_FILES['video_data']['name'][0])) {
            foreach ($_FILES['video_data']['tmp_name'] as $key => $tmp_name) {
                $video_data = file_get_contents($tmp_name);
                $sql_media = "INSERT INTO property_media (property_id, video_data) VALUES (?, ?)";
                $stmt_media = $conn->prepare($sql_media);
                $stmt_media->bind_param("ib", $property_id, $null);
                $stmt_media->send_long_data(1, $video_data);
                $stmt_media->execute();
            }
        }

        $_SESSION['success_message'] = "Property added successfully!";
        header("Location: addpro.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
        header("Location: addpro.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>LM HOMES | Property</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <!-- Header -->
    <?php include("header.php"); ?>
    <!-- /Sidebar -->
    <br>
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">
        
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Property</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Property</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Property Details</h4>
                        </div>
                        <form action="addpro.php" method="post" id="propertyForm" enctype="multipart/form-data">
                            <div class="card-body">
                                <h5 class="card-title">Property Detail</h5>
                                
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Property Name:</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="property_name" required placeholder="Enter property name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Description:</label>
                                            <div class="col-lg-9">
                                                <textarea class="tinymce form-control" name="description" rows="10" cols="30"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Property Type</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="type">
                                                    <option value="">Select Type</option>
                                                    <option value="apartment">Apartment</option>
                                                    <option value="flat">Flat</option>
                                                    <option value="building">Building</option>
                                                    <option value="house">House</option>
                                                    <option value="villa">Villa</option>
                                                    <option value="office">Office</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Selling Type</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="status">
                                                    <option value="">Select Status</option>
                                                    <option value="rent">Rent</option>
                                                    <option value="sale">Sale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Bathroom</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_baths" required placeholder="Enter Bathroom (only no 1 to 10)">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kitchen</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_kitchen" required placeholder="Enter Kitchen (only no 1 to 10)">
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="col-xl-6">
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-3 col-form-label">BHK</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="bhk">
                                                    <option value="">Select BHK</option>
                                                    <option value="1 BHK">1 BHK</option>
                                                    <option value="2 BHK">2 BHK</option>
                                                    <option value="3 BHK">3 BHK</option>
                                                    <option value="4 BHK">4 BHK</option>
                                                    <option value="5 BHK">5 BHK</option>
                                                    <option value="1,2 BHK">1,2 BHK</option>
                                                    <option value="2,3 BHK">2,3 BHK</option>
                                                    <option value="2,3,4 BHK">2,3,4 BHK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Bedroom</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_bed" required placeholder="Enter Bedroom  (only no 1 to 10)">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Balcony</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_balcony" required placeholder="Enter Balcony  (only no 1 to 10)">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Hall</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_hall" required placeholder="Enter Hall  (only no 1 to 10)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="card-title">Price & Location</h4>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Floor</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="floor">
                                                    <option value="">Select Floor</option>
                                                    <option value="1st Floor">1st Floor</option>
                                                    <option value="2nd Floor">2nd Floor</option>
                                                    <option value="3rd Floor">3rd Floor</option>
                                                    <option value="4th Floor">4th Floor</option>
                                                    <option value="5th Floor">5th Floor</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Price</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="property_price" required placeholder="Enter Price">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">City</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="city" required placeholder="Enter City">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">State</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="state" required placeholder="Enter State">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Total Floor</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="total_floor">
                                                    <option value="">Select Floor</option>
                                                    <option value="1 Floor">1 Floor</option>
                                                    <option value="2 Floor">2 Floor</option>
                                                    <option value="3 Floor">3 Floor</option>
                                                    <option value="4 Floor">4 Floor</option>
                                                    <option value="5 Floor">5 Floor</option>
                                                    <option value="6 Floor">6 Floor</option>
                                                    <option value="7 Floor">7 Floor</option>
                                                    <option value="8 Floor">8 Floor</option>
                                                    <option value="9 Floor">9 Floor</option>
                                                    <option value="10 Floor">10 Floor</option>
                                                    <option value="11 Floor">11 Floor</option>
                                                    <option value="12 Floor">12 Floor</option>
                                                    <option value="13 Floor">13 Floor</option>
                                                    <option value="14 Floor">14 Floor</option>
                                                    <option value="15 Floor">15 Floor</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Phone No:</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="phone" required placeholder="Enter Phone No">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Area Size</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="property_geo" required placeholder="Enter Area Size (in sqrt)">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Address</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="location" required placeholder="Enter Address">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Feature</label>
                                    <div class="col-lg-9">
                                        <p class="alert alert-danger">* Important Please Do Not Remove Below Content Only Change <b>Yes</b> Or <b>No</b> or Details and Do Not Add More Details</p>
                                        
                                        <textarea class="tinymce form-control" name="other_details" rows="10" cols="30">
                                            <!---feature area start--->
                                            <div class="col-md-4">
                                                <ul>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Property Age : </span>10 Years</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Swiming Pool : </span>Yes</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Parking : </span>Yes</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">GYM : </span>Yes</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-4">
                                                <ul>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Type : </span>Apartment</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Security : </span>Yes</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Dining Capacity : </span>10 People</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Church/Temple  : </span>No</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-4">
                                                <ul>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">3rd Party : </span>No</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Alivator : </span>Yes</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">CCTV : </span>Yes</li>
                                                    <li class="mb-3"><span class="text-secondary font-weight-bold">Water Supply : </span>Ground Water / Tank</li>
                                                </ul>
                                            </div>
                                            <!---feature area end---->
                                        </textarea>
                                    </div>
                                </div>
                                        
                                <h4 class="card-title">Image & Status</h4>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Main Image</label>
                                            <div class="col-lg-9">
                                                <input class="form-control" name="main_image" type="file" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Floor Plan Image:</label>
                                            <div class="col-lg-9">
                                                <input class="form-control" name="floorplanimage" type="file" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Property Images:(You Can Add Multiple Images)</label>
                                            <div class="col-lg-9">
                                                <input class="form-control" name="photo_data[]" type="file" multiple>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Property Videos(You Can Add Multiple Videos):</label>
                                            <div class="col-lg-9">
                                                <input class="form-control" type="file" name="video_data[]" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label"><b>Status</b></label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="status">
                                                    <option value="">Select Status</option>
                                                    <option value="available">Available</option>
                                                    <option value="sold out">Sold Out</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label"><b>Term and Condition</b></label>
                                            <div class="col-lg-9">
                                                <input class="" type="checkbox" name="terms_accepted" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="submit" value="Submit" class="btn btn-primary" name="submit" style="margin-left:200px;">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        
        </div>            
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>
    <script src="assets/plugins/tinymce/init-tinymce.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
    <!-- Slimscroll JS -->
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    
    <script>
        $(document).ready(function() {
            <?php if (isset($_SESSION['success_message'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo $_SESSION['success_message']; ?>',
                });
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?php echo $_SESSION['error_message']; ?>',
                });
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>