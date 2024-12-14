<?php
include("config.php");

// Add error checking for GET parameter
if (!isset($_GET['p_id'])) {
    $msg = "<p class='alert alert-warning'>No property ID provided</p>";
    header("Location:propertyview.php?msg=$msg");
    exit();
}

$p_id = $_GET['p_id'];
$p_id = mysqli_real_escape_string($con, $p_id);

// First delete related records from property_media table
$sql_media = "DELETE FROM property_media WHERE property_id = '{$p_id}'";
$result_media = mysqli_query($con, $sql_media);

// Then delete the property
$sql_property = "DELETE FROM property WHERE p_id = '{$p_id}'";
$result_property = mysqli_query($con, $sql_property);

if (!$result_property) {
    $msg = "<p class='alert alert-warning'>Error: " . mysqli_error($con) . "</p>";
} else {
    $msg = "<p class='alert alert-success'>Property and related media deleted successfully</p>";
}

header("Location:propertyview.php?msg=$msg");
mysqli_close($con);
?>