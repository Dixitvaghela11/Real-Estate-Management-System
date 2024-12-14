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

if (isset($_POST['aid']) && isset($_POST['p_id'])) {
    $aid = intval($_POST['aid']);  // Agent ID from session
    $p_id = intval($_POST['p_id']);  // Property ID from request

    // Check if the property is already in the wishlist
    $checkQuery = "SELECT * FROM wishlist WHERE aid = ? AND p_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $aid, $p_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Property already in wishlist
        echo json_encode(['status' => 'exists']);
    } else {
        // Insert into wishlist
        $query = "INSERT INTO wishlist (aid, p_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $aid, $p_id);

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
