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

// Function to retrieve property data
function getPropertyData($property_id) {
    global $conn;
    $sql = "SELECT * FROM property WHERE p_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Check if property ID is provided
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $property_data = getPropertyData($property_id);
} else {
    $_SESSION['error_message'] = "Property ID not provided.";
    header("Location: addpro.php");
    exit();
}

// Handle form submission
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
            header("Location: propertyedit.php?id=$property_id");
            exit();
        }
    }

    // Handle file uploads
    $main_image = $property_data['main_image'];
    $floorplanimage = $property_data['floorplanimage'];

    if (!empty($_FILES['main_image']['tmp_name'])) {
        $main_image = file_get_contents($_FILES['main_image']['tmp_name']);
    }

    if (!empty($_FILES['floorplanimage']['tmp_name'])) {
        $floorplanimage = file_get_contents($_FILES['floorplanimage']['tmp_name']);
    }

    // Update property table
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

    $sql = "UPDATE property SET 
            property_name = ?, property_price = ?, phone = ?, description = ?, location = ?, state = ?, city = ?, status = ?, type = ?, 
            min_bed = ?, min_baths = ?, min_kitchen = ?, min_hall = ?, min_balcony = ?, bhk = ?, property_geo = ?, other_details = ?, 
            main_image = ?, floorplanimage = ?, terms_accepted = ?, floor = ?, total_floor = ? 
            WHERE p_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssssssssiiiiisssbissi", $property_name, $property_price, $phone, $description, $location, $state, $city, $status, $type, $min_bed, $min_baths, $min_kitchen, $min_hall, $min_balcony, $bhk, $property_geo, $other_details, $main_image, $floorplanimage, $terms_accepted, $floor, $total_floor, $property_id);

    // Send binary data for images
    $stmt->send_long_data(17, $main_image);  // Index 17 corresponds to 'main_image'
    $stmt->send_long_data(18, $floorplanimage);  // Index 18 corresponds to 'floorplanimage'

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Property updated successfully!";
        header("Location: propertyedit.php?id=$property_id");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
        header("Location: propertyedit.php?id=$property_id");
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
    <title>LM HOMES | Update Property</title>
    
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
                        <h3 class="page-title">Update Property</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Update Property</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Property Details</h4>
                        </div>
                        <form action="propertyedit.php?id=<?php echo $property_id; ?>" method="post" id="propertyForm" enctype="multipart/form-data">
                            <div class="card-body">
                                <h5 class="card-title">Property Detail</h5>
                                
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Property Name:</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="property_name" required placeholder="Enter property name" value="<?php echo $property_data['property_name']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Description:</label>
                                            <div class="col-lg-9">
                                                <textarea class="tinymce form-control" name="description" rows="10" cols="30"><?php echo $property_data['description']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Property Type</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="type">
                                                    <option value="">Select Type</option>
                                                    <option value="apartment" <?php echo $property_data['type'] == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                                    <option value="flat" <?php echo $property_data['type'] == 'flat' ? 'selected' : ''; ?>>Flat</option>
                                                    <option value="building" <?php echo $property_data['type'] == 'building' ? 'selected' : ''; ?>>Building</option>
                                                    <option value="house" <?php echo $property_data['type'] == 'house' ? 'selected' : ''; ?>>House</option>
                                                    <option value="villa" <?php echo $property_data['type'] == 'villa' ? 'selected' : ''; ?>>Villa</option>
                                                    <option value="office" <?php echo $property_data['type'] == 'office' ? 'selected' : ''; ?>>Office</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Selling Type</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="status">
                                                    <option value="">Select Status</option>
                                                    <option value="rent" <?php echo ($property_data['status'] == 'rent') ? 'selected' : ''; ?>>Rent</option>
                                                    <option value="sale" <?php echo ($property_data['status'] == 'sale') ? 'selected' : ''; ?>>Sale</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Bathroom</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_baths" required placeholder="Enter Bathroom (only no 1 to 10)" value="<?php echo $property_data['min_baths']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kitchen</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_kitchen" required placeholder="Enter Kitchen (only no 1 to 10)" value="<?php echo $property_data['min_kitchen']; ?>">
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="col-xl-6">
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-3 col-form-label">BHK</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="bhk">
                                                    <option value="">Select BHK</option>
                                                    <option value="1 BHK" <?php echo $property_data['bhk'] == '1 BHK' ? 'selected' : ''; ?>>1 BHK</option>
                                                    <option value="2 BHK" <?php echo $property_data['bhk'] == '2 BHK' ? 'selected' : ''; ?>>2 BHK</option>
                                                    <option value="3 BHK" <?php echo $property_data['bhk'] == '3 BHK' ? 'selected' : ''; ?>>3 BHK</option>
                                                    <option value="4 BHK" <?php echo $property_data['bhk'] == '4 BHK' ? 'selected' : ''; ?>>4 BHK</option>
                                                    <option value="5 BHK" <?php echo $property_data['bhk'] == '5 BHK' ? 'selected' : ''; ?>>5 BHK</option>
                                                    <option value="1,2 BHK" <?php echo $property_data['bhk'] == '1,2 BHK' ? 'selected' : ''; ?>>1,2 BHK</option>
                                                    <option value="2,3 BHK" <?php echo $property_data['bhk'] == '2,3 BHK' ? 'selected' : ''; ?>>2,3 BHK</option>
                                                    <option value="2,3,4 BHK" <?php echo $property_data['bhk'] == '2,3,4 BHK' ? 'selected' : ''; ?>>2,3,4 BHK</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Bedroom</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_bed" required placeholder="Enter Bedroom  (only no 1 to 10)" value="<?php echo $property_data['min_bed']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Balcony</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_balcony" required placeholder="Enter Balcony  (only no 1 to 10)" value="<?php echo $property_data['min_balcony']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Hall</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="min_hall" required placeholder="Enter Hall  (only no 1 to 10)" value="<?php echo $property_data['min_hall']; ?>">
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
                                                    <?php
                                                    $floor_options = array("1st Floor", "2nd Floor", "3rd Floor", "4th Floor", "5th Floor");
                                                    foreach ($floor_options as $floor) {
                                                        echo "<option value='" . $floor . "' " . 
                                                             ($property_data['floor'] == $floor ? 'selected' : '') . 
                                                             ">" . $floor . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Price</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="property_price" required placeholder="Enter Price" value="<?php echo $property_data['property_price']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">City</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="city" required placeholder="Enter City" value="<?php echo $property_data['city']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">State</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="state" required placeholder="Enter State" value="<?php echo $property_data['state']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Total Floor</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" required name="total_floor">
                                                    <option value="">Select Total Floor</option>
                                                    <?php
                                                    for ($i = 1; $i <= 15; $i++) {
                                                        $floor_text = $i . " Floor";
                                                        echo "<option value='" . $floor_text . "' " . 
                                                             ($property_data['total_floor'] == $floor_text ? 'selected' : '') . 
                                                             ">" . $floor_text . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Phone No:</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="phone" required placeholder="Enter Phone No" value="<?php echo $property_data['phone']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Area Size</label>
                                            <div class="col-lg-9">
                                                <input type="number" class="form-control" name="property_geo" required placeholder="Enter Area Size (in sqrt)" value="<?php echo $property_data['property_geo']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Address</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="location" required placeholder="Enter Address" value="<?php echo $property_data['location']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Feature</label>
                                    <div class="col-lg-9">
                                        <p class="alert alert-danger">* Important Please Do Not Remove Below Content Only Change <b>Yes</b> Or <b>No</b> or Details and Do Not Add More Details</p>
                                        
                                        <textarea class="tinymce form-control" name="other_details" rows="10" cols="30"><?php echo $property_data['other_details']; ?></textarea>
                                    </div>
                                </div>
                                        
                                <h4 class="card-title">Image & Status</h4>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Main Image</label>
                                            <div class="col-lg-9">
                                                <input class="form-control" name="main_image" type="file">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Floor Plan Image:</label>
                                            <div class="col-lg-9">
                                                <input class="form-control" name="floorplanimage" type="file">
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
                                                    <option value="available" <?php echo $property_data['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                                    <option value="sold out" <?php echo $property_data['status'] == 'sold out' ? 'selected' : ''; ?>>Sold Out</option>
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
                                                <input class="" type="checkbox" name="terms_accepted" <?php echo $property_data['terms_accepted'] == 1 ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="submit" value="Update" class="btn btn-primary" name="submit" style="margin-left:200px;">
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