<?php
session_start(); // Start the session

// Check if the agent is logged in
if (!isset($_SESSION['agent_id'])) {
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

// Retrieve agent details
$agent_id = $_SESSION['agent_id'];
$query = "SELECT aemail, aname FROM agents WHERE aid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $agent = $result->fetch_assoc();
} else {
    echo "Agent not found.";
    exit();
}

$conn->close();

// At the beginning of propertygrid.php, after database connection
$filters = [];

// Get filter parameters from URL
if (!empty($_GET['keyword'])) $filters['keyword'] = $_GET['keyword'];
if (!empty($_GET['type'])) $filters['type'] = $_GET['type'];
if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
if (!empty($_GET['price'])) $filters['price'] = $_GET['price'];
if (!empty($_GET['city'])) $filters['city'] = $_GET['city'];
if (!empty($_GET['area'])) $filters['area'] = $_GET['area'];
if (!empty($_GET['min_bed'])) $filters['min_bed'] = $_GET['min_bed'];
if (!empty($_GET['min_kitchen'])) $filters['min_kitchen'] = $_GET['min_kitchen'];
if (!empty($_GET['min_bath'])) $filters['min_bath'] = $_GET['min_bath'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARO ESTATE | Properties page</title>
    <meta name="description" content="GARO is a real-estate template">
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
    <style>
    .pagination ul {
        list-style: none;
        padding: 0;
        margin: 20px 0;
        display: flex;
        gap: 5px;
    }

    .pagination li {
        display: inline-block;
    }

    .pagination li a {
        padding: 8px 16px;
        text-decoration: none;
        background-color: #000066;
        color: #333;
        border-radius: 4px;
    }

    .pagination li.active a {
        background-color: #FDC600;
        color: white;
    }

    .pagination li.disabled a {
        background-color: #ddd;
        color: #666;
        cursor: not-allowed;
    }

    .pagination li:not(.disabled):not(.active) a:hover {
        background-color: #ddd;
    }
    </style>
</head>
<body>
    <?php include("include/header.php"); ?>
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>
    <!-- Body content -->
    <div class="page-head"> 
        <div class="container">
            <div class="row">
                <div class="page-head-content">
                    <h1 class="page-title"><a style="color: white;" href="index.php">Home</a> | Properties</h1>               
                </div>
            </div>
        </div>
    </div>
    <!-- End page header -->

    <!-- property area -->
    <div class="properties-area recent-property" style="background-color: #FFF;">
        <div class="container"> 
            <div class="row  pr0 padding-top-40 properties-page">
                <div class="col-md-12 padding-bottom-40 large-search"> 
                    <div class="search-form wow pulse">
                        <form id="filterForm" class="form-inline">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <input type="text" name="keyword" class="form-control" placeholder="key word">
                                </div>
                                <div class="col-md-4">
                                    <select name="type" class="selectpicker form-control" data-live-search="true">
                                        <option value="">-Type-</option>
                                        <option value="Apartment">Apartment</option>
                                        <option value="Flat">Flat</option>
                                        <option value="House">House</option>
                                        <option value="Building">Building</option>
                                        <option value="Villa">Villa</option>
                                        <option value="Office">Office</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="status" class="selectpicker form-control" data-live-search="true">
                                        <option value="">-Status-</option>
                                        <option value="Rent">Rent</option>
                                        <option value="Sale">Sale</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="search-row">
                                    <div class="col-sm-4">
                                        <label for="city">City:</label>
                                        <input type="text" name="city" class="form-control" placeholder="Enter city">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="price">Price range (₹):</label>
                                        <input type="number" name="price" class="form-control" placeholder="Enter price">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="area">Property in square feet:</label>
                                        <input type="number" name="area" class="form-control" placeholder="Enter Area">
                                    </div>
                                </div>
                                <div class="search-row">
                                    <div class="col-sm-3">
                                        <label for="min_bed">Min bed:</label>
                                        <input type="number" name="min_bed" class="form-control" placeholder="Enter bed count">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="min_kitchen">Min kitchen:</label>
                                        <input type="number" name="min_kitchen" class="form-control" placeholder="Enter kitchen count">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="min_hall">Min hall:</label>
                                        <input type="number" name="min_hall" class="form-control" placeholder="Enter hall count">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="min_bath">Min bath:</label>
                                        <input type="number" name="min_bath" class="form-control" placeholder="Enter bath count">
                                    </div>
                                </div>
                                <div class="col-md-12 text-center" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary">Search Properties</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12  clear"> 
                <div class="col-xs-10 page-subheader sorting pl0">
                    <ul class="sort-by-list">
                        <li class="active">
                            <a href="javascript:void(0);" class="order_by_date" data-orderby="created_at" data-order="ASC">
                                Property Date <i class="fa fa-sort-amount-asc"></i>					
                            </a>
                        </li>
                        <li class="">
                            <a href="javascript:void(0);" class="order_by_price" data-orderby="property_price" data-order="DESC">
                                Property Price <i class="fa fa-sort-numeric-desc"></i>						
                            </a>
                        </li>
                    </ul><!--/ .sort-by-list-->

                  
                </div>

                <div class="col-xs-2 layout-switcher">
                    <a class="layout-list" href="javascript:void(0);"> <i class="fa fa-th-list"></i>  </a>
                    <a class="layout-grid active" href="javascript:void(0);"> <i class="fa fa-th"></i> </a>                          
                </div><!--/ .layout-switcher-->
            </div>

            <div class="col-md-12 clear "> 
                <div id="list-type" class="proerty-th">
                    <?php
                    include("include/config.php");

                    $sql = "SELECT * FROM property ORDER BY property_price DESC";
                    $result = $conn->query($sql); 
                    ?>
                      <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                        <div class="col-sm-6 col-md-3 p0">
                            <div class="box-two proerty-item">
                                <div class="item-thumb">
                                <a href="property-detail.php?p_id=<?php echo $row['p_id']; ?>" >
                                <img style="width: 100%; height: 200px;" src="data:image/jpeg;base64,<?php echo base64_encode($row['main_image']); ?>" alt="<?php echo $row['property_name']; ?>">            
                            </a>
                                </div>

                                <div class="item-entry overflow">
                                <h6><a href="property-detail.php?p_id=<?php echo $row['p_id']; ?>" ><?php echo $row['property_name']; ?> </a></h6>
                                <h5>For <?php echo $row['status']; ?></h5>

                                <div class="dot-hr"></div>
                                    <span class="pull-left"><b> Area :</b> <?php echo $row['property_geo']; ?> sq ft </span>
                                    <span class="proerty-price pull-right">INR <?php echo $row['property_price']; ?></span>
                                    <p style="display: none;">Suspendisse ultricies Suspendisse ultricies Nulla quis dapibus nisl. Suspendisse ultricies commodo arcu nec pretium ...</p>
                                    <div class="property-icon">
                                        <img src="assets/img/icon/bed1.png"> (<?php echo $row['min_bed']; ?>) |
                                        <img src="assets/img/icon/bath.png"> (<?php echo $row['min_baths']; ?>) |
                                        <img src="assets/img/icon/kit.png">   (<?php echo $row['min_kitchen']; ?>)
                                    </div>
                                </div>


                            </div>
                        </div> 
                        <?php
                }
            } else {
                echo "<div class='col-sm-12 text-center'><p>No properties found</p></div>";
            }
            ?>

                      
                </div>
            </div>
        
            <div class="col-md-12 clear"> 
                <div id="pagination-container" class="pull-right">
                    <!-- Pagination will be inserted here -->
                </div>                
            </div>
        </div>                
    </div>
