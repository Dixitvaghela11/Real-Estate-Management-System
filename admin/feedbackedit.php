<?php

include("config.php"); // Include your database connection file

$msg = "";
if(isset($_POST['update']))
{
    $id = $_GET['id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE testimonials SET status = '{$status}' WHERE id = {$id}";
    $result = mysqli_query($con, $sql);
    if($result == true)
    {
        $msg = "<p class='alert alert-success'>Testimonials Updated Successfully</p>";
        header("Location: feedbackview.php?msg=$msg");        
    }
    else
    {
        $msg = "<p class='alert alert-warning'>Testimonials Not Updated</p>";
        header("Location: feedbackview.php?msg=$msg");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>LM HOMES | About</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="assets/css/select2.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<!-- Main Wrapper -->
<div class="main-wrapper">

    <!-- Header -->
    <?php include("header.php"); ?>
    <!-- /Header -->
    
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">
        <br>
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Testimonials</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Testimonials</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Update Testimonials</h2>
                        </div>
                        <?php 
                        $id = $_GET['id'];
                        $sql = "SELECT * FROM testimonials WHERE id = {$id}";
                        $result = mysqli_query($con, $sql);
                        if($row = mysqli_fetch_assoc($result))
                        {
                        ?>
                        <form method="post">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <h5 class="card-title">Update Testimonials</h5>
                                        
                                        <?php echo $msg; ?>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Testimonials Id</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="id" value="<?php echo $row['id']; ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Status</label>
                                            <div class="col-lg-9">
                                                <select class="form-control" name="status" required>
                                                    <option value="0" <?php if($row['status'] == 0) echo 'selected'; ?>>Normal</option>
                                                    <option value="1" <?php if($row['status'] == 1) echo 'selected'; ?>>Testimonial</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <input type="submit" class="btn btn-primary" value="Submit" name="update" style="margin-left:200px;">
                                </div>
                            </div>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Wrapper -->

</div>
<!-- /Main Wrapper -->

<script src="assets/plugins/tinymce/tinymce.min.js"></script>
<script src="assets/plugins/tinymce/init-tinymce.min.js"></script>
<!-- jQuery -->
<script src="assets/js/jquery-3.2.1.min.js"></script>

<!-- Bootstrap Core JS -->
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<!-- Slimscroll JS -->
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Select2 JS -->
<script src="assets/js/select2.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/script.js"></script>
</body>
</html>