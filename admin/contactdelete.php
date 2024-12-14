<?php
include("config.php");
$contact_id = $_GET['id'];
$sql = "DELETE FROM contact_us WHERE contact_id = {$contact_id}";
$result = mysqli_query($con, $sql);
if($result == true)
{
	$msg="<p class='alert alert-success'>Contact Deleted</p>";
	header("Location:contactview.php?msg=$msg");
}
else{
	$msg="<p class='alert alert-warning'>Contact Not Deleted</p>";
	header("Location:contactview.php?msg=$msg");
}
mysqli_close($con);
?>
