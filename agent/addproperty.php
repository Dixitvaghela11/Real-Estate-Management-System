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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve agent ID from session
    $agent_id = $_SESSION['agent_id'];

    // Retrieve form data with checks for undefined keys
    $property_name = $_POST['property_name'] ?? '';
    $property_price = $_POST['property_price'] ?? '';
    $property_geo = ($_POST['property_geo']) ?? ''; // Boolean check
    $phone = $_POST['phone'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $status = $_POST['status'] ?? '';
    $type = $_POST['type'] ?? '';
    $min_bed = $_POST['min_bed'] ?? 0;
    $min_baths = $_POST['min_baths'] ?? 0;
    $min_kitchen = $_POST['min_kitchen'] ?? 0;
    $min_hall = $_POST['min_hall'] ?? 0;
    $min_balcony = $_POST['min_balcony'] ?? 0;
    $bhk = $_POST['bhk'] ?? '';
    $other_details = $_POST['other_details'] ?? '';
    $floor = $_POST['floor'] ?? 0;
    $total_floor = $_POST['total_floor'] ?? 0;
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;

    // Handle file uploads
    $main_image = file_get_contents($_FILES['main_image']['tmp_name']);
    $floorplanimage = file_get_contents($_FILES['floorplanimage']['tmp_name']);

    // Insert data into property table
    $sql_property = "INSERT INTO property (aid, property_name, property_price, property_geo, phone, description, location, state, city, status, type, min_bed, min_baths, min_kitchen, min_hall, min_balcony, bhk, other_details, main_image, floorplanimage, terms_accepted, floor, total_floor) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_property = $conn->prepare($sql_property);
    $stmt_property->bind_param("isdissssssssiiiiisssiis", $agent_id, $property_name, $property_price, $property_geo, $phone, $description, $location, $state, $city, $status, $type, $min_bed, $min_baths, $min_kitchen, $min_hall, $min_balcony, $bhk, $other_details, $main_image, $floorplanimage, $terms_accepted, $floor, $total_floor);

    if ($stmt_property->execute()) {
        $property_id = $stmt_property->insert_id;

        // Insert data into property_media table
        if (!empty($_FILES['photo_data']['name'][0])) {
            foreach ($_FILES['photo_data']['tmp_name'] as $key => $tmp_name) {
                $photo_data = file_get_contents($tmp_name);
                $sql_media = "INSERT INTO property_media (property_id, photo_data) VALUES (?, ?)";
                $stmt_media = $conn->prepare($sql_media);
                $stmt_media->bind_param("is", $property_id, $photo_data);
                $stmt_media->execute();
            }
        }

        if (!empty($_FILES['video_data']['name'][0])) {
            foreach ($_FILES['video_data']['tmp_name'] as $key => $tmp_name) {
                $video_data = file_get_contents($tmp_name);
                $sql_media = "INSERT INTO property_media (property_id, video_data) VALUES (?, ?)";
                $stmt_media = $conn->prepare($sql_media);
                $stmt_media->bind_param("is", $property_id, $video_data);
                $stmt_media->execute();
            }
        }

        echo "<script>showAlert('success', 'Success', 'Property submitted successfully!', 'manageproperty.php');</script>";
    } else {
        echo "<script>showAlert('error', 'Error', 'Error: " . $stmt_property->error . "');</script>";
    }

    $stmt_property->close();
}

// Retrieve agent details
$agent_id = $_SESSION['agent_id'];
$query = "SELECT aemail, aname FROM agents WHERE aid = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $agent = $result->fetch_assoc();
} else {
    echo "Agent not found.";
    exit();
}

$conn->close(); // Close the connection only at the end
?>

