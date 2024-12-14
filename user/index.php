<?php
session_start(); // Start the session

// Check if the agent is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "realestate";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->close();
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
        <title>Real ESTATE | Home page</title>
        <meta name="description" content="GARO is a real-estate template">
        <meta name="author" content="Kimarotec">
        <meta name="keyword" content="html5, css, bootstrap, property, real-estate theme , bootstrap template">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700,800' rel='stylesheet' type='text/css'>

        <!-- Place favicon.ico  the root directory -->
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
    <body>

        <div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
        <!-- Body content -->
<?php include("include/header.php"); ?>
        <div class="slider-area">
            <div class="slider">
                <div id="bg-slider" class="owl-carousel owl-theme">

                    <div class="item"><img src="assets/img/slide1/slider-image-4.jpg" alt="Mirror Edge"></div> 
                    <div class="item"><img src="assets/img/slide1/slider-image-2.jpg" alt="The Last of us"></div> 
                    <div class="item"><img src="assets/img/slide1/slider-image-1.jpg" alt="GTA V"></div>   

                </div>
            </div>
            <div class="container slider-content">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12">
            <h2 style="color: white;">Property Searching Just Got So Easy</h2>
            <div class="search-form wow pulse" data-wow-delay="0.8s">
                <form action="propertygrid.php" method="GET" class="form-inline">
                    <!-- Toggle Button -->
                    <button class="btn toggle-btn" type="button">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Keyword Search -->
                    <div class="form-group">
                        <input
                            type="text"
                            name="keyword"
                            style="display: block;
                                width: 100%;
                                height: 39.5px;
                                padding: 6px 12px;
                                font-size: 13px;
                                line-height: 1.42857;
                                color: rgb(0, 0, 0);
                                background-color: #FFF;
                                border: 1px solid #000000;
                                border-radius: 0px;
                                box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.1) inset;"
                            placeholder="Keyword"
                        />
                    </div>

                    <!-- Property Type -->
                    <div class="form-group">
                        <select name="type" class="selectpicker" data-live-search="true" title="Select your type">
                            <option value="Apartment">Apartment</option>
                            <option value="Flat">Flat</option>
                            <option value="House">House</option>
                            <option value="Building">Building</option>
                            <option value="Villa">Villa</option>
                            <option value="Office">Office</option>
                        </select>
                    </div>

                    <!-- Property Status -->
                    <div class="form-group">
                        <select name="status" class="selectpicker" data-live-search="true" title="Select your status">
                            <option value="Rent">Rent</option>
                            <option value="Sale">Sale</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <button class="btn search-btn" type="submit">
                        <i class="fa fa-search"></i>
                    </button>

                    <!-- Advanced Filters -->
                    <div style="display: none;" class="search-toggle">
                        <div class="search-row">
                            <!-- Price Range -->
                            <div class="form-group">
                                <label for="price_range">Price range (₹):</label>
                                <input
                                    type="text"
                                    name="price_range"
                                    style="display: block;
                                        width: 100%;
                                        height: 44px;
                                        padding: 6px 12px;
                                        font-size: 13px;
                                        color: rgb(0, 0, 0);
                                        background-color: #FFF;
                                        border: 1px solid #000000;"
                                    placeholder="Enter price"
                                />
                            </div>

                            <!-- City -->
                            <div class="form-group">
                                <label for="city">City:</label>
                                <input
                                    type="text"
                                    name="city"
                                    style="display: block;
                                        width: 100%;
                                        height: 44px;
                                        padding: 6px 12px;
                                        font-size: 13px;
                                        color: rgb(0, 0, 0);
                                        background-color: #FFF;
                                        border: 1px solid #000000;"
                                    placeholder="Enter city"
                                />
                            </div>

                            <!-- Area -->
                            <div class="form-group">
                                <label for="area">Property Area (sq ft):</label>
                                <input
                                    type="text"
                                    name="area"
                                    style="display: block;
                                        width: 100%;
                                        height: 44px;
                                        padding: 6px 12px;
                                        font-size: 13px;
                                        color: rgb(0, 0, 0);
                                        background-color: #FFF;
                                        border: 1px solid #000000;"
                                    placeholder="Enter area in sq ft"
                                />
                            </div>
                        </div>

                        <div class="search-row">
                            <!-- Minimum Halls -->
                            <div class="form-group">
                                <label for="min_halls">Min Halls:</label>
                                <input
                                    type="text"
                                    name="min_halls"
                                    style="display: block;
                                        width: 100%;
                                        height: 44px;
                                        padding: 6px 12px;
                                        font-size: 13px;
                                        color: rgb(0, 0, 0);
                                        background-color: #FFF;
                                        border: 1px solid #000000;"
                                    placeholder="Enter halls (1 to 10)"
                                />
                            </div>

                            <!-- Minimum Bedrooms -->
                            <div class="form-group">
                                <label for="min_beds">Min Bedrooms:</label>
                                <input
                                    type="text"
                                    name="min_beds"
                                    style="display: block;
                                        width: 100%;
                                        height: 44px;
                                        padding: 6px 12px;
                                        font-size: 13px;
                                        color: rgb(0, 0, 0);
                                        background-color: #FFF;
                                        border: 1px solid #000000;"
                                    placeholder="Enter bedrooms (1 to 10)"
                                />
                            </div>

                            <!-- Minimum Kitchens -->
                            <div class="form-group">
                                <label for="min_kitchen">Min Kitchens:</label>
                                <input
                                    type="text"
                                    name="min_kitchen"
                                    style="display: block;
                                        width: 100%;
                                        height: 44px;
                                        padding: 6px 12px;
                                        font-size: 13px;
                                        color: rgb(0, 0, 0);
                                        background-color: #FFF;
                                        border: 1px solid #000000;"
                                    placeholder="Enter kitchens (1 to 10)"
                                />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        </div>
