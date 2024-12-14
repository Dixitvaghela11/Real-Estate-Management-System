<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "realestate";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['uid']) && isset($_POST['p_id'])) {
    $uid = intval($_POST['uid']);  // Agent ID from session
    $p_id = intval($_POST['p_id']);  // Property ID from request

    // Check if the property is already in the wishlist
    $checkQuery = "SELECT * FROM wishlist WHERE uid = ? AND p_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $uid, $p_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Property already in wishlist
        echo json_encode(['status' => 'exists']);
    } else {
        // Insert into wishlist
        $query = "INSERT INTO wishlist (uid, p_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $uid, $p_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session or property ID']);
}

$conn->close();
?>
