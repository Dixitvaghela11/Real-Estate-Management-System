<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realestate";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle file uploads
function uploadFile($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$fileInputName]['tmp_name'];
        return file_get_contents($tmp_name);
    }
    return null;
}

// Insert into property table
$property_name = $_POST['property_name'] ?? '';
$property_price = $_POST['property_price'] ?? '';
$phone = $_POST['phone'] ?? '';
$description = $_POST['description'] ?? '';
$state = $_POST['state'] ?? '';
$city = $_POST['city'] ?? '';
$status = $_POST['status'] ?? '';
$min_bed = $_POST['min_bed'] ?? '';
$min_baths = $_POST['min_baths'] ?? '';
$property_geo = $_POST['property_geo'] ?? '';
$other_details = $_POST['other_details'] ?? '';
$main_image = uploadFile('main_image');
$terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;
$aid = $_POST['aid'] ?? '';
$admin_id = $_POST['admin_id'] ?? '';
$type = $_POST['type'] ?? '';
$location = $_POST['location'] ?? '';
$min_kitchen = $_POST['min_kitchen'] ?? '';
$min_hall = $_POST['min_hall'] ?? '';
$min_balcony = $_POST['min_balcony'] ?? '';
$bhk = $_POST['bhk'] ?? '';
$floorplanimage = uploadFile('floorplanimage');

$sql = "INSERT INTO property (
    property_name, 
    property_price, 
    phone, 
    description, 
    state, 
    city, 
    status, 
    min_bed, 
    min_baths, 
    property_geo, 
    other_details, 
    main_image, 
    terms_accepted, 
    aid, 
    admin_id, 
    type, 
    location, 
    min_kitchen, 
    min_hall, 
    min_balcony, 
    bhk, 
    floorplanimage
) VALUES (
    '$property_name', 
    '$property_price', 
    '$phone', 
    '$description', 
    '$state', 
    '$city', 
    '$status', 
    '$min_bed', 
    '$min_baths', 
    '$property_geo', 
    '$other_details', 
    '$main_image', 
    '$terms_accepted', 
    '$aid', 
    '$admin_id', 
    '$type', 
    '$location', 
    '$min_kitchen', 
    '$min_hall', 
    '$min_balcony', 
    '$bhk', 
    '$floorplanimage'
)";

if ($conn->query($sql) === TRUE) {
    $property_id = $conn->insert_id;

    // Insert into property_media table for images
    if (!empty($_FILES['property_images']['name'][0])) {
        foreach ($_FILES['property_images']['name'] as $key => $name) {
            $property_image = uploadFile('property_images[' . $key . ']');
            if ($property_image) {
                $sql_media = "INSERT INTO property_media (property_id, property_image) VALUES ('$property_id', '$property_image')";
                $conn->query($sql_media);
            }
        }
    }

    // Insert into property_media table for videos
    if (!empty($_FILES['property_videos']['name'][0])) {
        foreach ($_FILES['property_videos']['name'] as $key => $name) {
            $property_video = uploadFile('property_videos[' . $key . ']');
            if ($property_video) {
                $sql_media = "INSERT INTO property_media (property_id, property_video) VALUES ('$property_id', '$property_video')";
                $conn->query($sql_media);
            }
        }
    }

    echo "Property and media added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>