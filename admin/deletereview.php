<?php
include("config.php"); // Include your database connection file

if (isset($_GET['id'])) {
    $review_id = $_GET['id'];

    // Delete the review from the database
    $query = mysqli_query($con, "DELETE FROM reviews WHERE review_id = '$review_id'");

    if ($query) {
        header("Location: propertyreview.php?msg=Review deleted successfully");
    } else {
        header("Location: propertyreview.php?msg=Error deleting review");
    }
} else {
    header("Location: propertyreview.php?msg=Invalid request");
}
?>