<style>
    /* Add this to your stylesheet */
.proerty-th .proerty-item {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
    margin-bottom: 20px;
}

.proerty-th .proerty-item:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.proerty-th .proerty-item .item-thumb img {
    width: 100%;
    height: auto;
    display: block;
}

.proerty-th .proerty-item .item-entry {
    padding: 15px;
}

.proerty-th .proerty-item .item-entry h5 {
    margin-top: 0;
    margin-bottom: 10px;
}

.proerty-th .proerty-item .item-entry .dot-hr {
    margin: 10px 0;
}

.proerty-th .proerty-item .item-entry span {
    display: block;
    margin-bottom: 5px;
}
</style>
<?php
include("include/config.php");

$sql = "SELECT * FROM property LIMIT 7";
$result = $conn->query($sql);
?>

<!-- property area -->
<div class="content-area recent-property" style="background-color: #FCFCFC; padding-bottom: 55px;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12 text-center page-title">
                <!-- /.feature title -->
                <h2 style="color: black;">Top submitted property</h2>
                <p>There are no players. The advantage of the ultricies is that the bow is not expensive. But no more bows. </p>
            </div>
        </div>

        <div class="row">
            <div class="proerty-th">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <div class="col-sm-6 col-md-3 p0">
                            <div class="box-two proerty-item">
                                <div class="item-thumb">
                                    <a href="property-detail.php?id=<?php echo $row['p_id']; ?>" >
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['main_image']); ?>" alt="<?php echo $row['property_name']; ?>">
                                    </a>
                                </div>
                                <div class="item-entry overflow">
                                    <h5><a href="property-detail.php?id=<?php echo $row['p_id']; ?>" ><?php echo $row['property_name']; ?></a></h5>
                                    <div class="dot-hr"></div>
                                    <span class="pull-left"><b>Location :</b> <?php echo $row['city']; ?> </span>
                                    <span style="    font-size: 16px;
                                                    color: #27ae60;
                                                    font-weight: bold;" class="proerty-price pull-right">INR <?php echo $row['property_price']; ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='col-sm-12 text-center'><p>No properties found</p></div>";
                }
                ?>

                <div class="col-sm-6 col-md-3 p0">
                    <div class="box-tree more-proerty text-center">
                        <div class="item-tree-icon">
                            <i class="fa fa-th"></i>
                        </div>
                        <div class="more-entry overflow">
                            <h5>CAN'T DECIDE ? </h5>
                            <h5 class="tree-sub-ttl">Show all properties</h5>
                            <button class="btn border-btn more-black" value="All properties"><a href="propertiesgrid.php">All properties</a></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

        <!--Welcome area -->
        <div class="Welcome-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 Welcome-entry  col-sm-12">
                        <div class="col-md-5 col-md-offset-2 col-sm-6 col-xs-12">
                            <div class="welcome_text wow fadeInLeft" data-wow-delay="0.3s" data-wow-offset="100">
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-1 col-sm-12 text-center page-title">
                                        <!-- /.feature title -->
                                        <h2>Real ESTATE </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-6 col-xs-12">
                            <div  class="welcome_services wow fadeInRight" data-wow-delay="0.3s" data-wow-offset="100">
                                <div class="row">
                                    <div class="col-xs-6 m-padding">
                                        <div class="welcome-estate">
                                            <div class="welcome-icon">
                                                <i class="pe-7s-home pe-4x"></i>
                                            </div>
                                            <h3>Any property</h3>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 m-padding">
                                        <div class="welcome-estate">
                                            <div class="welcome-icon">
                                                <i class="pe-7s-users pe-4x"></i>
                                            </div>
                                            <h3>More Clients</h3>
                                        </div>
                                    </div>


                                    <div class="col-xs-12 text-center">
                                        <i class="welcome-circle"></i>
                                    </div>

                                    <div class="col-xs-6 m-padding">
                                        <div class="welcome-estate">
                                            <div class="welcome-icon">
                                                <i class="pe-7s-notebook pe-4x"></i>
                                            </div>
                                            <h3>Easy to use</h3>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 m-padding">
                                        <div class="welcome-estate">
                                            <div class="welcome-icon">
                                                <i class="pe-7s-help2 pe-4x"></i>
                                            </div>
                                            <h3>Any help </h3>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