</div>
<script>
let currentPage = 1;
let currentFilters = {};

function loadProperties(page = 1, filters = {}) {
    const listContainer = document.getElementById('list-type');
    const paginationContainer = document.getElementById('pagination-container');
    
    listContainer.innerHTML = '<div class="col-sm-12 text-center"><p>Loading...</p></div>';
    
    // Combine page number with filters
    const searchData = {
        ...filters,
        page: page
    };

    fetch('filter_properties.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(searchData)
    })
    .then(response => response.json())
    .then(response => {
        if (!response.success) {
            throw new Error(response.error || 'Failed to filter properties');
        }

        const properties = response.data;
        const pagination = response.pagination;
        
        if (properties.length === 0) {
            listContainer.innerHTML = '<div class="col-sm-12 text-center"><p>No properties found matching your criteria</p></div>';
            paginationContainer.innerHTML = '';
            return;
        }

        // Display properties
        listContainer.innerHTML = properties.map(property => `
            <div class="col-sm-6 col-md-3 p0">
                <div class="box-two proerty-item">
                    <div class="item-thumb">
                        <a href="property-detail.php?p_id=${property.p_id}">
                            <img style="width: 100%; height: 200px;" 
                                 src="data:image/jpeg;base64,${property.main_image}" 
                                 alt="${property.property_name}"
                                 onerror="this.src='assets/img/default-property.jpg'">
                        </a>
                    </div>
                    <div class="item-entry overflow">
                        <h6><a href="property-detail.php?p_id=${property.p_id}">${property.property_name}</a></h6>
                        <h5>For ${property.status}</h5>
                        <div class="dot-hr"></div>
                        <span class="pull-left"><b>Area:</b> ${property.property_geo} sq ft</span>
                        <span class="proerty-price pull-right">INR ${property.property_price}</span>
                        <div class="property-icon">
                            <img src="assets/img/icon/bed1.png">(${property.min_bed || 0}) |
                            <img src="assets/img/icon/bath.png">(${property.min_baths || 0}) |
                            <img src="assets/img/icon/kit.png">(${property.min_kitchen || 0})
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Update pagination
        updatePagination(pagination);
    })
    .catch(error => {
        console.error('Error:', error);
        listContainer.innerHTML = `
            <div class="col-sm-12 text-center">
                <p class="text-danger">An error occurred while filtering properties</p>
                <p class="text-muted">${error.message}</p>
            </div>`;
        paginationContainer.innerHTML = '';
    });
}

function updatePagination(pagination) {
    const container = document.getElementById('pagination-container');
    const { current_page, total_pages } = pagination;

    let html = '<div class="pagination"><ul>';
    
    // Previous button
    html += `<li class="${current_page === 1 ? 'disabled' : ''}">
        <a href="javascript:void(0)" onclick="changePage(${current_page - 1})" ${current_page === 1 ? 'disabled' : ''}>Prev</a>
    </li>`;

    // Page numbers
    for (let i = 1; i <= total_pages; i++) {
        html += `<li class="${i === current_page ? 'active' : ''}">
            <a href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
        </li>`;
    }

    // Next button
    html += `<li class="${current_page === total_pages ? 'disabled' : ''}">
        <a href="javascript:void(0)" onclick="changePage(${current_page + 1})" ${current_page === total_pages ? 'disabled' : ''}>Next</a>
    </li>`;

    html += '</ul></div>';
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadProperties(page, currentFilters);
}

// Update form submit handler
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const filters = {};
    formData.forEach((value, key) => {
        if(value) {
            filters[key] = value;
        }
    });
    
    currentFilters = filters;
    currentPage = 1;
    loadProperties(1, filters);
});

// Update the initial load to use URL parameters
const urlParams = new URLSearchParams(window.location.search);
const initialFilters = {};

// Convert URL parameters to filters object
urlParams.forEach((value, key) => {
    if (value) {
        initialFilters[key] = value;
        
        // Also set form values
        const input = document.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = value;
            
            // For select elements, trigger bootstrap-select update
            if (input.tagName === 'SELECT') {
                $(input).selectpicker('val', value);
            }
        }
    }
});

// Initial load with URL parameters
loadProperties(1, initialFilters);
</script>
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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    
<?php include("include/alert.php"); ?>

<script src="assets/js/grid.js">
    
</script>
<link rel="stylesheet" href="assets/css/grid.css">
<?php include("include/footer.php"); ?>
</body>
</html>