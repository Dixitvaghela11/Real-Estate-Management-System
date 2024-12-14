<?php
session_start();

if (!isset($_SESSION['agent_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

$agent_id = $_SESSION['agent_id'];

$data = json_decode(file_get_contents('php://input'), true);
$enquiry_id = $data['enquiry_id'];
$status = $data['status'];

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "realestate";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the status
$query = "UPDATE property_enquiries SET status = ? WHERE enquiry_id = ? AND aid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $status, $enquiry_id, $agent_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$conn->close();
?>