<?php
include("config.php"); // Include your database connection file

if (isset($_GET['id'])) {
    $enquiry_id = $_GET['id'];

    // Delete the enquiry from the database
    $query = mysqli_query($con, "DELETE FROM property_enquiries WHERE enquiry_id = '$enquiry_id'");

    if ($query) {
        header("Location: enquiryview.php?msg=Enquiry deleted successfully");
    } else {
        header("Location: enquiryview.php?msg=Error deleting enquiry");
    }
} else {
    header("Location: enquiryview.php?msg=Invalid request");
}
?>