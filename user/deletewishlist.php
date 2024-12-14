<?php
session_start(); // Start the session

// Check if the agent is logged in
if (!isset($_SESSION['user_id'])) {
    // Destroy the session and redirect to login page
    session_unset();
    session_destroy();
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

// Get the property ID from the query string
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Property ID not provided']);
    exit();
}

$property_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Prepare and execute the delete query
$query = "DELETE FROM wishlist WHERE uid = ? AND p_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $property_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Property removed from wishlist successfully', 'redirect' => 'index.php']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove property from wishlist']);
}

$stmt->close();
$conn->close();
?>