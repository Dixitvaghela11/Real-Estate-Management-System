<?php
include("include/config.php"); 
include("include/alert.php");
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from the form
    $p_id = isset($_POST['p_id']) ? intval($_POST['p_id']) : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : null;

    // Retrieve user or agent ID from session
    $u_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $aid = isset($_SESSION['agent_id']) ? intval($_SESSION['agent_id']) : null;

    // Validate required fields
    if (!$p_id) {
        echo "<script>showAlert('error', 'Submission Failed', 'Property ID is missing!');</script>";
        exit();
    }
    if (!$u_id && !$aid) {
        echo "<script>showAlert('error', 'Submission Failed', 'You must be logged in as a user or agent to submit an enquiry!');</script>";
        exit();
    }
    if (!$message) {
        echo "<script>showAlert('error', 'Submission Failed', 'Message cannot be empty!');</script>";
        exit();
    }

    // Insert the enquiry into the property_enquiries table
    $sql = "INSERT INTO property_enquiries (p_id, u_id, aid, message, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $p_id, $u_id, $aid, $message);

    // Check if the insertion was successful
    if ($stmt->execute()) {
        // Success: Set up success alert
        echo "<script>showAlert('success', 'Success', 'Your enquiry has been submitted successfully!', 'property-inquiry.php?p_id=$p_id');</script>";
    } else {
        // Error: Set up error alert
        echo "<script>showAlert('error', 'Submission Failed', 'There was an error submitting your enquiry. Please try again.');</script>";
    }

    // Close the statement
    $stmt->close();
    exit();
}
?>
