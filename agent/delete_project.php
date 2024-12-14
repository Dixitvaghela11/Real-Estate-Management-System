<?php
// Start the session
session_start();

// Check if the agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "realestate");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $agent_id = $_SESSION['agent_id'];

    // First, verify that the project belongs to the logged-in agent
    $verify_query = "SELECT * FROM past_projects WHERE id = ? AND agent_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param("ii", $project_id, $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Delete project images first
            $delete_images = "DELETE FROM project_images WHERE project_id = ?";
            $stmt = $conn->prepare($delete_images);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();

            // Then delete the project
            $delete_project = "DELETE FROM past_projects WHERE id = ?";
            $stmt = $conn->prepare($delete_project);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();

            // Commit transaction
            $conn->commit();
            echo "success";
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "error";
        }
    } else {
        echo "unauthorized";
    }
} else {
    echo "invalid_request";
}

$conn->close();
?>