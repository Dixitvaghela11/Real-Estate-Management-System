<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

//database connection
include("include/config.php");

// Retrieve all agents
$query = "SELECT * FROM agentsa";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>EstateAgency Bootstrap Template</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicons -->
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Main Stylesheet File -->
  <link href="assets/css/sty.css" rel="stylesheet">

  <!-- Place favicon.ico in the root directory -->
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="icon" href="favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="assets/css/normalize.css">
  <link rel="stylesheet" href="assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/fontello.css">
  <link href="assets/fonts/icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet">
  <link href="assets/fonts/icon-7-stroke/css/helper.css" rel="stylesheet">
  <link href="assets/css/animate.css" rel="stylesheet" media="screen">
  <link rel="stylesheet" href="assets/css/bootstrap-select.min.css"> 
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/icheck.min_all.css">
  <link rel="stylesheet" href="assets/css/price-range.css">
  <link rel="stylesheet" href="assets/css/owl.carousel.css">  
  <link rel="stylesheet" href="assets/css/owl.theme.css">
  <link rel="stylesheet" href="assets/css/owl.transitions.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<?php include 'include/header.php'; ?>
<div class="page-head"> 
  <div class="container">
    <div class="row">
      <div class="page-head-content">
        <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Agents</h1>               
      </div>
    </div>
  </div>
</div>
<!-- End page header -->

<!--/ Intro Single star /-->
<section class="intro-single">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-lg-8">
        <div class="title-single-box">
          <h1 class="title-single">Our Amazing Agents</h1>
          <span class="color-text-a">Grid Properties</span>
        </div>
      </div>
      <div class="col-md-12 col-lg-4">
        <nav aria-label="breadcrumb" class="breadcrumb-box d-flex justify-content-lg-end">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="#">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              Agents Grid
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>
<!--/ Intro Single End /-->

<!--/ Agents Grid Star /-->
<section class="agents-grid grid">
  <div class="container">
    <div class="row">
      <?php
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              ?>
              <div class="col-md-4">
                <div class="card-box-d">
                  <div class="card-img-d">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['aimage']); ?>" alt="" class="img-d img-fluid">
                  </div>
                  <div class="card-overlay card-overlay-hover">
                    <div class="card-header-d">
                      <div class="card-title-d align-self-center">
                        <h1 class="title-d">
                          <a href="useragent.php?aid=<?php echo $row['aid']; ?>" style="font-size: 30px;" class="link-two"><?php echo $row['aname']; ?></a>
                        </h1>
                      </div>
                    </div>
                    <div class="card-body-d">
                      <p style="font-size: 18px;" class="content-d color-text-a">
                        <?php echo $row['content']; ?>
                      </p>
                      <div class="info-agents color-a">
                        <p style="font-size: 20px;">
                          <strong>Phone: </strong> <?php echo $row['aphone']; ?></p>
                        <p style="font-size: 20px;">
                          <strong>Email: </strong> <?php echo $row['aemail']; ?></p>
                      </div>
                    </div>
                    <div class="social pull-right"> 
                      <ul>
                        <li><a class="wow fadeInUp animated" href="<?php echo $row['facebook']; ?>"><i class="fa fa-facebook"></i></a></li>
                        <li><a class="wow fadeInUp animated" href="<?php echo $row['instagram']; ?>" data-wow-delay="0.2s"><i class="fa fa-instagram"></i></a></li>
                        <li><a class="wow fadeInUp animated" href="<?php echo $row['linkedin']; ?>" data-wow-delay="0.3s"><i class="fa fa-linkedin"></i></a></li>
                      </ul> 
                    </div>
                  </div>
                </div>
              </div>
              <?php
          }
      } else {
          echo "<div class='col-md-12'><p>0 results</p></div>";
      }
      ?>
    </div>
  
  </div>
</section>
<!--/ Agents Grid End /-->

<?php include('include/footer.php'); ?>

<!-- JavaScript Libraries -->
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/jquery/jquery-migrate.min.js"></script>
<script src="lib/popper/popper.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/scrollreveal/scrollreveal.min.js"></script>
<!-- Contact Form JavaScript File -->
<script src="contactform/contactform.js"></script>

<!-- Template Main Javascript File -->
<script src="js/main.js"></script>
<script src="assets/js/modernizr-2.6.2.min.js"></script>

<script src="assets/js/jquery-1.10.2.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/bootstrap-select.min.js"></script>
<script src="assets/js/bootstrap-hover-dropdown.js"></script>

<script src="assets/js/easypiechart.min.js"></script>
<script src="assets/js/jquery.easypiechart.min.js"></script>

<script src="assets/js/owl.carousel.min.js"></script>        

<script src="assets/js/wow.js"></script>

<script src="assets/js/icheck.min.js"></script>
<script src="assets/js/price-range.js"></script>

<script src="assets/js/main.js"></script>
</body>
</html>