include("include/config.php");

$sql = "SELECT * FROM testimonials WHERE status = 1";
$result = $conn->query($sql);
?>

<!--TESTIMONIALS -->
<div class="testimonial-area recent-property" style="background-color: #FCFCFC; padding-bottom: 15px;">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12 text-center page-title">
                <h2>Our Customers Said</h2> 
            </div>
        </div>

        <div class="row">
            <div class="row testimonial">
                <div class="col-md-12">
                    <div id="testimonial-slider">
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                ?>
                                <div class="item">
                                    <div class="client-text">                                
                                        <p><?php echo $row['content']; ?></p>
                                        <h4><strong><?php echo $row['name']; ?>, </strong><i><?php echo $row['role']; ?></i></h4>
                                    </div>
                                    <div class="client-face wow fadeInRight" data-wow-delay=".9s"> 
                                        <?php if($row['image']): ?>
                                            <img style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" 
                                                 src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" 
                                                 alt="<?php echo $row['name']; ?>">
                                        <?php else: ?>
                                            <img style="width: 100px; height: 100px; border-radius: 50%;" 
                                                 src="assets/img/client-face1.png" 
                                                 alt="Default Client">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div class='item'>No testimonials found</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Count area -->
        <div class="count-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 col-sm-12 text-center page-title">
                        <!-- /.feature title -->
                        <h2>You can trust Us </h2> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12 percent-blocks m-main" data-waypoint-scroll="true">
                        <div class="row">
                            <div class="col-sm-3 col-xs-6">
                                <div class="count-item">
                                    <div class="count-item-circle">
                                        <span class="pe-7s-users"></span>
                                    </div>
                                    <div class="chart" data-percent="5000">
                                        <h2 class="percent" id="counter">0</h2>
                                        <h5>HAPPY CUSTOMER </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-6">
                                <div class="count-item">
                                    <div class="count-item-circle">
                                        <span class="pe-7s-home"></span>
                                    </div>
                                    <div class="chart" data-percent="12000">
                                        <h2 class="percent" id="counter1">0</h2>
                                        <h5>Properties in stock</h5>
                                    </div>
                                </div> 
                            </div> 
                            <div class="col-sm-3 col-xs-6">
                                <div class="count-item">
                                    <div class="count-item-circle">
                                        <span class="pe-7s-flag"></span>
                                    </div>
                                    <div class="chart" data-percent="120">
                                        <h2 class="percent" id="counter2">0</h2>
                                        <h5>City registered </h5>
                                    </div>
                                </div> 
                            </div> 
                            <div class="col-sm-3 col-xs-6">
                                <div class="count-item">
                                    <div class="count-item-circle">
                                        <span class="pe-7s-graph2"></span>
                                    </div>
                                    <div class="chart" data-percent="5000">
                                        <h2 class="percent"  id="counter3">5000</h2>
                                        <h5>DEALER BRANCHES</h5>
                                    </div>
                                </div> 

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- boy-sale area -->
        <div class="boy-sale-area">
            <div class="container">
                <div class="row">

                    <div class="col-md-6 col-sm-10 col-sm-offset-1 col-md-offset-0 col-xs-12">
                        <div class="asks-first">
                            <div class="asks-first-circle">
                                <span class="fa fa-search"></span>
                            </div>
                            <div class="asks-first-info">
                                <h2>ARE YOU LOOKING FOR A Property?</h2>
                                <p> varius od lio eget conseq uat blandit, lorem auglue comm lodo nisl no us nibh mas lsa</p>                                        
                            </div>
                            <div class="asks-first-arrow">
                                <a href="properties.html"><span class="fa fa-angle-right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-10 col-sm-offset-1 col-xs-12 col-md-offset-0">
                        <div  class="asks-first">
                            <div class="asks-first-circle">
                                <span class="fa fa-usd"></span>
                            </div>
                            <div class="asks-first-info">
                                <h2>DO YOU WANT TO SELL A Property?</h2>
                                <p> varius od lio eget conseq uat blandit, lorem auglue comm lodo nisl no us nibh mas lsa</p>
                            </div>
                            <div class="asks-first-arrow">
                                <a href="properties.html"><span class="fa fa-angle-right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <p  class="asks-call">QUESTIONS? CALL US  : <span class="strong"> + 3-123- 424-5700</span></p>
                    </div>
                </div>
            </div>
        </div>


         <?php include("include/footer.php"); ?>

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