<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>GARO ESTATE | Submit property Page</title>
        <meta name="description" content="GARO is a real-estate template">
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
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/icheck.min_all.css">
        <link rel="stylesheet" href="assets/css/price-range.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.css">  
        <link rel="stylesheet" href="assets/css/owl.theme.css">
        <link rel="stylesheet" href="assets/css/owl.transitions.css"> 
        <link rel="stylesheet" href="assets/css/wizard.css"> 
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/responsive.css">

        <!-- Froala Editor CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/froala-editor@4.0.15/css/froala_editor.pkgd.min.css">
    </head>
    <body>
    <?php include('include/header.php'); ?>
        <!-- page reload -->
        <div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
        <!-- End page reload -->
      
        <!-- Body content -->

        <div class="page-head"> 
            <div class="container">
                <div class="row">
                    <div class="page-head-content">
                        <h1 class="page-title">Submit new property</h1>               
                    </div>
                </div>
            </div>
        </div>
        <!-- End page header -->

        <!-- property area -->
        <div class="content-area submit-property" style="background-color: #FCFCFC;">&nbsp;
            <div class="container">
                <div class="clearfix" > 
                    <div class="wizard-container"> 

                        <div class="wizard-card ct-wizard-orange" id="wizardProperty">
                            <form action="" method="POST" enctype="multipart/form-data">                        
                                <div class="wizard-header">
                                    <h3>
                                        <b>Submit</b> YOUR PROPERTY <br>
                                        <small>Fill all form fields to proceed to next step</small>
                                    </h3>
                                </div>

                                <div class="wizard-navigation">
                                    <ul class="nav nav-pills">
                                        <li><a href="#step1" data-toggle="tab">Basic Info</a></li>
                                        <li><a href="#step2" data-toggle="tab">Description</a></li>
                                        <li><a href="#step3" data-toggle="tab">Media</a></li>
                                        <li><a href="#step4" data-toggle="tab">Finish</a></li>
                                    </ul>
                                    <!-- Add a progress bar -->
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                                    </div>
                                </div>

                                <div class="tab-content">

                                    <div class="tab-pane" id="step1">
                                        <div class="row p-b-15  ">
                                            <h4 class="info-text"> Let's start with the basic information (with validation)</h4>
                                            <div class="col-sm-4 col-sm-offset-1">
                                                <div class="picture-container">
                                                    <div class="picture">
                                                        <img src="assets/img/default-property.jpg" class="picture-src" id="wizardPicturePreview" title=""/>
                                                        <input type="file" id="wizard-picture" name="main_image">
                                                    </div> 
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Property name <small>(required)</small></label>
                                                    <input name="property_name" type="text" class="form-control" placeholder="Super villa ...">
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone number <small>(required)</small></label>
                                                    <input name="phone" type="text" class="form-control" placeholder="0123456789">
                                                </div>
                                                <div class="form-group">
                                                    <label>Property price <small>(required)</small></label>
                                                    <input name="property_price" type="text" class="form-control" placeholder="3330000">
                                                </div> 
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Property Type <small>(required)</small></label>
                                                    <select name="type" class="form-control">
                                                        <option>select property</option>
                                                        <option>Apartment</option>
                                                        <option>flat</option>
                                                        <option>House</option>
                                                        <option>Building</option>
                                                        <option>villa</option>
                                                        <option>office</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Area in sqft <small>(required)</small></label>
                                                    <input name="property_geo" type="number" class="form-control" placeholder="5000">
                                                </div>
                                                <div class="form-group">
                                                    <label>Bedrooms <small>(if unknown put 0)</small></label>
                                                    <input name="min_bed" type="number" class="form-control" placeholder="3">
                                                </div>
                                                <div class="form-group">
                                                    <label>Balcony <small>(if unknown put 0)</small></label>
                                                    <input name="min_balcony" type="number" class="form-control" placeholder="1">
                                                </div>
                                                <div class="form-group">
                                                    <label>Hall <small>(if unknown put 0)</small></label>
                                                    <input name="min_hall" type="number" class="form-control" placeholder="1">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Property Status <small>(required)</small></label>
                                                    <select name="status" class="form-control">
                                                        <option>select status</option>
                                                        <option>For Sale</option>
                                                        <option>For Rent</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>BHK <small>(required)</small></label>
                                                    <select name="bhk" class="form-control">
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
                                               <div class="form-group">
                                                    <label>Bathrooms <small>(if unknown put 0)</small></label>
                                                    <input name="min_baths" type="number" class="form-control" placeholder="2">
                                                </div>
                                                <div class="form-group">
                                                    <label>Kitchen <small>(if unknown put 0)</small></label>
                                                    <input name="min_kitchen" type="number" class="form-control" placeholder="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  End step 1 -->

                                    <div class="tab-pane" id="step2">
                                        <h4 class="info-text"> How much your Property is Beautiful ? </h4>
                                        <div class="row">
                                            <div class="col-sm-12"> 
                                                <div class="col-sm-12"> 
                                                    <div class="form-group">
                                                        <label>Property Description :</label>
                                                        <center><textarea style="width: 75%;" name="description" id="description" class="form-control"></textarea>
                                                        </center>
                                                    </div> 
                                                </div> 
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Total Floor :<small>(if unknown put 0)</small></label>
                                                        <input type="text" name="total_floor" class="form-control" placeholder="Total Floor">
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Floor :<small>(if unknown put 0))</small></label>
                                                        <input name="floor" type="text" class="form-control" placeholder="1">
                                                    </div>
                                                </div>
                                                   <div class="col-sm-4"> 
                                                   <div class="form-group">
            <label>Property State:</label>
            <input type="text" name="state" id="State" class="form-control" placeholder="State" onfocusout="validateStateCity()">
            <div class="suggestions" id="stateSuggestions"></div>
        </div>
                                                   </div>
                                                   <div class="col-sm-4">
        <!-- Property City -->
        <div class="form-group">
            <label>Property City:</label>
            <input type="text" name="city" id="City" class="form-control" placeholder="City" onfocusout="validateStateCity()">
            <div class="suggestions" id="citySuggestions"></div>
        </div>
    </div>

                                           <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Property Location :</label>
                                                    <input name="location" type="text" class="form-control" placeholder="property location">
                                                </div>
                                            </div><center>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Feature:</label>
                                                    <p style="width: 75%;" class="alert alert-danger">* Important Please Do Not Remove Below Content Only Change <b>Yes</b> Or <b>No</b> or Details and Do Not Add More Details</p>

                                                    <textarea style="width: 75%;" class="form-control" name="other_details" id="other_details" rows="10" cols="30">
