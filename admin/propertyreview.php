<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include("config.php"); // Include your database connection file
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>LM Homes | Property Reviews</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="assets/css/feathericon.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <!-- Main Wrapper -->

    <!-- Header -->
    <?php include("header.php"); ?>
    <!-- /Sidebar -->

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Property Reviews</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Property Reviews</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List Of Property Reviews</h4>
                            <?php
                            if (isset($_GET['msg'])) {
                                echo $_GET['msg'];
                            }
                            ?>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-stripped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Name</th>
                                            <th>Property Name</th>
                                            <th>Review Title</th>
                                            <th>Review Content</th>
                                            <th>Rating</th>
                                            <th>Review Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <?php
                                    $query = mysqli_query($con, "
                                        SELECT 
                                            r.review_id, 
                                            u.uname AS user_name, 
                                            a.aname AS agent_name, 
                                            p.property_name, 
                                            r.review_title, 
                                            r.review_content, 
                                            r.rating, 
                                            r.review_date 
                                        FROM 
                                            reviews r
                                        LEFT JOIN 
                                            users u ON r.uid = u.uid
                                        LEFT JOIN 
                                            agents a ON r.aid = a.aid
                                        LEFT JOIN 
                                            property p ON r.p_id = p.p_id
                                    ");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tbody>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['property_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['review_title']); ?></td>
                                            <td><?php echo htmlspecialchars(strip_tags($row['review_content'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['rating']); ?></td>
                                            <td><?php echo htmlspecialchars($row['review_date']); ?></td>
                                            <td>
                                                <a href="deletereview.php?id=<?php echo $row['review_id']; ?>"><button class="btn btn-danger">Delete</button></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <?php
                                        $cnt = $cnt + 1;
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

</body>

</html>