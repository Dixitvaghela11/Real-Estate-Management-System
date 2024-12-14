<?php
session_start();
include("include/config.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check if agent is logged in
if (!isset($_SESSION['agent_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Check if property ID is provided
if (!isset($_GET['p_id'])) {
    echo json_encode(['success' => false, 'message' => 'Property ID not provided']);
    exit();
}

$property_id = (int)$_GET['p_id'];
$agent_id = (int)$_SESSION['agent_id'];

try {
    // Start transaction
    $conn->begin_transaction();

    // First verify if the property exists and belongs to the agent
    $check_query = "SELECT p_id FROM property WHERE p_id = ? AND aid = ?";
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        throw new Exception("Prepare check statement failed: " . $conn->error);
    }
    
    $check_stmt->bind_param("ii", $property_id, $agent_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Property not found or not authorized to delete']);
        exit();
    }
    $check_stmt->close();

    // Delete order of operations:
    // 1. Delete from wishlist (child table)
    $delete_wishlist = "DELETE FROM wishlist WHERE p_id = ?";
    $wishlist_stmt = $conn->prepare($delete_wishlist);
    if (!$wishlist_stmt) {
        throw new Exception("Prepare statement failed for wishlist: " . $conn->error);
    }
    $wishlist_stmt->bind_param("i", $property_id);
    if (!$wishlist_stmt->execute()) {
        throw new Exception("Failed to delete wishlist entries: " . $wishlist_stmt->error);
    }
    $wishlist_stmt->close();

    // 2. Delete from property_media (child table)
    $delete_media = "DELETE FROM property_media WHERE property_id = ?";
    $media_stmt = $conn->prepare($delete_media);
    if (!$media_stmt) {
        throw new Exception("Prepare statement failed for property_media: " . $conn->error);
    }
    $media_stmt->bind_param("i", $property_id);
    if (!$media_stmt->execute()) {
        throw new Exception("Failed to delete media: " . $media_stmt->error);
    }
    $media_stmt->close();

    // 3. Finally delete the main property record
    $delete_property = "DELETE FROM property WHERE p_id = ? AND aid = ?";
    $property_stmt = $conn->prepare($delete_property);
    if (!$property_stmt) {
        throw new Exception("Prepare statement failed for property: " . $conn->error);
    }
    
    $property_stmt->bind_param("ii", $property_id, $agent_id);
    
    if (!$property_stmt->execute()) {
        throw new Exception("Delete property failed: " . $property_stmt->error);
    }

    if ($property_stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode([
            'success' => true, 
            'message' => 'Property and all associated data deleted successfully',
            'property_id' => $property_id
        ]);
    } else {
        throw new Exception("No rows affected when deleting property");
    }

    $property_stmt->close();

} catch (Exception $e) {
    error_log("Error in deletion process: " . $e->getMessage());
    
    if ($conn && $conn->connect_error === false) {
        $conn->rollback();
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'Error deleting property: ' . $e->getMessage(),
        'property_id' => $property_id
    ]);
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>