<div class="col-md-12">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Property Age</strong></td>
                <td>10 Years</td>
            </tr>
            <tr>
                <td><strong>Swimming Pool</strong></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td><strong>Parking</strong></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td><strong>GYM</strong></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td><strong>Type</strong></td>
                <td>Apartment</td>
            </tr>
            <tr>
                <td><strong>Security</strong></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td><strong>Dining Capacity</strong></td>
                <td>10 People</td>
            </tr>
            <tr>
                <td><strong>Church/Temple</strong></td>
                <td>No</td>
            </tr>
            <tr>
                <td><strong>3rd Party</strong></td>
                <td>No</td>
            </tr>
            <tr>
                <td><strong>Elevator</strong></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td><strong>CCTV</strong></td>
                <td>Yes</td>
            </tr>
            <tr>
                <td><strong>Water Supply</strong></td>
                <td>Ground Water / Tank</td>
            </tr>
        </tbody>
    </table>
</div>

                                        </textarea>
                                                </div>
                                            </div></center>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                    <!-- End step 2 -->

                                    <div class="tab-pane" id="step3">                                        
                                        <h4 class="info-text">Give us somme images and videos ? </h4>
                                        <div class="row">  
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="property-images">Property images :</label>
                                                    <input class="form-control" type="file" id="property-images" name="photo_data[]" multiple>
                                                    <p class="help-block">Select multiple images for your property .</p>
                                                </div>
                                                <div class="form-group">
                                                    <label for="property-images">floor plan images :</label>
                                                    <input class="form-control" type="file" id="floorplanimage" name="floorplanimage">
                                                </div>
                                            </div>
                                            <div class="col-sm-6"> 
                                            <div class="form-group">
                                                    <label for="property-images">Property Videos :</label>
                                                    <input class="form-control" type="file" id="property-videos" name="video_data[]" multiple>
                                                    <p class="help-block">Select multiple videos for your property .</p>
                                                </div>
                                           </div>
                                        </div>
                                    </div>
                                    <!--  End step 3 -->


                                    <div class="tab-pane" id="step4">                                        
                                        <h4 class="info-text"> Finished and submit </h4>
                                        <div class="row">  
                                            
                                            <div class="col-sm-12">
                                                <div class="">
                                                    <p>
                                                        <label><strong>Terms and Conditions</strong></label>
                                                        By accessing or using  GARO ESTATE services, such as 
                                                        posting your property advertisement with your personal 
                                                        information on our website you agree to the
                                                        collection, use and disclosure of your personal information 
                                                        in the legal proper manner
                                                    </p>

                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="terms_accepted" /> <strong>Accept terms and conditions.</strong>
                                                        </label>
                                                    </div> 

                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <!--  End step 4 -->

                                </div>

                                <div class="wizard-footer">
                                    <div class="pull-right">
                                        <input type='button' class='btn btn-next btn-primary' name='next' value='Next' />
                                        <input type='submit' class='btn btn-finish btn-primary ' name='finish' value='Finish' />
                                    </div>

                                    <div class="pull-left">
                                        <input type='button' class='btn btn-previous btn-default' name='previous' value='Previous' />
                                    </div>
                                    <div class="clearfix"></div>                                            
                                </div>	
                            </form>
                        </div>
                        <!-- End submit form -->
                    </div> 
                </div>
            </div>
        </div>
     
    <!-- Your existing HTML code here -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
            <script>
                // Handle URL hash and navigate to the corresponding step
                document.addEventListener('DOMContentLoaded', function() {
                    var hash = window.location.hash;
                    if (hash) {
                        var step = hash.replace('#', '');
                        navigateToStep(step);
                    }
                });

                function navigateToStep(step) {
                    var wizard = $('#wizardProperty');
                    wizard.bootstrapWizard('show', step);
                }
            </script>
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

        <!-- Froala Editor JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/froala-editor@4.0.15/js/froala_editor.pkgd.min.js"></script>
        <script>
            // Initialize Froala Editor with full customizations
            new FroalaEditor('#description', {
                // Toolbar settings
                toolbarInline: false, // False for fixed toolbar
                toolbarSticky: true, // Sticky toolbar on scroll
                toolbarButtons: [
                    'bold', 'italic', 'underline', 'strikeThrough', '|',
                    'fontFamily', 'fontSize', 'textColor', 'backgroundColor', '|',
                    'align', 'formatOL', 'formatUL', '|',
                    'insertLink', 'insertImage', 'insertTable', 'insertVideo', '|',
                    'undo', 'redo', 'fullscreen', 'html'
                ],

                // Appearance settings
                height: 300, // Set editor height
                theme: 'dark', // Enable dark theme
                placeholderText: 'Type your content here...',

                // Plugins (enabled by default in full package)
                pluginsEnabled: [
                    'align', 'charCounter', 'codeView', 'colors', 'draggable', 
                    'emoticons', 'entities', 'fontFamily', 'fontSize', 'fullscreen', 
                    'image', 'imageTUI', 'inlineStyle', 'lineBreaker', 'link', 
                    'lists', 'paragraphFormat', 'paragraphStyle', 'quote', 
                    'table', 'url', 'video'
                ],

                // Character counter
                charCounterCount: true,
                charCounterMax: 100000000000, // Limit characters to 1000

                // Image upload
                imageUploadURL: '/upload_image', // Replace with your image upload URL
                imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif'],

                // Custom events
                events: {
                    'blur': function () {
                        console.log('Editor lost focus');
                    },
                    'focus': function () {
                        console.log('Editor gained focus');
                    },
                    'contentChanged': function () {
                        console.log('Content was changed!');
                    }
                }
            });

            new FroalaEditor('#other_details', {
                // Toolbar settings
                toolbarInline: false, // False for fixed toolbar
                toolbarSticky: true, // Sticky toolbar on scroll
                toolbarButtons: [
                    'bold', 'italic', 'underline', 'strikeThrough', '|',
                    'fontFamily', 'fontSize', 'textColor', 'backgroundColor', '|',
                    'align', 'formatOL', 'formatUL', '|',
                    'insertLink', 'insertImage', 'insertTable', 'insertVideo', '|',
                    'undo', 'redo', 'fullscreen', 'html'
                ],

                // Appearance settings
                height: 300, // Set editor height
                theme: 'dark', // Enable dark theme
                placeholderText: 'Type your content here...',

                // Plugins (enabled by default in full package)
                pluginsEnabled: [
                    'align', 'charCounter', 'codeView', 'colors', 'draggable', 
                    'emoticons', 'entities', 'fontFamily', 'fontSize', 'fullscreen', 
                    'image', 'imageTUI', 'inlineStyle', 'lineBreaker', 'link', 
                    'lists', 'paragraphFormat', 'paragraphStyle', 'quote', 
                    'table', 'url', 'video'
                ],

                // Character counter
                charCounterCount: true,
                charCounterMax: 1000, // Limit characters to 1000

                // Image upload
                imageUploadURL: '/upload_image', // Replace with your image upload URL
                imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif'],

                // Custom events
                events: {
                    'blur': function () {
                        console.log('Editor lost focus');
                    },
                    'focus': function () {
                        console.log('Editor gained focus');
                    },
                    'contentChanged': function () {
                        console.log('Content was changed!');
                    }
                }
            });
        </script>
        <style>
    .suggestions {
        position: absolute;
        background-color: #fff;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
        display: none;
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        width: 100%;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .suggestions div {
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
        color: #333;
    }

    .suggestions div:hover {
        background-color: #f0f0f0;
        color: #000;
    }

    .suggestions div:active {
        background-color: #ddd;
        color: #111;
    }

    .suggestions::-webkit-scrollbar {
        width: 8px;
    }

    .suggestions::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }

    .suggestions::-webkit-scrollbar-thumb:hover {
        background-color: #999;
    }

    .suggestions div.selected {
        background-color: #007bff;
        color: #fff;
    }

    .suggestions div:not(:last-child) {
        border-bottom: 1px solid #eee;
    }


