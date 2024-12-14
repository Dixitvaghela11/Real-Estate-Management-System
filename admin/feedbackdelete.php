<?php
include("config.php");
$id = $_GET['id'];
$sql = "DELETE FROM testimonials WHERE id = {$id}";
$result = mysqli_query($con, $sql);
if($result == true)
{
    $msg="<p class='alert alert-success'>Testimonials Deleted</p>";
    header("Location: feedbackview.php?msg=$msg");
}
else{
    $msg="<p class='alert alert-warning'>Testimonials Not Deleted</p>";
    header("Location: feedbackview.php?msg=$msg");
}
mysqli_close($con);
?>