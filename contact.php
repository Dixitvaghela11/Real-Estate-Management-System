<?php
include("include/config.php");
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js"> <!--<![endif]-->
    <head>
         <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>GARO ESTATE | Home page</title>
        <meta name="description" content="company is a real-estate template">
        <meta name="author" content="Kimarotec">
        <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
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
<?php include("include/alert.php"); ?>
    </head>
    <body>
<?php include("include/header.php"); ?>
<div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
        <div class="page-head"> 
            <div class="container">
                <div class="row">
                    <div class="page-head-content">
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Contact</h1>               
                    </div>
                </div>
            </div>
        </div>
        <!-- End page header -->


        <!-- property area -->
        <div class="content-area recent-property padding-top-40" style="background-color: #FFF;">
            <div class="container">  

                <div class="col-md-9">

                    <div class="" id="contact1">                        
                        
                       
                        <hr>
                        <h2>Contact form</h2>
                        <form action="submit_contact.php" method="POST">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="firstname">Firstname</label>
                <input type="text" class="form-control" id="firstname" name="firstname">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="lastname">Lastname</label>
                <input type="text" class="form-control" id="lastname" name="lastname">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" class="form-control" name="message"></textarea>
            </div>
        </div>
        <div class="col-sm-12 text-center">
            <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send message</button>
        </div>
    </div>
    <!-- /.row -->
</form>
                    </div>
                </div>
                <!-- /.col-md-9 -->   

                <div class="col-md-3">                    
                    <div class="blog-asside-right">  
                        <div class="panel panel-default sidebar-menu wow fadeInRight animated">
                            <div class="panel-heading">
                                <h3 class="panel-title">Recommended</h3>
                            </div>
                            <!-- property list -->
                            <?php
                            include("include/config.php");
                            $sql = "SELECT * FROM property LIMIT 6";
                            $result = $conn->query($sql); // Assuming $conn is your database connection object
                            ?>
                            <div class="panel-body recent-property-widget">
                                <ul>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            ?>
                                            <li>
                                                <div class="col-md-3 blg-thumb p0">
                                                    <a href="single.html">
                                                        <img style="width: 100px; height: 50px;" src="data:image/jpeg;base64,<?php echo base64_encode($row['main_image']); ?>">
                                                    </a>
                                                </div>
                                                <div class="col-md-8 blg-entry">
                                                    <h6><a href="property-detail.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['property_name']; ?></a></h6>
                                                    <span style="color : green; font-weight: bold;" class="property-price"><?php echo $row['property_price']; ?></span>
                                                </div>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        echo "No properties found.";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>                     
                </div>        
            </div>
        </div>
        <div class="map-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d119066.41709495463!2d72.82229599874268!3d21.15934583599378!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e59411d1563%3A0xfe4558290938b042!2sSurat%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1648826892541!5m2!1sen!2sin" 
                    width="100%" 
                    height="400" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>
       

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
        
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
        <script src="assets/js/gmaps.js"></script>        
        <script src="assets/js/gmaps.init.js"></script>

        <script src="assets/js/main.js"></script>
        <script>
document.querySelector('form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Show processing message with spinner
    showAlert('processing', 'Processing', 'Please wait while we process your request...');

    // Collect form data
    const formData = new FormData(this);

    // Send form data via AJAX
    fetch('submit_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('success', 'Success!', data.message, 'contact.php');
        } else {
            showAlert('error', 'Error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'Error', 'Something went wrong!');
    });
});
</script>
<?php include("include/footer.php"); ?>
    </body>
</html>