<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realestatephp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file uploads and convert them to binary data
function handleFileUpload($file) {
    if (isset($file) && $file['error'] == 0) {
        return file_get_contents($file['tmp_name']);
    }
    return null;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $pcontent = $_POST['pcontent'];
    $type = $_POST['type'];
    $bhk = $_POST['bhk'];
    $stype = $_POST['stype'];
    $bedroom = $_POST['bedroom'];
    $bathroom = $_POST['bathroom'];
    $balcony = $_POST['balcony'];
    $kitchen = $_POST['kitchen'];
    $hall = $_POST['hall'];
    $floor = $_POST['floor'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $feature = $_POST['feature'];
    $pimage = handleFileUpload($_FILES['pimage']);
    $pimage1 = handleFileUpload($_FILES['pimage1']);
    $pimage2 = handleFileUpload($_FILES['pimage2']);
    $pimage3 = handleFileUpload($_FILES['pimage3']);
    $pimage4 = handleFileUpload($_FILES['pimage4']);
    $mapimage = handleFileUpload($_FILES['mapimage']);
    $topmapimage = handleFileUpload($_FILES['topmapimage']);
    $groundmapimage = handleFileUpload($_FILES['groundmapimage']);
    $totalfloor = $_POST['totalfloor'];
    $status = $_POST['status'];
    $isFeatured = isset($_POST['isFeatured']) ? 1 : 0;
    $uid = $_POST['uid']; // Assuming you have a way to get the UID

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Properties (title, pcontent, type, bhk, stype, bedroom, bathroom, balcony, kitchen, hall, floor, size, price, location, city, state, feature, pimage, pimage1, pimage2, pimage3, pimage4, mapimage, topmapimage, groundmapimage, totalfloor, status, isFeatured, uid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiissisissssssssssssisii", $title, $pcontent, $type, $bhk, $stype, $bedroom, $bathroom, $balcony, $kitchen, $hall, $floor, $size, $price, $location, $city, $state, $feature, $pimage, $pimage1, $pimage2, $pimage3, $pimage4, $mapimage, $topmapimage, $groundmapimage, $totalfloor, $status, $isFeatured, $uid);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: success_page.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Property</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        form {
            width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin: 15px 0 5px;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="file"] {
            margin: 5px 0 10px;
        }
        input[type="checkbox"] {
            margin: 5px 5px 10px 0;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Insert Property</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Content:</label>
        <textarea name="pcontent" required></textarea>

        <label>Type:</label>
        <input type="text" name="type" required>

        <label>BHK:</label>
        <input type="text" name="bhk" required>

        <label>Subtype:</label>
        <input type="text" name="stype" required>

        <label>Bedroom:</label>
        <input type="number" name="bedroom" required>

        <label>Bathroom:</label>
        <input type="number" name="bathroom" required>

        <label>Balcony:</label>
        <input type="number" name="balcony" required>

        <label>Kitchen:</label>
        <input type="number" name="kitchen" required>

        <label>Hall:</label>
        <input type="number" name="hall" required>

        <label>Floor:</label>
        <input type="text" name="floor" required>

        <label>Size (sq ft):</label>
        <input type="number" name="size" required>

        <label>Price:</label>
        <input type="text" name="price" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>City:</label>
        <input type="text" name="city" required>

        <label>State:</label>
        <input type="text" name="state" required>

        <label>Features:</label>
        <textarea name="feature" required></textarea>

        <label>Main Image:</label>
        <input type="file" name="pimage" required>

        <label>Image 1:</label>
        <input type="file" name="pimage1" required>

        <label>Image 2:</label>
        <input type="file" name="pimage2" required>

        <label>Image 3:</label>
        <input type="file" name="pimage3" required>

        <label>Image 4:</label>
        <input type="file" name="pimage4" required>

        <label>Map Image:</label>
        <input type="file" name="mapimage" required>

        <label>Top Map Image:</label>
        <input type="file" name="topmapimage" required>

        <label>Ground Map Image:</label>
        <input type="file" name="groundmapimage" required>

        <label>Total Floor:</label>
        <input type="text" name="totalfloor" required>

        <label>Status:</label>
        <input type="text" name="status" required>

        <label>Is Featured:</label>
        <input type="checkbox" name="isFeatured" value="1"><br><br>

        <label>UID:</label>
        <input type="number" name="uid" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>