</style>

        <script src="assets/js/main.js"></script>
        <script src="assets/js/statecity.js"></script>
<?php include("include/footer.php"); ?>
    </body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the wizard
    var wizard = $('#wizardProperty');
    
    // Handle direct URL hash navigation
    var hash = window.location.hash;
    if (hash) {
        var targetStep = parseInt(hash.replace('#step', '')) - 1;
        if (!isNaN(targetStep) && targetStep >= 0 && targetStep < 4) {
            wizard.bootstrapWizard('show', targetStep);
        }
    }

    // Update URL hash when steps change
    wizard.bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'onTabShow': function(tab, navigation, index) {
            var stepNumber = index + 1;
            window.location.hash = 'step' + stepNumber;
            
            // Update progress bar if you have one
            var $total = navigation.find('li').length;
            var $current = index + 1;
            var $percent = ($current / $total) * 100;
            $('#wizardProperty').find('.progress-bar').css({width: $percent + '%'});
        },
        'onNext': function(tab, navigation, index) {
            var form = $('#wizardProperty form');
            
            // Validate current step before proceeding
            var $valid = true;
            if (!validateCurrentStep(index - 1)) {
                $valid = false;
            }
            
            return $valid;
        }
    });

    // Add validation for each step
    function validateCurrentStep(stepIndex) {
        var isValid = true;
        
        switch(stepIndex) {
            case 0: // Step 1 validation
                var propertyName = $('input[name="property_name"]').val();
                var propertyPrice = $('input[name="property_price"]').val();
                var phone = $('input[name="phone"]').val();
                
                if (!propertyName || !propertyPrice || !phone) {
                    showAlert('error', 'Validation Error', 'Please fill in all required fields in Step 1');
                    isValid = false;
                }
                break;
                
            case 1: // Step 2 validation
                var description = $('textarea[name="description"]').val();
                var location = $('input[name="location"]').val();
                
                if (!description || !location) {
                    showAlert('error', 'Validation Error', 'Please fill in all required fields in Step 2');
                    isValid = false;
                }
                break;
                
            case 2: // Step 3 validation
                // Add validation for images/videos if needed
                break;
                
            case 3: // Step 4 validation
                var termsAccepted = $('input[name="terms_accepted"]').is(':checked');
                if (!termsAccepted) {
                    showAlert('error', 'Validation Error', 'Please accept the terms and conditions');
                    isValid = false;
                }
                break;
        }
        
        return isValid;
    }

    // Handle step links in the navigation
    $('.nav-pills a').click(function(e) {
        e.preventDefault();
        var targetStep = $(this).attr('href').replace('#step', '');
        wizard.bootstrapWizard('show', parseInt(targetStep) - 1);
    });

    // Handle form submission
    $('#wizardProperty form').on('submit', function(e) {
        if (!validateCurrentStep(3)) { // Validate final step
            e.preventDefault();
            return false;
        }
        // Form is valid, will submit normally
    });
});
</script>